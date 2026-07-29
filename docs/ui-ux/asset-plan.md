# Asset Plan — De Lux Aesthetic Clinic

## 1. Folder structure

```
public/assets/
├── web/
│   ├── images/
│   │   ├── hero/                 # Home & landing full-bleed
│   │   ├── treatments/           # Catalogue & detail
│   │   ├── practitioners/        # Team (non-CEO)
│   │   ├── team/                 # CEO Mac Tonto portraits (client-owned)
│   │   ├── academy/              # Training / classroom
│   │   ├── store/                # Product photography
│   │   ├── gallery/              # Clinic ambiance
│   │   ├── before-after/         # Consent-cleared comparisons
│   │   ├── blog/                 # Article covers
│   │   └── testimonials/         # Optional portraits
│   ├── icons/                    # Rare brand SVG marks
│   └── video/                    # Poster frames only (prefer YT/Vimeo links)
├── admin/
│   └── images/                   # Admin empty-state / report exports if any
├── portals/
│   └── images/                   # Portal empty states
└── shared/
    ├── fonts/                    # Optional self-hosted Cormorant / Manrope
    ├── logos/
    │   ├── delux-mark.svg
    │   └── delux-lockup.svg
    └── favicons/
```

Compiled Vite output remains in `public/build/` — do not mix source brand assets there.

Private uploads (assignment files, medical notes attachments) use storage disk + signed URLs — **not** `public/assets`.

---

## 2. CEO portraits (client-owned)

**Subject:** Mac Tonto — Chief Executive Officer & Founder  

| Asset | Source | Public path |
|-------|--------|-------------|
| Portrait A | Client FAB_0325 | `public/assets/web/images/team/ceo-mac-tonto-portrait-a.webp` (+ `.png` master if needed) |
| Portrait B | Client FAB_0390 | `public/assets/web/images/team/ceo-mac-tonto-portrait-b.webp` |

**Usage:** About, Team/Practitioners feature, Home story split.  
**Crop rules:** Rectangular in editorial layouts; circular **only** for avatar/profile contexts.  
**Licence:** Client ownership — not Unsplash. Never replace with lookalike stock.

Config keys: `config('clinic.ceo.portrait_a')`, `portrait_b`.

---

## 3. Approved external sources

| Need | Source | Rules |
|------|--------|-------|
| Clinic interiors, treatments, lifestyle | **Unsplash**, **Pexels**, Pixabay | Commercial-safe; download locally; **no hotlinking** |
| Product placeholders | Unsplash / Pexels | Until client packshots |
| Before/after demos | Licensed stock only | Label **demonstration content**; never imply real De Lux patient without consent |
| Icons | **Lucide** (MIT) | Preferred; Font Awesome / Bootstrap Icons only if Lucide lacks a metaphor |
| Fonts | **Google Fonts** — Cormorant Garamond, Manrope | OFL; prefer self-host in production |
| Maps | Google Maps or OpenStreetMap embed | Accra branch coordinates from settings |
| Video | YouTube / Vimeo links | Do not upload large video to cPanel hosting |
| Motion extras | Lottie (sparingly) | Not for primary brand moments; respect reduced motion |

**Forbidden:** Images copied from competing Ghana clinics; ThemeForest preview packs; random hotlinked CDNs; unlicensed medical imagery.

---

## 4. Formats & optimisation

- Deliver **WebP** with JPG/PNG fallback where needed  
- Hero: optimised ≤ ~250–400KB WebP where possible  
- Generate responsive widths (e.g. 480 / 768 / 1200 / 1800)  
- Lazy-load below-fold (`loading="lazy"`); hero `fetchpriority="high"`  
- Maintain `resources/data/asset-sources.json` manifest  
- Document licences in `docs/asset-licences.md`  
- Artisan `assets:fetch-demo` (when enabled) reads manifest only  

Manifest schema:

```json
{
  "filename": "clinic-treatment-room.webp",
  "source_url": "https://…",
  "creator": "…",
  "licence": "Unsplash License",
  "downloaded_at": "2026-07-27",
  "used_on": ["web.home", "web.about"],
  "attribution_required": false,
  "formats": ["webp", "jpg"]
}
```

---

## 5. Icon & illustration rules

- Stroke-consistent Lucide set at 16/20/24  
- Colour via currentColor (charcoal / bronze / semantic)  
- No emoji in UI  
- Empty states: typography + thin rules first; illustration optional and straight-line aligned  

---

## 6. Brand marks

- Wordmark text “De Lux” / “Aesthetic Clinic” acceptable in CSS (Cormorant) during build-out  
- SVG lockup in `shared/logos` when provided by client  
- Favicon: simple mark on ivory/bronze — no shadows  

---

## 7. Phase acquisition priorities

| Phase | Assets |
|-------|--------|
| 1–2 | Tokens fonts; hero; treatment placeholders; CEO portraits wired |
| 3 | Booking ambient imagery; practitioner photos |
| 4 | Academy / classroom |
| 5 | Product packshots |
| 6–7 | Admin empty states; final WebP pass; licence audit |
