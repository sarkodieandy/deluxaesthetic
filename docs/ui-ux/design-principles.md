# Design Principles — De Lux Aesthetic Clinic

## 1. Brand character

De Lux is a **premium medical-aesthetic** clinic and academy in Accra. The interface must feel clinical enough to trust with skin and body, and editorial enough to feel luxury — never spa-cliché, never hospital-cold, never startup-SaaS.

**Feel words:** Premium · Medical · Luxurious · Clean · Professional · Trustworthy · Elegant · Modern · Structured · Editorial · Calm · Accessible · Conversion-focused.

**Must not feel like:** Generic clinic template · Bootstrap demo · Basic e-commerce · Typical SaaS dashboard · AI landing page collage · Disconnected section stack · Rounded mobile app stretched to desktop.

---

## 2. One composition, brand first

- The first viewport is **one composition**, not a dashboard of widgets.
- **De Lux** is a hero-level brand signal — not only a nav word.
- Brand test: if you remove the nav and the first screen could belong to another clinic, branding is too weak.
- Hero budget: brand, one headline, one support sentence, one CTA group, one dominant full-bleed image. No stats, schedules, address blocks, or promo chips in the hero.

---

## 3. Non-negotiable straight-line rules

| Rule | Requirement |
|------|-------------|
| Default radius | **0px** |
| Maximum radius | **3px** (prefer 0–2px) |
| Cards | No rounded cards; border-only structure if needed |
| Buttons | Rectangular — **no pills** |
| Shadows | **No** box shadows, text shadows, glow |
| Effects | **No** glassmorphism, neumorphism, blob backgrounds |
| Dividers | Straight horizontal/vertical rules — no curved section dividers |
| Gradients | No random decorative gradients; restrained ivory atmospheric washes only |
| Icons | Lucide (or approved set) — **no emoji icons** |
| Floaters | No excessive floating badges, stickers, or promo chips on media |
| Templates | No default Tailwind “look”, Bootstrap look, or ThemeForest clones |

**Depth without shadow:** background contrast (ivory / white / stone / charcoal), thin borders (`#D9D2C9`), typography hierarchy, grid alignment, spacing, dividers, image composition, colour blocks, controlled overlap.

---

## 4. Circles — sparse exceptions only

Circular shapes are allowed **only** for:

- Profile photographs (avatars)
- Radio inputs
- Notification dots
- Loading indicators
- Chart points
- Small online-status indicators

CEO / practitioner editorial photography remains **rectangular** in story layouts; circular crop only in avatar contexts.

---

## 5. Multi-surface coherence

| Surface | Atmosphere | Density |
|---------|------------|---------|
| Public | Image-led, editorial ivory | Generous |
| Client / Student | Brand-aligned, task-first | Medium |
| Practitioner / Trainer | Operational calm | Medium-high |
| Admin | Charcoal sidebar, ivory content, dense tables | High |

Portals and admin share tokens but **not** identical layouts. Clients must never feel they opened the staff back-office.

---

## 6. Content principles

- Every section has **one purpose**, one headline, usually one support sentence.
- Support: trust, information, conversion, navigation, decision-making, education, or progress — not decoration.
- Real visual anchors: clinic rooms, treatments, academy practice, Accra-appropriate professional imagery — not abstract purple blobs.
- Before/after: consent disclaimer mandatory; label demonstration content when stock.

---

## 7. Conversion principles

- Book Appointment and Enrol Now remain visible at decision points.
- Multi-step flows (booking, enrolment, checkout) show progress, summary, and recovery from errors.
- Deposits, cancellation windows, and instalment terms are stated in plain language (GHS, Africa/Accra time).

---

## 8. What to avoid (hard list)

1. Purple-on-white / indigo SaaS gradients  
2. Warm-cream + terracotta AI cliché (we use ivory + **bronze**, intentionally specified — do not drift to terracotta/orange)  
3. Broadsheet dense newspaper columns with hairline overload  
4. Pill clusters, stat strips in the hero, icon rows as filler  
5. Inset hero cards, tiled collages, floating media frames as the default hero  
6. Detached labels / badges overlaid on hero media  
7. Cards in the hero  
8. Cards anywhere they are not required for interaction  
9. Dark-mode-first aesthetics for the public brand (admin sidebar dark is functional, not a brand “dark theme”)  
10. Oversized empty heroes with little useful content  

---

## 9. Motion principles

Motion creates presence and hierarchy — not noise. Prefer 2–3 intentional moments per major public page (hero reveal, line expand, section stagger). Respect `prefers-reduced-motion`. See [animation-guidelines.md](./animation-guidelines.md).

---

## 10. Accessibility & inclusion

Design assumes keyboard users, screen-reader users, and bilingual (EN/FR) text expansion (~30% longer in French). Contrast charcoal on ivory; never rely on colour alone for appointment or payment status.
