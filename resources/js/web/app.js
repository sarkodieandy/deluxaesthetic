import Alpine from 'alpinejs';
import { initNavigation, mobileNav } from './navigation';
import { initAnimations } from './animations';
import { teamCarousel } from './team-carousel';
import { heroCarousel } from './hero-carousel';
import { beforeAfterCompare } from './before-after-compare';

window.Alpine = Alpine;
Alpine.data('teamCarousel', teamCarousel);
Alpine.data('heroCarousel', heroCarousel);
Alpine.data('beforeAfterCompare', beforeAfterCompare);
Alpine.data('mobileNav', mobileNav);

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initAnimations();
});

Alpine.start();
