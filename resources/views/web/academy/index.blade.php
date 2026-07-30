@extends('web.layouts.app')
@section('title', 'Aesthetics Training & Masterclasses — '.config('clinic.name'))
@section('meta_description', 'Build practical skills through hands-on aesthetics training, Botox, fillers, skin treatments and certified masterclasses in Accra, Ghana.')
@section('content')
<section class="academy-v2-hero">
    <div class="container-site academy-v2-hero__grid">
        <div class="academy-v2-hero__copy reveal">
            <p class="text-label">De Luxe Aesthetic Clinic & Academy</p>
            <h1>Learn the science.<br><em>Master the technique.</em></h1>
            <p class="academy-v2-hero__lead">Clinic-led aesthetics education built for confident, safe and commercially ready practitioners—from your first consultation to advanced injectable and skin procedures.</p>
            <div class="academy-v2-hero__actions">
                <a href="{{ route('web.enrol') }}" class="btn btn-primary">Enquire about training</a>
                <a href="#programmes" class="btn btn-secondary">Explore programmes</a>
            </div>
            <dl class="academy-v2-proof">
                <div><dt>Hands-on</dt><dd>Supervised clinical practice</dd></div>
                <div><dt>Certified</dt><dd>Recognised training pathway</dd></div>
                <div><dt>Supported</dt><dd>Lifetime mentorship</dd></div>
            </dl>
        </div>
        <div class="academy-v2-hero__visual reveal reveal-delay-2">
            <img src="https://unsplash.com/photos/2LxO1Wef8wQ/download?force=true&w=1600" alt="A practitioner performing a professional facial treatment" fetchpriority="high">
            <div class="academy-v2-hero__badge">
                <span>Professional pathway</span>
                <strong>Aesthetics<br>Masterclass</strong>
            </div>
        </div>
    </div>
</section>

<section class="academy-v2-intro">
    <div class="container-site academy-v2-intro__grid">
        <div>
            <p class="text-label">Training designed around practice</p>
            <h2 class="text-section">More than a certificate.<br>A complete practitioner foundation.</h2>
        </div>
        <div>
            <p>Every programme combines anatomy, patient assessment, product selection, injection or treatment technique, complication management and aftercare. You learn what to do, why it works and how to deliver it responsibly.</p>
            <a href="#curriculum" class="academy-v2-text-link">View the complete curriculum <span aria-hidden="true">→</span></a>
        </div>
    </div>
</section>

<section class="academy-v2-programmes" id="programmes">
    <div class="container-site">
        <header class="academy-v2-section-head">
            <div>
                <p class="text-label">Choose your pathway</p>
                <h2 class="text-section">Four pillars of modern aesthetics</h2>
            </div>
            <p>Build a broad foundation or focus your development on the procedures most relevant to your practice.</p>
        </header>

        <div class="academy-v2-programme-grid">
            <article class="academy-v2-programme academy-v2-programme--wide reveal">
                <div class="academy-v2-programme__image">
                    <img src="https://unsplash.com/photos/HtXyytr9304/download?force=true&w=1400" alt="Professional aesthetic facial procedure" loading="lazy">
                    <span>01</span>
                </div>
                <div class="academy-v2-programme__body">
                    <p class="academy-v2-programme__eyebrow">Core masterclass</p>
                    <h3>Injectable foundations</h3>
                    <p>Develop the clinical thinking that sits behind safe, considered injectable work.</p>
                    <ul>
                        <li>History of Botox and fillers</li>
                        <li>Anatomy and physiology</li>
                        <li>Injection techniques</li>
                        <li>Patient and product selection</li>
                        <li>Complication management</li>
                    </ul>
                </div>
            </article>

            <article class="academy-v2-programme reveal reveal-delay-1">
                <div class="academy-v2-programme__image">
                    <img src="https://images.pexels.com/photos/37676759/pexels-photo-37676759.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="Professional administering a Botox injection to a client's forehead" loading="lazy">
                    <span>02</span>
                </div>
                <div class="academy-v2-programme__body">
                    <p class="academy-v2-programme__eyebrow">Botox</p>
                    <h3>Precision & facial balancing</h3>
                    <p>Technique-led training for expressive lines, facial refinement and advanced indications.</p>
                    <ul>
                        <li>Glabellar, forehead and crow’s feet</li>
                        <li>Bunny lines and face slimming</li>
                        <li>Botox for excessive sweating</li>
                    </ul>
                </div>
            </article>

            <article class="academy-v2-programme reveal reveal-delay-2">
                <div class="academy-v2-programme__image">
                    <img src="{{ asset('assets/web/images/hero/hero-body-care.jpg') }}" alt="Professional body contouring treatment" loading="lazy">
                    <span>03</span>
                </div>
                <div class="academy-v2-programme__body">
                    <p class="academy-v2-programme__eyebrow">Fillers & contour</p>
                    <h3>Shape, volume & rejuvenation</h3>
                    <p>Explore facial and body contouring with a focus on proportion, assessment and natural-looking results.</p>
                    <ul>
                        <li>Lips, jawline, chin and hand rejuvenation</li>
                        <li>Hip and butt filler techniques</li>
                        <li>Cat eye, goddess eyes and baby smile</li>
                    </ul>
                </div>
            </article>

            <article class="academy-v2-programme academy-v2-programme--wide academy-v2-programme--reverse reveal">
                <div class="academy-v2-programme__image">
                    <img src="https://unsplash.com/photos/2OVrIfOXDDY/download?force=true&w=1400" alt="Advanced professional skin treatment" loading="lazy">
                    <span>04</span>
                </div>
                <div class="academy-v2-programme__body">
                    <p class="academy-v2-programme__eyebrow">Advanced skin science</p>
                    <h3>Corrective & regenerative treatments</h3>
                    <p>Broaden your treatment menu with protocols designed around texture, pigmentation and skin renewal.</p>
                    <ul>
                        <li>Advanced microneedling and mesotherapy</li>
                        <li>PRP, skin boosters and chemical peels</li>
                        <li>Hyperpigmentation and stretch marks</li>
                        <li>Lipolysis and cosmetic science</li>
                    </ul>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="academy-v2-botox">
    <div class="container-site academy-v2-botox__grid">
        <div class="academy-v2-botox__visual reveal">
            <img src="https://images.pexels.com/photos/37676759/pexels-photo-37676759.jpeg?auto=compress&cs=tinysrgb&w=1600" alt="Professional administering a Botox injection to a client's forehead" loading="lazy">
            <div class="academy-v2-botox__stamp">
                <span>Focused pathway</span>
                <strong>Botox<br>Training</strong>
            </div>
        </div>
        <div class="academy-v2-botox__content reveal reveal-delay-2">
            <p class="text-label">Botox masterclass</p>
            <h2 class="text-section">Precision techniques for confident facial assessment.</h2>
            <p class="academy-v2-botox__lead">Learn the anatomy, consultation approach, dosing principles and practical techniques behind safe, balanced Botox treatments. Training combines theory, demonstration and supervised hands-on practice.</p>
            <div class="academy-v2-botox__topics">
                <article>
                    <span>01</span>
                    <div><h3>Upper face</h3><p>Glabellar lines, forehead lines and crow’s feet.</p></div>
                </article>
                <article>
                    <span>02</span>
                    <div><h3>Facial refinement</h3><p>Bunny lines and face-slimming techniques.</p></div>
                </article>
                <article>
                    <span>03</span>
                    <div><h3>Advanced indication</h3><p>Botox protocols for excessive sweating.</p></div>
                </article>
                <article>
                    <span>04</span>
                    <div><h3>Clinical safety</h3><p>Patient selection, product handling, aftercare and complication management.</p></div>
                </article>
            </div>
            <a href="{{ route('web.enrol') }}" class="btn btn-primary">Enquire about Botox training</a>
        </div>
    </div>
</section>

<section class="academy-v2-curriculum" id="curriculum">
    <div class="container-site">
        <header class="academy-v2-section-head academy-v2-section-head--light">
            <div>
                <p class="text-label">Complete curriculum</p>
                <h2 class="text-section">Skills you can build at De Luxe</h2>
            </div>
            <p>Course content and procedure combinations are confirmed during admissions, based on your experience and chosen training pathway.</p>
        </header>
        <div class="academy-v2-curriculum__grid">
            <article>
                <span>Foundation</span>
                <h3>Safety & consultation</h3>
                <ul>
                    <li>Anatomy and physiology</li>
                    <li>Patient consultation and selection</li>
                    <li>Product selection and recommendations</li>
                    <li>Injection technique</li>
                    <li>Complication management</li>
                    <li>Professional aftercare</li>
                </ul>
            </article>
            <article>
                <span>Injectables</span>
                <h3>Botox & dermal fillers</h3>
                <ul>
                    <li>Glabellar lines, crow’s feet and forehead</li>
                    <li>Bunny lines, face slimming and sweating</li>
                    <li>Italian lip, jawline and chin contour</li>
                    <li>Hip and butt fillers</li>
                    <li>Cat eye and hand rejuvenation</li>
                    <li>PDO mono and monothread</li>
                </ul>
            </article>
            <article>
                <span>Skin & body</span>
                <h3>Regeneration & contour</h3>
                <ul>
                    <li>Advanced microneedling</li>
                    <li>Mesotherapy and PRP</li>
                    <li>Skin boosters and chemical peel basics</li>
                    <li>Hyperpigmentation protocols</li>
                    <li>Stretch-mark treatments</li>
                    <li>Fat dissolving and basic weight loss</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="academy-v2-career">
    <div class="container-site academy-v2-career__grid">
        <div class="academy-v2-career__image reveal">
            <img src="https://images.pexels.com/photos/33607394/pexels-photo-33607394.jpeg?auto=compress&cs=tinysrgb&w=1400" alt="Beauty professional receiving hands-on training" loading="lazy">
        </div>
        <div class="academy-v2-career__copy reveal reveal-delay-2">
            <p class="text-label">Beyond the treatment room</p>
            <h2 class="text-section">We help you prepare for the industry.</h2>
            <p>Technical ability matters, but a sustainable practice also needs confidence, connections and professional presentation. Your academy experience can include:</p>
            <div class="academy-v2-career__list">
                <p><span>01</span> Customer service and client experience</p>
                <p><span>02</span> Referral network and industry recognition</p>
                <p><span>03</span> Lifetime mentorship and product seller contacts</p>
                <p><span>04</span> Internationally recognised certificates</p>
                <p><span>05</span> Graduation ceremony and professional photography</p>
            </div>
        </div>
    </div>
</section>

<section class="academy-v2-final">
    <div class="container-site academy-v2-final__inner">
        <p class="text-label">Your next chapter</p>
        <h2>Ready to train with De Luxe?</h2>
        <p>Tell us which procedures interest you. Our academy team will help you choose the right pathway and explain the physical enrolment process.</p>
        <p class="academy-v2-final__portal">Create your student portal account to track enrolment, courses and academy updates.</p>
        <div>
            <a href="{{ route('web.enrol') }}" class="btn btn-primary">Start an academy enquiry</a>
            <a href="{{ route('web.academy.student-portal.create') }}" class="btn btn-secondary">Create student account</a>
        </div>
    </div>
</section>
@endsection
