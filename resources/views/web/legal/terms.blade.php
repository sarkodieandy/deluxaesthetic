@extends('web.layouts.app')

@section('title', 'Terms of Service — '.config('clinic.name'))
@section('meta_description', 'Terms governing clinical bookings, Academy applications and student accounts, product orders, payments, refunds and use of the '.config('clinic.name').' website.')
@section('canonical', route('web.terms'))

@section('content')
<section class="legal-hero legal-hero--terms">
    <div class="container-site legal-hero__grid">
        <div>
            <p class="text-label">Website &amp; service terms</p>
            <h1>Terms of Service</h1>
        </div>
        <div class="legal-hero__summary">
            <p>These terms explain the rules for using our website and engaging our clinic, Academy and product services.</p>
            <p><strong>Effective date:</strong> 14 August 2026</p>
        </div>
    </div>
</section>

<section class="legal-page section">
    <div class="container-site legal-page__layout">
        <aside class="legal-page__aside" aria-label="Terms of service contents">
            <p class="text-label">On this page</p>
            <ol>
                <li><a href="#terms-acceptance">Acceptance</a></li>
                <li><a href="#terms-clinical">Clinical services</a></li>
                <li><a href="#terms-academy">Academy</a></li>
                <li><a href="#terms-products">Products and delivery</a></li>
                <li><a href="#terms-payments">Payments and refunds</a></li>
                <li><a href="#terms-accounts">Accounts and conduct</a></li>
                <li><a href="#terms-liability">Liability</a></li>
                <li><a href="#terms-contact">Contact</a></li>
            </ol>
        </aside>

        <article class="legal-page__content">
            <section id="terms-acceptance">
                <p class="legal-page__number">01</p>
                <h2>Acceptance and business details</h2>
                <p>These Terms of Service (“Terms”) form an agreement between you and {{ config('clinic.legal_name') }}, trading as {{ config('clinic.name') }} (“De Luxe”, “we”, “us” or “our”). By using this website, submitting a booking or Academy application, creating or using an approved student account, placing an order, or paying us, you agree to these Terms and our <a href="{{ route('web.privacy') }}">Privacy Policy</a>.</p>
                <p>If you do not agree, do not submit information, place an order or use an account. You must be at least 18 and legally capable of entering a contract, unless a parent or lawful guardian has made an arrangement that we have expressly accepted.</p>
                <p>Our contact address is {{ config('clinic.address') }}. Website descriptions are invitations to enquire or place an order; acceptance occurs when we expressly confirm the relevant booking, enrolment or order.</p>
            </section>

            <section id="terms-clinical">
                <p class="legal-page__number">02</p>
                <h2>Clinical consultations and procedures</h2>
                <ul>
                    <li>Website information is general information and is not a medical diagnosis, prescription or substitute for an in-person professional assessment.</li>
                    <li>A requested appointment is not confirmed until we issue confirmation. Emergencies must be directed to an appropriate emergency service, not this website or WhatsApp.</li>
                    <li>Procedure suitability, expected outcome, risks, alternatives and aftercare are assessed individually. We may decline, postpone or modify a procedure where safety, clinical judgement or legal requirements make that appropriate.</li>
                    <li>You must provide accurate and complete information about health, medication, allergies, pregnancy, prior procedures and other matters relevant to safe care, and follow preparation and aftercare instructions.</li>
                    <li>Results vary between individuals. Images, testimonials and examples do not guarantee an identical outcome.</li>
                </ul>
                <h3>Booking changes</h3>
                <p>Unless different terms are clearly shown during booking or agreed in writing, please request cancellation at least {{ config('clinic.booking.cancellation_hours') }} hours before the appointment and rescheduling at least {{ config('clinic.booking.reschedule_hours') }} hours before it. Late cancellation, non-attendance, late arrival or inability to proceed because material information was withheld may result in loss of a disclosed deposit or a reasonable charge, subject to applicable law and our written confirmation.</p>
            </section>

            <section id="terms-academy">
                <p class="legal-page__number">03</p>
                <h2>Academy applications and physical training</h2>
                <ul>
                    <li>Submitting an application expresses interest; it does not guarantee admission or activate a student account.</li>
                    <li>Admission is subject to review, contact by our team, any stated entry requirements, identity or qualification checks, availability, payment arrangements and written approval.</li>
                    <li>Course dates, trainers, venue, curriculum order and reasonable delivery details may change. We will communicate material changes to enrolled students.</li>
                    <li>Students must comply with safety, hygiene, professional conduct, attendance, assessment, confidentiality and model/patient-consent requirements.</li>
                    <li>Certificates are issued only when the applicable attendance, practical, assessment, conduct and payment requirements are satisfied. A certificate does not replace any licence, registration or insurance legally required to practise.</li>
                    <li>Course materials are licensed to the specific student for personal study. They may not be copied, resold, uploaded, shared or used to train others without written permission.</li>
                </ul>
                <p>Course-specific fee, deposit, cancellation, transfer and refund arrangements disclosed in the offer, invoice or enrolment confirmation form part of these Terms.</p>
            </section>

            <section id="terms-products">
                <p class="legal-page__number">04</p>
                <h2>Products, availability and delivery</h2>
                <ul>
                    <li>We take reasonable care with product descriptions and images, but packaging, colour and presentation may vary. You must review ingredients, directions, warnings and suitability before use.</li>
                    <li>Prices and availability may change before order acceptance. Obvious pricing or description errors may be corrected and the affected order cancelled with a refund of amounts received.</li>
                    <li>Products remain subject to stock confirmation. Adding an item to a cart or starting a WhatsApp conversation does not reserve stock.</li>
                    <li>Delivery estimates are not guarantees. Risk passes as provided by applicable law and the delivery arrangement confirmed for the order.</li>
                    <li>You must provide an accurate delivery address and inspect the order promptly. Notify us without unreasonable delay of a wrong, damaged, defective or missing item and preserve the product and packaging for review.</li>
                </ul>
                <h3>Returns</h3>
                <p>For health, hygiene and safety reasons, opened, used, unsealed or personalised cosmetics, skincare, injectable supplies and similar products cannot ordinarily be returned merely because you changed your mind. This does not limit rights for products that are defective, misdescribed, unsafe or incorrectly supplied. Contact us within seven calendar days of delivery where possible so we can assess the item and provide the applicable repair, replacement, credit or refund remedy.</p>
            </section>

            <section id="terms-payments">
                <p class="legal-page__number">05</p>
                <h2>Prices, payments, deposits and refunds</h2>
                <p>Prices are shown in the currency displayed on the relevant page or invoice. You authorise us and our payment provider to process the amount and transaction information required for the selected service. Online payments may be processed by Paystack or another provider identified at checkout; that provider’s own terms and privacy policy also apply to its payment interface.</p>
                <p>Do not send card PINs, one-time passwords or mobile-money approval codes to us by email, telephone or WhatsApp. Payment is complete only when confirmed by the payment provider and accepted in our records.</p>
                <p>Approved refunds are returned, where practicable, to the original payment method. Bank and payment-network processing times are outside our control. Fees already incurred for a delivered product, completed training, consumed material, completed consultation or procedure are not refundable except where required by law or expressly agreed. Where we cancel and cannot provide a paid service or product, we will offer an appropriate alternative, credit or refund.</p>
                <p>Chargebacks or payment disputes must be genuine. We may provide relevant order, attendance, consent, communication and delivery records to the payment provider or financial institution when responding to a dispute.</p>
            </section>

            <section id="terms-accounts">
                <p class="legal-page__number">06</p>
                <h2>Student accounts and acceptable use</h2>
                <p>Public customers do not need a website account to enquire, book or buy. Student portal access is limited to approved students, and administrative access is limited to authorised staff. You must keep credentials confidential, use only your own account, provide accurate information and notify us of suspected compromise.</p>
                <p>You must not attempt unauthorised access, scrape or overload the website, introduce malicious code, evade security controls, misuse another person’s data, infringe intellectual property, submit unlawful or misleading material, or use the website to harm others. We may suspend access reasonably necessary to protect users, records, services or legal compliance.</p>
            </section>

            <section id="terms-ip">
                <p class="legal-page__number">07</p>
                <h2>Intellectual property and third-party services</h2>
                <p>The website, branding, original text, graphics, course content, documents, photographs and other materials are owned by or licensed to us and protected by applicable law. You may view the public website for personal, non-commercial use but may not reproduce or exploit its content without permission.</p>
                <p>Links or integrations involving Paystack, Google, WhatsApp, maps, video hosts or other third parties are provided for specific functions. We do not control their services, availability or independent terms. Follow only genuine links from our official website and verify payment prompts before approving them.</p>
            </section>

            <section id="terms-liability">
                <p class="legal-page__number">08</p>
                <h2>Disclaimers and limitation of liability</h2>
                <p>We aim to keep website information accurate and services available, but the website may occasionally be unavailable or contain an error. To the extent permitted by law, we are not liable for indirect or consequential loss caused solely by website interruption, misuse, third-party systems, inaccurate information supplied by you, or events beyond our reasonable control.</p>
                <p>Nothing in these Terms excludes or limits liability that cannot lawfully be excluded, including liability for fraud, wilful misconduct, or death or personal injury caused by negligence. Your mandatory consumer, patient and data-protection rights remain unaffected.</p>
            </section>

            <section id="terms-law">
                <p class="legal-page__number">09</p>
                <h2>Changes, severability and governing law</h2>
                <p>We may revise these Terms to reflect service, provider or legal changes. The version displayed when a transaction is accepted applies to that transaction unless a change is required by law or agreed with you. If one provision is unenforceable, the remaining provisions continue to apply.</p>
                <p>These Terms are governed by the laws of the Republic of Ghana. The parties should first try in good faith to resolve a complaint directly. Ghanaian courts have jurisdiction, subject to any mandatory right to use another regulator, tribunal or dispute process.</p>
            </section>

            <section id="terms-contact">
                <p class="legal-page__number">10</p>
                <h2>Contact and complaints</h2>
                <p>Questions, cancellations, return requests or complaints can be sent to <a href="mailto:{{ config('clinic.email') }}">{{ config('clinic.email') }}</a>, raised by telephone at <a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}">{{ config('clinic.phone') }}</a>, or delivered to {{ config('clinic.legal_name') }}, {{ config('clinic.address') }}. Include the relevant booking, application, order or transaction reference, but never send a PIN or one-time password.</p>
            </section>
        </article>
    </div>
</section>
@endsection
