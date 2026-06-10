// MR phone-reveal worker.
//
// Invocation (called by PHP):
//   node worker.js --config /path/to/config.json
//
// The config JSON looks like:
//   {
//     "username":     "orbrise@gmail.com",
//     "password":     "kingumer",
//     "hostResolve":  "massagerepublic.com:172.66.43.117",   // optional, mirrors CURLOPT_RESOLVE
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
import { readFileSync } from 'node:fs';

const BASE = 'https://massagerepublic.com';

const emit = (obj) => process.stdout.write(JSON.stringify(obj) + '\n');

function parseArgs() {
  const args = process.argv.slice(2);
  const out = {};
  for (let i = 0; i < args.length; i++) {
    if (args[i] === '--config') out.config = args[++i];
  }
  return out;
}

async function dismissAgeCheck(page) {
  // MR shows an "I am 18+" modal on first visit; click the agree button if present.
  const modal = page.locator('#age-check');
  if (!(await modal.count())) return;
  const agreeBtn = modal.locator('a, button').filter({ hasText: /agree|enter|continue|yes|18\+/i }).first();
  if (await agreeBtn.count()) {
    await agreeBtn.click({ timeout: 5000 }).catch(() => {});
  } else {
    // Fallback — first clickable element in the modal
    await modal.locator('a, button').first().click({ timeout: 5000 }).catch(() => {});
  }
  // Wait for the overlay to go away
  await modal.waitFor({ state: 'hidden', timeout: 5000 }).catch(() => {});
}

async function login(page, username, password) {
  await page.goto(`${BASE}/sign-in`, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await dismissAgeCheck(page);
  await page.fill('input[name="account[email]"]', username);
  await page.fill('input[name="account[password]"]', password);
  await Promise.all([
    page.waitForLoadState('domcontentloaded'),
    page.click('button[type="submit"], input[type="submit"]'),
  ]);
  const body = await page.content();
  if (!/sign[-_]out|Sign Out/i.test(body)) {
    throw new Error('login failed (no sign-out marker after POST)');
  }
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
    await trigger.click();

    // The reCAPTCHA modal renders, the user (browser) auto-solves the checkbox,
    // and on success the page injects <a class="tel" href="tel:+...">.
    // We poll up to 90s for the tel anchor.
    const telLocator = page.locator('a.tel, a[href^="tel:"]');
    await telLocator.first().waitFor({ state: 'attached', timeout: 90000 });
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

  const launchOpts = {
    headless: config.headless !== false,
  };

  // hostResolve mirrors CURLOPT_RESOLVE — needed because massagerepublic.com
  // doesn't resolve via normal DNS from this network.
  // Format expected: "host ip" or "host:port ip:port" (a single MAP rule)
  if (config.hostResolve) {
    const rule = config.hostResolve.includes(' ')
      ? config.hostResolve
      : config.hostResolve.replace(':', ' ');
    launchOpts.args = [`--host-resolver-rules=MAP ${rule}`];
  }

  const browser = await chromium.launch(launchOpts);
  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    ignoreHTTPSErrors: true,
    viewport: { width: 1280, height: 800 },
  });

  try {
    const loginPage = await context.newPage();
    try {
      await login(loginPage, config.username, config.password);
    } finally {
      await loginPage.close().catch(() => {});
    }

    for (const slug of (config.slugs || [])) {
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
