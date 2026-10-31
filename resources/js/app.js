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
            openDialog(dialog);

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
        openDialog(document.getElementById(openParam));
    }

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.querySelectorAll('[data-dialog-close]').forEach((button) => {
            button.addEventListener('click', () => dialog.close());
        });

        /*
         * Click on the backdrop closes the dialog — but only when the
         * gesture started there.
         *
         * The target of a click is the common ancestor of where the
         * button went down and where it came up, so selecting the video
         * link by dragging across it and releasing a few pixels past the
         * field put the dialog itself in e.target. A drag that overshot
         * the form was read as a click on the backdrop, and the form
         * closed with everything typed into it still unsaved.
         *
         * Recording where the press began separates the two: a real
         * backdrop click goes down on the backdrop, a text selection
         * goes down inside the form.
         */
        let pressedOnBackdrop = false;

        dialog.addEventListener('mousedown', (e) => {
            pressedOnBackdrop = e.target === dialog;
        });

        dialog.addEventListener('click', (e) => {
            if (e.target === dialog && pressedOnBackdrop) dialog.close();
            pressedOnBackdrop = false;
        });

        /*
         * Hold the page still while the dialog is open.
         *
         * showModal() takes the page out of the dialog's reach but not
         * out of the wheel's: with the pointer anywhere outside the
         * form's own scroll box the document kept scrolling underneath,
         * and since the dialog is fixed and centred the content slid
         * away behind it. Selecting the video link with ctrl+A was the
         * way this was met — the browser scrolls to what it selects, and
         * the form appeared to jump.
         *
         * It is released on the dialog's own close event, which fires
         * whichever way it was closed — the button, the backdrop,
         * Escape, a submit — so no path can leave the page frozen. The
         * guard is for the one screen that opens a second dialog over
         * the first: the last one to close releases the lock.
         */
        dialog.addEventListener('close', () => {
            if (!document.querySelector('dialog[open]')) {
                lockPageScroll(false);
            }
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

    initConfirmDialog();
    initAdminScrollRestore();
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

/* ── Filter tabs ─────────────────────────────────────────────
   There is deliberately no handler here any more.

   The tabs were links to a real query — ?vue=passees, ?statut=brouillon —
   and this file called preventDefault() on them, then tried to do the
   same work in the browser: hide the rows whose text did not contain a
   keyword taken from the tab's own label.

   It could not work, for two reasons that compound.

   It filtered the rows on the page, and a page holds ten of them. On
   "Passées (11)" it hid everything that was not past among the ten
   visible, while the eleventh sat on page two — and the footer went on
   counting the unfiltered total, so the screen read "1 à 10 sur 14" with
   an empty table underneath.

   And the keywords were French: a map of 'passées' → 'terminé' written
   into this file. With the interface in English the labels are "Past"
   and "Coming soon", nothing matched, and every tab emptied the table.

   The controller has always done this properly: it reads the parameter,
   filters in the query and paginates what is left. Letting the link
   through is the whole fix. The live search box above still filters as
   you type, because that one searches what you are looking at and does
   not claim a total. */

/* ── Where the focus lands when a dialog opens ────────
   showModal() focuses the first focusable element, which is the close
   button, because that is what comes first in the markup. So opening a
   form put the caret on the way out of it. The first real field is what
   someone opening "Ajouter" means to type in. */
/*
 * Opening is showModal() plus the page lock, never one without the
 * other: a dialog opened by some other path would scroll the document
 * behind itself, which is the defect this pairing exists to stop. The
 * matching release is on each dialog's close event.
 */
function openDialog(dialog) {
    if (!dialog) return;

    dialog.showModal();
    lockPageScroll(true);
    focusFirstField(dialog);
}

/*
 * The lock goes on <html>, not <body>.
 *
 * document.scrollingElement is the html element here, and the admin
 * body has already grown to the full height of its content — so
 * overflow-hidden on the body (what the mobile drawer uses, and what
 * this first tried) changes nothing at all: the box it would clip is
 * not the box that scrolls. Measured with the dialog open: body
 * scrollHeight 1759 = clientHeight 1759, while html was 1759 against a
 * 651 viewport.
 *
 * The scrollbar's width is given back as padding, otherwise the page
 * widens by ~15 px the moment a dialog opens and the whole layout
 * shifts sideways under the backdrop.
 */
function lockPageScroll(locked) {
    const root = document.documentElement;

    if (!locked) {
        root.style.overflow = '';
        root.style.paddingRight = '';
        return;
    }

    const gap = window.innerWidth - root.clientWidth;

    root.style.overflow = 'hidden';
    if (gap > 0) root.style.paddingRight = `${gap}px`;
}

function focusFirstField(dialog) {
    if (!dialog) return;

    const field = dialog.querySelector(
        'input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled])'
    );

    // preventScroll: a trigger that opens the dialog at a particular
    // section (data-scroll-to) would otherwise be undone by the focus
    // jumping back to the top.
    field?.focus({ preventScroll: true });
}

/* ── Asking before an action runs ────────────────────────────
   One handler for both questions, and one dialog for the whole area.

   This used to call window.confirm(). That box is the operating
   system's: it prints "127.0.0.1:8123 says" above the question, cannot
   be styled, orders its buttons the OS way rather than ours, and labels
   them in the browser's language while everything around it is French.
   Next to these screens it read as a security warning rather than as a
   question the Clinic is asking.

   confirm() is synchronous and a <dialog> is not, so the submit is
   always stopped first and the form submitted again once the answer is
   yes. requestSubmit() rather than submit(): submit() skips the submit
   event, which is what every other handler here listens to, so a form
   confirmed this way would bypass them.

   A delete is not one question. Moving a row to the corbeille is
   reversible and a permanent delete is not, so a form states what to
   ask in data-confirm rather than this file guessing from a label. The
   fallback below covers the few screens whose rows have no corbeille —
   references, messages, newsletter — where "irréversible" is true. */
function initConfirmDialog() {
    const dialog = document.getElementById('confirm-dialog');
    if (!dialog) return;

    const message = dialog.querySelector('#confirm-dialog-message');
    const acceptLabel = dialog.querySelector('#confirm-dialog-accept-label');
    const accept = dialog.querySelector('[data-confirm-accept]');
    const cancel = dialog.querySelector('[data-confirm-cancel]');

    // The form waiting on an answer, and the flag that lets its second
    // submit through without asking again.
    let pending = null;

    document.addEventListener('submit', e => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.dataset.confirmed === 'yes') return;

        const declared = form.dataset.confirm;
        const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');
        if (!declared && !isDelete) return;

        let text = declared;
        if (!text) {
            const srOnly = form.querySelector('.sr-only');
            const item = srOnly?.textContent.replace('Supprimer', '').replace(':', '').trim() || 'cet élément';
            text = `Supprimer « ${item} » ?\nCette action est irréversible.`;
        }

        e.preventDefault();
        e.stopImmediatePropagation();

        pending = form;
        message.textContent = text;
        // "Supprimer définitivement" on the one delete that cannot be
        // undone, so the button says what it will do rather than "OK".
        acceptLabel.textContent = /irréversible/i.test(text) ? 'Supprimer définitivement' : 'Confirmer';
        openDialog(dialog);
    }, true);

    accept.addEventListener('click', () => {
        const form = pending;
        pending = null;
        dialog.close();

        if (!form) return;
        form.dataset.confirmed = 'yes';
        form.requestSubmit();
    });

    cancel.addEventListener('click', () => dialog.close());

    // Escape and the backdrop reach close() without touching the buttons,
    // so the pending form is dropped here rather than in each handler.
    dialog.addEventListener('close', () => { pending = null; });
}

/* ── Keeping the place after an action reloads the screen ───
   A row action — Supprimer, Dupliquer, Masquer — posts and the
   controller answers with a redirect, so the browser loads a fresh
   document at the top. On the dashboard the recent-articles panel sits
   below a chart, so deleting a row from it threw the administrator back
   to the top of the page, away from the row they were working on and
   from the confirmation itself.

   The position is written just before the form leaves, and read back on
   the next load of that same path. sessionStorage, not history.state:
   the answer is a redirect to a new document, which carries no state of
   ours. Keyed by path so a different screen never inherits an offset
   that means nothing on it.

   Restoring happens here rather than on load: the browser's own
   scrollRestoration has already run by then, and the markup above the
   fold is laid out, so the position is the one the page was left at. */
function initAdminScrollRestore() {
    const KEY = 'admin:scroll';

    document.addEventListener('submit', e => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        // Only the row and panel actions inside the administration, and
        // never a dialog's own form: that one closes and the screen is
        // meant to settle wherever the redirect puts it.
        if (!form.closest('.admin-shell')) return;

        try {
            sessionStorage.setItem(KEY, JSON.stringify({
                path: location.pathname + location.search,
                y: window.scrollY,
            }));
        } catch { /* private mode: the page simply opens at the top */ }
    }, true);

    let saved = null;
    try {
        saved = JSON.parse(sessionStorage.getItem(KEY) || 'null');
        sessionStorage.removeItem(KEY);
    } catch { /* unreadable: nothing to restore */ }

    if (!saved || saved.path !== location.pathname + location.search) return;
    if (!Number.isFinite(saved.y) || saved.y <= 0) return;

    // 'instant': the page was already scrolled when it was left, so an
    // animation would show a journey the administrator did not make.
    window.scrollTo({ top: saved.y, behavior: 'instant' });
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
