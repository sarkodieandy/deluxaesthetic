# Role & permission matrix

## Roles

| Role | Portal access | Scope |
|------|---------------|-------|
| Super Administrator | Admin (full) | All permissions |
| Clinic Administrator | Admin | Clinic ops, limited settings |
| Receptionist | Admin (ops) | Bookings, clients, consultations |
| Practitioner | Practitioner + limited admin | Own schedule & appointments |
| Therapist | Practitioner | Own schedule & treatments |
| Trainer | Trainer + limited admin | Own courses, attendance, assessments |
| Finance Officer | Admin | Payments, refunds, financial reports |
| Store Manager | Admin | Products, inventory, orders |
| Content Manager | Admin | CMS, blog, gallery, SEO |
| Support Agent | Admin | Consultations, AI conversations, notifications |
| Client | Client portal | Own records only |
| Student | Student portal | Own enrolment records only |

## Permission catalogue (representative)

### Appointments
`appointments.view` · `appointments.create` · `appointments.update` · `appointments.cancel` · `appointments.refund` · `appointments.calendar`

### Clients / students
`clients.view` · `clients.manage` · `students.view` · `students.update` · `students.manage`

### Courses / certificates
`courses.view` · `courses.manage` · `enrolments.manage` · `attendance.manage` · `assessments.manage` · `materials.manage` · `certificates.issue` · `certificates.revoke`

### Store
`products.manage` · `inventory.manage` · `orders.manage` · `orders.fulfill` · `reviews.moderate`

### Payments
`payments.view` · `payments.reconcile` · `refunds.manage` · `coupons.manage`

### Reports / users / settings
`reports.view` · `reports.financial` · `users.manage` · `roles.manage` · `settings.manage` · `audit.view` · `media.manage` · `content.manage` · `notifications.manage` · `ai.manage` · `loyalty.manage`

## Matrix (high level)

| Permission group | Super | Clinic Admin | Reception | Practitioner | Trainer | Finance | Store | Content | Support | Client | Student |
|------------------|:-----:|:------------:|:---------:|:------------:|:-------:|:-------:|:-----:|:-------:|:-------:|:------:|:-------:|
| appointments.* | ● | ● | ● | ◐ own | — | ◐ view | — | — | ◐ | ◐ own | — |
| clients.* | ● | ● | ● | ◐ assigned | — | ◐ | — | — | ◐ | — | — |
| students/courses | ● | ● | — | — | ● | ◐ fees | — | — | — | — | ◐ own |
| certificates | ● | ● | — | — | ◐ | — | — | — | — | — | ◐ own |
| products/orders | ● | ● | — | — | — | ◐ | ● | — | — | ◐ own orders | — |
| payments/refunds | ● | ● | — | — | — | ● | ◐ | — | — | ◐ own | ◐ own |
| reports.financial | ● | ● | — | — | — | ● | ◐ | — | — | — | — |
| users/roles | ● | ◐ | — | — | — | — | — | — | — | — | — |
| settings | ● | ◐ | — | — | — | — | — | — | — | — | — |
| content/media | ● | ● | — | — | — | — | — | ● | — | — | — |
| AI / notifications | ● | ● | — | — | — | — | — | — | ● | — | — |

● = full · ◐ = limited / own records · — = none

## Enforcement

1. Spatie roles & permissions on `User`
2. Route middleware: `role:` / `permission:`
3. Policies per model (Appointment, Enrolment, Order, Certificate, …)
4. Blade `@can` / `@role` for UI — **never** the only layer
5. Signed URLs + policy checks for private downloads
6. IDOR prevention: always scope queries to authorised ownership

## Seed defaults

- Super admin from `SEED_ADMIN_EMAIL` / `SEED_ADMIN_PASSWORD`
- Demo staff, clients, students with env-driven passwords
- CEO practitioner profile: **Mac Tonto** with supplied photographs
