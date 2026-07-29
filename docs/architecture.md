# De Lux Aesthetic Clinic — Architecture

**Project:** De Lux Aesthetic Clinic  
**Client:** Mac Tonto  
**Developer:** Sarkodie Andrews  
**Stack:** Laravel 13 · PHP 8.5 · MySQL · Blade · Tailwind · Alpine.js · Vite · GSAP

## 1. System overview

One Laravel application serving six interfaces against a shared MySQL database:

| Interface | Prefix / host | Audience |
|-----------|---------------|----------|
| Public website | `/` | Visitors |
| Client portal | `/client` | Clinic clients |
| Student portal | `/student` | Academy students |
| Practitioner portal | `/practitioner` | Doctors / therapists |
| Trainer portal | `/trainer` | Academy trainers |
| Admin dashboard | `/admin` or `admin.*` subdomain | Staff & admins |

Shared layers: authentication, RBAC, payments, messaging, media, AI, reports, settings.

## 2. Architectural principles

1. **Domain-oriented folders** — Actions, Services, Enums, DTOs per domain (Appointments, Academy, Orders, Payments, etc.).
2. **Thin controllers** — Form Requests for validation; Actions/Services for business logic; Policies for authorisation.
3. **Single database** — Normalised schema with foreign keys, soft deletes where appropriate, UTC timestamps.
4. **Queue-first notifications** — Email / SMS / WhatsApp / in-app via database queue + scheduler (cPanel-friendly).
5. **Provider contracts** — Payment, messaging, and AI behind interfaces with mock adapters when credentials are absent.
6. **No Node in production** — Vite builds assets at deploy time; PHP serves the app.
7. **Config over hard-coding** — Business details, credentials, and feature flags in `.env` + `settings` table.

## 3. Request flow

```
HTTP → Route group (web/admin/client/…)
     → Middleware (auth, role, locale, throttle)
     → Form Request
     → Controller
     → Action / Service (DB transaction when needed)
     → Model / Events
     → Queued Jobs (notifications, PDFs, webhooks)
     → Blade view / redirect / JSON
```

## 4. Route registration

Separate route files registered in `bootstrap/app.php`:

- `routes/web.php` — public site
- `routes/admin.php` — admin (prefix `admin` + optional subdomain)
- `routes/client.php` — client portal
- `routes/student.php` — student portal
- `routes/practitioner.php` — practitioner tools
- `routes/trainer.php` — trainer tools
- `routes/api.php` — webhooks & limited API
- `routes/console.php` — scheduled commands

## 5. Domain modules

| Domain | Responsibilities |
|--------|------------------|
| Identity | Users, roles, permissions, 2FA, sessions |
| Clinic | Branches, treatments, practitioners, schedules, appointments, consultations, consent |
| Academy | Courses, schedules, enrolments, materials, attendance, assessments, certificates |
| Store | Products, inventory, cart, wishlist, coupons, orders, deliveries, reviews |
| Payments | Paystack gateway, attempts, webhooks, refunds, receipts |
| Messaging | Email / SMS / WhatsApp / in-app templates & logs |
| AI | Knowledge base, conversations, safety boundaries |
| Loyalty | Points, referrals, rewards |
| CMS | Pages, sections, blog, FAQ, gallery, menus, SEO |
| Media | Uploads, private files, signed downloads |
| Reports | Metrics, exports, audit logs |
| Settings | Clinic/academy/ecommerce/messaging/payments/AI config |

## 6. Frontend architecture

| Surface | CSS entry | JS entry | Notes |
|---------|-----------|----------|-------|
| Public | `resources/css/web/app.css` | `resources/js/web/app.js` | GSAP, Swiper, booking/store modules |
| Admin | `resources/css/admin/admin.css` | `resources/js/admin/admin.js` | Chart.js, tables, forms |
| Client | `resources/css/portals/client.css` | `resources/js/portals/client.js` | Portal UX |
| Student | `resources/css/portals/student.css` | `resources/js/portals/student.js` | Portal UX |

Design system: straight lines, max 3px radius, **no box/text shadows**, editorial medical-aesthetic palette.

## 7. Integration architecture

| Concern | Contract | Default adapter |
|---------|----------|-----------------|
| Payments | `PaymentGatewayInterface` | Paystack (+ mock mode) |
| Email | `EmailProviderInterface` | Laravel Mail |
| SMS | `SmsProviderInterface` | Configurable / mock |
| WhatsApp | `WhatsAppProviderInterface` | Business API / mock |
| AI | `AIProviderInterface` | Configurable / mock |

When credentials are missing: validate config, log attempts, return safe mock responses, document how to enable live mode.

## 8. Hosting model (cPanel)

- PHP + Composer + MySQL + SSL
- Database queue driver
- Cron: `* * * * * php artisan schedule:run`
- Scheduler processes queues when workers unavailable
- `php artisan assets:build` equivalent: `npm ci && npm run build` on deploy machine; upload `public/build`

## 9. Assumptions

1. Primary currency: GHS (configurable).
2. Time zone: Africa/Accra (stored UTC).
3. CEO / founder: Mac Tonto — photos supplied by client stored under `public/assets/web/images/team/`.
4. Demo seed credentials from env (`SEED_ADMIN_EMAIL`, `SEED_ADMIN_PASSWORD`, etc.).
5. Spatie Laravel Permission for RBAC.
6. Pest for tests; Laravel Pint for formatting.
7. French translations: UI strings in `lang/fr`; dynamic content via translation tables / JSON columns.

## 10. Risks

| Risk | Mitigation |
|------|------------|
| Shared hosting queue limits | DB queue + non-overlapping scheduled `queue:work --stop-when-empty` |
| Missing Paystack / WhatsApp / AI keys | Mock providers + docs |
| Double booking | Transactions + row locks on availability |
| Medical liability via AI | Hard safety prompts; no diagnosis/prescription |
| Large video hosting | External platforms only (YouTube/Vimeo links) |
| Scope size | Phased delivery; keep app runnable after each phase |
