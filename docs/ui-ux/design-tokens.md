# Design Tokens — De Lux Aesthetic Clinic

Canonical CSS custom properties for all surfaces. Implement in `resources/css/web/design-tokens.css` (shared import) and mirror into admin/portal entry files. Hex values below are **source of truth** for the UI/UX redesign (supersede any drifted values in older docs until CSS is aligned).

---

## 1. Colour — surfaces & brand

| Token | CSS variable | Value | Role |
|-------|--------------|-------|------|
| Warm ivory | `--color-ivory` | `#F4F0E8` | Page ground, public body |
| White | `--color-white` | `#FFFFFF` | Cards/panels, form surfaces |
| Soft stone | `--color-stone` | `#E7E0D7` | Alternating bands, table header |
| Light beige | `--color-beige` | `#D7CAB9` | Soft fills, disabled tracks |
| Deep charcoal | `--color-charcoal` | `#1A1A1A` | Primary text |
| Soft black | `--color-soft-black` | `#0E0E0E` | Admin sidebar, footer strong |
| Muted bronze | `--color-bronze` | `#967452` | **Primary accent** / CTAs / focus |
| Medical sage | `--color-sage` | `#74867A` | Clinical secondary accent |
| Dusty rose | `--color-rose` | `#A97C81` | Soft highlight (sparingly) |
| Soft grey | `--color-soft-grey` | `#74706A` | Secondary text, icons mute |
| Border | `--color-border` | `#D9D2C9` | Rules, inputs, tables |

---

## 2. Colour — semantic

| Token | CSS variable | Value | Use |
|-------|--------------|-------|-----|
| Success | `--color-success` | `#386B4B` | Paid, confirmed, completed |
| Error | `--color-error` | `#A43B3B` | Failed, validation, destructive |
| Warning | `--color-warning` | `#9A6A1E` | Policy risk, low stock |
| Info | `--color-info` | `#365F7C` | Neutral system notices |
| Pending | `--color-pending` | `#7A6B46` | Awaiting payment / review |
| Inactive | `--color-inactive` | `#777777` | Disabled, archived |

Always pair with text/icon label — colour is not the only signal.

---

## 3. Radius & elevation

```css
:root {
  --radius-none: 0px;
  --radius-default: 0px;
  --radius-minimal: 2px;
  --radius-max: 3px;
  --border-width: 1px;
  --border-width-strong: 2px;
  --shadow-none: none;
}
```

**Rule:** `box-shadow` and `text-shadow` stay `none` / `var(--shadow-none)`. Elevation = border + background shift only.

---

## 4. Typography tokens

```css
:root {
  --font-display: "Cormorant Garamond", Georgia, "Times New Roman", serif;
  --font-body: "Manrope", "Segoe UI", Helvetica, Arial, sans-serif;

  --text-display: clamp(2.75rem, 5vw, 4.5rem);
  --text-page-title: clamp(2rem, 3.5vw, 3rem);
  --text-section-title: clamp(1.75rem, 2.5vw, 2.25rem);
  --text-subsection: clamp(1.25rem, 1.5vw, 1.5rem);
  --text-card-title: 1.25rem;
  --text-body-lg: 1.125rem;
  --text-body: 1rem;
  --text-body-sm: 0.875rem;
  --text-label: 0.75rem;
  --text-caption: 0.6875rem;
  --text-nav: 0.8125rem;
  --text-button: 0.8125rem;
  --text-table-heading: 0.75rem;
  --text-metric: clamp(1.75rem, 2vw, 2.5rem);
  --text-helper: 0.8125rem;

  --leading-tight: 1.15;
  --leading-snug: 1.35;
  --leading-normal: 1.6;
  --leading-relaxed: 1.75;

  --tracking-label: 0.14em;
  --tracking-button: 0.08em;
  --tracking-nav: 0.06em;
  --font-weight-regular: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
}
```

Full usage: [typography.md](./typography.md).

---

## 5. Spacing tokens

```css
:root {
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 24px;
  --space-6: 32px;
  --space-7: 48px;
  --space-8: 64px;
  --space-9: 80px;
  --space-10: 112px;
  --space-11: 144px;

  --gutter: clamp(1rem, 3vw, 2rem);
  --section-space: clamp(3.5rem, 8vw, 6.5rem);
  --header-height: 7.25rem;
  --header-height-sticky: 4.5rem;
  --announcement-height: 2.25rem;
}
```

---

## 6. Layout tokens

```css
:root {
  --container-max: 90rem;          /* ~1440px */
  --container-narrow: 48rem;
  --container-reading: 42rem;
  --grid-columns-desktop: 12;
  --grid-columns-tablet: 8;
  --grid-columns-mobile: 4;
  --grid-gap: var(--space-5);
  --sidebar-width-admin: 16.5rem;
  --sidebar-width-portal: 15rem;
  --booking-summary-width: 22rem;
}
```

Target content width: **1360–1440px** (`~85–90rem`). Prefer `90rem` max with side gutters.

---

## 7. Motion tokens

```css
:root {
  --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
  --ease-in-out: cubic-bezier(0.45, 0, 0.55, 1);
  --duration-micro: 160ms;     /* 120–220ms band */
  --duration-fast: 160ms;
  --duration-base: 280ms;      /* 200–350ms band */
  --duration-slow: 560ms;      /* section reveals ~450–800ms */
  --duration-reveal: 650ms;
}
```

---

## 8. Z-index scale

```css
:root {
  --z-base: 1;
  --z-sticky: 100;
  --z-header: 200;
  --z-dropdown: 300;
  --z-drawer: 400;
  --z-modal: 500;
  --z-toast: 600;
  --z-overlay-max: 700;
}
```

---

## 9. Focus & interaction

```css
:root {
  --focus-ring-color: var(--color-bronze);
  --focus-ring-width: 2px;
  --focus-ring-offset: 2px;
  --tap-min: 44px;
}
```

```css
:focus-visible {
  outline: var(--focus-ring-width) solid var(--focus-ring-color);
  outline-offset: var(--focus-ring-offset);
}
```

---

## 10. Full `:root` reference block

```css
:root {
  /* Radius & elevation */
  --radius-default: 0px;
  --radius-minimal: 2px;
  --radius-max: 3px;
  --border-width: 1px;
  --shadow-none: none;

  /* Surfaces */
  --color-ivory: #F4F0E8;
  --color-white: #FFFFFF;
  --color-stone: #E7E0D7;
  --color-beige: #D7CAB9;
  --color-charcoal: #1A1A1A;
  --color-soft-black: #0E0E0E;
  --color-bronze: #967452;
  --color-sage: #74867A;
  --color-rose: #A97C81;
  --color-soft-grey: #74706A;
  --color-border: #D9D2C9;

  /* Semantic */
  --color-success: #386B4B;
  --color-error: #A43B3B;
  --color-warning: #9A6A1E;
  --color-info: #365F7C;
  --color-pending: #7A6B46;
  --color-inactive: #777777;

  /* Typography */
  --font-display: "Cormorant Garamond", Georgia, "Times New Roman", serif;
  --font-body: "Manrope", "Segoe UI", Helvetica, Arial, sans-serif;

  /* Layout */
  --container-max: 90rem;
  --gutter: clamp(1rem, 3vw, 2rem);
  --section-space: clamp(3.5rem, 8vw, 6.5rem);
}
```

---

## 11. Tailwind mapping (guidance)

Map brand colours in `tailwind.config.js` to these hex values (`bronze`, `ivory`, `stone`, etc.). Prefer CSS variables in custom CSS for public editorial layouts; use Tailwind utilities in admin tables where faster. Never introduce shadow utilities (`shadow-sm`, etc.) in De Lux UI.
