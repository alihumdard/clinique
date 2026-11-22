{{--
    The Statut field of a create/edit form. A membre does not choose one:
    the server files every save of theirs as "en attente de validation",
    so the form says that instead of offering a select it would override.
--}}
@props([
    'id',
    'value' => 'published',
    'required' => false,
])

@if(auth()->user()?->managesContent())
    <div {{ $attributes }}>
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
            Statut
            @if($required)
                <span class="text-rose-600" aria-hidden="true">*</span>
                <span class="sr-only">(obligatoire)</span>
            @endif
        </label>
        <select id="{{ $id }}" name="status" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 pr-9 text-sm text-slate-700 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
            @if($value === 'pending')
                <option value="pending" selected>En attente de validation</option>
            @endif
            <option value="published" @selected($value === 'published')>Publié</option>
            <option value="draft" @selected($value === 'draft')>Brouillon</option>
            <option value="archived" @selected($value === 'archived')>Archivé</option>
        </select>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'col-span-full']) }}>
        <input type="hidden" name="status" value="pending">
        <p class="flex items-start gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3.5 py-2.5 text-sm text-violet-800">
            <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
            Votre contribution sera envoyée au coordinateur, qui la publiera après validation.
        </p>
    </div>
@endif
