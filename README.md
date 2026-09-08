# ProxWorld — Setup Guide

This is the full application code. Since Composer/PHP couldn't run in the
build environment, follow these steps on your own machine to get it running.

## 1. Create the base Laravel skeleton

```bash
composer create-project laravel/laravel proxworld-base "^11.0"
```

Then copy every folder from this delivery **on top of** `proxworld-base`
(overwrite `app/`, `database/`, `routes/`, `config/`, `resources/`,
`bootstrap/app.php`, `composer.json`, `.env.example`) — the base project
supplies `artisan`, `public/index.php`, and the rest of the framework
skeleton this delivery doesn't duplicate.

## 2. Install dependencies

```bash
composer install
npm install   # if/when you wire up your own asset build
```

## 3. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Fill in `.env` with real values:
- `DB_*` — your MySQL credentials
- `BREVO_API_KEY`, `BREVO_SENDER_EMAIL` — from your Brevo account
- `FLUTTERWAVE_PUBLIC_KEY`, `FLUTTERWAVE_SECRET_KEY`, `FLUTTERWAVE_SECRET_HASH` — from your Flutterwave dashboard. Register the webhook URL as `https://yourdomain.com/wallet/flutterwave-webhook` (add this route if you haven't yet — see `FlutterwaveController::webhook`).
- `EXCHANGE_RATE_API_KEY` — from exchangerate-api.com (or swap provider in `ExchangeRateService`)
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` — from Google Cloud Console (OAuth consent screen + credentials)
- `CLOUDINARY_URL` — from your Cloudinary dashboard
- `SUPPORT_TELEGRAM_URL`, and the social links baked into `SettingsSeeder`

## 4. Database

```bash
php artisan migrate --seed
```

This creates every table and seeds:
- Roles/permissions (Super Admin, Admin, Manager, Accountant, HR)
- A first Super Admin: `owner@proxworld.test` / `ChangeMe123!` — **change this password immediately**
- Default currencies (NGN default, USD, GBP, GHS, KES, ZAR, EUR)
- Default pricing config and social links

## 5. Storage & queue

```bash
php artisan storage:link
php artisan queue:work        # run this as a persistent process (Supervisor/systemd) in production
php artisan schedule:work     # or add the real cron entry below in production
```

Production crontab entry (instead of `schedule:work`):
```
* * * * * cd /path/to/proxworld && php artisan schedule:run >> /dev/null 2>&1
```

## 6. Add your first proxy provider

Log into `/{ADMIN_ROUTE_PREFIX}` (defaults to `/control-panel`, see below) as the seeded Super Admin, go to **Providers**,
and add your first supplier's API key. Then run:

```bash
php artisan providers:sync-services
php artisan providers:sync-balances
```

## 7. Your frontend assets

Copy your CSS/JS/images into `public/assets/...` matching the paths the
Blade views already reference (`asset('assets/...')`).

## 8. Admin route prefix & reseller base domain

Both live in `config/proxworld.php`, driven entirely by env vars — no code
changes needed to move them:

- `ADMIN_ROUTE_PREFIX` (default `control-panel`) — the URL segment the
  admin panel is mounted under. Change it in `.env`, then
  `php artisan config:clear` (and `config:cache` in production).
- `BASE_DOMAIN` — the root domain reseller subdomains are appended to
  (e.g. subdomain `acme` + `BASE_DOMAIN=proxworld.com` →
  `acme.proxworld.com`). Leave blank to default to `APP_URL`'s host.

## Known gaps to finish before launch

- **Manual bank-transfer wallet top-ups**: the admin `wallet.approve` /
  `wallet.reject` review flow exists, but `WalletController::fund` (customer
  side) currently only creates Flutterwave-based instant top-ups. Add a
  "pay by bank transfer" option there that creates a `pending`
  `WalletTransaction` for admin review if you want that path.
- **Google Sign-In on the reseller storefront**: `GoogleAuthController`
  always redirects into the main site (`route('dashboard')`) after
  authenticating, so "Continue with Google" is wired up on the main
  site's login/register pages only. If you want storefront customers to
  sign in with Google too, the callback needs to remember which
  reseller subdomain the flow started from (e.g. via the OAuth `state`
  parameter) and redirect back there instead.
- **View variable double-checking**: ~90 Blade files were merged in from
  your uploaded frontend and reconciled against controller output using
  every `route()` call found in them as ground truth. Route names and the
  major pages (dashboard, orders, wallet, resellers, admin dashboard) were
  checked in detail; give the less-trafficked admin pages a click-through
  once the app is running, since a few `$variable` names may need minor
  alignment that only shows up at runtime.
- All legal doc content (Terms, Privacy, AUP, Reseller Agreement, Refund,
  Cookie Policy) should get a lawyer's review pass before going live.
