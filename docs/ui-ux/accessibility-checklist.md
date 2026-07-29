# Accessibility Checklist — De Lux Aesthetic Clinic

Target: **WCAG 2.2 Level AA** oriented practices across public, portals, and admin. Bilingual EN/FR must not break accessibility.

---

## 1. Foundations

- [ ] Valid semantic HTML5 landmarks: `header`, `nav`, `main`, `aside`, `footer`
- [ ] One logical `h1` per page; heading levels not skipped
- [ ] Skip link “Skip to content” as first focusable element
- [ ] `lang` on `<html>` updates with locale (`en` / `fr`)
- [ ] Page `<title>` unique and descriptive (include De Lux where appropriate)
- [ ] `dir` remains `ltr` for EN/FR

---

## 2. Keyboard & focus

- [ ] All interactive controls reachable via Tab
- [ ] Visible `:focus-visible` bronze ring (2px) — never `outline: none` without replacement
- [ ] Mobile menu, modal, drawer: focus trap + Escape closes + return focus to trigger
- [ ] No keyboard traps in booking, chat, or date pickers
- [ ] Custom selects/slots operable with arrows + Enter/Space
- [ ] Do not rely on hover alone for critical actions (Book, Pay, Enrol)

---

## 3. Forms

- [ ] Every input has a visible `<label>` (not placeholder-only)
- [ ] Required fields indicated in text, not colour alone
- [ ] Errors associated with `aria-describedby` / `aria-invalid`
- [ ] Error summary at top of long / multi-step forms with links to fields
- [ ] User input preserved after validation failure
- [ ] Autocomplete attributes for name, email, tel, address where relevant
- [ ] Consent checkboxes explicit for booking cancellation policy and marketing opt-in

---

## 4. Navigation

- [ ] `aria-current="page"` on active nav items
- [ ] Mobile menu `aria-expanded`, `aria-controls`
- [ ] Dropdowns (if any) follow disclosure / menubar patterns
- [ ] Breadcrumbs marked as `nav` with `aria-label`
- [ ] Admin/portal sidebars labelled (`aria-label="Client portal"` etc.)

---

## 5. Colour & contrast

- [ ] Charcoal on ivory/white body text passes contrast
- [ ] Bronze buttons with white text checked for AA (large text / UI)
- [ ] Status never colour-only — text or icon + text
- [ ] Focus indicator contrast sufficient against ivory and soft-black
- [ ] Charts have non-colour encodings (patterns/labels) where critical

---

## 6. Media & content

- [ ] Meaningful `alt` on images; empty `alt=""` for decorative
- [ ] CEO / practitioner portraits: descriptive alt including name
- [ ] Before/after: alt describes context; consent disclaimer visible
- [ ] Videos (YouTube/Vimeo): captions preferred; no autoplay audio
- [ ] Decorative Lucide icons: `aria-hidden="true"` when adjacent text exists
- [ ] Icon-only buttons: `aria-label`
- [ ] Link text descriptive (“View facial treatments” not “Click here”)

---

## 7. Components

- [ ] Accordions: `button` + `aria-expanded` + controlled panel id
- [ ] Modals: `role="dialog"`, `aria-modal="true"`, labelled by title
- [ ] Toasts/alerts: `role="status"` or `role="alert"` as appropriate
- [ ] Tabs (admin forms): arrow key support + `aria-selected`
- [ ] Tables: `<th>` with scope; caption or `aria-label` for complex admin tables
- [ ] Pagination: current page announced
- [ ] Time-slot grid: selected slot announced; unavailable slots disabled not just greyed

---

## 8. Motion

- [ ] `prefers-reduced-motion` disables GSAP/scroll animations
- [ ] No seizure-inducing flashes
- [ ] Auto-updating carousels pausable

---

## 9. AI assistant

- [ ] Chat region live updates via `aria-live="polite"`
- [ ] Clear escalation path to human (consultation / WhatsApp / contact)
- [ ] Disclaimer: no diagnosis or prescription
- [ ] Keyboard operable composer and message list

---

## 10. Documents & downloads

- [ ] Signed material downloads accessible after auth
- [ ] PDF receipts/certificates tagged when generated (where feasible)
- [ ] Certificate verification results announced to screen readers

---

## 11. Surface-specific

| Surface | Extra checks |
|---------|----------------|
| Booking | Step progress announced; errors per step |
| Checkout | Payment errors clear; no silent Fail |
| Student | Assignment deadlines readable; file upload status |
| Admin | Bulk actions confirm; destructive labelled |
| Auth | Password show toggle accessible; lockout messages clear |

---

## 12. Manual test ritual (each phase)

1. Keyboard-only pass on new pages  
2. VoiceOver / TalkBack spot check on booking & checkout  
3. Zoom 200% — layout usable  
4. Windows contrast / forced colours spot check if possible  
5. EN/FR toggle — no clipped focusable controls  
