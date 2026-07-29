const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function heroCarousel(total = 5) {
    return {
        active: 0,
        total,
        timer: null,
        intervalMs: 4000,
        reducedMotion: false,

        init() {
            this.reducedMotion = prefersReducedMotion();
            this.$nextTick(() => {
                this.paintSlides(false);
                this.startAutoplay();
            });
        },

        startAutoplay() {
            if (this.reducedMotion || this.total < 2) {
                return;
            }

            this.stopAutoplay();
            this.timer = window.setInterval(() => {
                this.next(false);
            }, this.intervalMs);
        },

        stopAutoplay() {
            if (this.timer) {
                window.clearInterval(this.timer);
                this.timer = null;
            }
        },

        next(resetTimer = true) {
            this.goTo((this.active + 1) % this.total, resetTimer);
        },

        prev() {
            this.goTo((this.active - 1 + this.total) % this.total, true);
        },

        goTo(index, resetTimer = true) {
            if (index === this.active) {
                return;
            }

            this.active = index;
            this.paintSlides(true);

            if (resetTimer) {
                this.startAutoplay();
            }
        },

        async paintSlides(animate) {
            const root = this.$refs.carousel;
            if (!root) {
                return;
            }

            const slides = [...root.querySelectorAll('[data-hero-slide]')];

            slides.forEach((slide, index) => {
                const isActive = index === this.active;
                slide.classList.toggle('is-active', isActive);
                slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });

            if (this.reducedMotion || !animate) {
                return;
            }

            try {
                const { gsap } = await import('gsap');

                slides.forEach((slide, index) => {
                    const img = slide.querySelector('img');
                    const isActive = index === this.active;

                    gsap.killTweensOf([slide, img]);

                    if (isActive) {
                        gsap.set(slide, { zIndex: 2, opacity: 1 });
                        gsap.fromTo(slide, { opacity: 0 }, { opacity: 1, duration: 0.7, ease: 'power2.out' });
                        if (img) {
                            gsap.fromTo(img, { scale: 1.08 }, { scale: 1, duration: 4, ease: 'power2.out' });
                        }
                    } else {
                        gsap.to(slide, {
                            opacity: 0,
                            duration: 0.55,
                            ease: 'power2.inOut',
                            onComplete: () => gsap.set(slide, { zIndex: 0 }),
                        });
                    }
                });
            } catch {
                // CSS .is-active fallback already applied
            }
        },

        destroy() {
            this.stopAutoplay();
        },
    };
}
