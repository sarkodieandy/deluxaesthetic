# Asset acquisition plan

## Client-owned assets

| File | Source | Usage | Licence |
|------|--------|-------|---------|
| CEO portrait A | Client-supplied FAB_0325 | About, Team, homepage feature | Client ownership — Mac Tonto |
| CEO portrait B | Client-supplied FAB_0390 | About alternate / gallery | Client ownership — Mac Tonto |

Local paths after install:

- `public/assets/web/images/team/ceo-mac-tonto-portrait-a.png`
- `public/assets/web/images/team/ceo-mac-tonto-portrait-b.png`

Optimised WebP variants generated during Phase 1/2 asset pipeline.

## Stock sources (commercial-safe)

| Need | Source | Notes |
|------|--------|-------|
| Clinic interiors / treatments | Unsplash / Pexels | Download locally; never hotlink |
| Product placeholders | Unsplash / Pexels | Until client product photos |
| Before/after demos | Licensed stock | Label as **demonstration content** |
| Icons | Lucide | MIT |
| Fonts | Google Fonts — Cormorant Garamond, Manrope | OFL |

## Process

1. Maintain `resources/data/asset-sources.json` for every downloaded asset.
2. Document in `docs/asset-licences.md`.
3. Artisan command `assets:fetch-demo` reads manifest and downloads approved URLs.
4. Convert to WebP; generate responsive sizes; lazy-load off-screen images.
5. Large videos: YouTube/Vimeo links only — not uploaded to shared hosting.

## Register schema (`asset-sources.json`)

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
