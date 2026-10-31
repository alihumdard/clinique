{{--
    Edit / preview / duplicate / delete controls for a table row.

    Each control names the row it acts on via sr-only text, so a screen
    reader user is never left with a list of identical "Modifier" buttons.
    Delete is a submit button inside a form, never a link: a GET request
    must not change state, and the form carries the CSRF token.
--}}
@props([
    'label' => '',
    'editHref' => null,
    // The id of an admin.edit-modal component on the same page — takes
    // priority over editHref, since every list screen now edits via a
    // dialog rather than a separate page.
    'editDialog' => null,
    'viewHref' => null,
    // A route that streams the row's own file (a guide's PDF, a report's
    // PDF) straight from the list, without going through the public page.
    'downloadHref' => null,
    // A route to POST to for a real duplicate (a draft copy); null hides
    // the button entirely rather than showing a control that does nothing.
    'duplicateAction' => null,
    'galleryHref' => null,
    'deleteAction' => null,
    // For list types that are shown or hidden rather than scheduled.
    'toggleAction' => null,
    'isVisible' => true,
    // Set on a row that is in the corbeille. Either one switches this
    // component to the two controls a deleted row can actually carry.
    'restoreAction' => null,
    'forceDeleteAction' => null,
])

@php
    // relative: without it, the sr-only label's absolute positioning falls
    // back to a further-out ancestor and can widen the document's scrollable
    // area even though it's visually clipped inside a scrolling table wrapper.
    $base = 'relative flex h-8 w-8 items-center justify-center rounded-md transition-colors';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-1']) }}>

@if($restoreAction || $forceDeleteAction)

    {{--
        A row in the corbeille. It deliberately offers neither Modifier nor
        Aperçu: the update route binds the model, SoftDeletes excludes a
        deleted one, so submitting the edit is answered with a 404 — and
        the 404 is the public one, which throws the administrator out of
        the administration entirely. Restore the row and its four ordinary
        controls come back with it.
    --}}

    @if($restoreAction)
        <form method="POST" action="{{ $restoreAction }}" class="contents">
            @csrf
            <button type="submit" class="{{ $base }} text-emerald-600 hover:bg-emerald-50">
                <x-icon name="refresh" class="h-4 w-4" />
                <span class="sr-only">Restaurer{{ $label ? ' : '.$label : '' }}</span>
            </button>
        </form>
    @endif

    @if($forceDeleteAction)
        {{-- Administrator only, and the one delete in the CMS of which
             "irréversible" is actually true. --}}
        <form
            method="POST"
            action="{{ $forceDeleteAction }}"
            class="contents"
            data-confirm="Supprimer définitivement {{ $label ? '« '.$label.' »' : 'cet élément' }} ?&#10;Cette action est irréversible."
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="{{ $base }} text-rose-700 hover:bg-rose-50">
                <x-icon name="trash" class="h-4 w-4" />
                <span class="sr-only">Supprimer définitivement{{ $label ? ' : '.$label : '' }}</span>
            </button>
        </form>
    @endif

@else

    @if($editDialog)
        <button type="button" data-open-dialog="{{ $editDialog }}" class="{{ $base }} text-primary-600 hover:bg-primary-50">
            <x-icon name="pencil" class="h-4 w-4" />
            <span class="sr-only">Modifier{{ $label ? ' : '.$label : '' }}</span>
        </button>
    @elseif($editHref)
        <a href="{{ $editHref }}" class="{{ $base }} text-primary-600 hover:bg-primary-50">
            <x-icon name="pencil" class="h-4 w-4" />
            <span class="sr-only">Modifier{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    {{-- A draft has no public page to preview, so there is nothing to
         point at. An href="#" reloads the page, which reads as the screen
         refusing to do anything rather than as the control being absent. --}}
    @if($viewHref)
        <a href="{{ $viewHref }}" class="{{ $base }} text-slate-500 hover:bg-slate-100">
            <x-icon name="eye" class="h-4 w-4" />
            <span class="sr-only">Aperçu{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    @if($downloadHref)
        <a href="{{ $downloadHref }}" class="{{ $base }} text-slate-500 hover:bg-slate-100">
            <x-icon name="download" class="h-4 w-4" />
            <span class="sr-only">Télécharger{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    @if($galleryHref)
        <a href="{{ $galleryHref }}" class="{{ $base }} text-slate-500 hover:bg-slate-100">
            <x-icon name="image" class="h-4 w-4" />
            <span class="sr-only">Galerie photos{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    @if($toggleAction)
        <form method="POST" action="{{ $toggleAction }}" class="contents">
            @csrf
            {{--
                A pill-and-dot switch rather than an eye icon: "Aperçu"
                already uses an eye, and the two actions do different
                things (preview the public page vs. show/hide this row on
                it) — a second eye made them look like the same control.
            --}}
            <button type="submit" class="{{ $base }} {{ $isVisible ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="2" y="7" width="20" height="10" rx="5" stroke="currentColor" stroke-width="1.75" fill="{{ $isVisible ? 'currentColor' : 'none' }}" fill-opacity="0.15" />
                    <circle cx="{{ $isVisible ? '16' : '8' }}" cy="12" r="3.25" fill="currentColor" />
                </svg>
                <span class="sr-only">{{ $isVisible ? 'Masquer' : 'Afficher' }}{{ $label ? ' : '.$label : '' }}</span>
            </button>
        </form>
    @endif

    @if($duplicateAction)
        <form method="POST" action="{{ $duplicateAction }}" class="contents">
            @csrf
            <button type="submit" class="{{ $base }} text-slate-500 hover:bg-slate-100">
                <x-icon name="copy" class="h-4 w-4" />
                <span class="sr-only">Dupliquer{{ $label ? ' : '.$label : '' }}</span>
            </button>
        </form>
    @endif

    {{-- The form carries its own wording, because this delete moves the
         row to the corbeille: calling it irreversible contradicted the
         design, and was only true while nothing could be restored. --}}
    @if($deleteAction)
    <form
        method="POST"
        action="{{ $deleteAction }}"
        class="contents"
        data-confirm="Mettre {{ $label ? '« '.$label.' »' : 'cet élément' }} à la corbeille ?&#10;Vous pourrez le restaurer depuis le filtre « Corbeille »."
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ $base }} text-rose-600 hover:bg-rose-50">
            <x-icon name="trash" class="h-4 w-4" />
            <span class="sr-only">Supprimer{{ $label ? ' : '.$label : '' }}</span>
        </button>
    </form>
    @endif

@endif
</div>
