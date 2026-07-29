# UX Flows — De Lux Aesthetic Clinic

Twenty required flows. Each list is the **happy path** with critical branches noted. Minimise steps; reuse auth session when already logged in. Times in **Africa/Accra**; money in **GHS**.

---

## 1. Visitor → appointment booking

1. Land on Home or Treatments; choose treatment (or open `/book` directly).  
2. Enter `/book` step 1 — confirm/select treatment.  
3. Select branch (Accra location).  
4. Select practitioner (or “first available”).  
5. Pick date → load slots (`/book/slots` / availability API) → select time.  
6. If guest: register or log in as Client (return to booking with state preserved).  
7. Confirm client details + consultation/intake questions.  
8. Accept cancellation/reschedule policy (hours from settings).  
9. Pay deposit (default ~30%) or full amount via Paystack.  
10. Confirmation page + email/SMS/WhatsApp receipt → appears in `/client/appointments`.

**Branches:** Slot taken → refresh slots; payment fail → retry; unverified email → verify gate if middleware requires.

---

## 2. Visitor → consultation request

1. Navigate to `/consultation` (header, treatment detail, or AI escalate).  
2. Read expectations (not emergency care; response window).  
3. Complete form: name, contact, concern, preferred practitioner (optional), photos upload optional.  
4. Submit → success state with reference number.  
5. Admin/Support/Practitioner triages in `/admin/consultations` or `/practitioner/consultations`.  
6. Client sees status under `/client/consultations`.

---

## 3. Visitor → course enrolment

1. Discover Academy `/academy` or `/courses`.  
2. Open `/courses/{slug}` — review modules, dates, price, instalment plan.  
3. Click Enrol → `/enrol` with course context.  
4. Auth as Student (register/login); create student profile if needed.  
5. Select schedule/cohort with remaining seats.  
6. Choose payment: full or instalment plan.  
7. Pay deposit/first instalment via Paystack.  
8. Enrolment confirmed → `/student/courses` materials unlock per policy.

**Branches:** Course full → waitlist/message; prerequisites unmet → block with explanation.

---

## 4. Visitor → product checkout

1. Browse `/store` → product `/store/{slug}`.  
2. Select variant/qty → Add to cart (`/cart`).  
3. Optional wishlist save.  
4. Checkout `/checkout` — shipping vs pickup Accra branch.  
5. Auth if required; apply coupon; review stock.  
6. Pay via Paystack.  
7. Success → order confirmation → `/client/orders/{order}` + tracking.

**Branches:** Stock depleted on submit → adjust qty; payment cancelled → return to cart.

---

## 5. Client registration and login

1. `/register` — name, email, phone, password; role intent Client (or post-booking).  
2. Accept terms.  
3. Verify email link.  
4. `/login` → `/dashboard` → `/client`.  
5. Complete profile (DOB, preferences, emergency contact) if prompted.

**Branches:** Forgot password → `/forgot-password` → reset; lockout after failed attempts → support message.

---

## 6. Student registration and login

1. Register from enrolment flow or `/register` with student intent.  
2. Verify email.  
3. Login → `/student`.  
4. If no enrolment yet → CTA to `/courses`.  
5. Security: optional 2FA later under `/student/security`.

---

## 7. Appointment rescheduling

1. Client opens `/client/appointments/{id}`.  
2. Choose Reschedule (enabled only if within `reschedule_hours`, e.g. 12h).  
3. Select new date/time from available slots for same treatment/practitioner (or allowed change).  
4. Confirm — no double-booking (transaction/lock).  
5. Notifications to client + practitioner; status history logged.  
6. Success → updated appointment detail.

**Branches:** Outside window → message + contact reception; deposit difference if price rules change.

---

## 8. Appointment cancellation

1. Client opens appointment → Cancel.  
2. Show policy (e.g. free cancel ≥24h).  
3. Confirm reason (optional).  
4. Submit → status Cancelled; refund eligibility calculated.  
5. Finance processes refund if entitled (`/admin/refunds`).  
6. Confirmation toast + notification.

**Branches:** Late cancel → warn fee/no refund; already completed → cancel disabled.

---

## 9. Course payment instalment

1. Student `/student/payments` sees plan schedule.  
2. Select due instalment → Pay.  
3. Paystack checkout.  
4. Webhook/verify → instalment marked paid; receipt available.  
5. If overdue → warning state + reminder notifications; access policy applied (materials lock if configured).

---

## 10. Certificate download

1. Student completes assessments/attendance rules.  
2. Admin/trainer issues certificate (`/admin/certificates`).  
3. Student sees card on `/student/certificates`.  
4. Download PDF (signed URL).  
5. Optional share link to public verify page.

---

## 11. Certificate verification

1. Visitor opens `/verify/certificate/{number}` or enters number on verify form.  
2. System returns Valid / Invalid / Revoked with graduate name, course, date (privacy-minimised).  
3. No login required.  
4. Error state for malformed numbers.

---

## 12. Product return request

1. Client opens `/client/orders/{order}`.  
2. Request return (within policy window; eligible items only).  
3. Select items, reason, preferred resolution (refund/replace).  
4. Submit → Store Manager reviews `/admin/orders`.  
5. Approve → refund via Paystack/refunds module or replacement shipment.  
6. Client notified; order timeline updated.

---

## 13. Referral sharing

1. Client opens `/client/referrals` (or loyalty).  
2. Copy unique referral code/link.  
3. Share via WhatsApp/social.  
4. Friend registers/books using code.  
5. System attributes referral; rewards both parties per `/admin/loyalty` rules.  
6. Client sees reward status pending → granted.

---

## 14. Admin appointment management

1. Reception/Admin opens `/admin/appointments`.  
2. Filter by date, branch, practitioner, status.  
3. Create walk-in booking or open existing.  
4. Update status (confirmed, checked-in, completed, no-show, cancelled).  
5. Take/record payment; add notes (permissioned).  
6. Notify client on material changes.  
7. Audit log entry written.

---

## 15. Admin course creation

1. Content/Clinic Admin → `/admin/courses` → Create.  
2. Sections: basic info · media · modules · pricing · schedules · SEO · EN/FR translations · status.  
3. Save draft → preview → publish.  
4. Attach trainers; set capacity.  
5. Course appears on `/courses` when published.

---

## 16. Admin product & inventory management

1. Store Manager → `/admin/products` → Create/Edit (variants, images, pricing).  
2. Publish to `/store`.  
3. `/admin/inventory` — receive stock, adjustments, low-stock alerts.  
4. Orders `/admin/orders` — fulfil, pack, mark delivery/pickup.  
5. Reviews moderation `/admin/reviews`.

---

## 17. Admin payment reconciliation

1. Finance → `/admin/payments` filter by date/gateway status.  
2. Match Paystack webhook/transactions to appointments, enrolments, orders.  
3. Flag mismatches; retry verify.  
4. Process `/admin/refunds` with reason codes.  
5. Export report `/admin/reports/revenue`.  
6. Audit trail retained.

---

## 18. Trainer attendance entry

1. Trainer opens `/trainer/attendance/{schedule}` for today’s class.  
2. Roster loads enrolled students.  
3. Mark Present / Late / Absent / Excused.  
4. Save (inline loading per row or batch).  
5. Students see update under `/student/attendance`.  
6. Chronic absence may alert admin (notification rule).

---

## 19. Practitioner schedule management

1. Practitioner opens `/practitioner/availability` and `/practitioner/calendar`.  
2. Set working hours / block leave dates.  
3. View daily schedule `/practitioner/schedule`.  
4. Open appointment → add treatment notes; complete visit.  
5. Triage assigned consultations.  
6. Availability feeds public booking slot engine (no double book).

---

## 20. Client support escalation from AI chat

1. Visitor/client opens AI chat widget; asks question.  
2. AI answers from knowledge base with safety boundaries (no diagnosis/prescription).  
3. If medical advice, abuse, payment dispute, or “talk to human”: show **Escalate** CTA.  
4. Escalation creates support conversation / consultation handoff (`/admin/ai/conversations` + notifications to Support Agent).  
5. User receives confirmation + optional WhatsApp/contact fallback.  
6. Agent responds; AI session marked escalated; transcript retained for audit.

---

## Cross-flow UX rules

- Preserve form state across auth redirects.  
- Always show payment amount, currency GHS, and what the charge covers.  
- Every terminal state offers a next action.  
- Prefer optimistic UI only where rollback is safe; payments stay pessimistic until verified.  
- Log status transitions for appointments, enrolments, orders, certificates.  
