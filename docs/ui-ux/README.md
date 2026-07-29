# De Lux Aesthetic Clinic — UI/UX Documentation

**Brand:** De Lux Aesthetic Clinic · Accra, Ghana  
**Stack:** Laravel · Blade · Tailwind CSS · Alpine.js · GSAP · Vite  
**Surfaces:** Public site · Client · Student · Practitioner · Trainer · Admin  
**Visual system:** Straight-line (radius 0–3px), no shadows, warm ivory / muted bronze

This folder is the source of truth for interface architecture before and during implementation. Pair with `/docs/architecture.md`, `/docs/route-map.md`, `/docs/design-system.md`, and `/docs/role-permission-matrix.md`.

---

## Document index

| # | Document | Purpose |
|---|----------|---------|
| 1 | [information-architecture.md](./information-architecture.md) | Major areas, user types, content ownership, domain relationships |
| 2 | [sitemap.md](./sitemap.md) | Full URL trees for public, portals, and admin |
| 3 | [navigation-map.md](./navigation-map.md) | Primary/secondary/utility nav, portal & admin sidebars, mobile |
| 4 | [design-principles.md](./design-principles.md) | Premium medical-aesthetic principles and non-negotiables |
| 5 | [design-tokens.md](./design-tokens.md) | CSS custom properties — colour, radius, motion, layout |
| 6 | [typography.md](./typography.md) | Cormorant Garamond + Manrope scale and usage |
| 7 | [colour-system.md](./colour-system.md) | Accent hierarchy, semantic colour, do-nots |
| 8 | [spacing-grid.md](./spacing-grid.md) | Spacing scale, 12/8/4 grids, containers, gutters |
| 9 | [component-inventory.md](./component-inventory.md) | Public, portal, admin components and states |
| 10 | [page-topology.md](./page-topology.md) | Section anatomy for key pages (Home 17 sections) |
| 11 | [responsive-rules.md](./responsive-rules.md) | Breakpoints 360–1920 and layout rules |
| 12 | [animation-guidelines.md](./animation-guidelines.md) | Permitted GSAP/CSS motion and reduced-motion |
| 13 | [accessibility-checklist.md](./accessibility-checklist.md) | WCAG-oriented platform checklist |
| 14 | [asset-plan.md](./asset-plan.md) | `public/assets` structure, licences, CEO portraits |
| 15 | [ux-flows.md](./ux-flows.md) | Twenty required end-to-end UX flows |

---

## Implementation phases (1–7)

Implement UI after documentation. Keep the app runnable after every phase. After each phase: desktop + mobile layout, keyboard nav, contrast, console, assets, empty/loading states, EN/FR expansion, update `docs/progress.md`.

### Phase 1 — Foundations
Design tokens, typography, colour system, grid, spacing, buttons, forms, core shared components (`resources/css/web/design-tokens.css` and surface entry CSS).

### Phase 2 — Public shell & brand pages
Header, navigation, footer, Home (17 sections), About (Mac Tonto CEO story), Treatments catalogue, Practitioners.

### Phase 3 — Clinic conversion & client portal
Multi-step booking, consultation request, client portal dashboard and appointment/payment flows.

### Phase 4 — Academy surfaces
Academy landing, courses, enrolment, student portal, trainer portal (attendance, assessments, materials).

### Phase 5 — Store commerce
Store catalogue, product detail, cart, checkout (Paystack), orders, wishlist.

### Phase 6 — Admin operations
Admin shell with sidebar groups (OVERVIEW → SYSTEM), dashboard metrics, tables, forms, reports, settings.

### Phase 7 — Polish & quality
GSAP/CSS animations, responsive refinement, accessibility, performance, browser testing, EN/FR QA.

---

## Design non-negotiables (quick reference)

- Radius default **0px**, maximum **3px**
- **No** box shadows, text shadows, pills, glassmorphism, or blob shapes
- Dominant accent: **muted bronze `#967452`**
- Fonts: **Cormorant Garamond** (display) + **Manrope** (UI/body)
- Circles only for portraits, radios, dots, loaders, chart points, status indicators
- Currency **GHS**, timezone **Africa/Accra**, locales **EN / FR**

---

## Ownership

| Role | Responsibility |
|------|----------------|
| Product / design | Keep this folder aligned with shipped UI |
| Engineering | Implement tokens and components from these specs |
| Content | Own EN/FR copy against topology section purposes |
| Client (Mac Tonto) | Approve CEO imagery and brand tone |
