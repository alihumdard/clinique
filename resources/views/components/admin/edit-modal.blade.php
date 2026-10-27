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
--}}
<dialog id="{{ $id }}" class="fixed inset-0 m-auto w-full max-w-lg rounded-xl p-0 backdrop:bg-slate-900/50">
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="flex max-h-[85vh] flex-col">
        @csrf
        @if(strtoupper($method) !== 'POST')
            @method($method)
        @endif

        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-bold text-slate-900">{{ $title }}</h2>
            <button type="button" data-dialog-close class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                <x-icon name="plus" class="h-5 w-5 rotate-45" />
                <span class="sr-only">Fermer</span>
            </button>
        </div>

        <div class="space-y-4 overflow-y-auto px-5 py-5">
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
