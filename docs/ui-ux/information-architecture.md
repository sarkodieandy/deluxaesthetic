# Information Architecture — De Lux Aesthetic Clinic

## 1. Purpose

De Lux Aesthetic Clinic is a single Laravel application serving six interfaces over one MySQL database. The IA must make **clinic care**, **academy training**, and **store commerce** feel like one Accra-based premium brand — not three disconnected products glued together.

Primary location narrative: Accra, Ghana. Currency: GHS. Locales: English and French.

---

## 2. Platform surfaces

| Surface | URL prefix | Audience | Job to be done |
|---------|------------|----------|----------------|
| Public website | `/` | Visitors, prospects | Discover brand, book, enrol, shop, verify certificates |
| Client portal | `/client` | Registered clinic clients | Manage appointments, payments, orders, loyalty |
| Student portal | `/student` | Academy students | Learn, attend, pay fees, earn certificates |
| Practitioner portal | `/practitioner` | Doctors / therapists | Run schedule, notes, consultations |
| Trainer portal | `/trainer` | Academy trainers | Classes, attendance, assessments, materials |
| Admin dashboard | `/admin` (or `admin.*`) | Staff by RBAC | Operate clinic, academy, store, content, finance |

Auth (`/login`, `/register`, password reset, email verify) is shared. After login, `/dashboard` routes the user to their portal home via role.

---

## 3. Domain relationship map

```
                    ┌─────────────────────────────────────┐
                    │         PUBLIC WEBSITE (CMS)         │
                    │  Trust · Catalogue · Conversion     │
                    └──────────────┬──────────────────────┘
           ┌───────────────────────┼───────────────────────┐
           ▼                       ▼                       ▼
     ┌──────────┐           ┌──────────┐           ┌──────────┐
     │  CLINIC  │           │ ACADEMY  │           │  STORE   │
     │Treatments│           │ Courses  │           │ Products │
     │Bookings  │           │Enrolments│           │ Orders   │
     │Consults  │           │Certs     │           │Inventory │
     └────┬─────┘           └────┬─────┘           └────┬─────┘
          │                      │                      │
          ▼                      ▼                      ▼
   /client portal         /student portal          /client orders
   /practitioner          /trainer                 + checkout
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 ▼
                    ┌────────────────────────┐
                    │   ADMIN + SHARED CORE  │
                    │ Payments · Messaging · │
                    │ AI · Loyalty · Media · │
                    │ RBAC · Settings · Audit│
                    └────────────────────────┘
```

**How domains relate**

- **Clinic** owns treatments, branches, practitioners, appointments, consultations, consent.
- **Academy** owns courses, schedules, enrolments, materials, attendance, assessments, certificates.
- **Store** owns products, inventory, cart, coupons, orders, deliveries, reviews.
- **Payments** is cross-cutting: appointment deposits, course fees/instalments, store checkout (Paystack).
- **Loyalty / referrals** primarily serve clients; rewards may apply to store or clinic.
- **CMS / media** feed the public site; admin Content managers own publishing.
- **AI assistant** answers public/client questions from a knowledge base; escalates to human support — never diagnoses or prescribes.

---

## 4. User types

| User type | Access | Primary goals | Content they own |
|-----------|--------|---------------|------------------|
| Visitor (guest) | Public | Learn, book intent, enrol intent, shop | Session cart only |
| Client | Client portal + public | Book, reschedule, pay, order, refer | Own appointments, orders, profile, loyalty |
| Student | Student portal + public | Enrol, attend, submit work, pay fees, certificates | Own enrolments, submissions, payments |
| Practitioner / Therapist | Practitioner (+ limited admin) | Daily schedule, notes, consult triage | Own notes, availability; assigned clients |
| Trainer | Trainer (+ limited admin) | Class roster, attendance, grading | Own course assignments & attendance |
| Receptionist | Admin (ops) | Bookings, walk-ins, consultations | Operational records (not settings) |
| Clinic Administrator | Admin | Clinic + academy ops, limited settings | Operational config |
| Finance Officer | Admin | Payments, refunds, financial reports | Finance records |
| Store Manager | Admin | Catalogue, stock, fulfilment | Products, inventory, orders |
| Content Manager | Admin | Pages, blog, gallery, SEO, menus | CMS content EN/FR |
| Support Agent | Admin | Consultations, AI conversations, notifications | Support threads |
| Super Administrator | Admin (full) | Everything including roles & audit | System |

CEO / Founder **Mac Tonto** appears as a featured practitioner/brand figure on public pages (About, Home, Team). Imagery is client-owned, not stock.

---

## 5. Major content areas (public)

| Area | Purpose | Conversion target |
|------|---------|-------------------|
| Home | Brand composition + pathfinding across clinic/academy/store | Book / Explore / Enrol |
| About | Story, standards, Mac Tonto, facility trust | Book / Meet practitioners |
| Treatments | Catalogue + filters | Book / View detail |
| Practitioners | Expertise & trust | Book with preferred practitioner |
| Book Appointment | Multi-step clinic conversion | Confirmed appointment + deposit |
| Consultation | Online intake for advice/triage | Request submitted |
| Academy | Training brand & pathways | Enrol |
| Courses | Catalogue & detail | Enrol + payment plan |
| Store | Skincare commerce | Cart → checkout |
| Gallery / Before–After | Visual proof (with consent) | Book / View treatments |
| Blog / FAQ | Education & SEO | Soft conversion |
| Contact / Map | Accra location & channels | Call / WhatsApp / visit |
| Certificate verify | Public trust for academy grads | Verification result |
| Legal / CMS pages | Policy compliance | — |

---

## 6. Content ownership matrix

| Content | Created by | Published by | Consumed by |
|---------|------------|--------------|-------------|
| Treatments & prices | Clinic Admin | Clinic Admin | Public, booking, client |
| Practitioner bios | Clinic Admin / practitioner | Clinic Admin | Public, booking |
| Courses & schedules | Clinic Admin / Trainer | Clinic Admin | Public, student, trainer |
| Certificates | Trainer request → Admin issue | Admin | Student, public verify |
| Products & stock | Store Manager | Store Manager | Public, client orders |
| Blog / FAQ / gallery | Content Manager | Content Manager | Public |
| Menus / homepage sections | Content Manager | Content Manager | Public |
| Notification templates | Support / Admin | Admin | All channels |
| AI knowledge articles | Support / Content | Admin (`ai.manage`) | AI chat |
| Settings (hours, deposit %, policies) | Super / Clinic Admin | Admin | Booking & checkout UX copy |

Dynamic EN/FR: UI strings in `lang/{en,fr}`; catalogue entities via translation tables / JSON.

---

## 7. Portal IA summaries

### Client
Overview → Appointments (upcoming/history) → Consultations → Payments/Receipts → Orders/Tracking → Wishlist → Loyalty → Referrals → Notifications → Profile → Security.

Priority on dashboard: next appointment, outstanding payment, recent order, loyalty, quick book/reschedule.

### Student
Overview → My Courses → Training Calendar → Materials → Assignments → Assessments → Attendance → Payments → Certificates → Notifications → Profile → Security.

Priority: current course, next class, balance due, assignments, certificate progress.

### Practitioner
Overview → Appointment Calendar → Daily Schedule → Client Appointments → Availability → Consultation Requests → Treatment Notes → Notifications → Profile.

### Trainer
Overview → Assigned Courses → Class Schedule → Student List → Attendance → Materials → Assignments → Assessments → Certificates → Announcements → Profile.

### Admin (grouped)
OVERVIEW · CLINIC · ACADEMY · STORE · FINANCE · MARKETING · CONTENT · COMMUNICATION · SYSTEM — see [navigation-map.md](./navigation-map.md).

---

## 8. Cross-surface journeys (IA level)

| Journey | Enters via | Completes in | Continues in |
|---------|------------|--------------|--------------|
| Book treatment | Public treatments / book | Auth + payment | Client appointments |
| Consult online | `/consultation` | Admin/practitioner triage | Client consultations |
| Enrol course | Academy / courses | Auth + fee/instalment | Student portal |
| Buy product | Store | Checkout + Paystack | Client orders |
| Earn certificate | Student assessments | Admin issue | Student download + public verify |
| Refer a friend | Client loyalty | Referral code share | New client registration |

---

## 9. IA rules for designers & engineers

1. One brand voice across surfaces; admin is denser but still ivory/charcoal/bronze — not a generic SaaS skin.
2. Never bury Book or Enrol behind more than one unnecessary click from relevant catalogues.
3. Portals must not clone admin chrome; clients/students get task-first navigation.
4. Every public page has one primary job (trust, decide, or convert).
5. Certificate verification stays public and unauthenticated.
6. Medical safety: AI and public copy never claim diagnosis/prescription; escalate to human consult.
