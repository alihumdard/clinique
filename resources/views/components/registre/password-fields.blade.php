{{--
    A new password and its confirmation. Twelve characters, which is what
    the security plan asks of a production account.
--}}
@props(['prefix', 'label' => 'Mot de passe'])

<div>
    <label for="{{ $prefix }}-password" class="block text-sm font-semibold text-slate-700">{{ $label }} <span class="text-rose-600">*</span></label>
    <input
        type="password"
        id="{{ $prefix }}-password"
        name="password"
        required
        minlength="12"
        autocomplete="new-password"
        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    >
    <p class="mt-1.5 text-xs text-slate-500">Douze caractères au minimum.</p>
</div>

<div>
    <label for="{{ $prefix }}-password-confirm" class="block text-sm font-semibold text-slate-700">Confirmer <span class="text-rose-600">*</span></label>
    <input
        type="password"
        id="{{ $prefix }}-password-confirm"
        name="password_confirmation"
        required
        minlength="12"
        autocomplete="new-password"
        class="mt-1.5 w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
    >
</div>
