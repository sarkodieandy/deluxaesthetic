# Current authentication analysis

## Stack

- Laravel 13.x with Breeze-style auth (`routes/auth.php`)
- Single `web` guard, `users` table, Spatie roles
- `PortalRedirect` for role-based post-login routing
- Public roles: **Client** (`/register`), **Student** (`/academy` student portal)
- Staff roles assigned only in admin; `EnsureAdminAccess` blocks Client/Student from `/admin`

## User model

- Fields: `name`, `email`, `password`, `phone`, `locale`, `is_active`, `last_login_at`, 2FA columns
- Implements `MustVerifyEmail`
- Profiles: `ClientProfile`, `StudentProfile`, practitioner/trainer profiles

## Gaps addressed by this integration

- No OAuth / Socialite
- `password` is required (non-null) — must be nullable for Google-only users
- No `accepted_terms_at` / `accepted_privacy_at` on users
- Email: `MAIL_MAILER=log`, `QUEUE_CONNECTION=database` (ready for queued mail)
- `notification_delivery_logs` exists but is minimal; full `email_logs` + templates added
- Student invitation uses `StudentPortalInvitationNotification` directly

## Existing services to reuse

- `PortalRedirect`, `StudentPortalRegistrationService`, `PhysicalEnrolmentService::allocateStudentNumber()`
- Role seeder: `RolePermissionSeeder`

## Security baseline

- Session regeneration on login
- Rate-limited login (`LoginRequest`)
- OAuth state handled by Socialite
- No automatic Google link by email match alone
