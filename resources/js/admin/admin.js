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

    sidebar.querySelectorAll('[data-admin-nav-group]').forEach((group) => {
        const button = group.querySelector('[data-admin-group-toggle]');
        const key = group.dataset.groupKey;
        if (!button || !key) return;

        const storageKey = `deluxe-admin-nav-${key}`;
        const stored = window.localStorage.getItem(storageKey);
        const startsCollapsed = stored !== 'expanded' && !group.classList.contains('is-active');

        const setCollapsed = (collapsed) => {
            group.classList.toggle('is-collapsed', collapsed);
            button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        };

        setCollapsed(startsCollapsed);
        button.addEventListener('click', () => {
            const collapsed = !group.classList.contains('is-collapsed');
            setCollapsed(collapsed);
            window.localStorage.setItem(storageKey, collapsed ? 'collapsed' : 'expanded');
        });
    });
}

document.addEventListener('DOMContentLoaded', initAdminNavigation);
