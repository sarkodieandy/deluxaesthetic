const STORAGE_WIDTH = 'student-portal-sidebar-width';
const STORAGE_COLLAPSED = 'student-portal-sidebar-collapsed';

function parseWidth(value, fallback) {
    const n = parseFloat(value);
    return Number.isFinite(n) ? n : fallback;
}

function remToPx(rem) {
    const root = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
    return rem * root;
}

function pxToRem(px) {
    const root = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
    return px / root;
}

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-student-shell]');
    const sidebar = document.querySelector('[data-student-sidebar]');
    const resizeHandle = document.querySelector('[data-student-resize]');
    const collapseBtn = document.querySelector('[data-student-collapse]');
    const menuBtn = document.querySelector('[data-student-menu]');
    const backdrop = document.querySelector('[data-student-backdrop]');

    if (!shell || !sidebar) {
        return;
    }

    const minPx = () => remToPx(parseWidth(getComputedStyle(document.documentElement).getPropertyValue('--student-sidebar-min'), 11.5));
    const maxPx = () => remToPx(parseWidth(getComputedStyle(document.documentElement).getPropertyValue('--student-sidebar-max'), 22));

    const savedWidth = localStorage.getItem(STORAGE_WIDTH);
    if (savedWidth) {
        shell.style.setProperty('--student-sidebar-width', savedWidth);
    }

    const isMobile = () => window.matchMedia('(max-width: 899px)').matches;

    const syncSidebarOpenState = (open) => {
        shell.classList.toggle('has-sidebar-open', open);
        collapseBtn?.setAttribute('aria-expanded', open ? 'true' : 'false');
        menuBtn?.setAttribute('aria-expanded', open ? 'true' : 'false');

        if (isMobile()) {
            document.body.classList.toggle('student-mobile-nav-open', open);
            if (backdrop) {
                backdrop.hidden = !open;
                backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
            }
        } else if (backdrop) {
            backdrop.hidden = true;
            backdrop.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('student-mobile-nav-open');
        }
    };

    const savedCollapsed = localStorage.getItem(STORAGE_COLLAPSED) === '1';
    if (savedCollapsed && !isMobile()) {
        syncSidebarOpenState(false);
    } else {
        syncSidebarOpenState(true);
    }

    const setDesktopCollapsed = (open) => {
        syncSidebarOpenState(open);
        localStorage.setItem(STORAGE_COLLAPSED, open ? '0' : '1');
    };

    menuBtn?.addEventListener('click', () => {
        const open = !shell.classList.contains('has-sidebar-open');
        if (isMobile()) {
            syncSidebarOpenState(open);
        } else {
            setDesktopCollapsed(open);
        }
    });

    backdrop?.addEventListener('click', () => {
        if (isMobile()) {
            syncSidebarOpenState(false);
        }
    });

    collapseBtn?.addEventListener('click', () => {
        if (isMobile()) {
            syncSidebarOpenState(false);
            return;
        }
        setDesktopCollapsed(!shell.classList.contains('has-sidebar-open'));
    });

    sidebar.querySelectorAll('.student-nav__links a[href]').forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobile()) {
                syncSidebarOpenState(false);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && shell.classList.contains('has-sidebar-open') && isMobile()) {
            syncSidebarOpenState(false);
        }
    });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            document.body.classList.remove('student-mobile-nav-open');
            if (backdrop) {
                backdrop.hidden = true;
            }
            const collapsed = localStorage.getItem(STORAGE_COLLAPSED) === '1';
            syncSidebarOpenState(!collapsed);
        }
    });

    if (resizeHandle) {
        let startX = 0;
        let startWidthPx = 0;

        const onMove = (event) => {
            const clientX = event.touches ? event.touches[0].clientX : event.clientX;
            const delta = clientX - startX;
            let next = startWidthPx + delta;
            next = Math.min(maxPx(), Math.max(minPx(), next));
            const rem = `${pxToRem(next).toFixed(2)}rem`;
            shell.style.setProperty('--student-sidebar-width', rem);
        };

        const onEnd = () => {
            shell.classList.remove('is-resizing');
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onEnd);
            document.removeEventListener('touchmove', onMove);
            document.removeEventListener('touchend', onEnd);
            const width = getComputedStyle(shell).getPropertyValue('--student-sidebar-width').trim();
            if (width) {
                localStorage.setItem(STORAGE_WIDTH, width);
            }
            if (!shell.classList.contains('has-sidebar-open')) {
                setDesktopCollapsed(true);
            }
        };

        const onStart = (event) => {
            if (isMobile()) {
                return;
            }
            event.preventDefault();
            startX = event.touches ? event.touches[0].clientX : event.clientX;
            const current = getComputedStyle(shell).getPropertyValue('--student-sidebar-width').trim();
            startWidthPx = remToPx(parseFloat(current) || 16);
            shell.classList.add('is-resizing');
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onEnd);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onEnd);
        };

        resizeHandle.addEventListener('mousedown', onStart);
        resizeHandle.addEventListener('touchstart', onStart, { passive: false });
    }
});
