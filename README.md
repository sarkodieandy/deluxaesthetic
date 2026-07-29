# De Lux Aesthetic Clinic

Production Laravel platform for **De Lux Aesthetic Clinic** — public website, client portal, student portal, academy, store, payments, notifications, and administration.

**Client:** Mac Tonto  
**Developer:** Sarkodie Andrews

## Stack

- PHP 8.3+ / Laravel 13
- MySQL (SQLite supported locally)
- Blade · Tailwind CSS · Alpine.js · Vite · GSAP
- Spatie Laravel Permission
- Paystack (mock mode when credentials absent)

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_* (MySQL) or use SQLite
touch database/database.sqlite   # if using sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Set seed passwords in `.env` (`SEED_ADMIN_PASSWORD`, etc.). Defaults are documented in `.env.example`.

## Portals

| URL | Role |
|-----|------|
| `/` | Public website |
| `/admin` | Staff / administrators |
| `/client` | Clients |
| `/student` | Students |
| `/practitioner` | Practitioners / therapists |
| `/trainer` | Trainers |

## Documentation

See `/docs` for architecture, schema, routes, roles, design system, deployment notes, and progress.

## CEO assets

Client-supplied portraits of Mac Tonto are stored at:

- `public/assets/web/images/team/ceo-mac-tonto-portrait-a.png`
- `public/assets/web/images/team/ceo-mac-tonto-portrait-b.png`
