# Animation Guidelines — De Lux Aesthetic Clinic

Stack: **GSAP + ScrollTrigger** for public storytelling; **CSS transitions** for UI chrome; **Alpine.js** for show/hide. Chart.js entrance only in admin.

---

## 1. Permitted motions

| Motion | Where | Tech |
|--------|-------|------|
| Hero text mask / clip reveal | Home hero | GSAP |
| Image clipping reveal | Hero, story split | GSAP |
| Horizontal divider expansion | Hero rule, section rules | GSAP/CSS |
| Vertical line reveal | Hero accent | GSAP |
| Navigation underline movement | Primary nav | CSS |
| Section fade + short slide (8–16px) | Public sections | GSAP ScrollTrigger |
| Grid-item stagger | Treatments, products, courses | GSAP |
| Image zoom on hover | Cards (scale ≤1.04) | CSS |
| Button fill transition | Buttons | CSS |
| Accordion height | FAQ | CSS/Alpine |
| Modal fade + short slide | Dialogs | CSS/Alpine |
| Booking step transition | `/book` | CSS/Alpine |
| Form success feedback | Forms | CSS |
| Counter animation | Home stats | GSAP |
| Before–after slider | Gallery | JS |
| Testimonial transition | Home | CSS/Swiper |
| Product image transition | Store | CSS |
| Admin chart entrance | Dashboard | Chart.js + opacity |
| Toast in/out | All | CSS |
| Skeleton pulse | Loading | CSS opacity |
| Progress indicators | Steps, uploads | CSS |

---

## 2. Timing

| Band | Duration | Examples |
|------|----------|----------|
| Micro-interactions | **120–220ms** | Hover, underline, button fill |
| Component transitions | **200–350ms** | Accordion, modal, menu, toasts |
| Section reveals | **450–800ms** | Scroll reveals, hero sequence |
| Stagger children | 40–80ms between items | Grids |

Easing: `--ease-out` (`cubic-bezier(0.22, 1, 0.36, 1)`). Avoid long delays (>200ms) before first meaningful paint motion.

---

## 3. Public page motion budget

Aim for **2–3 intentional moments** above the fold / early scroll (hero reveal, line expand, first section stagger). Do not animate every element on the page.

---

## 4. Forbidden

- Bouncing / elastic novelty easings  
- Floating decorative blobs or particles  
- Continuous spinning decorations (loaders exempt, brief)  
- Excessive parallax (subtle ken-burns ≤ subtle scale only)  
- Random perpetual movement  
- Motions that block clicks or trap scroll  
- Shadow/glow pulses  

---

## 5. `prefers-reduced-motion`

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

JS: if `matchMedia('(prefers-reduced-motion: reduce)').matches`, skip GSAP ScrollTrigger timelines; set final states immediately. Interface must be **fully usable** with motion off.

---

## 6. Performance

- Animate `transform` and `opacity` only  
- Don’t ScrollTrigger hundreds of nodes — batch section roots  
- Kill/cleanup GSAP on Turbo/live navigations if added later  
- Pause hero video when off-screen  

---

## 7. Surface notes

| Surface | Motion level |
|---------|--------------|
| Public | Highest (editorial) |
| Portals | Low (toasts, drawers, steppers) |
| Admin | Minimal (charts, toasts, drawers) |
