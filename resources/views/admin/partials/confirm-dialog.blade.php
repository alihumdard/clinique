{{--
    The confirmation an action asks before it runs.

    This replaces window.confirm(). The browser's own box is drawn by the
    operating system: it carries the address "127.0.0.1:8123 says" above
    the question, cannot be styled, puts its buttons in the OS order
    rather than ours, and reads in the browser's language while the rest
    of this administration is in French. Beside the screens it interrupts
    it looked like a security warning rather than a question the Clinic
    is asking.

    One dialog for the whole area, filled in by app.js from the form that
    asked. A <dialog> and not a hand-rolled overlay, like every other
    dialog here: the focus trap and Escape come from the browser.

    The wording is the form's, not this file's — moving a row to the
    corbeille is reversible and a permanent delete is not, so each form
    carries its own data-confirm and its own button label.
--}}
<dialog
    id="confirm-dialog"
    aria-labelledby="confirm-dialog-title"
    class="m-auto hidden w-[calc(100vw-2rem)] max-w-md overflow-clip rounded-xl p-0 backdrop:bg-slate-900/50 open:block"
>
    <div class="flex items-start gap-4 px-5 pt-5">
        {{-- The icon carries the tone, so the text does not have to shout. --}}
        <span id="confirm-dialog-icon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
            <x-icon name="trash" class="h-5 w-5" />
        </span>

        <div class="min-w-0 pt-0.5">
            <h2 id="confirm-dialog-title" class="text-base font-bold text-slate-900">Confirmer l'action</h2>
            {{-- Filled from the form's data-confirm. Line breaks in that
                 attribute become real lines here, which is how the second
                 sentence ("Vous pourrez le restaurer…") stays readable. --}}
            <p id="confirm-dialog-message" class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-600"></p>
        </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2.5 border-t border-slate-200 px-5 py-4">
        <button
            type="button"
            data-confirm-cancel
            class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
        >
            Annuler
        </button>
        <button
            type="button"
            data-confirm-accept
            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700"
        >
            <span id="confirm-dialog-accept-label">Confirmer</span>
        </button>
    </div>
</dialog>
