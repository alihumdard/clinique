{{--
    The fields of a Registre account, shared by the create and the edit
    dialog so the two can never drift apart — a create form missing a
    field the edit form has is how an account ends up half-filled.
--}}
@props([
    'roles',
    'prefix',
    'account' => null,
    'withPassword' => false,
])

<div>
    <label for="{{ $prefix }}-name" class="block text-sm font-semibold text-slate-700">Nom <span class="text-rose-600">*</span></label>
    <input
        type="text"
        id="{{ $prefix }}-name"
        name="name"
        required
        value="{{ old('name', $account?->name) }}"
        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    >
</div>

<div>
    <label for="{{ $prefix }}-email" class="block text-sm font-semibold text-slate-700">Adresse e-mail <span class="text-rose-600">*</span></label>
    <input
        type="email"
        id="{{ $prefix }}-email"
        name="email"
        required
        value="{{ old('email', $account?->email) }}"
        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    >
</div>

<div>
    <label for="{{ $prefix }}-role" class="block text-sm font-semibold text-slate-700">Profil <span class="text-rose-600">*</span></label>
    <select
        id="{{ $prefix }}-role"
        name="role"
        required
        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    >
        @foreach ($roles as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $account?->role) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <p class="mt-1.5 text-xs text-slate-500">
        Les droits attachés à chaque profil sont en attente de validation par la Clinique.
    </p>
</div>

@if($withPassword)
    <x-registre.password-fields :prefix="$prefix" />
@endif
