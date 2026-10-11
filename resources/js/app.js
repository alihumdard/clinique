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
});
