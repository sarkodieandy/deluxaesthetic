# Admin route map

Base: `/admin` · Name prefix: `admin.` · Middleware: `web`, `auth`, `verified`, `active.account`, `admin.access`

| Route name | Method | Path | Status |
|------------|--------|------|--------|
| `admin.dashboard` | GET | `/admin` | Live — dashboard metrics |
| `admin.activity.index` | GET | `/admin/activity` | Live — audit log |
| `admin.audit.index` | GET | `/admin/audit` | Live — audit log |
| `admin.practitioners.*` | * | `/admin/practitioners` | Live — CRUD |
| `admin.appointments.index` | GET | `/admin/appointments` | Placeholder |
| `admin.clients.index` | GET | `/admin/clients` | Placeholder |
| … | GET | `/admin/{module}` | Placeholder — see `routes/admin.php` |

Sensitive actions (approve, refund, issue certificate, etc.) will use explicit POST/PATCH routes — never GET — as modules are implemented.

Register routes only in `routes/admin.php`.
