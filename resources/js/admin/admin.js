function initAdminNavigation() {
    const shell = document.querySelector('[data-admin-shell]');
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const toggle = document.querySelector('[data-admin-nav-toggle]');
    const closeButton = document.querySelector('[data-admin-nav-close]');

    if (!shell || !sidebar || !toggle) {
        return;
    }

    const desktopQuery = window.matchMedia('(min-width: 960px)');

    const isMobile = () => window.innerWidth < 960;

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        shell.classList.toggle('has-sidebar-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('admin-nav-open', open && isMobile());
    };

    const syncInitialState = () => {
        setOpen(desktopQuery.matches);
    };

    syncInitialState();

    desktopQuery.addEventListener('change', (event) => {
        setOpen(event.matches);
    });

    toggle.addEventListener('click', () => {
        setOpen(!shell.classList.contains('has-sidebar-open'));
    });

    closeButton?.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && shell.classList.contains('has-sidebar-open') && isMobile()) {
            setOpen(false);
        }
    });

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobile()) {
                setOpen(false);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initAdminNavigation);
