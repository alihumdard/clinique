import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    if (menuButton && menu) {
        menuButton.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('flex');
            menu.classList.toggle('hidden', !isOpen);
            menuButton.setAttribute('aria-expanded', String(isOpen));
        });
    }

    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY >= 400) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        }, { passive: true });
    }

    // Dismiss buttons for CSP-safe modals (e.g. the contact form's success
    // dialog) — an inline onclick is blocked by the script-src CSP.
    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.closeModal)?.remove();
        });
    });

    // <dialog>-based modals that stay open for the page's lifetime (e.g. the
    // admin "Modifier" edit modals, one per row): opened by a trigger naming
    // the dialog's id, closed by any [data-dialog-close] inside it or by the
    // dialog's own backdrop click.
    document.querySelectorAll('[data-open-dialog]').forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const dialog = document.getElementById(trigger.dataset.openDialog);
            dialog?.showModal();

            // A trigger naming a section inside the dialog (e.g. the
            // "Galerie" count in an activity's row) opens straight to that
            // section rather than the top of a long form.
            if (trigger.dataset.scrollTo) {
                document.getElementById(trigger.dataset.scrollTo)
                    ?.scrollIntoView({ block: 'start' });
            }
        });
    });

    // A link to a create dialog from outside its own page (the sidebar's
    // "Ajouter..." sub-item) navigates here with ?ouvrir=<dialog-id> rather
    // than an anchor, since the dialog isn't a scroll target any more —
    // open it once the page has loaded.
    const openParam = new URLSearchParams(window.location.search).get('ouvrir');
    if (openParam) {
        document.getElementById(openParam)?.showModal();
    }

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.querySelectorAll('[data-dialog-close]').forEach((button) => {
            button.addEventListener('click', () => dialog.close());
        });

        // Click on the backdrop (the dialog element itself receives the
        // click only outside its content box) closes it too.
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) dialog.close();
        });
    });

    // Mobile/tablet nav sub-menu (e.g. "La Clinique" > À propos / Équipe):
    // collapsed by default, expands on tap of its toggle button.
    document.querySelectorAll('[data-mobile-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-mobile-dropdown-toggle]');
        const panel = dropdown.querySelector('[data-mobile-dropdown-panel]');
        const chevron = dropdown.querySelector('[data-mobile-dropdown-chevron]');
        if (!toggle || !panel) return;

        toggle.addEventListener('click', () => {
            const isOpen = panel.classList.toggle('flex');
            panel.classList.toggle('hidden', !isOpen);
            toggle.setAttribute('aria-expanded', String(isOpen));
            chevron?.classList.toggle('rotate-180', isOpen);
        });
    });

    // Animated FAQ accordions: keep native <details>/<summary> for a11y and
    // no-JS fallback, but intercept the toggle to slide the answer open/closed
    // via a CSS grid-template-rows transition instead of the instant snap.
    document.querySelectorAll('[data-faq-item]').forEach((details) => {
        const summary = details.querySelector('summary');
        const wrapper = details.querySelector('[data-faq-wrapper]');
        if (!summary || !wrapper) return;

        summary.addEventListener('click', (event) => {
            event.preventDefault();

            if (details.open) {
                wrapper.style.gridTemplateRows = '0fr';
                wrapper.addEventListener('transitionend', () => {
                    details.open = false;
                }, { once: true });
            } else {
                details.open = true;
                requestAnimationFrame(() => {
                    wrapper.style.gridTemplateRows = '1fr';
                });
            }
        });
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

    initCounters();

    /* ── Admin dashboard functionality ───────────────────── */
    initAdminToast();
    initAdminSelectAll();
    initAdminTableSearch();
    initAdminFilterTabs();
    initAdminDeleteConfirm();
    initDeclaredConfirm();
    initAdminForms();
    initAdminSettingsTabs();

    // Admin sidebar drawer (mobile only; the sidebar is static from lg up).
    const adminSidebar = document.getElementById('admin-sidebar');
    const adminToggle = document.getElementById('admin-sidebar-toggle');
    const adminBackdrop = document.getElementById('admin-sidebar-backdrop');
    const adminClose = document.getElementById('admin-sidebar-close');

    if (adminSidebar && adminToggle) {
        const setDrawer = (open) => {
            adminSidebar.classList.toggle('-translate-x-full', !open);
            adminToggle.setAttribute('aria-expanded', String(open));
            adminBackdrop?.classList.toggle('hidden', !open);

            // Stop the page scrolling behind the open drawer.
            document.body.classList.toggle('overflow-hidden', open);

            // Move focus into the drawer when it opens, back to the button
            // when it closes, so keyboard users are never left adrift.
            if (open) {
                adminSidebar.querySelector('a, button')?.focus();
            } else if (document.activeElement && adminSidebar.contains(document.activeElement)) {
                adminToggle.focus();
            }
        };

        adminToggle.addEventListener('click', () => {
            setDrawer(adminSidebar.classList.contains('-translate-x-full'));
        });

        adminClose?.addEventListener('click', () => setDrawer(false));
        adminBackdrop?.addEventListener('click', () => setDrawer(false));

        // Escape closes the drawer, matching normal dialog behaviour.
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setDrawer(false);
        });

        // Returning to desktop width leaves the drawer state behind.
        window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
            if (event.matches) {
                adminBackdrop?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });
    }
});

/* ═══════════════════════════════════════════════════════════
   Admin dashboard — frontend functionality
   ═══════════════════════════════════════════════════════════ */

/* ── Toast notifications ──────────────────────────────────── */
let _toastWrap = null;

function initAdminToast() {
    if (_toastWrap) return;
    _toastWrap = document.createElement('div');
    _toastWrap.className = 'fixed top-4 right-4 z-[999] flex flex-col gap-2 pointer-events-none';
    document.body.appendChild(_toastWrap);
}

function adminToast(message, type = 'success') {
    if (!_toastWrap) return;

    const isSuccess = type === 'success';
    const el = document.createElement('div');
    el.className = [
        'flex items-center gap-3 rounded-xl px-5 py-3.5 text-sm font-medium shadow-lg pointer-events-auto',
        'translate-x-0 opacity-100 transition-all duration-300',
        isSuccess ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white',
    ].join(' ');

    el.innerHTML = `
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            ${isSuccess
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>'}
        </svg>
        <span>${message}</span>`;

    _toastWrap.appendChild(el);

    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(100%)';
        setTimeout(() => el.remove(), 300);
    }, 3000);
}

/* ── Select-all checkbox ──────────────────────────────────── */
function initAdminSelectAll() {
    document.querySelectorAll('table').forEach(table => {
        const selectAll = table.querySelector('thead input[type="checkbox"]');
        if (!selectAll) return;

        const getRows = () => [...table.querySelectorAll('tbody input[type="checkbox"]')];

        const syncHeader = () => {
            const rows = getRows();
            const checkedCount = rows.filter(cb => cb.checked).length;
            selectAll.checked = checkedCount > 0 && checkedCount === rows.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
        };

        selectAll.addEventListener('change', () => {
            getRows().forEach(cb => { cb.checked = selectAll.checked; });
            syncHeader();
        });

        table.addEventListener('change', e => {
            if (e.target !== selectAll && e.target.type === 'checkbox') syncHeader();
        });
    });
}

/* ── Live table search ────────────────────────────────────── */
function initAdminTableSearch() {
    document.querySelectorAll('[role="search"] input[type="search"]').forEach(input => {
        const section = input.closest('section');
        const tbody = section?.querySelector('tbody');
        if (!tbody) return;

        input.addEventListener('input', () => {
            const q = input.value.trim().toLowerCase();
            adminFilterRows(tbody, q);
        });
    });
}

function adminFilterRows(tbody, q) {
    let anyVisible = false;
    [...tbody.querySelectorAll('tr:not(.js-empty)')].forEach(row => {
        const visible = !q || row.textContent.toLowerCase().includes(q);
        row.hidden = !visible;
        if (visible) anyVisible = true;
    });

    let empty = tbody.querySelector('tr.js-empty');
    if (!anyVisible && !empty) {
        empty = document.createElement('tr');
        empty.className = 'js-empty';
        empty.innerHTML = '<td colspan="20" class="px-5 py-10 text-center text-sm text-slate-400">Aucun résultat trouvé.</td>';
        tbody.appendChild(empty);
    } else if (anyVisible && empty) {
        empty.remove();
    }
}

/* ── Filter tabs (visual switch + row filtering) ──────────── */
function initAdminFilterTabs() {
    document.querySelectorAll('[data-filter-tabs]').forEach(tabsEl => {
        const tabs = [...tabsEl.querySelectorAll('a')];
        if (!tabs.length) return;

        const section = tabsEl.closest('section');
        const tbody = section?.querySelector('tbody');

        tabs.forEach(tab => {
            tab.addEventListener('click', e => {
                e.preventDefault();

                // Switch active classes
                tabs.forEach(t => {
                    t.classList.remove('border-primary-600', 'font-semibold', 'text-primary-600');
                    t.classList.add('border-transparent', 'font-medium', 'text-slate-500');
                    t.removeAttribute('aria-current');
                });
                tab.classList.add('border-primary-600', 'font-semibold', 'text-primary-600');
                tab.classList.remove('border-transparent', 'font-medium', 'text-slate-500');
                tab.setAttribute('aria-current', 'page');

                if (!tbody) return;

                // Derive filter keyword from label (strip count)
                const label = tab.textContent.replace(/\(\d+\)/g, '').trim().toLowerCase();

                if (label.startsWith('toutes') || label.startsWith('tous')) {
                    [...tbody.querySelectorAll('tr:not(.js-empty)')].forEach(r => { r.hidden = false; });
                    tbody.querySelector('tr.js-empty')?.remove();
                    return;
                }

                // Map plural/alias → singular status text found in rows
                const map = {
                    'publiées': 'publié', 'publiés': 'publié',
                    'brouillons': 'brouillon',
                    'archivées': 'archivé', 'archivés': 'archivé',
                    'passées': 'terminé',
                    'caravanes juridiques': 'caravane',
                };
                const filter = map[label] ?? label;
                adminFilterRows(tbody, filter);
            });
        });
    });
}

/* ── Delete confirmation ──────────────────────────────────── */
function initAdminDeleteConfirm() {
    document.addEventListener('submit', e => {
        const form = e.target;
        // references, messages and newsletter rows have no corbeille, so
        // "irréversible" is true of them. A form that has one says so
        // itself, and initDeclaredConfirm asks the right question instead.
        if (form.dataset?.confirm) return;
        if (!form.querySelector('input[name="_method"][value="DELETE"]')) return;

        const srOnly = form.querySelector('.sr-only');
        const item = srOnly?.textContent.replace('Supprimer', '').replace(':', '').trim() || 'cet élément';

        if (!confirm(`Supprimer « ${item} » ?\nCette action est irréversible.`)) {
            e.preventDefault();
        }
    });
}

/* ── Forms that word their own confirmation ──────────────────
   A delete is not one question. Moving a row to the corbeille is
   reversible and a permanent delete is not, so the form states what to
   ask rather than this file guessing from the button's label. */
function initDeclaredConfirm() {
    document.addEventListener('submit', e => {
        const message = e.target?.dataset?.confirm;
        if (!message) return;

        if (!confirm(message)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });
}

/* ── Sidebar / aside forms → toast (presentation-only pages) */
function initAdminForms() {
    document.querySelectorAll('aside form').forEach(form => {
        if (form.getAttribute('action') !== '#') return;

        form.addEventListener('submit', e => {
            e.preventDefault();

            const submitter = e.submitter;
            const btnText = submitter?.textContent.trim() ?? '';

            let msg = 'Enregistré avec succès.';
            if (btnText.includes('Publier'))   msg = 'Publié avec succès.';
            if (btnText.includes('Envoyer'))   msg = 'Envoyé avec succès.';
            if (btnText.includes('brouillon')) msg = 'Enregistré en brouillon.';

            adminToast(msg);
            form.reset();
        });
    });

    // Settings main form (not in aside)
    const settingsForm = document.querySelector('main > form[method="POST"]');
    if (settingsForm && settingsForm.getAttribute('action') === '#') {
        settingsForm.addEventListener('submit', e => {
            e.preventDefault();
            const msg = e.submitter?.value === 'reset'
                ? 'Paramètres réinitialisés.'
                : 'Paramètres enregistrés.';
            adminToast(msg);
        });
    }
}

/* ── Settings page horizontal tabs ───────────────────────── */
function initAdminSettingsTabs() {
    const tabsEl = document.querySelector('[data-settings-tabs]');
    if (!tabsEl) return;

    const tabs = [...tabsEl.querySelectorAll('a')];
    const cards = [...document.querySelectorAll('.mt-5.grid.grid-cols-1 section.rounded-xl')];

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', e => {
            e.preventDefault();

            tabs.forEach(t => {
                t.classList.remove('border-primary-600', 'font-semibold', 'text-primary-600');
                t.classList.add('border-transparent', 'font-medium', 'text-slate-500');
                t.removeAttribute('aria-current');
            });
            tab.classList.add('border-primary-600', 'font-semibold', 'text-primary-600');
            tab.classList.remove('border-transparent', 'font-medium', 'text-slate-500');
            tab.setAttribute('aria-current', 'page');

            const target = cards[i];
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

/* ── Animated stat counters (e.g. "Impact de nos activités") ─────────────
   Counts up from 0 to the target value once the element scrolls into
   view, then disconnects — no repeat animation on re-scroll. Respects
   prefers-reduced-motion by jumping straight to the final value. */
function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    // The markup already prints the real figure, so an older browser
    // without IntersectionObserver simply keeps it: no animation, nothing
    // missing. Same when scripts are off entirely.
    if (!counters.length || !('IntersectionObserver' in window)) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const duration = 1200;

    const animate = (el) => {
        const target = parseInt(el.dataset.counterTarget, 10) || 0;
        const suffix = el.dataset.counterSuffix || '';

        // Leave the printed figure alone when motion is unwelcome.
        if (prefersReducedMotion) return;

        const start = performance.now();

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(target * eased);
            el.textContent = current.toLocaleString('fr-FR') + suffix;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animate(entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.4 });

    counters.forEach(el => observer.observe(el));
}
