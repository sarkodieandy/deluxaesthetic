# Design system — De Lux Aesthetic Clinic

## Brand character

Editorial medical-aesthetic: warm ivory grounds, charcoal type, bronze accent, sage clinical support. Straight lines, square corners, border-based structure — no shadows, no pills, no glassmorphism.

## Tokens

```css
:root {
  /* Radius & elevation */
  --radius-default: 0px;
  --radius-minimal: 2px;
  --border-width: 1px;
  --shadow-none: none;

  /* Surfaces */
  --color-ivory: #F4F0E8;
  --color-white: #FFFFFF;
  --color-stone: #E5DED3;
  --color-beige: #D6C9B9;
  --color-charcoal: #1B1B1B;
  --color-soft-black: #101010;
  --color-bronze: #967452;      /* primary accent */
  --color-rose: #AC7E82;
  --color-sage: #748779;
  --color-olive: #3D4A3F;
  --color-soft-grey: #77736E;
  --color-border: #D8D1C7;

  /* Semantic */
  --color-error: #A43B3B;
  --color-success: #386B4B;
  --color-warning: #9B6C20;
  --color-info: #365F7C;
}
```

## Typography

| Role | Font | Notes |
|------|------|-------|
| Display / headings | **Cormorant Garamond** | Editorial serif |
| Body / UI | **Manrope** | Clean sans |

Scale tokens: `display`, `page-title`, `section-title`, `card-title`, `body-lg`, `body`, `body-sm`, `label`, `caption`, `button`, `table-heading`, `metric`.

Rules: no text-shadow; comfortable line-height; uppercase micro-labels with letter-spacing; large admin metrics.

## Layout topology

- 12-column grids
- Split-screen & full-bleed media
- Asymmetrical image/text
- Thin horizontal rules & vertical dividers
- Rectangular blocks; max radius 3px (prefer 0–2px)
- Controlled overlap; generous negative space

## Components (rules)

| Element | Rule |
|---------|------|
| Buttons | Rectangular; fill transition; no pills |
| Inputs | Straight borders; no rounded search |
| Cards | Optional border only; no shadow; not in hero |
| Tables | Sharp containers; thin borders |
| Icons | Lucide; semantic colours; no emoji icons |
| Photos | Square/rect; **circular only for profile portraits** |
| Hero | Brand-first; one headline; one support line; CTA group; full-bleed image |

## Motion

GSAP + ScrollTrigger for: hero mask/clip reveal, line expansion, staggered text, section entrances. CSS for hovers/underlines. Respect `prefers-reduced-motion`. No bounce, blobs, or heavy parallax.

## Surfaces

| Surface | Atmosphere |
|---------|------------|
| Public | Ivory/stone, image-led, editorial |
| Admin | Operational charcoal/ivory, dense tables, metric panels |
| Client / student portals | Brand-aligned, practical, distinct from admin |

## CEO imagery

Client-supplied portraits of **Mac Tonto** (CEO/Founder):

- `public/assets/web/images/team/ceo-mac-tonto-portrait-a.webp` (from FAB_0325)
- `public/assets/web/images/team/ceo-mac-tonto-portrait-b.webp` (from FAB_0390)

Used on About, Team, and homepage practitioner feature. Circular crop only in avatar contexts; rectangular in editorial layouts.
