// MR cookie-minter.
//
// Invocation (called by PHP via `php artisan mr:refresh-session`):
//   node mint-cookies.js --config /path/to/config.json
//
// Config JSON:
//   {
//     "username":    "hafog49849@aghism.com",
//     "password":    "kingumer",
//     "baseHost":    "massagerepublic.com",             // MUST match what the Guzzle scraper hits
//     "hostResolve": "massagerepublic.com:172.66.43.117",// optional (mirrors CURLOPT_RESOLVE)
//     "outputPath":  "/home/evoory/public_html/storage/app/mr-session.json",
//     "headless":    true
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
import { readFileSync, writeFileSync, existsSync, mkdirSync } from 'node:fs';
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

async function passCloudflareChallenge(page, baseUrl) {
  // Cloudflare's interstitial can take anywhere from 5s (cached challenge)
  // to 30-60s (fresh challenge with proof-of-work + Turnstile). We poll for
  // up to 60s. patchright's stealth patches let the challenge script's
  // browser-fingerprint checks pass; the rest is just waiting.
  step(`cf: goto ${baseUrl}`);
  await withTimeout(page.goto(baseUrl, { waitUntil: 'domcontentloaded', timeout: 60000 }), 75000, 'goto homepage');

  const deadline = Date.now() + 60000;
  while (Date.now() < deadline) {
    const title = await page.title().catch(() => '');
    const body = await page.content().catch(() => '');
    // Both signals — some CF versions swap the title, some don't.
    if (!/just a moment/i.test(title) && !/challenge-platform/i.test(body) && !/Just a moment\.\.\./i.test(body)) {
      step(`cf: cleared (title="${title.slice(0, 60)}")`);
      return true;
    }
    step(`cf: still challenging (title="${title.slice(0, 40)}"), waiting…`);
    await new Promise((r) => setTimeout(r, 2500));
  }
  throw new Error('Cloudflare challenge did not clear within 60s');
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
    if (!/\/sign[-_]?in\b/i.test(currentUrl) &&
        /\/(my-profile|dashboard|account|listings\/mine|inbox)/i.test(currentUrl)) {
      step(`login: OK (redirected to ${currentUrl})`);
      return;
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
      await passCloudflareChallenge(page, baseUrl);
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
