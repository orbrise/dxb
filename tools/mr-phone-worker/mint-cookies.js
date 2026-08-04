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
 * Extract the Turnstile sitekey from the current CF challenge page. Tries
 * several DOM locations because CF varies the markup between "invisible"
 * Turnstile, managed challenge, and Under Attack Mode.
 *
 * Returns { sitekey, source } on success, null on failure.
 */
async function extractTurnstileSitekey(page) {
  return await page.evaluate(() => {
    // 1. Explicit sitekey attribute on the widget div
    const el = document.querySelector('[data-sitekey]');
    if (el) return { sitekey: el.getAttribute('data-sitekey'), source: 'data-sitekey attr' };

    // 2. Challenge iframe src contains the sitekey: /turnstile/if/<version>/<sitekey>/…
    const iframe = document.querySelector('iframe[src*="challenges.cloudflare.com"]');
    if (iframe) {
      const m = iframe.src.match(/\/turnstile\/[^/]+\/([^/?]+)/);
      if (m) return { sitekey: m[1], source: 'turnstile iframe src' };
    }

    // 3. Inline scripts sometimes carry the sitekey as a config arg
    for (const s of document.querySelectorAll('script')) {
      const m = (s.textContent || '').match(/sitekey["'\s:]+["']([0-9A-Za-z_-]{15,})["']/);
      if (m) return { sitekey: m[1], source: 'inline script' };
    }

    // 4. Some CF challenge pages carry the sitekey in a global object
    //    called `window._cf_chl_opt` or similar. Grab whatever's on window
    //    for logging even if we can't find a sitekey.
    return null;
  });
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
  if (!extracted || !extracted.sitekey) {
    const dumpBase = await dumpChallengePage(page, 'no-sitekey');
    throw new Error(
      `CF challenge active but no Turnstile sitekey found. Dump: ${dumpBase || 'n/a'}.html and ${dumpBase || 'n/a'}.signatures.json — paste the signatures file so we can pick the right CapSolver task type.`
    );
  }
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
  await withTimeout(context.addInitScript(() => {
    try { Object.defineProperty(navigator, 'webdriver', { get: () => undefined }); } catch (e) {}
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
