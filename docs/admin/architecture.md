# Admin architecture

The administration platform lives inside the **same Laravel application** as the public website, client portal, student portal, practitioner portal, and trainer portal. It uses the same database, Eloquent models, domain services, queues, notifications, and payment integrations.

## Separation strategy

| Layer | Location |
|--------|-----------|
| Routes | `routes/admin.php` — prefix `/admin`, name `admin.*` |
| Controllers | `app/Http/Controllers/Admin/` |
| Form requests | `app/Http/Requests/Admin/` |
| Actions | `app/Actions/Admin/` (per module, expanded over phases) |
| Services | `app/Services/Admin/` |
| Policies | `app/Policies/Admin/` (expanded over phases) |
| Views | `resources/views/admin/` |
| CSS | `resources/css/admin/` — Vite entry `resources/css/admin/admin.css` |
| JS | `resources/js/admin/` — Vite entry `resources/js/admin/admin.js` |
| Config | `config/admin.php` — navigation + staff roles |
| Docs | `docs/admin/` |

Public website assets and bundles are **not** mixed with admin bundles.

## Middleware stack

All admin routes use:

`web` → `auth` → `verified` → `active.account` → `admin.access`

Module routes add `permission:*` or policies as needed.

## Data flow

1. Admin UI calls Admin controllers.
2. Controllers authorize via Spatie permissions / policies.
3. Controllers delegate to **shared** domain services (appointments, treatments, payments, etc.).
4. Services persist to the same tables the website reads.
5. Events + cache invalidation keep the public site and portals in sync (expanded per module).

## Dashboard

`App\Services\Admin\Dashboard\DashboardMetricsService` aggregates live metrics from appointments, clients, students, orders, products, and payments tables (with schema guards where migrations exist but models are not yet implemented).

## Navigation

`config/admin.php` defines sidebar groups. `App\Support\AdminNavigation` filters items by the authenticated user’s permissions and registered routes.

## Subdomain (optional)

Production may use `admin.example.com` pointing to the same `routes/admin.php` group. When subdomains are unavailable, use `/admin`.

See also: [integration-map.md](./integration-map.md), [route-map.md](./route-map.md), [implementation-checklist.md](./implementation-checklist.md).
