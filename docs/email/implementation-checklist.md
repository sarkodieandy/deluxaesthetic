# Implementation checklist (Google + email)

## Done in codebase

- [x] Laravel Socialite installed
- [x] `social_accounts`, `email_templates`, `email_logs` migrations
- [x] Google OAuth routes + Client/Student registration flow
- [x] Secure email match linking (password required)
- [x] Account link/unlink (authenticated)
- [x] `PortalRedirect` integration
- [x] Queued templated email job + auth email listeners
- [x] Admin email template + log modules (baseline)
- [x] EN email template seeds (`EmailTemplateSeeder`)

## Next phases

- [ ] Wire appointment / order / enrolment / payment events to template keys
- [ ] French `lang/fr/auth.php` + full `lang/*/emails.php`
- [ ] Responsive email layout components + preview/test-send in admin
- [ ] Scheduled reminders (appointments, instalments, assignments)
- [ ] Promotional unsubscribe + preference storage
- [ ] SPF/DKIM/DMARC production runbook verification
