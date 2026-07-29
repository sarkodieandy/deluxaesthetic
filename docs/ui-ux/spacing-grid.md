# Spacing & Grid — De Lux Aesthetic Clinic

## 1. Spacing scale

Use only these tokens — no one-off `13px` / `27px` margins.

| Token | px | rem (base 16) | Typical use |
|-------|----|----|-------------|
| `--space-1` | 4 | 0.25 | Hairline gaps, icon padding |
| `--space-2` | 8 | 0.5 | Compact stack, input inner |
| `--space-3` | 12 | 0.75 | Label → field |
| `--space-4` | 16 | 1 | Default component padding |
| `--space-5` | 24 | 1.5 | Card padding, grid gap |
| `--space-6` | 32 | 2 | Group spacing |
| `--space-7` | 48 | 3 | Small section gaps |
| `--space-8` | 64 | 4 | Medium section |
| `--space-9` | 80 | 5 | Large section (tablet+) |
| `--space-10` | 112 | 7 | Major public bands |
| `--space-11` | 144 | 9 | Rare hero/editorial breathing |

**Section vertical rhythm:** `var(--section-space)` = `clamp(3.5rem, 8vw, 6.5rem)` (~56–104px), aligning between `--space-8` and `--space-10`.

**Form stacks:** label `space-3` above control; helper/error `space-2` below; field groups `space-5`–`space-6`.

---

## 2. Containers

| Token | Width | Use |
|-------|-------|-----|
| `--container-max` | **90rem (~1440px)** | Public & admin main shells |
| Practical target | **1360–1440px** | Design mock max width |
| `--container-narrow` | 48rem | Forms, booking step column |
| `--container-reading` | 42rem | Long articles, policies |

```css
.container-site {
  width: min(100% - 2 * var(--gutter), var(--container-max));
  margin-inline: auto;
}
```

**Gutters:** `--gutter: clamp(1rem, 3vw, 2rem)` (16–32px). Never let content touch viewport edges on mobile.

Full-bleed heroes and ambiance bands break out of the container; text overlays still respect inner max measure.

---

## 3. Column grids

| Viewport | Columns | Gap | Notes |
|----------|---------|-----|-------|
| Desktop ≥1024px | **12** | 24px (`--space-5`) | Split 7/5, 8/4, 6/6, 3×4 treatment grids |
| Tablet 768–1023px | **8** | 16–24px | Collapse sidebars to top; 2-up cards |
| Mobile <768px | **4** | 16px | Single column stacks; 2-up only for small tiles |

### Common desktop patterns

| Pattern | Columns |
|---------|---------|
| Treatment catalogue | Filter 3 + results 9 (or toolbar + 12) |
| Treatment detail | Content 8 + sticky booking 4 |
| Home featured treatments | 7 lead + 5 stack, or 4-4-4 asymmetric |
| Academy dual promo | 6 / 6 |
| Contact + map | 5 / 7 |
| Admin | Sidebar fixed + fluid content |
| Portal | Sidebar fixed + content |

Align to baseline: section titles share left edge with body columns; thin rules span full content width.

---

## 4. Layout topology tools

- Split-screen image/text  
- Full-bleed media band  
- Asymmetric editorial grids  
- Horizontal metric row with vertical dividers  
- Sticky summary panels (booking, enrolment, checkout)  
- Filter + results  
- Multi-step with progress on top  

Avoid identical “heading + 3 equal cards” on every section.

---

## 5. Density by surface

| Surface | Padding comfort | Table cell padding |
|---------|-----------------|--------------------|
| Public | Generous (`space-5`–`space-8`) | N/A |
| Portals | Medium (`space-4`–`space-6`) | `space-3`–`space-4` |
| Admin | Compact (`space-3`–`space-5`) | `space-2`–`space-3` |

Mobile: reduce section space one step; keep tap targets ≥44px; do not “fix” with huge empty padding that forces endless scroll.

---

## 6. Breakout & overlap

Controlled overlap (e.g. story frames offset by `space-5`–`space-7`) is allowed. Keep overlaps rectangular; no circular crop frames except avatars. Overlap must not hide critical CTAs or text at any breakpoint.
