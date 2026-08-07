// MR cookie-minter.
//
// Invocation (called by PHP via `php artisan mr:refresh-session`):
//   node mint-cookies.js --config /path/to/config.json
//
// Config JSON:
//   {
//     "username":         "hafog49849@aghism.com",
//     "password":         "kingumer",
//     "baseHost":         "massagerepublic.com",             // MUST match what the Guzzle scraper hits
//     "hostResolve":      "massagerepublic.com:172.66.43.117",// optional (mirrors CURLOPT_RESOLVE)
//     "outputPath":       "/home/evoory/public_html/storage/app/mr-session.json",
//     "headless":         true,
//     "capsolverApiKey":  "CAP-XXXX..."                       // optional — enables Turnstile fallback when patchright can't clear CF alone
//   }
//
// What it does:
//   1. Launches headless Chromium from the SERVER's IP (same as the Guzzle
//      scraper will use later). Chromium's real JS engine solves Cloudflare's
//      "Just a moment…" challenge automatically, so cf_clearance gets minted
//      bound to (server IP, Chromium's UA) — which is exactly what Guzzle
//      needs.
//   2. Logs in with credentials so _session_id is populated too.
//   3. Extracts all cookies from the browser context.
//   4. Writes them to `outputPath` as a JSON array in Cookie-Editor format
//      (the same shape MassageRepublicScraper::loadSessionFile() already
//      accepts — no PHP-side changes required).
//
// Emits a single JSON line to stdout on completion:
//   {"ok":true,"cookiesWritten":8,"hasCfClearance":true,"hasSessionId":true,"outputPath":"..."}
//   {"ok":false,"error":"..."}
//
// Reuses the mr-phone-worker's playwright install — no separate `npm install`.

// patchright is a Playwright fork with baked-in anti-fingerprinting patches
// specifically for modern Cloudflare Turnstile / "Under Attack Mode". Drop-in
// replacement for `playwright` — same API. Vanilla playwright's fingerprint
// was being flagged (challenge never resolved after 30s of polling).
import { chromium } from 'patchright';
import { readFileSync, writeFileSync, existsSync, mkdirSync, unlinkSync } from 'node:fs';
import { dirname } from 'node:path';
import { homedir } from 'node:os';

const emit = (obj) => process.stdout.write(JSON.stringify(obj) + '\n');
const step = (label) => process.stderr.write(`[step ${Date.now()}] ${label}\n`);

const withTimeout = (p, ms, label) => Promise.race([
  p,
  new Promise((_, reject) => setTimeout(() => reject(new Error(`step timed out after ${ms}ms: ${label}`)), ms)),
]);

function parseArgs() {
  const args = process.argv.slice(2);
  const out = {};
  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--config') out.config = args[++i];
  }
  return out;
}

// Copy of mr-phone-worker's age-check dismisser. Inlined rather than imported
// so this script stays self-contained and can be updated independently.
async function dismissAgeCheck(page) {
  const modal = page.locator('#age-check');
  await modal.waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
  if (!(await modal.count())) return;
  if (!(await modal.isVisible().catch(() => false))) return;

  const dismissBtn = modal.locator('[data-dismiss="modal"]').first();
  if (await dismissBtn.count()) {
    await dismissBtn.click({ timeout: 3000, force: true }).catch(() => {});
  }

  if (await modal.isVisible().catch(() => false)) {
    const agreeBtn = modal.locator('a, button').filter({
      hasText: /agree|enter|continue|yes|accept|i am|over\s*18|18\s*\+|18\s*(or\s*older|and\s*over)|adult/i,
    }).first();
    if (await agreeBtn.count()) {
      await agreeBtn.click({ timeout: 3000, force: true }).catch(() => {});
    }
  }

  if (await modal.isVisible().catch(() => false)) {
    await page.evaluate(() => {
      document.querySelectorAll('#age-check, .modal-backdrop').forEach((el) => el.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
    }).catch(() => {});
  }
}

/**
 * Ask CapSolver to solve the Turnstile widget on the current page. Uses
 * their AntiTurnstileTaskProxyLess task type — cheapest and doesn't need
 * us to expose a proxy. Returns the solved token string.
 *
 * Two-phase API:
 *   1. POST /createTask → get taskId
 *   2. Poll POST /getTaskResult until status === "ready" → get token
 *
 * Typical solve time: 10-25s. Cost: ~$0.001/solve.
 */
async function solveTurnstileWithCapsolver(sitekey, pageUrl, apiKey) {
  step(`capsolver: creating task (sitekey=${sitekey.slice(0, 12)}…)`);

  const createResp = await fetch('https://api.capsolver.com/createTask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      clientKey: apiKey,
      task: {
        type: 'AntiTurnstileTaskProxyLess',
        websiteURL: pageUrl,
        websiteKey: sitekey,
      },
    }),
  });
  if (!createResp.ok) {
    throw new Error(`capsolver createTask HTTP ${createResp.status}`);
  }
  const createData = await createResp.json();
  if (createData.errorId !== 0) {
    throw new Error(`capsolver createTask: ${createData.errorDescription || createData.errorCode}`);
  }
  const taskId = createData.taskId;
  step(`capsolver: task ${taskId} created, polling…`);

  const deadline = Date.now() + 120000; // CapSolver docs say 15-30s typical, allow 2min
  while (Date.now() < deadline) {
    await new Promise((r) => setTimeout(r, 3000));
    const pollResp = await fetch('https://api.capsolver.com/getTaskResult', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clientKey: apiKey, taskId }),
    });
    if (!pollResp.ok) continue; // transient, keep polling
    const pollData = await pollResp.json();
    if (pollData.errorId !== 0) {
      throw new Error(`capsolver getTaskResult: ${pollData.errorDescription || pollData.errorCode}`);
    }
    if (pollData.status === 'ready') {
      const token = pollData.solution && pollData.solution.token;
      if (!token) throw new Error('capsolver returned ready status but no token');
      step(`capsolver: got token (${token.length} chars)`);
      return token;
    }
    // status === "processing", keep waiting
  }
  throw new Error('capsolver polling timed out after 120s');
}

/**
 * Full-page Cloudflare solve via CapSolver's AntiCloudflareTaskS2. Used when
 * the sitekey extraction fails — typically because CF is serving a challenge
 * type that never puts a Turnstile widget on the page, or is in "Under Attack
 * Mode" where the JS obfuscates everything and never renders Turnstile.
 *
 * Returns { cookies: [{name,value,domain,path,expires,httpOnly,secure}], userAgent }
 * that we then inject into the Playwright context to bypass CF on subsequent
 * navigation. Cost: ~$0.002-0.005 per solve (2-5× a Turnstile solve).
 *
 * WARNING: The cf_clearance cookie returned by CapSolver was minted using
 * CapSolver's IP, not ours. Some sites strictly IP-bind cf_clearance and
 * will reject it — for those sites, only a residential proxy or a real
 * browser on the target IP will work. We'll know inside 60s whether MR is
 * strict or lenient.
 */
async function solveCloudflareWithCapsolver(pageUrl, apiKey) {
  step(`capsolver: creating AntiCloudflareTaskS2 for ${pageUrl}`);

  const createResp = await fetch('https://api.capsolver.com/createTask', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      clientKey: apiKey,
      task: {
        type: 'AntiCloudflareTaskS2',
        websiteURL: pageUrl,
      },
    }),
  });
  if (!createResp.ok) {
    throw new Error(`capsolver createTask (S2) HTTP ${createResp.status}`);
  }
  const createData = await createResp.json();
  if (createData.errorId !== 0) {
    throw new Error(`capsolver createTask (S2): ${createData.errorDescription || createData.errorCode}`);
  }
  const taskId = createData.taskId;
  step(`capsolver: S2 task ${taskId} created, polling…`);

  const deadline = Date.now() + 180000; // Full-page solve can take 30-90s
  while (Date.now() < deadline) {
    await new Promise((r) => setTimeout(r, 4000));
    const pollResp = await fetch('https://api.capsolver.com/getTaskResult', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ clientKey: apiKey, taskId }),
    });
    if (!pollResp.ok) continue;
    const pollData = await pollResp.json();
    if (pollData.errorId !== 0) {
      throw new Error(`capsolver getTaskResult (S2): ${pollData.errorDescription || pollData.errorCode}`);
    }
    if (pollData.status === 'ready') {
      const solution = pollData.solution || {};
      const cookies = solution.cookies || [];
      const userAgent = solution.userAgent || null;
      if (!Array.isArray(cookies) || cookies.length === 0) {
        throw new Error('capsolver S2 returned ready but no cookies');
      }
      step(`capsolver: S2 solved (${cookies.length} cookies, UA=${(userAgent || '').slice(0, 40)}…)`);
      return { cookies, userAgent };
    }
  }
  throw new Error('capsolver S2 polling timed out after 180s');
}

/**
 * Extract the Turnstile sitekey from the current CF challenge page.
 *
 * CF's Under Attack Mode page lazily injects the Turnstile widget after
 * the initial DOM is ready — so we FIRST wait for the widget markup to
 * appear (up to 15s), THEN try DOM extraction. If DOM extraction still
 * comes up empty (some CF variants render the widget inside a sandboxed
 * iframe we can't reach via querySelector), we fall back to raw-HTML
 * regex — the sitekey is always in the HTML string somewhere, even when
 * it's not in an accessible DOM node.
 *
 * Returns { sitekey, source } on success, null on failure.
 */
async function extractTurnstileSitekey(page) {
  // Give the CF challenge script time to call turnstile.render(), which
  // our addInitScript-installed hook captures on window.__mrTsKey. This
  // is the ONLY reliable extraction path when CF is in "render=explicit"
  // mode — the sitekey never lands in the HTML in that mode.
  //
  // We also wait on standard widget selectors as a secondary signal for
  // older challenge variants that DO put the sitekey in a data-attr.
  step('cf: waiting for turnstile.render() call or widget mount…');
  const deadline = Date.now() + 20000;
  while (Date.now() < deadline) {
    // Primary: check the intercepted sitekey.
    const intercepted = await page.evaluate(() => window.__mrTsKey || null).catch(() => null);
    if (intercepted) {
      step(`cf: sitekey intercepted from turnstile.render(): ${intercepted.slice(0, 12)}…`);
      return { sitekey: intercepted, source: 'turnstile.render() interceptor' };
    }
    // Secondary: static DOM selectors.
    const hasWidget = await page.evaluate(() => {
      return !!(
        document.querySelector('[data-sitekey]') ||
        document.querySelector('.cf-turnstile') ||
        document.querySelector('iframe[src*="challenges.cloudflare.com"]')
      );
    }).catch(() => false);
    if (hasWidget) {
      step('cf: Turnstile widget detected in DOM (no render() call yet)');
      break;
    }
    await new Promise((r) => setTimeout(r, 500));
  }

  // Try DOM extraction first (fastest, most precise).
  const domResult = await page.evaluate(() => {
    const el = document.querySelector('[data-sitekey]');
    if (el) return { sitekey: el.getAttribute('data-sitekey'), source: 'data-sitekey attr' };

    const iframe = document.querySelector('iframe[src*="challenges.cloudflare.com"]');
    if (iframe) {
      const m = iframe.src.match(/\/turnstile\/[^/]+\/([^/?]+)/);
      if (m) return { sitekey: m[1], source: 'turnstile iframe src' };
    }

    for (const s of document.querySelectorAll('script')) {
      const m = (s.textContent || '').match(/sitekey["'\s:]+["']([0-9A-Za-z_-]{15,})["']/);
      if (m) return { sitekey: m[1], source: 'inline script' };
    }

    // window._cf_chl_opt sometimes carries the sitekey as .cvId or in nested config
    try {
      const opt = window._cf_chl_opt;
      if (opt && typeof opt === 'object') {
        const flat = JSON.stringify(opt);
        const m = flat.match(/"([0-9]x[0-9A-Za-z_-]{20,})"/);
        if (m) return { sitekey: m[1], source: 'window._cf_chl_opt' };
      }
    } catch (e) {}

    return null;
  });

  if (domResult && domResult.sitekey) {
    return domResult;
  }

  // Fallback: raw HTML regex. CF's sitekey format is `0x` + exactly 22
  // alphanumeric characters (24 chars total). This catches cases where the
  // widget is inside a sandboxed iframe or the sitekey is embedded in a
  // dynamic script (render=explicit mode) that querySelector misses.
  step('cf: DOM extraction empty — regex-scanning raw HTML');
  const html = await page.content().catch(() => '');

  // Pattern 1: explicit data-sitekey attribute (most reliable when present)
  let m = html.match(/data-sitekey\s*=\s*["']([0-9A-Za-z_-]{15,})["']/i);
  if (m) return { sitekey: m[1], source: 'raw HTML data-sitekey' };

  // Pattern 2: sitekey: "…" or sitekey='…' as a JS/JSON key.
  // Length is 18-30 chars after `0x` because sitekeys are not fixed-length:
  //   • standard CF Turnstile: 22 chars after 0x (24 total)
  //   • MR's live sitekey:     23 chars after 0x (25 total, e.g. 0xxADhZDZMRdRMq3zYVnvjA2c)
  //   • test keys:             22 chars after 0x
  m = html.match(/sitekey["'\s:]+["']?(0x[a-zA-Z0-9]{18,30})["']?/i);
  if (m) return { sitekey: m[1], source: 'raw HTML sitekey= key' };

  // Pattern 3: find all canonical CF sitekeys (0x + 18-30 alphanumeric, no
  // surrounding quote requirement, delimited by non-alphanumeric on both
  // sides). Return the one that appears most often — real sitekeys are
  // referenced 2-3 times in the challenge page (widget div, callback
  // registration, render call), while noise like SVG path data or hash
  // fragments appears once at most.
  const candidates = [...html.matchAll(/(?<![a-zA-Z0-9])(0x[a-zA-Z0-9]{18,30})(?![a-zA-Z0-9])/g)]
    .map((match) => match[1]);
  if (candidates.length > 0) {
    const counts = {};
    for (const c of candidates) counts[c] = (counts[c] || 0) + 1;
    // Sort by frequency descending, take the most repeated one.
    const [best] = Object.entries(counts).sort((a, b) => b[1] - a[1]);
    if (best) {
      return { sitekey: best[0], source: `raw HTML 0x…-sitekey (found ${best[1]}x)` };
    }
  }

  return null;
}

/**
 * Best-effort dump of the current page's HTML, title, and URL to /tmp so
 * we can inspect what CF actually served when the challenge-solver fails.
 * Filenames include a timestamp so cpanel /tmp cleanup won't nuke them
 * mid-investigation. Errors here are swallowed — this is diagnostics only.
 */
async function dumpChallengePage(page, tag) {
  try {
    const ts = Date.now();
    const base = `/tmp/mr-cf-${tag}-${ts}`;
    const html = await page.content().catch(() => '');
    const title = await page.title().catch(() => '');
    const url = page.url();
    if (html) {
      writeFileSync(`${base}.html`, `<!-- URL: ${url} -->\n<!-- Title: ${title} -->\n${html}`);
    }
    // A short signature the operator can grep for on their end to see
    // which challenge type CF is serving — cf-turnstile, cf-mitigated,
    // challenge-platform, etc. Written to a separate file for quick reading.
    const signatures = {
      hasTurnstileDiv: /cf-turnstile|data-sitekey/i.test(html),
      hasChallengeIframe: /challenges\.cloudflare\.com/i.test(html),
      hasCfChlOpt: /_cf_chl_opt|window\._cf_chl/i.test(html),
      hasCfMitigated: /cf-mitigated|mitigated: challenge/i.test(html),
      hasCfBrowserVerification: /Checking your browser|browser verification/i.test(html),
      hasHcaptcha: /hcaptcha\.com/i.test(html),
      hasCfChlScript: /\/cdn-cgi\/challenge-platform\/h\/[bg]\/scripts/i.test(html),
      hasScriptSrcTurnstile: /challenges\.cloudflare\.com\/turnstile\//i.test(html),
    };
    writeFileSync(`${base}.signatures.json`, JSON.stringify({ url, title, signatures }, null, 2));
    return base;
  } catch (e) {
    return null;
  }
}

async function passCloudflareChallenge(page, baseUrl, capsolverApiKey) {
  step(`cf: goto ${baseUrl}`);
  await withTimeout(page.goto(baseUrl, { waitUntil: 'domcontentloaded', timeout: 60000 }), 75000, 'goto homepage');

  // Fast path: give patchright ~15s to solve on its own. When CF is
  // lenient (~30% of runs) it clears in under 5s. When it's aggressive,
  // patchright never clears no matter how long we wait — so bail early
  // rather than eating the full timeout, and hand off to CapSolver.
  const fastPathDeadline = Date.now() + 15000;
  while (Date.now() < fastPathDeadline) {
    const title = await page.title().catch(() => '');
    const body = await page.content().catch(() => '');
    if (!/just a moment/i.test(title) && !/challenge-platform/i.test(body)) {
      step(`cf: cleared without capsolver (title="${title.slice(0, 60)}")`);
      return true;
    }
    await new Promise((r) => setTimeout(r, 2000));
  }

  // Slow path: still on the challenge → try CapSolver if configured.
  if (!capsolverApiKey) {
    throw new Error('Cloudflare challenge did not clear within 15s and no CAPSOLVER_API_KEY is set');
  }

  step('cf: patchright could not solve — falling back to CapSolver');
  const extracted = await extractTurnstileSitekey(page);

  // Path A: sitekey found → cheap AntiTurnstileTaskProxyLess ($0.001/solve).
  // Path B: no sitekey → full-page AntiCloudflareTaskS2 ($0.002-0.005/solve).
  // Path B injects the returned cookies+UA into the Playwright context and
  // reloads the page, then continues with login as normal.
  if (!extracted || !extracted.sitekey) {
    step('cf: no Turnstile widget on page → falling back to AntiCloudflareTaskS2 (full-page solve)');

    let solution;
    try {
      solution = await solveCloudflareWithCapsolver(page.url(), capsolverApiKey);
    } catch (e) {
      // If S2 fails, dump the page + candidate diagnostics so we can see why
      const dumpBase = await dumpChallengePage(page, 'no-sitekey-s2-failed');
      throw new Error(`AntiCloudflareTaskS2 failed: ${e.message} — dump at ${dumpBase || 'n/a'}.html`);
    }

    // Inject CapSolver's cookies into the Playwright context. Convert their
    // format (which mirrors DevTools Cookie object) to Playwright's addCookies
    // shape. Domain normalization: default to the current host if missing.
    const context = page.context();
    const targetHost = new URL(baseUrl).hostname;
    const playwrightCookies = solution.cookies.map((c) => {
      const cookie = {
        name: c.name,
        value: c.value,
        domain: c.domain || `.${targetHost}`,
        path: c.path || '/',
        httpOnly: c.httpOnly !== false,
        secure: c.secure !== false,
        sameSite: c.sameSite || 'None',
      };
      if (c.expires && c.expires > 0) cookie.expires = c.expires;
      return cookie;
    });
    await context.addCookies(playwrightCookies);
    step(`cf: injected ${playwrightCookies.length} cookies from CapSolver into browser context`);

    // Match CapSolver's User-Agent so cf_clearance's UA-binding validation
    // passes on subsequent requests. cf_clearance is bound to (IP, UA) — if
    // we send a different UA than the one that solved the challenge, CF
    // rejects the cookie regardless of what the value is.
    if (solution.userAgent) {
      await context.setExtraHTTPHeaders({ 'User-Agent': solution.userAgent });
      step(`cf: switched browser UA to CapSolver's: ${solution.userAgent.slice(0, 60)}…`);
    }

    // Reload the page with the freshly-injected cookies. If MR doesn't strictly
    // IP-bind cf_clearance, CF will honor CapSolver's cookie and pass us through.
    step('cf: reloading page with injected CapSolver cookies…');
    await withTimeout(page.goto(baseUrl, { waitUntil: 'domcontentloaded', timeout: 60000 }), 75000, 'reload with cookies');

    // Verify CF is now cleared
    const verifyDeadline = Date.now() + 20000;
    while (Date.now() < verifyDeadline) {
      const title = await page.title().catch(() => '');
      const body = await page.content().catch(() => '');
      if (!/just a moment|performing security/i.test(title) && !/challenge-platform/i.test(body)) {
        step(`cf: cleared via AntiCloudflareTaskS2 (title="${title.slice(0, 60)}")`);
        return true;
      }
      await new Promise((r) => setTimeout(r, 2000));
    }

    // Still challenging → MR strictly IP-binds and we've hit the wall
    const dumpBase = await dumpChallengePage(page, 'after-s2-inject');
    throw new Error(
      `CapSolver returned cookies but CF still challenging after cookie injection — MR is strictly IP-binding cf_clearance. Only a residential proxy will work from here. Dump at ${dumpBase || 'n/a'}.html`
    );
  }

  // Path A: sitekey found → cheap Turnstile-only solve
  step(`cf: found Turnstile sitekey via ${extracted.source}: ${extracted.sitekey.slice(0, 12)}…`);

  const token = await solveTurnstileWithCapsolver(extracted.sitekey, page.url(), capsolverApiKey);

  // Inject the solved token into the page. Cloudflare's challenge widget
  // exposes a global window.turnstile callback and/or a hidden input
  // named cf-turnstile-response. Fire both — one of them will trigger CF
  // to accept the token and set cf_clearance.
  step('cf: injecting capsolver token…');
  await page.evaluate((t) => {
    // Method A: hidden input the challenge form will submit
    const input = document.querySelector('input[name="cf-turnstile-response"]');
    if (input) {
      input.value = t;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
    // Method B: some CF pages expose a global callback registered via
    // data-callback="…" on the widget div — try to invoke it directly.
    try {
      const widget = document.querySelector('[data-callback]');
      if (widget) {
        const cbName = widget.getAttribute('data-callback');
        if (cbName && typeof window[cbName] === 'function') {
          window[cbName](t);
        }
      }
    } catch (e) {}
    // Method C: submit the outer form if there is one — some CF Under
    // Attack Mode pages use a plain <form> that just needs to POST back.
    try {
      const form = document.querySelector('form');
      if (form && (typeof form.requestSubmit === 'function')) form.requestSubmit();
    } catch (e) {}
  }, token);

  // Give CF a moment to accept the token and issue cf_clearance, then
  // navigate to the real page to verify.
  step('cf: waiting for clearance after token injection…');
  const acceptDeadline = Date.now() + 30000;
  while (Date.now() < acceptDeadline) {
    await new Promise((r) => setTimeout(r, 2000));
    const title = await page.title().catch(() => '');
    if (!/just a moment/i.test(title)) {
      step(`cf: cleared via capsolver (title="${title.slice(0, 60)}")`);
      return true;
    }
  }
  throw new Error('Cloudflare did not accept CapSolver token within 30s');
}

async function login(page, baseUrl, username, password) {
  step(`login: goto ${baseUrl}/sign-in`);
  await withTimeout(page.goto(`${baseUrl}/sign-in`, { waitUntil: 'domcontentloaded', timeout: 45000 }), 60000, 'goto sign-in');

  // If cf_clearance from the homepage visit already got us signed in via
  // a persisted session (rare on first mint, common on re-mints), the /sign-in
  // URL will 302 to /my-profile. Detect that and short-circuit.
  const landedUrl = page.url();
  if (!/\/sign[-_]?in\b/i.test(landedUrl)) {
    step(`login: already signed in (landed on ${landedUrl})`);
    return;
  }

  await withTimeout(dismissAgeCheck(page), 10000, 'dismissAgeCheck').catch(() => {});

  step('login: fill email');
  await withTimeout(page.fill('input[name="account[email]"]', username), 10000, 'fill email');
  step('login: fill password');
  await withTimeout(page.fill('input[name="account[password]"]', password), 10000, 'fill password');
  await withTimeout(dismissAgeCheck(page), 10000, 'dismissAgeCheck #2').catch(() => {});

  step('login: submit form via JS');
  const submitted = await withTimeout(page.evaluate(() => {
    const pw = document.querySelector('input[name="account[password]"]');
    const form = pw ? pw.form : document.querySelector('form');
    if (!form) return false;
    if (typeof form.requestSubmit === 'function') form.requestSubmit();
    else form.submit();
    return true;
  }), 10000, 'form.submit()').catch(() => false);
  if (!submitted) {
    throw new Error('login form not found on /sign-in — MR markup may have changed');
  }

  step('login: polling for signed-in state...');
  const deadline = Date.now() + 60000;
  while (Date.now() < deadline) {
    const currentUrl = page.url();

    // Signal 1: URL is a known post-login page.
    if (!/\/sign[-_]?in\b/i.test(currentUrl) &&
        /\/(my-profile|dashboard|account|listings\/mine|inbox)/i.test(currentUrl)) {
      step(`login: OK — post-login URL (${currentUrl})`);
      return;
    }

    // Signal 2: URL is anything OTHER than /sign-in AND the page body
    // contains a sign-out link. MR often redirects newly-authenticated
    // users to `/` (homepage) instead of a dashboard URL — the URL
    // pattern above misses that case. Body-content check catches it.
    // Mirrors mr-phone-worker/worker.js's belt-and-braces logic.
    if (!/\/sign[-_]?in\b/i.test(currentUrl)) {
      const body = await withTimeout(page.content(), 5000, 'page.content').catch(() => '');
      if (/sign[-_]?out|Sign Out|logout|Log Out|\/my-profile|\/my-account/i.test(body)) {
        step(`login: OK — sign-out marker in body (${currentUrl})`);
        return;
      }
    }

    // Signal 3: still on /sign-in and MR showed an inline error → surface it.
    if (/\/sign[-_]?in\b/i.test(currentUrl)) {
      const errText = await withTimeout(
        page.locator('.alert-danger, .flash-error').first().textContent({ timeout: 500 }),
        2000, 'locator.textContent'
      ).catch(() => null);
      if (errText && errText.trim() !== '') {
        throw new Error(`login rejected: ${errText.trim().slice(0, 120)}`);
      }
    }

    await new Promise((r) => setTimeout(r, 1500));
  }
  throw new Error(`login failed (no signed-in signal after 60s, url=${page.url()})`);
}

/**
 * Convert Playwright cookies to the Cookie-Editor JSON array format that
 * MassageRepublicScraper::loadSessionFile() expects. Also filters out cookies
 * for other domains (analytics tags on subdomains, etc.) — we only want ones
 * whose domain matches or is a suffix of the MR host.
 */
function cookiesToLoaderFormat(cookies, targetHost) {
  const targetSuffix = targetHost.replace(/^www\./, '');
  return cookies
    .filter((c) => {
      const d = (c.domain || '').replace(/^\./, '');
      return d === targetSuffix || d.endsWith('.' + targetSuffix) || targetSuffix.endsWith('.' + d);
    })
    .map((c) => ({
      name: c.name,
      value: c.value,
      domain: c.domain,
      path: c.path || '/',
      expirationDate: c.expires && c.expires > 0 ? c.expires : undefined,
      secure: !!c.secure,
      httpOnly: !!c.httpOnly,
      sameSite: c.sameSite || 'Lax',
    }));
}

async function main() {
  const { config: configPath } = parseArgs();
  if (!configPath) {
    emit({ ok: false, error: 'missing --config' });
    process.exit(2);
  }

  let config;
  try {
    config = JSON.parse(readFileSync(configPath, 'utf8'));
  } catch (e) {
    emit({ ok: false, error: `cannot read config: ${e.message}` });
    process.exit(2);
  }

  if (!config.username || !config.password || !config.baseHost || !config.outputPath) {
    emit({ ok: false, error: 'config missing one of: username, password, baseHost, outputPath' });
    process.exit(2);
  }

  const baseUrl = 'https://' + String(config.baseHost).replace(/^https?:\/\//, '').replace(/\/+$/, '');

  const launchOpts = {
    headless: config.headless !== false,
    args: [
      '--disable-blink-features=AutomationControlled',
      '--disable-dev-shm-usage',
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-gpu',
      '--disable-background-networking',
      '--disable-features=TranslateUI,BlinkGenPropertyTrees',
      '--disable-extensions',
      '--disable-sync',
    ],
  };

  if (config.hostResolve) {
    const rule = config.hostResolve.includes(' ')
      ? config.hostResolve
      : config.hostResolve.replace(':', ' ');
    launchOpts.args.push(`--host-resolver-rules=MAP ${rule}`);
  }

  // patchright works best with launchPersistentContext against a real
  // profile directory — the profile accumulates across runs (site data,
  // cookies, fingerprint entropy), which raises Cloudflare's trust score
  // over time. Vanilla `launch()` starts fresh every run and reads as an
  // obvious bot. See patchright README > "Persistent Context".
  const userDataDir = config.userDataDir || `${homedir()}/.cache/mr-cookie-minter/profile`;
  if (!existsSync(userDataDir)) {
    mkdirSync(userDataDir, { recursive: true });
    step(`profile: created new persistent profile at ${userDataDir}`);
  } else {
    step(`profile: reusing existing persistent profile at ${userDataDir}`);
  }

  step('boot: chromium.launchPersistentContext');
  const context = await withTimeout(
    chromium.launchPersistentContext(userDataDir, {
      ...launchOpts,
      userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
      locale: 'en-US',
      timezoneId: 'Europe/London',
      ignoreHTTPSErrors: true,
      viewport: { width: 1366, height: 768 },
    }),
    75000,
    'chromium.launchPersistentContext'
  );

  // patchright ships with navigator.webdriver already patched, but stack
  // this on top belt-and-braces (older Chromium releases in Playwright
  // sometimes reintroduce the getter).
  //
  // ALSO install a hook that intercepts `turnstile.render(container, opts)`
  // calls. In CF's newer "render=explicit" challenge mode, the sitekey is
  // never present in the HTML — it lives inside obfuscated CF JavaScript
  // and only becomes visible at the moment their code calls
  // `turnstile.render()` with the sitekey as `opts.sitekey`. Trapping that
  // call lets us capture the sitekey no matter how well CF obfuscates it.
  await withTimeout(context.addInitScript(() => {
    try { Object.defineProperty(navigator, 'webdriver', { get: () => undefined }); } catch (e) {}

    // Turnstile intercept: poll for window.turnstile every 50ms, and as
    // soon as it appears, wrap render() so any sitekey passed through it
    // is captured on window.__mrTsKey. 30s cap so this doesn't run forever
    // on non-Turnstile pages.
    try {
      window.__mrTsKey = null;
      let patched = false;
      const iv = setInterval(() => {
        try {
          if (patched) return;
          const t = window.turnstile;
          if (t && typeof t.render === 'function') {
            const orig = t.render.bind(t);
            t.render = function (container, options) {
              try {
                if (options && options.sitekey) {
                  window.__mrTsKey = options.sitekey;
                }
              } catch (e) {}
              return orig(container, options);
            };
            patched = true;
            clearInterval(iv);
          }
        } catch (e) {}
      }, 50);
      setTimeout(() => { try { clearInterval(iv); } catch (e) {} }, 30000);
    } catch (e) {}
  }), 10000, 'addInitScript').catch(() => {});

  try {
    const page = await context.newPage();
    try {
      await passCloudflareChallenge(page, baseUrl, config.capsolverApiKey || null);
      await login(page, baseUrl, config.username, config.password);

      // Small settle — MR sometimes injects an extra cookie or two on the
      // post-login redirect target.
      await new Promise((r) => setTimeout(r, 1500));

      step('extract: context.cookies()');
      const allCookies = await context.cookies();
      const targetHost = config.baseHost.replace(/^https?:\/\//, '').replace(/\/+$/, '');
      const forLoader = cookiesToLoaderFormat(allCookies, targetHost);

      const hasCfClearance = forLoader.some((c) => c.name === 'cf_clearance');
      const hasSessionId = forLoader.some((c) => c.name === '_session_id');

      // Make sure the output dir exists (Laravel's storage/app should always
      // exist, but the config might point somewhere unusual for testing).
      const dir = dirname(config.outputPath);
      if (!existsSync(dir)) mkdirSync(dir, { recursive: true });
      writeFileSync(config.outputPath, JSON.stringify(forLoader, null, 2), 'utf8');
      step(`extract: wrote ${forLoader.length} cookies to ${config.outputPath}`);

      emit({
        ok: true,
        cookiesWritten: forLoader.length,
        hasCfClearance,
        hasSessionId,
        outputPath: config.outputPath,
      });
    } finally {
      await page.close().catch(() => {});
    }
  } catch (e) {
    emit({ ok: false, error: e.message });
    process.exit(1);
  } finally {
    // launchPersistentContext returns a context that owns its own browser
    // — closing the context also closes the underlying Chromium.
    await context.close().catch(() => {});
  }
}

main().catch((e) => {
  emit({ ok: false, error: `unhandled: ${e.message}` });
  process.exit(1);
});
