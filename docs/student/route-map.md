# Student route map

| Route name | Path | Purpose |
|---|---|---|
| student.dashboard | GET /student | Live dashboard |
| student.course.show | GET /student/course | Assigned course details |
| student.calendar.index | GET /student/calendar | Schedule sessions |
| student.materials.* | GET /student/materials | Published materials + signed downloads |
| student.attendance.index | GET /student/attendance | Read-only attendance |
| student.assignments.* | GET/POST /student/assignments | View/submit assignments |
| student.assessments.index | GET /student/assessments | Published results |
| student.payments.* | GET /student/payments | Balances, history, receipts |
| student.certificates.* | GET /student/certificates | Issued certificates |
| student.notifications.index | GET /student/notifications | In-app notifications |
| student.support.* | GET/POST /student/support | Support requests |
| student.profile.* | GET/PUT /student/profile | Approved profile fields |
| student.security.index | GET /student/security | Security hub |
| student.activate.* | GET/POST /student/activate/{token} | Invitation activation |

Middleware: `auth`, `verified`, `active.account`, `student.role`, `student.profile.complete`, `student.enrolment.active` (materials/assignments).
