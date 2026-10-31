{{--
    A dialog-based form — either a per-row "Modifier" (pre-filled, PUT to
    the existing update route) or a page-header "Ajouter" create dialog
    (empty, POST to the existing store route). Same shell either way, only
    the method and whether the slot's fields start blank differ.

    Opened via a [data-open-dialog] trigger (row-actions' pencil icon, or
    the page-header's action button); closed via [data-dialog-close] or a
    backdrop click, both wired in app.js. <dialog> rather than a
    hand-rolled overlay: native focus trap, native Escape-to-close, and no
    extra markup for the backdrop.
--}}
@props(['id', 'title', 'action', 'method' => 'PUT', 'submitLabel' => 'Enregistrer'])

{{--
    Tailwind's preflight zeroes out every element's margin, which is what
    centers a native <dialog> (the UA stylesheet centers it via
    `margin: auto` on a fixed-position box) — so that margin is restored
    explicitly here, along with the fixed positioning it depends on, rather
    than relying on the browser default the reset already removed.

    max-h, not h: a fixed height always claims the full 85vh even for a
    short form, leaving blank space below the footer; max-h lets a short
    form's dialog shrink to its content while still capping a long form at
    85vh. The grid goes directly on the <dialog> (not on a height:100%
    form nested inside it): a percentage/h-full height only resolves
    against a *definite* ancestor height, and max-height alone doesn't
    give the form one, so its 1fr row was sizing to the slot's full
    content height instead of the dialog's actual, possibly-capped height
    — overflowing every dialog with more than a couple of fields. The
    <dialog> itself has a real (shrink-to-fit, max-capped) height, so grid
    tracks resolve against that correctly. overflow-hidden on the dialog:
    a native <dialog> scrolls on its own too, so without it both the
    dialog and the inner div would scroll — two scrollbars for one form.
--}}
{{--
    hidden open:grid, not grid: a closed <dialog> is display:none by the
    browser's own stylesheet, and a `grid` utility overrides that — so
    every closed dialog on a screen was being laid out in the page, below
    the content. On a long table they sat under the fold and nobody saw
    them; on a short one they appeared stacked beneath it.
--}}
{{-- aria-labelledby, not aria-label: the visible heading is already the
     dialog's name, and a screen reader announced an unnamed dialog only
     because that heading had no id to point at. --}}
{{--
    overflow-clip rather than overflow-hidden on the dialog itself.

    overflow-hidden hides the scrollbar but leaves the box scrollable by
    script, and the browser scrolls it on its own: clicking the "Cliquez
    pour télécharger" area focuses the sr-only file input inside the
    label, and focusing an element scrolls every scrollable ancestor to
    bring it into view. The dialog slid 298 px under its own header —
    the heading disappeared off the top, the footer ended up halfway up
    the box and the rest was empty white. overflow-clip is not
    scrollable at all, so there is nothing for that to move.

    The inner div keeps overflow-y-auto: the form is meant to scroll,
    just not the frame around it. --}}
<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title" class="m-auto hidden max-h-[85vh] w-[calc(100vw-2rem)] max-w-lg grid-rows-[auto_1fr_auto] overflow-clip rounded-xl p-0 backdrop:bg-slate-900/50 open:grid">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="contents">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif

        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h2 id="{{ $id }}-title" class="text-base font-bold text-slate-900">{{ $title }}</h2>
            <button type="button" data-dialog-close class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <x-icon name="plus" class="h-5 w-5 rotate-45" />
                <span class="sr-only">Fermer</span>
            </button>
        </div>

        {{--
            scrollbar-thin + scrollbar-gutter: Windows/Chrome's overlay
            scrollbar is invisible until the cursor is over the scrolling
            box, so a long form looked like it had no way to scroll at all
            until the user happened to hover it. A reserved gutter keeps a
            thin track always visible instead.
        --}}
        <div class="min-h-0 space-y-4 overflow-y-auto px-5 py-5 [scrollbar-gutter:stable] [scrollbar-width:thin]">
            {{ $slot }}
        </div>

        <div class="flex items-center justify-end gap-2.5 border-t border-slate-200 px-5 py-4">
            <button type="button" data-dialog-close class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Annuler
            </button>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                <x-icon name="save" class="h-4 w-4" />
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</dialog>
