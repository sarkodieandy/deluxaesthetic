# Admin implementation checklist

## Phase 1 — Foundation (in progress)

- [x] `docs/admin/` planning started
- [x] `config/admin.php` navigation + staff roles
- [x] `routes/admin.php` middleware stack (`active.account`, `admin.access`)
- [x] Admin layout + permission-filtered sidebar
- [x] Separate admin Vite bundle (existing entries confirmed)
- [x] `DashboardMetricsService` (database-backed metrics)
- [x] Expanded Spatie permissions in `RolePermissionSeeder`
- [x] Placeholder routes for all planned modules
- [x] Practitioners CRUD (existing)
- [x] Audit log listing (read-only)
- [ ] Admin design token split (tokens.css started)
- [ ] Feature tests for admin access denial (client/student)
- [ ] Shared domain services wired for each module

## Phase 2 — Users, roles, audit

- [ ] Users CRUD
- [ ] Roles / permissions UI
- [ ] Audit log detail + export
- [ ] 2FA enforcement for administrators

## Phase 3 — Clinic

- [ ] Appointments list + calendar + workflows
- [ ] Consultations
- [ ] Clients
- [ ] Treatments admin CRUD + publish
- [ ] Schedules (shared `AvailabilityService`)

## Phase 4 — Academy

- [ ] Courses, enrolments, attendance, assessments, certificates

## Phase 5 — Store & finance

- [ ] Products, inventory, orders, payments, refunds

## Phase 6 — CMS

- [ ] Homepage sections, pages, blog, gallery, media, translations

## Phase 7 — Communications & reports

- [ ] Notification templates, AI knowledge, reports + exports

## Phase 8 — Hardening

- [ ] Full Pest coverage, security review, performance, deployment docs

After each phase: migrate, seed permissions, test, `npm run build`, update `docs/progress.md`.
