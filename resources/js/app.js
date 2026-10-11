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
});
