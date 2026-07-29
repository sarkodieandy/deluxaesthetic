# Route map

Admin supports `admin.{domain}` subdomain **or** `/admin` prefix fallback.

## Public (`routes/web.php`)

| Method | URI | Name | Notes |
|--------|-----|------|-------|
| GET | `/` | `web.home` | Homepage |
| GET | `/about` | `web.about` | About + CEO |
| GET | `/treatments` | `web.treatments.index` | Catalogue |
| GET | `/treatments/{slug}` | `web.treatments.show` | Detail |
| GET | `/practitioners` | `web.practitioners.index` | Team |
| GET | `/practitioners/{slug}` | `web.practitioners.show` | Profile |
| GET | `/academy` | `web.academy.index` | Academy landing |
| GET | `/courses` | `web.courses.index` | Course catalogue |
| GET | `/courses/{slug}` | `web.courses.show` | Course detail |
| GET | `/store` | `web.store.index` | Shop |
| GET | `/store/{slug}` | `web.store.show` | Product |
| GET | `/gallery` | `web.gallery` | Gallery |
| GET | `/blog` | `web.blog.index` | Blog |
| GET | `/blog/{slug}` | `web.blog.show` | Post |
| GET | `/contact` | `web.contact` | Contact |
| POST | `/contact` | `web.contact.store` | Contact form |
| GET | `/consultation` | `web.consultation.create` | Consult request |
| POST | `/consultation` | `web.consultation.store` | Submit |
| GET | `/book` | `web.booking.create` | Multi-step booking |
| POST | `/book/*` | `web.booking.*` | Booking steps |
| GET | `/verify/certificate/{number}` | `web.certificates.verify` | Public verify |
| GET | `/locale/{locale}` | `locale.switch` | EN/FR |
| GET | `/cart` | `web.cart.show` | Cart |
| POST | `/cart/*` | `web.cart.*` | Cart actions |
| GET | `/checkout` | `web.checkout.show` | Checkout |
| GET | `/pages/{slug}` | `web.pages.show` | CMS pages |
| GET | `/sitemap.xml` | `web.sitemap` | SEO |

## Auth (`routes/web.php` / auth group)

| Method | URI | Name |
|--------|-----|------|
| GET/POST | `/login` | `login` |
| GET/POST | `/register` | `register` |
| POST | `/logout` | `logout` |
| GET/POST | `/forgot-password` | `password.*` |
| GET | `/email/verify` | `verification.*` |

## Client portal (`/client`)

| Area | URIs |
|------|------|
| Dashboard | `GET /client` |
| Appointments | `/client/appointments` CRUD-ish (list, show, reschedule, cancel) |
| Consultations | `/client/consultations` |
| Orders | `/client/orders` |
| Payments / receipts | `/client/payments` |
| Loyalty / referrals | `/client/loyalty` |
| Profile / security | `/client/profile`, `/client/security` |
| Notifications | `/client/notifications` |

Middleware: `auth`, `verified`, `role:client` (or client profile).

## Student portal (`/student`)

| Area | URIs |
|------|------|
| Dashboard | `GET /student` |
| Courses / materials | `/student/courses`, `/student/materials/{id}/download` (signed) |
| Attendance / assessments | `/student/attendance`, `/student/assessments` |
| Certificates | `/student/certificates` |
| Payments | `/student/payments` |
| Profile | `/student/profile` |

## Practitioner / trainer

| Prefix | Key routes |
|--------|------------|
| `/practitioner` | Schedule, appointments, notes, consultations |
| `/trainer` | Classes, attendance, assignments, assessments, materials |

## Admin (`/admin`)

| Module | Prefix |
|--------|--------|
| Dashboard | `/admin` |
| Users / roles / permissions | `/admin/users`, `/admin/roles` |
| Clients / students / staff profiles | `/admin/clients`, `/admin/students`, … |
| Branches / treatments / schedules | `/admin/branches`, `/admin/treatments`, … |
| Appointments / consultations | `/admin/appointments`, `/admin/consultations` |
| Courses / enrolments / certificates | `/admin/courses`, `/admin/enrolments`, `/admin/certificates` |
| Products / inventory / orders | `/admin/products`, `/admin/orders` |
| Payments / refunds | `/admin/payments`, `/admin/refunds` |
| Loyalty / referrals | `/admin/loyalty` |
| CMS / media / menus | `/admin/pages`, `/admin/media`, `/admin/menus` |
| Notifications / AI | `/admin/notifications`, `/admin/ai` |
| Reports / audit / settings | `/admin/reports`, `/admin/audit`, `/admin/settings` |

All admin routes: `auth` + permission middleware.

## API / webhooks (`routes/api.php`)

| Method | URI | Purpose |
|--------|-----|---------|
| POST | `/api/webhooks/paystack` | Payment webhooks |
| POST | `/api/webhooks/whatsapp` | WhatsApp inbound |
| POST | `/api/ai/chat` | Public AI assistant (throttled) |
| GET | `/api/booking/availability` | Slot lookup |

## Console / schedule

- Appointment reminders
- Course / instalment reminders
- Queue drain (`queue:work --stop-when-empty`)
- Loyalty expiry
- Failed notification retries
