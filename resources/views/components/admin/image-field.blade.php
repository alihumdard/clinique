{{--
    The image on an existing row: what it is now, how to replace it, and
    how to take it away.

    Until this component there was no way to take one away. "Choose File"
    replaces an image and nothing removes it, so a cover uploaded by
    mistake stayed on the public page for good — the only way out was to
    upload a second picture over it.

    The removal is a checkbox rather than a button because it belongs to
    the form: nothing happens until Enregistrer, so it is undone by
    Annuler like every other change in the dialog, and one trip to the
    server carries both "remove the image" and whatever else was edited.

    An unchecked checkbox posts nothing at all, so the controller reads
    it with $request->boolean() — the absence has to mean false rather
    than "leave it as it was". The checkbox is only rendered when there
    is something to remove; offering it on an empty field would be a
    control that does nothing.

    Props:
      name     the file input's name (image, cover, photo, logo)
      current  the stored path, or null — decides whether removal shows
      preview  a URL for the thumbnail (the model's own accessor)
      id       prefix for the label/input ids, unique per row
      label    the visible field name
      hint     optional sentence under the control
--}}
@props([
    'name',
    'current' => null,
    'preview' => null,
    'id',
    'label' => 'Image',
    'hint' => null,
    'accept' => 'image/jpeg,image/png,image/webp',
])

<div>
    <span class="block text-sm font-medium text-slate-700">{{ $label }}</span>

    <div class="mt-1.5 flex items-center gap-3">
        @if($preview)
            <img src="{{ $preview }}" alt="" class="h-14 w-20 shrink-0 rounded-md border border-slate-200 object-cover">
        @endif

        <input
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            accept="{{ $accept }}"
            class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
        >
    </div>

    @if($current)
        {{-- Only when a stored image exists: a tick-box that removes
             nothing would be one of the dead controls the rest of this
             administration deliberately does not render. --}}
        <label for="{{ $id }}-remove" class="mt-2 inline-flex items-center gap-2 text-sm text-slate-600">
            <input
                type="checkbox"
                id="{{ $id }}-remove"
                name="remove_{{ $name }}"
                value="1"
                class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
            >
            <span>Retirer l'image actuelle</span>
        </label>
    @endif

    @if($hint)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
