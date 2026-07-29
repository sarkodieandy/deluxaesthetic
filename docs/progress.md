# Progress checklist

**Project:** De Lux Aesthetic Clinic  
**Last updated:** 2026-07-27

## UI/UX architecture (2026-07-27)

- [x] `docs/ui-ux/` complete (IA, sitemap, nav, tokens, typography, colour, spacing, components, topology, responsive, motion, a11y, assets, 20 UX flows)
- [x] Design tokens aligned to ivory/bronze straight-line system (`#F4F0E8`, bronze primary, radius 0–3px, no shadows)
- [x] CSS layout layer (`resources/css/web/layout.css`) + admin components
- [x] Home rebuilt to full public topology (hero → services → treatments → about → practitioners → B&A → academy/store → stats → testimonials → FAQ → blog → contact → CTA)
- [x] Structured public pages: practitioners, academy, courses, store, gallery, blog, contact
- [x] Client + student portal shells with IA-aligned side navigation
- [x] Admin sidebar grouped by Overview / Clinic / Academy / Store / Finance / System
- [ ] Multi-step booking UI (Phase 3)
- [ ] Full academy/store catalogue modules (Phases 4–5)
- [ ] Admin CRUD tables/forms (Phase 6)
- [ ] Motion/a11y polish pass (Phase 7)

## Planning

- [x] Architecture plan
- [x] Database entity plan
- [x] Route map
- [x] Role & permission matrix
- [x] Design system
- [x] Asset licences (CEO assets registered)
- [x] Milestones & risks
- [x] Progress file

## Phase 1 — Foundation

- [x] Laravel 13 scaffold
- [x] Domain folder structure
- [x] Config: clinic, academy, ecommerce, messaging, payments, ai, media
- [x] Separate route files (web, admin, client, student, practitioner, trainer, api)
- [x] Auth (Breeze Blade) + email verification
- [x] Spatie roles & permissions + seeders
- [x] Design tokens, web/admin/portal Vite entries
- [x] Public + admin layouts and navigation
- [x] CEO portraits installed and used on Home/About
- [x] `.env.example` expanded
- [x] Migrations runnable; SQLite local; MySQL-ready
- [x] Payment gateway contract + Paystack + mock binding

## Database (current)

- **Engine:** MAMP MySQL 8 (`127.0.0.1:8889`)
- **Database:** `deluxaesthetic`
- **Migrated + seeded:** yes (users, roles, treatments, schedules, sample appointment)

## Phase 2 — Public website

- [x] Home (brand-first hero with CEO imagery)
- [x] About (CEO dual portrait)
- [x] EN/FR language switcher + lang files
- [x] Treatments catalogue + detail (DB-backed filters/sort/pagination)
- [x] Stub shells: practitioners, academy, courses, store, gallery, blog, contact, enrol
- [ ] Full CMS-driven sections, blog/FAQ CRUD content

## Phase 3 — Clinic

- [x] Treatments / appointments schema
- [x] `CreateAppointmentAction` with double-booking lock
- [x] `AvailabilityService` + `/book/slots` JSON endpoint (28 weekday slots verified on MySQL)
- [x] Live booking form with Alpine slot picker
- [x] Client dashboard lists appointments from DB
- [x] Clinic demo seeder (treatments + CEO weekday schedule)
- [x] Feature test: double-booking prevention
- [ ] Payment deposit step, reschedule/cancel flows
- [ ] Consultation request full flow + notifications

## Phase 4–8

- [x] Academy + store/payment schema migrations
- [ ] Enrolment UI, certificates PDF/QR
- [ ] Cart/checkout UI
- [ ] Live Paystack/SMS/WhatsApp/AI adapters
- [ ] Full admin CRUD modules & reports
- [x] Admin Phase 1 foundation (`docs/admin/`, config navigation, middleware, DB dashboard metrics, permission expansion)
- [ ] Deployment hardening & full acceptance pass

## Demo accounts (from env)

Configure via `SEED_*` variables. Example emails:

- `admin@deluxaesthetic.test` — Super Administrator
- `ceo@deluxaesthetic.test` — Mac Tonto (Clinic Admin + Practitioner)
- `client@deluxaesthetic.test` — Client
- `student@deluxaesthetic.test` — Student
- `trainer@deluxaesthetic.test` — Trainer

## Risks / assumptions

- Local DB: SQLite until MySQL is provisioned on hosting.
- Payment/SMS/WhatsApp/AI run in mock mode until credentials are supplied.
- Remaining public catalogue pages are structured stubs pending CMS/content seeders.
