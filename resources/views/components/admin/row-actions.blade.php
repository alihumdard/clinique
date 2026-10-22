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
    'viewHref' => null,
    'duplicate' => true,
    'galleryHref' => null,
    'deleteAction' => null,
])

@php
    // relative: without it, the sr-only label's absolute positioning falls
    // back to a further-out ancestor and can widen the document's scrollable
    // area even though it's visually clipped inside a scrolling table wrapper.
    $base = 'relative flex h-8 w-8 items-center justify-center rounded-md transition-colors';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-end gap-1']) }}>

    <a href="{{ $editHref ?? '#' }}" class="{{ $base }} text-primary-600 hover:bg-primary-50">
        <x-icon name="pencil" class="h-4 w-4" />
        <span class="sr-only">Modifier{{ $label ? ' : '.$label : '' }}</span>
    </a>

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

    @if($duplicate)
        <button type="button" class="{{ $base }} text-slate-500 hover:bg-slate-100">
            <x-icon name="copy" class="h-4 w-4" />
            <span class="sr-only">Dupliquer{{ $label ? ' : '.$label : '' }}</span>
        </button>
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
