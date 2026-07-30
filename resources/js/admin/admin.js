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

function initPageEditor() {
    const editor = document.querySelector('[data-page-editor]');
    if (!editor) return;

    const list = editor.querySelector('[data-page-sections]');
    const template = document.querySelector('[data-page-section-template]');
    const addButton = editor.querySelector('[data-add-page-section]');
    if (!list || !template || !addButton) return;

    const reindex = () => {
        list.querySelectorAll('[data-page-section]').forEach((section, index) => {
            section.querySelector('[data-section-number]').textContent = String(index + 1);
            section.querySelectorAll('[name]').forEach((field) => {
                field.name = field.name.replace(/sections\[[^\]]+]/, `sections[${index}]`);
            });
        });
    };

    const bind = (section) => {
        section.querySelector('[data-section-remove]')?.addEventListener('click', () => {
            section.remove();
            reindex();
        });
        section.querySelector('[data-section-up]')?.addEventListener('click', () => {
            const previous = section.previousElementSibling;
            if (previous) list.insertBefore(section, previous);
            reindex();
        });
        section.querySelector('[data-section-down]')?.addEventListener('click', () => {
            const next = section.nextElementSibling;
            if (next) list.insertBefore(next, section);
            reindex();
        });
    };

    list.querySelectorAll('[data-page-section]').forEach(bind);
    addButton.addEventListener('click', () => {
        const index = list.querySelectorAll('[data-page-section]').length;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index)).trim();
        const section = wrapper.firstElementChild;
        if (!section) return;
        list.append(section);
        bind(section);
        reindex();
        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
}

document.addEventListener('DOMContentLoaded', initPageEditor);
