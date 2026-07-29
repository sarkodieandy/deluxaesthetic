# Admin permission matrix (summary)

Permissions are defined in `database/seeders/RolePermissionSeeder.php` and enforced via middleware / policies.

| Role | Dashboard | Clinic | Academy | Store | Finance | Content | System |
|------|-----------|--------|---------|-------|---------|---------|--------|
| Super Administrator | Full | Full | Full | Full | Full | Full | Full |
| Clinic Administrator | Yes | Broad | View/manage academy | View | Payments/reports | Yes | No roles/permissions |
| Receptionist | Yes | Appointments, clients | — | — | — | — | — |
| Practitioner / Therapist | Yes | Own appointments | — | — | — | — | — |
| Trainer | Yes | — | Courses, students | — | — | — | — |
| Finance Officer | Yes | View appointments | — | — | Full finance | — | — |
| Store Manager | Yes | — | — | Full store | — | — | — |
| Content Manager | Yes | — | — | — | — | Full CMS | — |
| Support Agent | Yes | View clients/appts | — | — | — | — | Notifications / AI |
| Client / Student | **Denied** | — | — | — | — | — | — |

Granular permission names follow the spec prefix pattern (`appointments.approve`, `certificates.issue`, `inventory.adjust`, etc.).

Navigation items in `config/admin.php` each declare a required permission.

See `docs/admin/architecture.md` for middleware stack.
