# Typography — De Lux Aesthetic Clinic

## 1. Font pairing

| Role | Family | Weights | Source |
|------|--------|---------|--------|
| Display / headings | **Cormorant Garamond** | 500, 600 (optional 400 italic for pull quotes) | Google Fonts (OFL) |
| Body / UI | **Manrope** | 400, 500, 600 | Google Fonts (OFL) |

Load via `preconnect` + Google Fonts CSS, or self-host under `public/assets/shared/fonts/` for production privacy/performance.

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
```

Do **not** use Inter, Roboto, Arial, or system-ui as the designed brand face (system stack is fallback only).

---

## 2. Type scale

| Token | Size | Font | Weight | Line-height | Tracking | Usage |
|-------|------|------|--------|-------------|----------|-------|
| Hero display | `clamp(2.75rem, 5vw, 4.5rem)` | Display | 500–600 | 1.15 | −0.01em optional | Home hero brand/headline |
| Page title | `clamp(2rem, 3.5vw, 3rem)` | Display | 600 | 1.2 | normal | Interior page H1 |
| Section heading | `clamp(1.75rem, 2.5vw, 2.25rem)` | Display | 500–600 | 1.25 | normal | Section H2 |
| Subsection | `clamp(1.25rem, 1.5vw, 1.5rem)` | Display | 500 | 1.3 | normal | H3 clusters |
| Card title | `1.25rem` | Display or Manrope 600 | 500–600 | 1.35 | normal | Treatment/product titles |
| Body large | `1.125rem` | Manrope | 400 | 1.7 | normal | Lead paragraphs |
| Body regular | `1rem` | Manrope | 400 | 1.6 | normal | Default copy |
| Body small | `0.875rem` | Manrope | 400–500 | 1.5 | normal | Meta, table cells |
| Interface label | `0.75rem` | Manrope | 500–600 | 1.4 | `0.14em` | Uppercase micro-labels |
| Navigation | `0.8125rem` | Manrope | 500 | 1.2 | `0.06em` | Primary nav |
| Button | `0.8125rem` | Manrope | 600 | 1 | `0.08em` | Buttons (often uppercase) |
| Caption | `0.6875rem` | Manrope | 400 | 1.4 | normal | Fine print, legal under images |
| Table heading | `0.75rem` | Manrope | 600 | 1.2 | `0.08em` | Admin/portal tables |
| Dashboard metric | `clamp(1.75rem, 2vw, 2.5rem)` | Display | 500 | 1.1 | normal | KPI numbers |
| Form helper | `0.8125rem` | Manrope | 400 | 1.45 | normal | Help & errors |

---

## 3. Hierarchy rules

1. **One display voice** — Cormorant for brand moments and headings; Manrope for everything interactive.
2. **Uppercase only** for micro-labels, nav (optional), buttons, table headers — never long body sentences.
3. **Measure:** reading text max ~65–75 characters (`--container-reading` / `42rem`).
4. **Contrast:** charcoal `#1A1A1A` on ivory/white; white text on soft-black or image overlays with sufficient scrim.
5. **No text-shadow.**
6. Avoid weights thinner than 400 for UI; avoid 300 entirely.
7. Mobile minimum body: **16px** (`1rem`) to reduce iOS zoom; captions not below ~11px.
8. Do not place long paragraphs over busy photography — use solid ivory panels or darkened scrims.
9. French strings: allow wrapping; do not truncate mid-word in buttons — prefer slightly wider buttons.

---

## 4. Semantic HTML mapping

| Element | Class / token |
|---------|----------------|
| Brand wordmark | `.site-logo__mark` → display, large |
| `h1` page | `.text-page-title` |
| `h2` section | `.text-section` / `--text-section-title` |
| `h3` | subsection / card title |
| Eyebrow | `.text-label` uppercase |
| Body | default Manrope |
| Metric | `.metric-value` |

Maintain logical heading order per page — do not skip levels for style.

---

## 5. Special cases

| Context | Treatment |
|---------|-----------|
| Hero brand “De Lux” | Cormorant, larger than headline or equal emphasis; headline must not overpower brand |
| CEO name (Mac Tonto) | Section title Cormorant; title/role in bronze Manrope |
| Price (GHS) | Manrope medium/semibold; consistent `GHS 0,000.00` formatting |
| Certificate numbers | Monospace optional for codes only — not for brand UI |
| Admin tables | Manrope only; Cormorant reserved for page titles & metrics |

---

## 6. Do not

- Mix a third display font (Playfair, DM Serif, etc.) once Cormorant is chosen  
- Use italic body for long medical contraindications (reserve italic for short quotes)  
- Center-align long paragraphs  
- Animate letter-spacing wildly on scroll  
