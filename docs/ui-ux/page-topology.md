# Page Topology — De Lux Aesthetic Clinic

General anatomy (adapt per page — do not clone identically):

1. Announcement bar (as needed)  
2. Main navigation  
3. Page introduction / contextual header  
4. Primary content  
5. Supporting information  
6. Related content  
7. Conversion action  
8. Footer  

---

## 1. Home — 17 sections

| # | Section | Purpose | Key content |
|---|---------|---------|-------------|
| 1 | Announcement bar | Timely signal | Promo treatment, new course, phone shortcut |
| 2 | Main header | Global nav | Logo De Lux, primary links, Enrol, Book |
| 3 | Hero | Brand composition | Full-bleed clinic image; brand; one headline; one support line; Book + Explore Treatments (+ Enrol secondary); thin vertical accent line; light credibility line — **no floating card** |
| 4 | Service index | Pathfinding | Facial · Skin · Body · Injectables · Wellness · Professional training — lines, numbers, Lucide, typography |
| 5 | Featured treatments | Decide & book | Editorial asymmetric grid (lead + supporting); image, category, name, price GHS, duration, summary, Book |
| 6 | About the clinic | Trust | Split image/text; brand story; standards; facility; link to About |
| 7 | Practitioners | Expertise | Portraits (Mac Tonto featured); name, title, speciality, short profile, availability, View / Book |
| 8 | Before and after | Proof | Consent disclaimer; category; slider; View gallery |
| 9 | Training academy | Academy brand | Contrasting band; intro; featured course hooks; trainers; Enrol |
| 10 | Featured courses | Enrol convert | Name, level, date, duration, mode, price, spaces, trainer, Enrol |
| 11 | Product store | Shop convert | Clean grid; image, name, category, price, rating, stock, Add / View |
| 12 | Statistics | Credibility | Large metrics + vertical dividers (clients, treatments, students, practitioners, years) |
| 13 | Testimonials | Social proof | Restrained type/imagery — not oversized quote cards |
| 14 | FAQ | Objection handling | Straight accordion, thin separators |
| 15 | Blog | Education / SEO | Editorial article teasers |
| 16 | Contact and map | Visit / call | Address Accra, phone, email, hours, WhatsApp, map, directions |
| 17 | Footer | Persistence | Brand, Treatments, Academy, Store, Policies, Contact, social, newsletter, language, © |

Hero excludes stats, schedules, and address blocks (those live in later sections).

---

## 2. Treatment catalogue `/treatments`

1. Page heading + intro  
2. Category navigation  
3. Filter sidebar or toolbar (category, price, duration, practitioner, branch, availability)  
4. Results count + sorting  
5. Treatment grid (square/portrait imagery)  
6. Pagination  
7. Booking CTA band  

Item: large image · category · name · benefit · duration · price · practitioner count · View · Book.

---

## 3. Treatment detail `/treatments/{slug}`

1. Breadcrumbs  
2. Treatment hero image  
3. Title  
4. Price + duration  
5. Book Appointment  
6. Overview  
7. Benefits  
8. Suitable candidates  
9. Contraindications  
10. Preparation  
11. Aftercare  
12. Expected recovery  
13. Recommended sessions  
14. Practitioners  
15. Before and after  
16. Reviews  
17. FAQs  
18. Related treatments  
19. Final booking action  

Desktop: main column + **sticky booking summary**. Mobile: sticky bottom Book bar; collapsible sections.

---

## 4. Booking multi-step `/book`

Progress indicator + step title + Back/Next + sticky summary (desktop) / collapsible + sticky footer (mobile).

| Step | Content |
|------|---------|
| 1 | Treatment |
| 2 | Branch |
| 3 | Practitioner |
| 4 | Date & time (slot grid) |
| 5 | Client details |
| 6 | Consultation questions / intake |
| 7 | Payment (deposit % from settings, e.g. 30%) |
| 8 | Confirmation |

Show unavailable slots clearly; inline validation; policy agreement; Paystack trust copy. Do not dump all fields on one page.

---

## 5. Academy `/academy` & courses

**Landing:** Hero · philosophy · categories · featured courses · trainers · certification · student testimonials · training gallery · upcoming calendar · enrolment process · FAQs · Enrol CTA.

**Catalogue `/courses`:** Filters (category, level, mode, date, price, availability) · search · sort · list/grid.

**Detail `/courses/{slug}`:** Hero · title · price · deposit · duration · mode · dates · Enrol · overview · outcomes · modules · entry requirements · trainer · materials · assessment · certification · payment plan · FAQs · related · final Enrol. Sticky enrolment summary on desktop.

---

## 6. Store `/store`

1. Store hero  
2. Category navigation  
3. Featured products  
4. Filters · search · sort  
5. Product grid  
6. Pagination  
7. Store info  
8. Delivery / pickup info  

**Product detail:** gallery · title · price · variants · qty · stock · usage · description · ingredients · delivery · pickup · reviews · related. Avoid marketplace clutter.

---

## 7. Client dashboard `/client`

Desktop: left nav · top account header · main · optional contextual actions.  
Mobile: compact header · bottom/drawer nav · quick actions.

**Prioritise:** Next appointment · status · outstanding payment · recent order · loyalty points · notifications · quick actions (Book, Reschedule, Consultation, Shop, Receipts, Refer).

No admin-style chart overload.

---

## 8. Student dashboard `/student`

**Prioritise:** Current course · next class · attendance · outstanding balance · assignments · new materials · assessment results · certificate progress.

Sections: course progress · upcoming classes · payment status · attendance · assignments · materials · announcements · certificates · notifications. Educational tone — not admin chrome.

---

## 9. Admin dashboard `/admin`

Layout: fixed sidebar · compact header · breadcrumbs · title + actions · toolbar · main · drawer/modal.

**Metrics (hierarchical, not identical boxes):** Today’s appointments · pending · monthly revenue (GHS) · active students · upcoming classes · outstanding payments · new orders · low stock · failed notifications · recent activity.

Combine: wide primary metric · supporting metrics · table · timeline · chart · status summaries. No shadows on panels — thin borders only.

---

## 10. Other key pages (brief)

| Page | Topology notes |
|------|----------------|
| About | CEO Mac Tonto story split; portraits A/B; standards; CTA Book |
| Practitioners index/detail | Editorial portraits; specialities; book |
| Consultation | Short intake form; expectations; success next steps |
| Gallery / Before–after | Full-width gallery; filters; consent |
| Contact | Split form + details + map |
| Auth | Guest layout; rectangular fields; calm ivory |
| Cart / Checkout | Line items · summary · Paystack · sticky pay on mobile |
| Certificate verify | Number entry · result valid/invalid/revoked |
