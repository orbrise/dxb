// MR phone-reveal worker.
//
// Invocation (called by PHP):
//   node worker.js --config /path/to/config.json
//
// The config JSON looks like:
//   {
//     "username":     "orbrise@gmail.com",
//     "password":     "kingumer",
//     "baseHost":     "massagerepublic.tk",                  // overrides the hardcoded default
//     "hostResolve":  "massagerepublic.tk:172.66.43.117",    // optional, mirrors CURLOPT_RESOLVE
//     "listingPath":  "/female-escorts-in-dubai",
//     "slugs":        ["tina-indian-independent-high-profile", "amanda-independence", ...],
//     "headless":     true
//   }
//
// Output: a single JSON line per slug to stdout, e.g.:
//   {"slug":"tina-indian-independent-high-profile","phone":"+91 8655 033 983","ok":true}
//   {"slug":"amanda-independence","phone":null,"ok":false,"error":"timeout"}
// PHP reads these line-by-line and updates `users_profiles.phone`.

import { chromium } from 'playwright';
import { readFileSync, existsSync, mkdirSync, unlinkSync } from 'node:fs';
import { homedir } from 'node:os';
import { dirname } from 'node:path';

// BASE host is driven by config.baseHost (from MASSAGE_REPUBLIC_HOST in
// .env). Default is the open-traffic mirror .tk — the .com host front-
// door is Cloudflare-gated and returns interstitials to headless
// browsers, so the .tk is the working target in practice.
let BASE = 'https://massagerepublic.tk';

const emit = (obj) => process.stdout.write(JSON.stringify(obj) + '\n');
// Step-tracker for hangs. Goes to stderr so PHP's regular JSON parsing
// stays clean, and we can dump it into the Laravel log to see exactly
// where the worker stalled when the 600s Symfony timeout fires.
const step = (label) => process.stderr.write(`[step ${Date.now()}] ${label}\n`);
// Hard-timeout wrapper so a hung Playwright call can't stall the whole
// process indefinitely. Wraps promise; if it doesn't settle in ms, rejects.
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

async function dismissAgeCheck(page) {
  // MR shows an "I am 18+" modal (#age-check .modal.in) on first visit.
  // Every downstream click fails with "modal intercepts pointer events"
  // until this is gone, so try every reasonable dismissal path.
  const modal = page.locator('#age-check');
  // Modal is often injected after DOMContentLoaded, so give it a beat.
  await modal.waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
  if (!(await modal.count())) return;
  if (!(await modal.isVisible().catch(() => false))) return;

  // 1. Bootstrap's canonical dismiss control.
  const dismissBtn = modal.locator('[data-dismiss="modal"]').first();
  if (await dismissBtn.count()) {
    await dismissBtn.click({ timeout: 3000, force: true }).catch(() => {});
  }

  // 2. Text-based match — wider set than before ("I am", "over 18",
  //    "adult", "accept", plus the original agree/enter/continue words).
  if (await modal.isVisible().catch(() => false)) {
    const agreeBtn = modal.locator('a, button').filter({
      hasText: /agree|enter|continue|yes|accept|i am|over\s*18|18\s*\+|18\s*(or\s*older|and\s*over)|adult/i,
    }).first();
    if (await agreeBtn.count()) {
      await agreeBtn.click({ timeout: 3000, force: true }).catch(() => {});
    }
  }

  // 3. Nuclear option — rip the modal + Bootstrap's stacking backdrop
  //    out of the DOM directly. Also strips `modal-open` from <body>,
  //    which otherwise leaves `overflow:hidden` on the page.
  if (await modal.isVisible().catch(() => false)) {
    await page.evaluate(() => {
      document.querySelectorAll('#age-check, .modal-backdrop').forEach((el) => el.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
    }).catch(() => {});
  }

  // Final sanity check — if the modal is still visible we can't proceed,
  // so surface the failure instead of letting the caller silently hang.
  const stillThere = await modal.isVisible().catch(() => false);
  if (stillThere) {
    throw new Error('age-check modal still visible after dismiss attempts');
  }
}

async function saveDebugSnapshot(page, tag) {
  // Called from catch-paths to leave a Chromium screenshot + HTML dump on
  // disk so we can see exactly what MR served when the worker gave up.
  // Files land in /tmp/mrphone-debug-<tag>-<ts>.png/.html so cpanel /tmp
  // cleaners won't nuke them mid-run. Errors here are swallowed — this
  // is best-effort diagnostics, not a critical path.
  try {
    const ts = Date.now();
    const base = `/tmp/mrphone-debug-${tag}-${ts}`;
    await page.screenshot({ path: `${base}.png`, fullPage: true }).catch(() => {});
    const html = await page.content().catch(() => '');
    if (html) {
      const { writeFileSync } = await import('node:fs');
      writeFileSync(`${base}.html`, html);
    }
    return base;
  } catch (e) {
    return null;
  }
}

async function login(page, username, password) {
  step('login: goto /sign-in');
  await withTimeout(
    page.goto(`${BASE}/sign-in`, { waitUntil: 'domcontentloaded', timeout: 30000 }),
    45000, 'goto /sign-in'
  );

  // Short-circuit: if the goto landed us anywhere other than /sign-in,
  // MR has already recognised us as signed in (via a persistent cookie
  // from an earlier run) — no need to fill the form.
  const landedUrl = page.url();
  if (!/\/sign[-_]?in\b/i.test(landedUrl)) {
    step(`login: already signed in (landed on ${landedUrl}) — skipping fill/submit`);
    return;
  }

  step('login: dismissAgeCheck #1');
  await withTimeout(dismissAgeCheck(page), 15000, 'dismissAgeCheck #1').catch((e) => step(`  warn: ${e.message}`));

  step('login: fill email');
  try {
    await withTimeout(page.fill('input[name="account[email]"]', username), 10000, 'fill email');
  } catch (e) {
    // Fill failed — either the form isn't there (Cloudflare challenge,
    // rate limit, whatever) or MR renamed the field. Snapshot the
    // page so we can see what was actually served.
    const dump = await withTimeout(saveDebugSnapshot(page, 'fill-email-fail'), 15000, 'snapshot').catch(() => null);
    throw new Error(`fill email failed: ${e.message} (url=${page.url()}, dump=${dump || 'n/a'})`);
  }
  step('login: fill password');
  await withTimeout(page.fill('input[name="account[password]"]', password), 10000, 'fill password');
  step('login: dismissAgeCheck #2');
  await withTimeout(dismissAgeCheck(page), 15000, 'dismissAgeCheck #2').catch((e) => step(`  warn: ${e.message}`));
  // Submit the login form via JS rather than clicking the button.
  // Why: on production, every page.click on the Sign In button hangs
  // at "performing click action" and hits the 15s timeout. That means
  // MR's onclick handler (Cloudflare Turnstile pre-flight, form
  // validation, whatever) is what's freezing — not the navigation.
  // Calling form.submit() bypasses the click event entirely and the
  // browser processes a native POST. Follow-up navigation is
  // observed by the polling loop below, same as before.
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
  let loggedIn = false;
  while (Date.now() < deadline) {
    // Trust the URL first — MR redirects to /my-profile/… /dashboard,
    // or /account after a successful login POST. Any of those means we
    // are signed in even if the sign-out link is buried in a dropdown
    // that hasn't been rendered/hydrated yet.
    const currentUrl = page.url();
    if (!/\/sign[-_]?in\b/i.test(currentUrl) &&
        /\/(my-profile|dashboard|account|listings\/mine|inbox)/i.test(currentUrl)) {
      loggedIn = true;
      break;
    }
    // Belt & braces: still accept a body-text sign-out marker in case
    // MR ever changes the redirect target.
    const body = await withTimeout(page.content(), 5000, 'page.content').catch(() => '');
    if (/sign[-_]out|Sign Out|logout|Log Out/i.test(body)) { loggedIn = true; break; }
    // If MR bounced us back to /sign-in with an error, surface that.
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
  if (!loggedIn) {
    step('login: poll deadline hit, taking dump');
    const dump = await withTimeout(saveDebugSnapshot(page, 'login-timeout'), 15000, 'saveDebugSnapshot').catch(() => null);
    const url = page.url();
    throw new Error(`login failed (no signed-in signal after 60s, url=${url}, dump=${dump || 'n/a'})`);
  }
  step(`login: OK (redirected to ${page.url()})`);
}

async function revealPhone(context, listingPath, slug) {
  const page = await context.newPage();
  const profileUrl = `${BASE}${listingPath}/${slug}`;
  try {
    await page.goto(profileUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await dismissAgeCheck(page);

    // The "Make a Call" / contact-phone link triggers the reveal modal.
    // Wait briefly in case the contact panel is hydrated after DOM-ready.
    const trigger = page.locator('a.contact-phone, a[href*="/show_phone"]').first();
    try {
      await trigger.waitFor({ state: 'attached', timeout: 8000 });
    } catch (e) {
      throw new Error('contact-phone link not on page');
    }
    // Re-dismiss if MR re-rendered the age modal on the profile page too.
    await dismissAgeCheck(page).catch(() => {});
    await trigger.click({ force: true });

    // The reCAPTCHA modal renders, the user (browser) auto-solves the checkbox,
    // and on success the page injects <a class="tel" href="tel:+...">.
    // We poll up to 90s for the tel anchor.
    const telLocator = page.locator('a.tel, a[href^="tel:"]');
    try {
      await telLocator.first().waitFor({ state: 'attached', timeout: 90000 });
    } catch (e) {
      const dump = await saveDebugSnapshot(page, `reveal-${slug}`);
      throw new Error(`tel: link never appeared (recaptcha likely didn't pass; dump=${dump || 'n/a'})`);
    }
    const href = await telLocator.first().getAttribute('href');
    const phone = href ? href.replace(/^tel:/, '').trim() : null;

    // After reveal, MR also injects messaging-app icons (WhatsApp / Telegram /
    // Signal / WeChat) for whichever services the number is registered with.
    // Detect by href hostname (robust) AND class name (fallback for WeChat,
    // which has no public URL scheme).
    const detection = await page.evaluate(() => {
      const has = (sel) => !!document.querySelector(sel);
      const apps = {
        whatsapp: has('a[href*="wa.me"], a[href*="api.whatsapp.com"], a.icon-whatsapp, [class*="whatsapp" i]'),
        telegram: has('a[href*="t.me"], a[href*="telegram.me"], a.icon-telegram, [class*="telegram" i]'),
        signal:   has('a[href*="signal.me"], a.icon-signal, [class*="signal" i]'),
        wechat:   has('a.icon-wechat, .icon-wechat, [class*="wechat" i]'),
      };
      // Diagnostic: also dump the modal HTML so we can fingerprint MR's markup
      // on the first run. Looks for the most likely modal/container around the
      // revealed tel link, falls back to the tel anchor's parent.
      let modalHtml = null;
      const tel = document.querySelector('a.tel, a[href^="tel:"]');
      if (tel) {
        const container =
          tel.closest('.callnow, .modal, .reveal, .phone-popup, .contact-popup, [class*="contact" i], [class*="phone" i]') ||
          tel.parentElement?.parentElement ||
          tel.parentElement;
        if (container) {
          modalHtml = (container.outerHTML || '').slice(0, 4000);
        }
      }
      return { apps, modalHtml };
    });

    return { phone: phone || null, apps: detection.apps, modalHtml: detection.modalHtml };
  } finally {
    await page.close().catch(() => {});
  }
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

  // Let the PHP layer flip us onto the open-traffic mirror if
  // massagerepublic.com starts refusing headless sessions again.
  if (config.baseHost) {
    BASE = 'https://' + String(config.baseHost).replace(/^https?:\/\//, '').replace(/\/+$/, '');
  }

  const launchOpts = {
    headless: config.headless !== false,
    args: [
      // Hides `navigator.webdriver = true`, which Cloudflare uses as an
      // instant-block signal for headless browsers.
      '--disable-blink-features=AutomationControlled',
      // Absolutely required on shared cPanel hosts: default Chromium
      // stores shared-memory tab state in /dev/shm which is only a few
      // MB on cPanel. Without this flag, Chromium OOM-crashes right
      // after login and every subsequent context.newPage() fails with
      // "Target page, context or browser has been closed" — which was
      // exactly the symptom in the last run.
      '--disable-dev-shm-usage',
      // No sandbox on cPanel — the shared-hosting user has no
      // CAP_SYS_ADMIN, and starting the sandbox helper itself
      // crashes the browser.
      '--no-sandbox',
      '--disable-setuid-sandbox',
      // No GPU on the server; without this Chromium probes for a GPU
      // and hangs briefly on startup.
      '--disable-gpu',
      // Kills a raft of background features (Translate, media router,
      // extension update pings) that use RAM we don't have.
      '--disable-background-networking',
      '--disable-features=TranslateUI,BlinkGenPropertyTrees',
      '--disable-extensions',
      '--disable-sync',
    ],
  };

  // hostResolve mirrors CURLOPT_RESOLVE — needed because massagerepublic.com
  // doesn't resolve via normal DNS from this network.
  // Format expected: "host ip" or "host:port ip:port" (a single MAP rule)
  if (config.hostResolve) {
    const rule = config.hostResolve.includes(' ')
      ? config.hostResolve
      : config.hostResolve.replace(':', ' ');
    launchOpts.args.push(`--host-resolver-rules=MAP ${rule}`);
  }

  // Persist login session between runs so we don't POST /accounts/sign_in
  // on every 30-min scheduler tick — that pattern is exactly what
  // Cloudflare / MR treats as bot behavior. On the next run the cookies
  // are already in place, the goto /sign-in redirects straight to
  // /my-account, and login() short-circuits (see the landedUrl check in
  // login()). If MR ever invalidates the cookie the login flow runs again
  // and re-saves fresh state. Path is overrideable via config.sessionPath.
  const sessionPath = config.sessionPath
    || `${homedir()}/.cache/mr-phone-worker/session.json`;
  const hasSession = existsSync(sessionPath);
  if (hasSession) {
    step(`session: reusing persisted state from ${sessionPath}`);
  } else {
    step(`session: no persisted state at ${sessionPath} — will login fresh`);
  }

  step('boot: chromium.launch');
  const browser = await withTimeout(chromium.launch(launchOpts), 60000, 'chromium.launch');
  step('boot: browser.newContext');
  const contextOpts = {
    // Chrome 130+ UA — MR/Cloudflare cross-checks reported version against
    // TLS/HTTP2 fingerprint; the old Chrome 126 string was flagged as a
    // bot signal. Bump when Chrome major bumps (roughly every 4 weeks).
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    locale: 'en-US',
    timezoneId: 'Europe/London',
    ignoreHTTPSErrors: true,
    viewport: { width: 1366, height: 768 },
  };
  if (hasSession) contextOpts.storageState = sessionPath;
  const context = await withTimeout(browser.newContext(contextOpts), 30000, 'browser.newContext');
  step('boot: addInitScript');
  // Kill navigator.webdriver so Cloudflare doesn't insta-block. Wrapped
  // in try/catch inside the browser context because navigator.webdriver
  // is a non-configurable getter in newer Chromium and Object.defineProperty
  // throws — which would crash the init and possibly hang subsequent
  // navigations.
  await withTimeout(context.addInitScript(() => {
    try { Object.defineProperty(navigator, 'webdriver', { get: () => undefined }); } catch (e) {}
  }), 10000, 'addInitScript').catch(() => {});

  // Chromium may have died between launch and the first newPage() call
  // (rare, but seen once during login when /dev/shm is exhausted).
  // Emit a disconnect handler so the fatal reason is surfaced, not
  // swallowed as N identical "context closed" per-slug errors.
  let browserDied = false;
  browser.on('disconnected', () => { browserDied = true; });

  try {
    const loginPage = await context.newPage();
    try {
      try {
        await login(loginPage, config.username, config.password);
      } catch (loginErr) {
        // If we had a persisted session that's now stale/rejected, wipe it
        // so the next run starts from a clean slate instead of looping on
        // the same dead cookies.
        if (hasSession) {
          try { unlinkSync(sessionPath); step(`session: removed stale state at ${sessionPath}`); }
          catch (e) { step(`session: could not remove stale state — ${e.message}`); }
        }
        throw loginErr;
      }
      // Save the fresh cookies so the next run skips the login form entirely
      // via the "already signed in" short-circuit in login(). Best-effort;
      // failure here doesn't break the current run.
      try {
        const dir = dirname(sessionPath);
        if (!existsSync(dir)) mkdirSync(dir, { recursive: true });
        await context.storageState({ path: sessionPath });
        step(`session: saved to ${sessionPath}`);
      } catch (e) {
        step(`session: save failed — ${e.message}`);
      }
    } finally {
      await loginPage.close().catch(() => {});
    }

    // Health check — if Chromium died during login, everything below
    // will fail identically. Bail loudly instead.
    if (browserDied || !browser.isConnected()) {
      throw new Error('browser crashed during login (likely OOM or /dev/shm exhaustion — check --disable-dev-shm-usage flag is present)');
    }

    for (const slug of (config.slugs || [])) {
      if (browserDied || !browser.isConnected()) {
        emit({ slug, phone: null, apps: null, modalHtml: null, ok: false,
               error: 'browser died mid-batch (previous slug likely OOM-killed Chromium)' });
        continue;
      }
      try {
        const { phone, apps, modalHtml } = await revealPhone(context, config.listingPath, slug);
        emit({ slug, phone, apps, modalHtml, ok: !!phone, error: phone ? null : 'no phone in DOM' });
      } catch (e) {
        emit({ slug, phone: null, apps: null, modalHtml: null, ok: false, error: e.message });
      }
    }
  } catch (e) {
    emit({ ok: false, error: `fatal: ${e.message}` });
    process.exitCode = 1;
  } finally {
    await context.close().catch(() => {});
    await browser.close().catch(() => {});
  }
}

main();
