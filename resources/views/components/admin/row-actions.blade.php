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
    // A route to POST to for a real duplicate (a draft copy); null hides
    // the button entirely rather than showing a control that does nothing.
    'duplicateAction' => null,
    'galleryHref' => null,
    'deleteAction' => null,
    // For list types that are shown or hidden rather than scheduled.
    'toggleAction' => null,
    'isVisible' => true,
])

@php
    // relative: without it, the sr-only label's absolute positioning falls
    // back to a further-out ancestor and can widen the document's scrollable
    // area even though it's visually clipped inside a scrolling table wrapper.
    $base = 'relative flex h-8 w-8 items-center justify-center rounded-md transition-colors';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-1']) }}>

    @if($editDialog)
        <button type="button" data-open-dialog="{{ $editDialog }}" class="{{ $base }} text-primary-600 hover:bg-primary-50">
            <x-icon name="pencil" class="h-4 w-4" />
            <span class="sr-only">Modifier{{ $label ? ' : '.$label : '' }}</span>
        </button>
    @else
        <a href="{{ $editHref ?? '#' }}" class="{{ $base }} text-primary-600 hover:bg-primary-50">
            <x-icon name="pencil" class="h-4 w-4" />
            <span class="sr-only">Modifier{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    <a href="{{ $viewHref ?? '#' }}" class="{{ $base }} text-slate-500 hover:bg-slate-100">
        <x-icon name="eye" class="h-4 w-4" />
        <span class="sr-only">Aperçu{{ $label ? ' : '.$label : '' }}</span>
    </a>

    @if($galleryHref)
        <a href="{{ $galleryHref }}" class="{{ $base }} text-slate-500 hover:bg-slate-100">
            <x-icon name="image" class="h-4 w-4" />
            <span class="sr-only">Galerie photos{{ $label ? ' : '.$label : '' }}</span>
        </a>
    @endif

    @if($toggleAction)
        <form method="POST" action="{{ $toggleAction }}" class="contents">
            @csrf
            <button type="submit" class="{{ $base }} {{ $isVisible ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100' }}">
                <x-icon :name="$isVisible ? 'eye' : 'eye-off'" class="h-4 w-4" />
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

    <form method="POST" action="{{ $deleteAction ?? '#' }}" class="contents">
        @csrf
        @method('DELETE')
        <button type="submit" class="{{ $base }} text-rose-600 hover:bg-rose-50">
            <x-icon name="trash" class="h-4 w-4" />
            <span class="sr-only">Supprimer{{ $label ? ' : '.$label : '' }}</span>
        </button>
    </form>
</div>
