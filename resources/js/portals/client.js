document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.portal-nav__links a[href]');

    links.forEach((link) => {
        if (link instanceof HTMLAnchorElement && link.pathname === window.location.pathname) {
            link.classList.add('is-active');
            link.setAttribute('aria-current', 'page');
        }
    });
});
