# Student portal architecture

- **Public web**: course catalogue + enquiry forms only.
- **Admin**: physical registration, enrolment activation, attendance, materials, assessments, payments, certificates.
- **Student portal** (`routes/student.php`, `app/Http/Controllers/Student/*`, `resources/views/student/*`): read/submit within policy boundaries.
- **Shared domain**: Eloquent models (`Enrolment`, `Course`, `CourseMaterial`, `AttendanceRecord`, etc.) and services (`PhysicalEnrolmentService`, `StudentPortalService`).
- **Events**: `EnrolmentActivated`, `StudentAccountActivated` for notifications and audit trails.
