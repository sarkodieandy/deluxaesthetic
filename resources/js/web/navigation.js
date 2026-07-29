function trapFocus(container, event) {
    const focusable = [...container.querySelectorAll('a[href], button:not([disabled])')].filter(
        (el) => !el.hasAttribute('disabled') && el.offsetParent !== null,
    );

    if (focusable.length === 0) {
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

export function mobileNav() {
    return {
        open: false,

        toggle() {
            this.open = !this.open;
            this.syncBody(this.open);
        },

        close() {
            this.open = false;
            this.syncBody(false);
        },

        syncBody(isOpen) {
            document.body.classList.toggle('is-menu-open', isOpen);
            document.body.style.overflow = isOpen ? 'hidden' : '';
        },

        onKey(event) {
            if (event.key === 'Escape') {
                this.close();
            }

            const menu = document.getElementById('mobile-menu');
            if (event.key === 'Tab' && this.open && menu) {
                trapFocus(menu, event);
            }
        },
    };
}

export function initNavigation() {
    // Reserved for future desktop nav enhancements.
}
