# Physical enrolment workflow

1. Visitor clicks **Academy** on the website (`/academy`) and creates a **student portal account** (name, email, phone, password).
2. Student completes their profile in `/student/profile` and can sign in anytime at `/login`.
3. Optional: visitor submits a **course enquiry** (`/enrol`) or browses `/courses`.
4. Staff review enquiries in **Admin → Academy → Course enquiries**.
5. Applicant visits the academy; staff verify identity and documents in person.
6. Staff may also create accounts via **Physical enrolment → Create student** if needed.
7. Staff record **physical enrolment** (course, schedule, fee, payments, policies).
8. Authorised staff **activate** the enrolment (`enrolments.activate`) so materials and assignments unlock.
9. Legacy flow: activation email (`/student/activate/{token}`) remains for accounts created only by staff without a chosen password.

Students cannot self-enrol on a course online; staff assign the course after physical registration.
