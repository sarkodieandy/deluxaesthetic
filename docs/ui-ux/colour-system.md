# Colour System — De Lux Aesthetic Clinic

## 1. Palette

### Foundations

| Name | Hex | Role |
|------|-----|------|
| Warm ivory | `#F4F0E8` | Default public background |
| White | `#FFFFFF` | Elevated panels, inputs, admin content |
| Soft stone | `#E7E0D7` | Alternate section band, zebra soft |
| Light beige | `#D7CAB9` | Soft block fills, progress tracks |
| Deep charcoal | `#1A1A1A` | Primary text, icons |
| Soft black | `#0E0E0E` | Footer, admin sidebar |
| Border | `#D9D2C9` | All structural lines |

### Accents

| Name | Hex | Dominance |
|------|-----|-----------|
| **Muted bronze** | `#967452` | **Primary** — CTAs, links hover, focus, active nav |
| Medical sage | `#74867A` | Secondary — clinical/academy cues, success-adjacent charts |
| Dusty rose | `#A97C81` | Tertiary — rare soft emphasis (wellness, testimonials) |
| Soft grey | `#74706A` | Neutral secondary text |

### Semantic

| Name | Hex | Meaning |
|------|-----|---------|
| Success | `#386B4B` | Confirmed, paid, completed, in stock |
| Error | `#A43B3B` | Failed payment, validation, destructive |
| Warning | `#9A6A1E` | Cancellation risk, low stock, deadline |
| Info | `#365F7C` | System information |
| Pending | `#7A6B46` | Awaiting payment, pending approval |
| Inactive | `#777777` | Disabled, archived, unread mute |

---

## 2. Accent dominance

Bronze is the **only** dominant brand accent. Rough usage budget on a marketing page:

| Colour | Approx. share of accented UI |
|--------|------------------------------|
| Bronze | ~70% of accent moments |
| Sage | ~20% (academy / clinical callouts) |
| Rose | ~10% or less |

Never give sage, rose, and bronze equal weight in one viewport. Do not invent purple, teal, or electric gold accents.

---

## 3. Usage hierarchy

| Element | Colour |
|---------|--------|
| Page background (public) | Ivory; optional subtle vertical wash ivory→stone |
| Section alternate | Stone or white |
| Primary button | Bronze fill, white text; hover → slightly deeper bronze via filter/brightness or `#7F5F42` |
| Secondary button | Transparent + charcoal/bronze border |
| Ghost button | Text + underline/border, no fill |
| Body text | Charcoal |
| Muted text | Soft grey |
| Links in body | Charcoal with bronze underline on hover |
| Focus ring | Bronze 2px |
| Borders | Border token |
| Admin sidebar | Soft black; active item bronze rule |
| Success toast | Success on white/stone, border success |
| Error banner | Error text + light tinted bg from error at ~8% — **still no shadow** |

---

## 4. Status colour mapping (domain)

| Domain status | Colour token | Label example |
|---------------|--------------|---------------|
| Appointment confirmed | Success | Confirmed |
| Appointment pending | Pending | Pending payment |
| Appointment cancelled | Error / Inactive | Cancelled |
| Appointment completed | Sage or Success | Completed |
| Enrolment active | Success | Active |
| Instalment overdue | Error | Overdue |
| Instalment due soon | Warning | Due soon |
| Order paid | Success | Paid |
| Order processing | Info | Processing |
| Order refunded | Soft grey / Info | Refunded |
| Stock low | Warning | Low stock |
| Out of stock | Inactive | Out of stock |
| Certificate revoked | Error | Revoked |
| Certificate valid | Success | Valid |

Always show text label beside colour chip/dot.

---

## 5. Charts (admin)

Prefer bronze, sage, charcoal, beige, soft grey series. Success/error only for explicitly positive/negative series. No rainbow palettes. Chart.js defaults must be overridden.

---

## 6. Accessibility

- Charcoal on ivory and white meets body-text contrast targets.
- White on bronze: verify large text/buttons; if borderline, darken bronze on hover/active.
- Do not use beige text on ivory.
- Pending and warning can be similar — differentiate with icons + labels.

---

## 7. Do-nots

1. Do not use colour as the only status indicator.  
2. Do not apply bronze large fills behind long reading text.  
3. Do not tint every card differently.  
4. Do not use pure black `#000` or pure error reds outside the semantic token.  
5. Do not introduce dark-mode public marketing themes without a separate approved palette.  
6. Do not hot-swap to purple/indigo “premium” clichés.  
7. Do not use dusty rose for errors or destructive actions.  
