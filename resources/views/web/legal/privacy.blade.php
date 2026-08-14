@extends('web.layouts.app')

@section('title', 'Privacy Policy — '.config('clinic.name'))
@section('meta_description', 'How '.config('clinic.name').' collects, uses, protects and shares personal data for clinical bookings, Academy applications, student accounts and product orders.')
@section('canonical', route('web.privacy'))

@section('content')
<section class="legal-hero">
    <div class="container-site legal-hero__grid">
        <div>
            <p class="text-label">Legal &amp; privacy</p>
            <h1>Privacy Policy</h1>
        </div>
        <div class="legal-hero__summary">
            <p>We respect the trust involved when you share personal, educational, order or treatment information with us.</p>
            <p><strong>Effective date:</strong> 14 August 2026</p>
        </div>
    </div>
</section>

<section class="legal-page section">
    <div class="container-site legal-page__layout">
        <aside class="legal-page__aside" aria-label="Privacy policy contents">
            <p class="text-label">On this page</p>
            <ol>
                <li><a href="#privacy-who">Who we are</a></li>
                <li><a href="#privacy-data">Data we collect</a></li>
                <li><a href="#privacy-use">How we use data</a></li>
                <li><a href="#privacy-sharing">Sharing and payments</a></li>
                <li><a href="#privacy-retention">Retention and security</a></li>
                <li><a href="#privacy-rights">Your rights</a></li>
                <li><a href="#privacy-contact">Contact us</a></li>
            </ol>
        </aside>

        <article class="legal-page__content">
            <section id="privacy-who">
                <p class="legal-page__number">01</p>
                <h2>Who we are and when this policy applies</h2>
                <p>{{ config('clinic.legal_name') }} (trading as {{ config('clinic.name') }}, “De Luxe”, “we”, “us” or “our”) is responsible for the personal data described in this policy. Our principal contact address is {{ config('clinic.address') }}.</p>
                <p>This policy applies when you visit our website, request a consultation, book a clinical procedure, apply to the Academy, use an approved student account, purchase or enquire about a product, communicate through email, telephone or WhatsApp, or otherwise interact with us.</p>
                <p>We process personal data in accordance with Ghana’s Data Protection Act, 2012 (Act 843) and other applicable professional, health, consumer, accounting and regulatory requirements.</p>
            </section>

            <section id="privacy-data">
                <p class="legal-page__number">02</p>
                <h2>Personal data we collect</h2>
                <ul>
                    <li><strong>Identity and contact data:</strong> name, email address, telephone number, address and preferred contact method.</li>
                    <li><strong>Clinical and booking data:</strong> requested procedure, appointment date, consultation notes, relevant health information, contraindications, consent, treatment history and before/after media where separately authorised.</li>
                    <li><strong>Academy and student data:</strong> course interest, qualifications or experience, application details, approval status, attendance, schedules, learning materials accessed, assignments, support conversations and certificate records.</li>
                    <li><strong>Order and transaction data:</strong> products, quantities, delivery information, order value, payment status, transaction reference, refunds and customer-service records.</li>
                    <li><strong>Account data:</strong> login email, password hash, role, account status and, where you choose Google sign-in, the basic profile information Google supplies with your permission.</li>
                    <li><strong>Technical data:</strong> IP address, device/browser information, timestamps, security logs, session identifiers, cookie data and pages or features used.</li>
                    <li><strong>Communications:</strong> enquiries, emails, calls, WhatsApp messages, feedback and support requests.</li>
                </ul>
                <p>Clinical information may be sensitive personal data. We request only information reasonably needed to assess suitability, provide safe care, maintain professional records, or satisfy legal obligations. Please do not send unnecessary medical or identity documents through unsecured channels.</p>
            </section>

            <section id="privacy-use">
                <p class="legal-page__number">03</p>
                <h2>Why and how we use personal data</h2>
                <p>Depending on the interaction and applicable law, we process data with your consent, to take steps at your request or perform a contract, to comply with legal or professional obligations, to protect vital interests, and for legitimate interests that do not override your rights.</p>
                <ul>
                    <li>responding to enquiries and arranging consultations or appointments;</li>
                    <li>assessing suitability, obtaining consent, providing procedures and delivering aftercare;</li>
                    <li>reviewing Academy applications, contacting applicants, approving student access and administering physical training;</li>
                    <li>providing course schedules, materials, assignments, support and certificates to authorised students;</li>
                    <li>processing orders, payments, delivery, refunds and transaction records;</li>
                    <li>authenticating users, preventing fraud, securing the website and investigating misuse;</li>
                    <li>meeting accounting, tax, insurance, health, safety, regulatory and legal requirements;</li>
                    <li>improving services and measuring website reliability using appropriately limited technical information; and</li>
                    <li>sending marketing only where permitted and allowing you to opt out at any time.</li>
                </ul>
                <p>We do not make decisions that produce legal or similarly significant effects about you solely through automated processing.</p>
            </section>

            <section id="privacy-cookies">
                <p class="legal-page__number">04</p>
                <h2>Cookies and similar technology</h2>
                <p>We use essential cookies and local storage for security, sign-in, language preferences, shopping-cart operation and session continuity. These are necessary for the website to function. Third-party services opened from our website, including Google, WhatsApp and a payment provider, may apply their own cookies under their respective policies. We do not use cookies to sell personal data.</p>
                <p>You can restrict cookies in your browser, but essential account, cart or booking features may stop working correctly.</p>
            </section>

            <section id="privacy-sharing">
                <p class="legal-page__number">05</p>
                <h2>Who receives personal data</h2>
                <p>We do not sell or rent personal data. We disclose only what is reasonably necessary to:</p>
                <ul>
                    <li>authorised staff, trainers and practitioners who need the information to perform their duties;</li>
                    <li>hosting, database, email, security, storage and technical service providers acting for us;</li>
                    <li>Paystack or another disclosed payment processor, banks and payment networks when online payment is selected;</li>
                    <li>Google when you choose Google sign-in, and Meta/WhatsApp when you contact or order from us through WhatsApp;</li>
                    <li>delivery providers where required to fulfil an order;</li>
                    <li>professional advisers, insurers, auditors or regulators; and</li>
                    <li>law-enforcement bodies, courts or other parties where required by law or necessary to protect rights, safety and security.</li>
                </ul>
                <h3>Payment information</h3>
                <p>When online payments are enabled, payment-card or mobile-money credentials are entered on the payment provider’s secure interface and handled under that provider’s privacy terms. We receive transaction details such as the amount, status, payment channel and reference, but we do not intend to store complete card numbers, PINs or mobile-money authentication credentials.</p>
                <h3>International processing</h3>
                <p>Some technology providers may process information outside Ghana. Where this occurs, we take reasonable steps to use providers and safeguards that protect the information consistently with applicable law.</p>
            </section>

            <section id="privacy-retention">
                <p class="legal-page__number">06</p>
                <h2>Retention and security</h2>
                <p>We keep personal data only for as long as needed for the purpose collected and for applicable legal, tax, accounting, insurance, professional-record, dispute or regulatory periods. Retention depends on the record: unsuccessful enquiries and applications are ordinarily reviewed for deletion after 24 months; transaction and treatment records may be held for up to seven years or longer where law or professional obligations require; active student and account records are retained while the relationship continues and for an appropriate period afterwards.</p>
                <p>We use role-based access, authentication, encrypted HTTPS transport, protected file storage, backups, logging, software updates and administrative safeguards. No internet service is risk-free, so please use a strong unique password and notify us promptly if you suspect unauthorised account activity.</p>
            </section>

            <section id="privacy-rights">
                <p class="legal-page__number">07</p>
                <h2>Your data-protection rights</h2>
                <p>Subject to applicable law and necessary identity verification, you may ask us to:</p>
                <ul>
                    <li>confirm whether we process your data and provide access to it;</li>
                    <li>correct inaccurate, incomplete, misleading or outdated data;</li>
                    <li>stop, restrict, erase or destroy data where the legal conditions are met;</li>
                    <li>explain the purpose and recipients of processing;</li>
                    <li>honour withdrawal of consent for future consent-based processing;</li>
                    <li>stop direct marketing; or</li>
                    <li>consider an objection or complaint about our processing.</li>
                </ul>
                <p>Withdrawal does not invalidate processing already performed and may not require deletion where records must be retained by law or for legitimate professional or legal claims.</p>
                <h3>Children</h3>
                <p>Our clinical and professional Academy services are not directed to children acting independently. Where a service may lawfully involve a person under 18, a parent or legal guardian must contact us and provide the required authority and consent.</p>
            </section>

            <section id="privacy-contact">
                <p class="legal-page__number">08</p>
                <h2>Questions, requests and complaints</h2>
                <p>Email <a href="mailto:{{ config('clinic.email') }}">{{ config('clinic.email') }}</a>, call <a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}">{{ config('clinic.phone') }}</a>, or write to {{ config('clinic.legal_name') }}, {{ config('clinic.address') }}. Please use the subject “Privacy Request” and describe your request clearly.</p>
                <p>You may also complain to Ghana’s Data Protection Commission at <a href="https://dataprotection.org.gh/" target="_blank" rel="noopener noreferrer">dataprotection.org.gh</a>.</p>
                <p>We may update this policy when our services, providers or legal duties change. Material changes will be published here with a revised effective date.</p>
            </section>
        </article>
    </div>
</section>
@endsection
