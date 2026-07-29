# Deployment guide (cPanel / managed PHP)

## Requirements

- PHP 8.3+ with extensions: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` or `imagick`
- Composer 2
- MySQL 8+
- Node 20+ on build machine (not required on production PHP host)
- SSL certificate
- Cron access

## Production `.env` (required)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.tld
LOG_LEVEL=error

SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@your-domain.tld

PAYMENT_MOCK=false
PAYSTACK_PUBLIC_KEY=...
PAYSTACK_SECRET_KEY=...

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Confirm Google redirect with `php artisan google:redirect-uri` and add that exact URI in Google Cloud Console.

## Deploy steps

1. Upload application files (exclude `node_modules`, keep `vendor` or run `composer install --no-dev --optimize-autoloader` on server).
2. Copy `.env.example` → `.env` and set production values above (DB, APP_URL, mail, Paystack, Google).
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. First deploy only — seed roles/settings/templates (**not** demo users):
   ```bash
   php artisan db:seed --class=ProductionSeeder --force
   ```
   Do **not** run `php artisan db:seed` (that loads demo accounts).
6. Create the first admin manually in the database or with a one-off artisan tinker role assign after creating a user.
7. `php artisan storage:link`
8. Build assets locally/CI: `npm ci && npm run build`, upload `public/build`
9. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
10. Set document root to `/public`
11. Permissions: `storage/` and `bootstrap/cache/` writable

## Cron

```
* * * * * php /home/USER/path/to/artisan schedule:run >> /dev/null 2>&1
```

Queue strategy (shared hosting): database driver + scheduled `queue:work --stop-when-empty --max-time=50`.

## Callbacks

- Google OAuth: `https://your-domain.tld/auth/google/callback`
- Paystack callback / webhook URLs must use HTTPS
- WhatsApp webhook URL when credentials are available

## Smoke test after go-live

- `/up` health check
- Login / register / Google sign-in
- Student portal modules + certificate download (after storage link)
- Admin dashboard + audit log
- Send a test email (queue must be running)

## Rollback

Keep previous release directory; restore `.env`, `storage`, and previous `public/build`; re-point document root.
