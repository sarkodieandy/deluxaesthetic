# Testing plan

Automated coverage in `tests/Feature/AcademyEnrolmentEnquiryTest.php` and `tests/Feature/PhysicalEnrolmentWorkflowTest.php`:

- Course enquiry creates `course_enquiries` row, not `enrolments`.
- Admin can create student + physical enrolment.
- Unauthorised staff cannot activate enrolment.
- Student cannot access admin routes.
- Client cannot access student portal.
- Activated student sees assigned course on dashboard.

Manual QA: public course page notice, WhatsApp/call CTAs, admin activation email, portal navigation, suspended enrolment messaging.
