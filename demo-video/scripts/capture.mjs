// Captures real screenshots of redhopper.co for the demo video.
// Output: public/shots/*.png and public/shots/layout.json (section offsets on the full landing page).
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const OUT = path.resolve('public/shots');
const BASE = 'https://redhopper.co';
const EMAIL = process.env.DEMO_EMAIL ?? 'demo@chat.test';
const PASSWORD = process.env.DEMO_PASSWORD ?? 'password';

fs.mkdirSync(OUT, { recursive: true });

const browser = await chromium.launch({
  executablePath: process.env.CHROME_PATH ?? '/opt/pw-browsers/chromium-1194/chrome-linux/chrome',
  proxy: process.env.HTTPS_PROXY ? { server: process.env.HTTPS_PROXY } : undefined,
});
const page = await browser.newPage({ viewport: { width: 1920, height: 1080 }, locale: 'az-AZ' });

const dismissCookies = async () => {
  const btn = page.getByRole('button', { name: 'Yalnız zəruri' });
  if (await btn.isVisible().catch(() => false)) await btn.click();
};

// 1. Landing page
await page.goto(`${BASE}/az`, { waitUntil: 'networkidle' });
await dismissCookies();
await page.waitForTimeout(800);
await page.screenshot({ path: `${OUT}/hero.png` });

// Open one FAQ item so the FAQ section shows an answer
const faq = page.getByText('AI cavab tapmasa nə olur?');
if (await faq.isVisible().catch(() => false)) await faq.click();
await page.waitForTimeout(500);

// Trigger lazy/animated sections by scrolling through the page
const height = await page.evaluate(() => document.body.scrollHeight);
for (let y = 0; y < height; y += 500) {
  await page.evaluate((v) => window.scrollTo(0, v), y);
  await page.waitForTimeout(120);
}
await page.evaluate(() => window.scrollTo(0, 0));
await page.waitForTimeout(500);

const layout = await page.evaluate(() => {
  const top = (el) => (el ? Math.round(el.getBoundingClientRect().top + window.scrollY) : null);
  const heading = (text) => [...document.querySelectorAll('h1,h2,h3')].find((h) => h.textContent.trim().startsWith(text));
  return {
    width: document.documentElement.scrollWidth,
    height: document.body.scrollHeight,
    how: top(heading('Üç addımda')),
    features: top(heading('Müasir dəstək')),
    channels: top(heading('Müştərilərinizin olduğu')),
    pricing: top(heading('Sadə qiymətlər')),
    faq: top(heading('Tez-tez verilən')),
    cta: top(heading('Saytınıza AI')),
    contact: top(heading('Bizimlə əlaqə')),
  };
});
await page.screenshot({ path: `${OUT}/landing-full.png`, fullPage: true });
fs.writeFileSync(`${OUT}/layout.json`, JSON.stringify(layout, null, 2));
console.log('layout', layout);

// 2. Operator login form, filled step by step
await page.goto(`${BASE}/login`, { waitUntil: 'networkidle' });
await dismissCookies();
await page.waitForTimeout(500);
await page.screenshot({ path: `${OUT}/login-empty.png` });

const email = page.locator('input[type=email]');
const password = page.locator('input[type=password]');
const box = async (loc) => {
  const b = await loc.boundingBox();
  return b && { x: Math.round(b.x), y: Math.round(b.y), w: Math.round(b.width), h: Math.round(b.height) };
};
const submit = page.locator('button[type=submit]');
const loginLayout = { email: await box(email), password: await box(password), submit: await box(submit) };

await email.click();
await email.pressSequentially(EMAIL, { delay: 30 });
await page.screenshot({ path: `${OUT}/login-email.png` });
await password.click();
await password.pressSequentially(PASSWORD, { delay: 30 });
await page.screenshot({ path: `${OUT}/login-filled.png` });
fs.writeFileSync(`${OUT}/login-layout.json`, JSON.stringify(loginLayout, null, 2));

// 3. Try to sign in. Needs api.redhopper.co and Cloudflare Turnstile to be reachable.
let loggedIn = false;
try {
  await submit.click({ timeout: 15000 });
  await page.waitForURL((u) => !u.pathname.startsWith('/login'), { timeout: 20000 });
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(1500);
  await page.screenshot({ path: `${OUT}/dashboard.png` });
  loggedIn = true;
  console.log('Logged in, landed on', page.url());
} catch (e) {
  console.warn('Operator login not possible from this environment:', e.message.split('\n')[0]);
}
fs.writeFileSync(`${OUT}/status.json`, JSON.stringify({ loggedIn, capturedAt: new Date().toISOString() }, null, 2));

await browser.close();
