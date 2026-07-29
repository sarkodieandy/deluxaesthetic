# Development milestones & risks

## Milestones

| Phase | Milestone | Exit criteria |
|-------|-----------|---------------|
| 0 | Planning | Docs in `/docs`; assumptions logged |
| 1 | Foundation | Auth, RBAC, layouts, tokens, routes boot |
| 2 | Public site | Premium pages EN/FR; CMS-ready sections |
| 3 | Clinic | Real booking + client portal |
| 4 | Academy | Enrolment + student portal + certificates |
| 5 | Store | Cart → checkout → inventory |
| 6 | Integrations | Paystack + messaging + AI (mock-capable) |
| 7 | Admin | Full CRUD, reports, settings, audit |
| 8 | Quality | Tests green, deploy docs, acceptance met |

## Risk register

| ID | Risk | Likelihood | Impact | Mitigation |
|----|------|------------|--------|------------|
| R1 | Scope exceeds single delivery window | High | High | Phased runnable app; progress.md |
| R2 | Missing Paystack/WhatsApp/AI credentials | High | Medium | Interfaces + mock mode + docs |
| R3 | Double booking under concurrency | Medium | High | Transactions + row locks |
| R4 | Shared hosting queue gaps | Medium | Medium | DB queue + scheduled drain |
| R5 | AI medical advice liability | Medium | High | Hard safety rules; escalate to human |
| R6 | Asset copyright issues | Low | High | Manifest + licences; client CEO photos |
| R7 | Performance on shared hosting | Medium | Medium | Eager load, cache, split assets |
| R8 | Multilingual content drift | Medium | Low | Translation tables + review checklist |
