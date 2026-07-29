# Responsive Rules — De Lux Aesthetic Clinic

## 1. Breakpoints (test matrix)

| Width | Class | Expectation |
|-------|-------|-------------|
| 360px | Small phone | Primary actions visible; no horizontal spill |
| 390px | iPhone standard | Baseline mobile QA |
| 430px | Large phone | Still single column public sections |
| 768px | Tablet start | 8-column grid; filters → top/drawer |
| 1024px | Small desktop | 12-column; sidebars appear |
| 1280px | Desktop | Sticky booking/enrol summaries |
| 1440px | Design target | Full editorial layouts |
| 1920px | Large | Container capped ~1440px; no ultra-stretched text |

CSS guidance:

```css
/* mobile first */
/* sm ~640, md ~768, lg ~1024, xl ~1280, 2xl ~1440 */
```

---

## 2. Desktop (≥1024px)

- Full 12-column grid  
- Supporting panels and sticky summaries (booking, enrolment, checkout, treatment detail)  
- Data tables in admin/portals  
- Multi-column marketing sections  
- Primary nav visible; burger hidden  
- Admin/portal sidebars fixed  

---

## 3. Tablet (768–1023px)

- 8-column grid  
- Preserve hierarchy — do not naïvely stack every two-column into chaos  
- Sidebars → drawers or top filter bars  
- Forms remain readable (single column ok)  
- Avoid awkward half-width orphan columns  
- Home featured treatments: 2-up acceptable; keep lead treatment emphasis if possible  

---

## 4. Mobile (<768px)

- 4-column grid; mostly 4-wide stacks  
- Primary actions visible (sticky bottom for Book / Enrol / Pay / Checkout)  
- Filters in drawers  
- Accordions for long treatment/course detail  
- Compact header + full-height menu  
- Tap targets ≥44×44px  
- Avoid long text over images; strengthen scrims  
- Tables → stacked records (admin/finance may allow controlled horizontal scroll)  
- Modals fit viewport with internal scroll  
- Cart badge and language remain reachable  

---

## 5. Component-specific rules

| Component | Mobile behaviour |
|-----------|------------------|
| Hero | Full-bleed; type scales down; CTA stack vertical |
| Service index | Vertical numbered list |
| Treatment grid | 1-up (2-up only if image stays sharp) |
| Before/after | Full width slider; large handles |
| Stats | 2×2 grid with dividers |
| Footer | Stacked columns |
| Booking steps | Full width; summary collapsed |
| Portal nav | Bottom bar + More drawer |
| Admin | Sidebar drawer; tables as cards |

---

## 6. Images

- `srcset` / WebP; `fetchpriority="high"` on hero only  
- Lazy-load below fold  
- Do not serve 4000px images to 360px screens  
- Aspect ratios stable to prevent CLS  

---

## 7. QA checklist per breakpoint

- [ ] No horizontal scrollbar  
- [ ] Bronze CTAs not clipped  
- [ ] Focus states visible  
- [ ] EN and FR labels don’t overflow buttons  
- [ ] Sticky elements don’t hide content behind them  
- [ ] Maps/forms usable  
