# Sitemap — De Lux Aesthetic Clinic

Laravel-style URL patterns. Route names follow `web.*`, `client.*`, `student.*`, `practitioner.*`, `trainer.*`, `admin.*`. Admin also supports `admin.{domain}` host with the same path tree under `/`.

Locales switch via `GET /locale/{locale}` (`en` | `fr`) and do not change path prefixes.

---

## PUBLIC

```
/
├── /                                    # Home
├── /about                               # About + CEO Mac Tonto
├── /treatments                          # Treatment catalogue
│   └── /treatments/{slug}               # Treatment detail
├── /treatments/categories/{slug}        # Category filter view (optional pretty URL)
├── /practitioners
│   └── /practitioners/{slug}
├── /book                                # Booking step 1 (create)
│   ├── /book/slots                      # Availability JSON/partial
│   └── POST /book                       # Store (auth client)
├── /consultation                        # Online consultation request
│   └── POST /consultation
├── /academy                             # Academy landing
├── /courses
│   └── /courses/{slug}
├── /enrol                               # Enrolment entry (course context via query)
│   └── /enrol/{course:slug}             # Optional deep link
├── /training-calendar                   # Upcoming class dates
├── /store
│   ├── /store/categories/{slug}
│   └── /store/{slug}                    # Product detail
├── /cart
│   └── POST /cart/*                     # add / update / remove
├── /checkout
│   ├── /checkout/success
│   └── /checkout/cancelled
├── /orders/track/{reference}            # Guest/order tracking (signed or ref+email)
├── /gallery
├── /before-and-after
├── /testimonials
├── /blog
│   └── /blog/{slug}
├── /faqs
├── /contact
│   └── POST /contact
├── /verify/certificate/{number}
├── /pages/{slug}                        # CMS legal/marketing pages
├── /sitemap.xml
├── /locale/{locale}
├── /login · /register · /forgot-password · /reset-password/{token}
├── /email/verify · /email/verify/{id}/{hash}
└── /dashboard                           # Role redirect hub
```

**Secondary public utilities**

| Path | Purpose |
|------|---------|
| `/search` | Global search (treatments, courses, products, posts) |
| `/privacy` `/terms` `/refund-policy` | May alias to `/pages/{slug}` |
| `/api/booking/availability` | Slot lookup (API) |
| `/api/ai/chat` | Public AI assistant (throttled) |

---

## CLIENT (`/client`)

Middleware: `auth`, `verified`, `role:Client`

```
/client
├── /client                              # Overview dashboard
├── /client/appointments
│   ├── /client/appointments/{appointment}
│   ├── /client/appointments/{appointment}/reschedule
│   └── /client/appointments/{appointment}/cancel
├── /client/consultations
│   └── /client/consultations/{consultation}
├── /client/payments
│   └── /client/payments/{payment}/receipt
├── /client/orders
│   ├── /client/orders/{order}
│   └── /client/orders/{order}/track
├── /client/wishlist
├── /client/loyalty
├── /client/referrals
├── /client/notifications
├── /client/profile
└── /client/security                     # password, 2FA, sessions
```

---

## STUDENT (`/student`)

Middleware: `auth`, `verified`, `role:Student`

```
/student
├── /student                             # Overview
├── /student/courses
│   └── /student/courses/{enrolment}
├── /student/calendar
├── /student/materials
│   └── /student/materials/{material}/download   # signed URL
├── /student/assignments
│   ├── /student/assignments/{assignment}
│   └── POST /student/assignments/{assignment}/submit
├── /student/assessments
│   └── /student/assessments/{assessment}
├── /student/attendance
├── /student/payments
│   └── /student/payments/{instalment}/pay
├── /student/certificates
│   └── /student/certificates/{certificate}/download
├── /student/notifications
├── /student/profile
└── /student/security
```

---

## PRACTITIONER (`/practitioner`)

Middleware: `auth`, `verified`, `role:Practitioner|Therapist`

```
/practitioner
├── /practitioner                        # Overview
├── /practitioner/calendar
├── /practitioner/schedule               # Daily schedule
├── /practitioner/appointments
│   └── /practitioner/appointments/{appointment}
│       └── /practitioner/appointments/{appointment}/notes
├── /practitioner/availability
├── /practitioner/consultations
│   └── /practitioner/consultations/{consultation}
├── /practitioner/notifications
└── /practitioner/profile
```

---

## TRAINER (`/trainer`)

Middleware: `auth`, `verified`, `role:Trainer`

```
/trainer
├── /trainer                             # Overview
├── /trainer/courses
│   └── /trainer/courses/{course}
├── /trainer/schedule
├── /trainer/students
│   └── /trainer/courses/{course}/students
├── /trainer/attendance
│   └── /trainer/attendance/{schedule}   # mark roster
├── /trainer/materials
├── /trainer/assignments
│   └── /trainer/assignments/{assignment}/submissions
├── /trainer/assessments
├── /trainer/certificates                # request issue / view issued
├── /trainer/announcements
└── /trainer/profile
```

---

## ADMIN (`/admin`)

Middleware: `auth`, `verified`, permission-scoped. Sidebar groups match [navigation-map.md](./navigation-map.md).

```
/admin
├── /admin                               # Dashboard
├── /admin/activity

# CLINIC
├── /admin/appointments
│   └── /admin/appointments/{appointment}
├── /admin/consultations
├── /admin/clients
│   └── /admin/clients/{client}
├── /admin/practitioners
├── /admin/therapists
├── /admin/treatments
│   ├── /admin/treatments/categories
│   └── /admin/treatments/{treatment}
├── /admin/branches
│   └── /admin/branches/{branch}

# ACADEMY
├── /admin/students
├── /admin/trainers
├── /admin/courses
│   ├── /admin/courses/{course}
│   └── /admin/courses/{course}/modules
├── /admin/schedules                     # course schedules
├── /admin/enrolments
├── /admin/attendance
├── /admin/assessments
├── /admin/certificates
│   └── /admin/certificates/{certificate}

# STORE
├── /admin/products
│   ├── /admin/products/categories
│   └── /admin/products/{product}
├── /admin/inventory
├── /admin/orders
│   └── /admin/orders/{order}
├── /admin/deliveries
├── /admin/reviews

# FINANCE
├── /admin/payments
├── /admin/refunds
├── /admin/reports
│   ├── /admin/reports/revenue
│   ├── /admin/reports/appointments
│   └── /admin/reports/enrolments

# MARKETING
├── /admin/loyalty
├── /admin/referrals
├── /admin/testimonials
├── /admin/promotions                    # coupons / campaigns

# CONTENT
├── /admin/pages
├── /admin/blog
├── /admin/faqs
├── /admin/gallery
├── /admin/before-and-after
├── /admin/media
├── /admin/menus
├── /admin/translations

# COMMUNICATION
├── /admin/notifications
├── /admin/notification-templates
├── /admin/ai/knowledge
├── /admin/ai/conversations

# SYSTEM
├── /admin/users
├── /admin/roles
├── /admin/permissions
├── /admin/audit
└── /admin/settings
    ├── /admin/settings/clinic
    ├── /admin/settings/academy
    ├── /admin/settings/store
    ├── /admin/settings/payments
    ├── /admin/settings/messaging
    └── /admin/settings/ai
```

---

## API / webhooks (non-page)

| Method | URI | Purpose |
|--------|-----|---------|
| POST | `/api/webhooks/paystack` | Payment events |
| POST | `/api/webhooks/whatsapp` | Inbound WhatsApp |
| POST | `/api/ai/chat` | Assistant |
| GET | `/api/booking/availability` | Slots |

These are not sitemap navigation destinations; document for integration UX (loading, failure toasts).
