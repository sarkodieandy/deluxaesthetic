# Admin ↔ website integration map

| Admin module | Shared domain layer | Public / portal surface |
|--------------|---------------------|-------------------------|
| Treatments | `Treatment` model, publish workflow | `/treatments`, booking prices |
| Appointments | `AvailabilityService`, `CreateAppointmentAction` | Booking flow, client appointments |
| Practitioners | `PractitionerProfile`, schedules | Team page, booking practitioner pick |
| Courses / enrolments | Academy models (phase 4) | Academy catalogue, student portal |
| Products / inventory | Store tables | Shop, stock availability |
| Orders | Order + payment services | Client orders, tracking |
| Settings / CMS | `Setting`, homepage sections | Homepage, contact, footer content |
| Notifications | Template + queue services | Email/SMS/WhatsApp to clients |

Rule: **one business rule, one service** — admin and website call the same service classes.

Events (to expand): `TreatmentPublished`, `AppointmentApproved`, `OrderStatusChanged`, `CertificateIssued`, etc.
