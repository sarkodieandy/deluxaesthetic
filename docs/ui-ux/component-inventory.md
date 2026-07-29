# Component Inventory — De Lux Aesthetic Clinic

All components: rectangular (radius 0–3px), no shadows, EN/FR-ready labels, keyboard accessible. States listed where relevant: **default · hover · focus · active · disabled · loading · empty · error · success**.

Blade locations (target): `resources/views/{web,admin,client,student,practitioner,trainer}/components/`.

---

## 1. Public website

| Component | Purpose | Key states |
|-----------|---------|------------|
| Announcement bar | Promo / course / contact shortcut | default, dismissible |
| Header | Brand + utility + primary nav | default, sticky, scrolled |
| Primary navigation | Desktop links | default, hover, active, focus |
| Mobile menu | Full-height panel | open/closed, focus-trap |
| Language switcher | EN/FR | default, current |
| Search bar / panel | Site search | default, loading, empty results, error |
| Hero | Full-bleed brand composition | loading image, reduced-motion |
| Section heading | Label + title + optional action | — |
| Treatment card | Catalogue / home feature | hover image zoom, loading skeleton, empty |
| Practitioner card | Team grids | hover, loading, empty |
| Course item | Academy lists | spaces left warning, loading, empty |
| Product card | Store grid | stock states, wishlist toggle, loading, empty |
| Testimonial | Quote + attribution | carousel states |
| FAQ accordion | Straight-line expand | collapsed/expanded, focus |
| Before–after slider | Comparison | dragging, keyboard, consent note |
| Blog card | Editorial teaser | hover, loading |
| Contact block | Address / channels | — |
| Footer | Multi-column sitemap | — |
| CTA section | End-of-page conversion | — |
| Breadcrumbs | Hierarchy | — |
| Pagination | Catalogue pages | disabled edges |
| Filter drawer / toolbar | Treatments, courses, store | open, applied count |
| Status chip | Booking/order labels | semantic colours + text |
| Button (`btn-primary/secondary/ghost/light`) | Actions | hover, focus, disabled, loading |
| Form field set | Inputs, select, textarea | error, success, disabled |
| Time-slot grid | Booking | available, selected, unavailable, loading |
| Cart line item | Cart page | update qty loading, remove |
| AI chat widget | Assistant entry | open, typing, error, escalate CTA |
| Toast / alert | Feedback | success, error, info |
| Skeleton block | Loading placeholder | animated pulse (opacity only) |
| Empty state | No results | with recovery CTA |
| Modal / dialog | Confirmations | open, focus-trap |
| Newsletter field | Footer | error, success |

---

## 2. Shared form controls

| Control | Notes |
|---------|-------|
| Text, email, tel, password | Label above; never placeholder-only |
| Select | Native or custom listbox — rectangular |
| Date picker | Mobile-friendly; Accra TZ labels |
| Checkbox / radio | Radio may be circular; checkbox square radius ≤3px |
| File upload | Materials, assignments, consent docs |
| Textarea | Consultation questions, notes |
| OTP / 2FA inputs | Security screens |
| Consent checkbox | Booking & enrolment policies |

Validation: inline under field + summary on multi-step. Preserve user input on error.

---

## 3. Portal components (client / student / practitioner / trainer)

| Component | Surfaces | States |
|-----------|----------|--------|
| Portal sidebar | All portals | collapsed mobile, active |
| Portal header | All | notifications badge |
| Quick action | Client/student dashboards | disabled if policy blocks |
| Summary metric | Dashboards | loading skeleton |
| Appointment item | Client, practitioner | status chip, actions |
| Course progress | Student | 0–100%, empty |
| Payment status | Client, student | pending/paid/failed |
| Order status | Client | pipeline steps |
| Notification item | All | unread/read |
| Profile section | All | edit/save loading |
| Security panel | Client, student | 2FA states |
| Attendance roster | Trainer | mark present/late/absent — saving |
| Assignment list | Student, trainer | submitted/graded/overdue |
| Certificate card | Student | download loading, revoked |
| Schedule calendar | Practitioner, trainer | loading, empty day |
| Notes editor | Practitioner | saving, error |
| Empty panel | All | contextual CTA |

---

## 4. Admin components

| Component | Purpose | States |
|-----------|---------|--------|
| Admin sidebar | Grouped nav | permission-filtered, mobile drawer |
| Admin header | Title, actions, search | — |
| Metric panel | KPI (asymmetric widths) | loading, empty |
| Chart panel | Revenue / bookings | loading, empty, error |
| Data table | CRUD lists | loading, empty, error, bulk select |
| Filter toolbar | Search/filter/sort | applied |
| Status label | Domain statuses | semantic |
| Empty state | No rows | CTA to create |
| Form section | Grouped admin forms | error summary |
| Drawer | Quick edit | open |
| Modal | Destructive confirms | open |
| Confirmation dialog | Delete/cancel/refund | — |
| Toast | Save feedback | success/error |
| Activity timeline | Dashboard / client history | empty |
| Audit record | Audit log rows | — |
| Report filter | Date/branch/range | — |
| Export action | CSV/PDF | loading, error |
| Media picker | CMS / products | uploading, error |
| Translation tabs | EN/FR fields | missing translation warning |
| Permission gate | Hide actions | unauthorized |

### Admin table requirements (every major table)

Search · filters · sort · pagination · column toggle · bulk select · bulk actions · export · empty · loading · responsive stacked records · permission-aware row actions.

---

## 5. State patterns (global)

### Loading
- Skeleton shapes matching final layout (straight rectangles)
- Button spinner (CSS) + disabled duplicate submit
- Table: overlay or skeleton rows — no shadow modal

### Empty
- One sentence + primary recovery action (e.g. “No appointments — Book now”)
- Illustrations optional; prefer typography + thin rules over cartoon art

### Error
- What happened · what to do · retry · contact/WhatsApp when payment fails

### Success
- Confirmation message · next action (View appointment / Download receipt / Return to dashboard)

---

## 6. Iconography

- **Lucide** icons, 1.5–2px stroke, charcoal/bronze/semantic colours
- Sizes: 16 / 20 / 24px
- No emoji as UI icons
- Decorative icons optional; functional icons need text or `aria-label`
