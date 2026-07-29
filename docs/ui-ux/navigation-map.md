# Navigation Map — De Lux Aesthetic Clinic

## 1. Public header anatomy

```
┌──────────────────────────────────────────────────────────────────────────┐
│ ANNOUNCEMENT BAR (utility) — promo / new course / phone shortcut          │
├──────────────────────────────────────────────────────────────────────────┤
│ UTILITY ROW — phone · hours · EN/FR · Account · Cart                      │
├──────────────────────────────────────────────────────────────────────────┤
│ LOGO (De Lux) │ PRIMARY NAV │ Enrol (ghost) │ Book (primary) │ ☰ mobile   │
└──────────────────────────────────────────────────────────────────────────┘
```

Sticky behaviour: after scroll, compact header with border-bottom on ivory/white — **no shadow**. Active link: bronze underline (1px) or `aria-current="page"`.

---

## 2. Primary navigation (desktop)

| Label | Route | Active match |
|-------|-------|--------------|
| Home | `web.home` `/` | exact |
| About | `web.about` `/about` | `/about` |
| Treatments | `web.treatments.index` | `/treatments*` |
| Practitioners | `web.practitioners.index` | `/practitioners*` |
| Academy | `web.academy.index` | `/academy*`, `/courses*`, `/enrol*` |
| Store | `web.store.index` | `/store*`, `/cart`, `/checkout` |
| Gallery | `web.gallery` | `/gallery*`, `/before-and-after` |
| Blog | `web.blog.index` | `/blog*` |
| Contact | `web.contact` | `/contact` |

**Prominent CTAs (not in the text link row)**

| Action | Style | Route |
|--------|-------|-------|
| Enrol Now | `btn-ghost` / secondary | `/enrol` or academy |
| Book Appointment | `btn-primary` bronze fill | `/book` |

---

## 3. Secondary navigation

| Context | Items |
|---------|-------|
| Treatments detail | Breadcrumbs: Home / Treatments / {Category} / {Name} |
| Academy | Sub-links on landing: Courses · Calendar · Enrol · Certification |
| Store | Categories strip under hero; Cart link in utility |
| Footer columns | Treatments · Academy · Store · Policies · Contact |
| Legal | Privacy · Terms · Refund · Cookie (CMS pages) |

Footer is the durable secondary map for SEO and low-intent browsing.

---

## 4. Utility navigation

| Item | Placement | Notes |
|------|-----------|-------|
| Announcement | Top bar | Dismissible optional; CMS-driven |
| Phone / hours | Utility strip | Accra hours Mon–Sat |
| Language EN ↔ FR | Utility + mobile | `locale.switch` |
| Search | Utility or icon in header | Opens panel/drawer — rectangular |
| Account | Login / Register or portal hub | `/dashboard` when authed |
| Cart | Count badge (square, not pill) | `/cart` |
| WhatsApp | Footer + contact | Deep link to business WhatsApp |

---

## 5. Mobile navigation

Full-height ivory panel, radius 0, border separators only.

Order:

1. Primary links (+ Courses, Blog if collapsed on desktop)
2. Language switcher
3. Search
4. Account / Login / Register
5. Cart
6. Book Appointment (primary)
7. Enrol Now (secondary)

Behaviour: focus trap, Escape closes, `aria-expanded` on burger, body scroll lock, Alpine `x-show` with opacity transition only.

---

## 6. Client portal sidebar

| Group | Items | Paths |
|-------|-------|-------|
| Home | Overview | `/client` |
| Care | Appointments, Consultations | `/client/appointments`, `/client/consultations` |
| Commerce | Orders, Wishlist | `/client/orders`, `/client/wishlist` |
| Finance | Payments & Receipts | `/client/payments` |
| Rewards | Loyalty, Referrals | `/client/loyalty`, `/client/referrals` |
| Account | Notifications, Profile, Security | `/client/notifications`, `/client/profile`, `/client/security` |

Mobile: bottom nav for Overview · Appointments · Orders · More (drawer).

---

## 7. Student portal sidebar

| Group | Items |
|-------|-------|
| Home | Overview |
| Learning | My Courses, Calendar, Materials |
| Work | Assignments, Assessments, Attendance |
| Finance | Payments |
| Credentials | Certificates |
| Account | Notifications, Profile, Security |

Mobile: Overview · Courses · Calendar · More.

---

## 8. Practitioner sidebar

Overview · Calendar · Daily Schedule · Appointments · Availability · Consultations · Notes (via appointment) · Notifications · Profile.

---

## 9. Trainer sidebar

Overview · Courses · Class Schedule · Students · Attendance · Materials · Assignments · Assessments · Certificates · Announcements · Profile.

---

## 10. Admin sidebar groups

Fixed left sidebar (charcoal soft-black `#0E0E0E` / `#1A1A1A`), Manrope labels, Cormorant brand mark “De Lux”. Group labels uppercase micro-labels (`tracking-label`). Items permission-filtered.

### OVERVIEW
- Dashboard → `/admin`
- Activity → `/admin/activity`

### CLINIC
- Appointments → `/admin/appointments`
- Consultation Requests → `/admin/consultations`
- Clients → `/admin/clients`
- Practitioners → `/admin/practitioners`
- Therapists → `/admin/therapists`
- Treatments → `/admin/treatments`
- Branches → `/admin/branches`

### ACADEMY
- Students → `/admin/students`
- Trainers → `/admin/trainers`
- Courses → `/admin/courses`
- Schedules → `/admin/schedules`
- Enrolments → `/admin/enrolments`
- Attendance → `/admin/attendance`
- Assessments → `/admin/assessments`
- Certificates → `/admin/certificates`

### STORE
- Products → `/admin/products`
- Categories → `/admin/products/categories`
- Inventory → `/admin/inventory`
- Orders → `/admin/orders`
- Deliveries → `/admin/deliveries`
- Reviews → `/admin/reviews`

### FINANCE
- Payments → `/admin/payments`
- Refunds → `/admin/refunds`
- Reports → `/admin/reports`

### MARKETING
- Loyalty → `/admin/loyalty`
- Referrals → `/admin/referrals`
- Testimonials → `/admin/testimonials`
- Promotions → `/admin/promotions`

### CONTENT
- Pages → `/admin/pages`
- Blog → `/admin/blog`
- FAQs → `/admin/faqs`
- Gallery → `/admin/gallery`
- Media → `/admin/media`
- Menus → `/admin/menus`
- Translations → `/admin/translations`

### COMMUNICATION
- Notifications → `/admin/notifications`
- Templates → `/admin/notification-templates`
- AI Knowledge Base → `/admin/ai/knowledge`
- AI Conversations → `/admin/ai/conversations`

### SYSTEM
- Users → `/admin/users`
- Roles → `/admin/roles`
- Permissions → `/admin/permissions`
- Audit Logs → `/admin/audit`
- Settings → `/admin/settings`

Admin header utilities: breadcrumbs, page title + primary action, global search, notification bell, profile menu, “View website”, Logout. Mobile: sidebar becomes drawer.

---

## 11. Active state rules

| Surface | Active treatment |
|---------|------------------|
| Public | `aria-current="page"` + 1px bronze underline; no fill pills |
| Portals | Left border 2px bronze or background soft stone; text charcoal |
| Admin | Background `#2A2A2A` + bronze left rule; white text |

Disabled / unauthorized items: omit from nav (preferred) rather than greying out, except temporarily locked features during phased rollout.
