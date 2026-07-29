const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initScrollReveals() {
    const items = document.querySelectorAll('.reveal, .line-reveal');

    if (items.length === 0) {
        return;
    }

    if (prefersReducedMotion()) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -5% 0px' },
    );

    items.forEach((el) => observer.observe(el));
}

export async function initHeroMotion() {
    const hero = document.querySelector('[data-hero]');
    const mask = document.querySelector('[data-hero-mask]');
    const brand = document.querySelector('[data-hero-brand]');
    const headline = document.querySelector('[data-hero-headline]');
    const support = document.querySelector('[data-hero-support]');
    const actions = document.querySelector('[data-hero-actions]');

    if (!hero) {
        return;
    }

    if (prefersReducedMotion()) {
        mask?.classList.add('is-revealed');
        [brand, headline, support, actions].forEach((el) => {
            el?.classList.add('is-visible');
        });
        return;
    }

    try {
        const { gsap } = await import('gsap');

        const timeline = gsap.timeline({ defaults: { ease: 'power3.out' } });

        if (mask) {
            timeline.fromTo(
                mask,
                { clipPath: 'inset(0 0 100% 0)' },
                { clipPath: 'inset(0 0 0 0)', duration: 1.1 },
            );
        }

        if (brand) {
            timeline.from(brand, { y: 28, opacity: 0, duration: 0.8 }, '-=0.55');
        }

        if (headline) {
            timeline.from(headline, { y: 20, opacity: 0, duration: 0.7 }, '-=0.45');
        }

        if (support) {
            timeline.from(support, { y: 16, opacity: 0, duration: 0.65 }, '-=0.4');
        }

        if (actions) {
            timeline.from(actions.children, { y: 12, opacity: 0, duration: 0.55, stagger: 0.08 }, '-=0.35');
        }
    } catch {
        mask?.classList.add('is-revealed');
        [brand, headline, support, actions].forEach((el) => {
            el?.classList.add('is-visible');
        });
    }
}

export function initAnimations() {
    initScrollReveals();
    initHeroMotion();
    initSectionReveals();
    initHomeExperience();
}

function initHomeExperience() {
    const home = document.querySelector('.home-v3-hero');

    if (!home || prefersReducedMotion()) {
        document.querySelectorAll('[data-count]').forEach((item) => {
            item.textContent = `${Number(item.dataset.count).toLocaleString()}+`;
        });
        return;
    }

    initHomeCounters();
    initHomeParallax();
    initHomeTilt();
    initHomeStagger();
}

function initHomeCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const element = entry.target;
            const target = Number(element.dataset.count || 0);
            const duration = 1500;
            const start = performance.now();

            const paint = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 4);
                element.textContent = `${Math.floor(target * eased).toLocaleString()}+`;
                if (progress < 1) requestAnimationFrame(paint);
            };

            requestAnimationFrame(paint);
            observer.unobserve(element);
        });
    }, { threshold: 0.5 });

    counters.forEach((counter) => observer.observe(counter));
}

function initHomeParallax() {
    const images = [...document.querySelectorAll('[data-parallax-image], [data-parallax-card] img')];
    if (!images.length) return;

    let ticking = false;
    const update = () => {
        const viewport = window.innerHeight;
        images.forEach((image) => {
            const rect = image.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > viewport) return;
            const position = (rect.top + rect.height / 2 - viewport / 2) / viewport;
            image.style.setProperty('--parallax-y', `${position * -24}px`);
        });
        ticking = false;
    };

    const requestUpdate = () => {
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    };

    update();
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate, { passive: true });
}

function initHomeTilt() {
    document.querySelectorAll('[data-tilt-card]').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            if (event.pointerType === 'touch') return;
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            card.style.setProperty('--tilt-x', `${y * -2.2}deg`);
            card.style.setProperty('--tilt-y', `${x * 2.2}deg`);
        });
        card.addEventListener('pointerleave', () => {
            card.style.setProperty('--tilt-x', '0deg');
            card.style.setProperty('--tilt-y', '0deg');
        });
    });
}

function initHomeStagger() {
    const groups = document.querySelectorAll('.home-v3-worlds__grid, .home-v3-treatments__grid, .home-v3-testimonials');
    groups.forEach((group) => {
        [...group.children].forEach((child, index) => {
            child.style.setProperty('--reveal-delay', `${index * 110}ms`);
        });
    });
}

function initSectionReveals() {
    const sections = document.querySelectorAll('.our-work .reveal, .ba-compare.reveal');

    if (sections.length === 0) {
        return;
    }

    if (prefersReducedMotion()) {
        sections.forEach((el) => el.classList.add('is-visible'));

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -4% 0px' },
    );

    sections.forEach((el) => observer.observe(el));
}
