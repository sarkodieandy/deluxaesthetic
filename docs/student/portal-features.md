# Portal features

All portal metrics and lists are loaded from database records tied to the logged-in student's `student_profile_id` and active `enrolment`.

- Dashboard: enrolment status, attendance %, balance, next session, quick links.
- Materials/assignments gated by active enrolment status.
- Payments: view-only unless `ACADEMY_ONLINE_BALANCE_PAYMENT_ENABLED=true` (future online balance payments against existing enrolment only).
- Certificates: download only when status is `issued` and PDF exists.
