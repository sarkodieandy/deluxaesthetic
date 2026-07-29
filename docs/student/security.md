# Student portal security

- Role middleware (`student.role`) + active account check.
- Ownership checks on certificates, receipts, materials, assignments.
- Private files served via controller downloads (not public storage URLs for restricted files).
- Portal invitation uses hashed token + expiry; passwords never emailed in plain text.
- Sensitive admin actions audited via `audit_logs`.
- IDOR protection: students cannot access another student's enrolment or files.
