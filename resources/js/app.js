import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    if (!menuButton || !menu) return;

    menuButton.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('flex');
        menu.classList.toggle('hidden', !isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));
    });

    const backToTop = document.getElementById('back-to-top');
    backToTop?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.querySelectorAll('[data-carousel-prev], [data-carousel-next]').forEach((button) => {
        const key = button.dataset.carouselPrev || button.dataset.carouselNext;
        const track = document.getElementById(`carousel-${key}`);
        if (!track) return;

        const direction = button.hasAttribute('data-carousel-prev') ? -1 : 1;
        button.addEventListener('click', () => {
            track.scrollBy({ left: direction * track.clientWidth * 0.8, behavior: 'smooth' });
        });
    });

    // Click-to-load video embeds: swap the poster for the real iframe only
    // once the visitor presses play. Keeps ~1 Mo per embed off the initial
    // page load for visitors on metered mobile data.
    document.querySelectorAll('[data-video-embed]').forEach((container) => {
        const trigger = container.querySelector('[data-video-play]');
        if (!trigger) return;

        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const iframe = document.createElement('iframe');
            iframe.src = container.dataset.videoEmbed;
            iframe.title = container.dataset.videoTitle || 'Lecteur vidéo';
            iframe.loading = 'lazy';
            iframe.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.className = 'absolute inset-0 h-full w-full';

            container.replaceChildren(iframe);
        });
    });

    // Password reveal. The button ships hidden and is only shown once this
    // runs, so there is no dead control when scripts are unavailable.
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const field = document.getElementById(button.dataset.passwordToggle);
        if (!field) return;

        button.hidden = false;

        button.addEventListener('click', () => {
            const revealed = field.type === 'text';
            field.type = revealed ? 'password' : 'text';
            button.setAttribute('aria-pressed', String(!revealed));
            button.setAttribute(
                'aria-label',
                revealed ? 'Afficher le mot de passe' : 'Masquer le mot de passe'
            );
        });
    });

    // Admin sidebar drawer (mobile only; the sidebar is static from lg up).
    const adminSidebar = document.getElementById('admin-sidebar');
    const adminToggle = document.getElementById('admin-sidebar-toggle');
    const adminBackdrop = document.getElementById('admin-sidebar-backdrop');

    if (adminSidebar && adminToggle) {
        const setDrawer = (open) => {
            adminSidebar.classList.toggle('-translate-x-full', !open);
            adminToggle.setAttribute('aria-expanded', String(open));
            adminBackdrop?.classList.toggle('hidden', !open);
        };

        adminToggle.addEventListener('click', () => {
            setDrawer(adminSidebar.classList.contains('-translate-x-full'));
        });

        adminBackdrop?.addEventListener('click', () => setDrawer(false));

        // Escape closes the drawer, matching normal dialog behaviour.
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setDrawer(false);
        });
    }
});
