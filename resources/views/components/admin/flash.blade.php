{{--
    Confirmation and error messages above an admin screen's content.
    role="status" for the confirmation and role="alert" for the errors, so
    a screen reader announces a failure with the urgency it deserves.
--}}
@if (session('status'))
    <div role="status" class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p class="text-sm text-emerald-800">{{ session('status') }}</p>
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
        <x-icon name="help-circle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
        <div class="min-w-0">
            <p class="text-sm font-semibold text-rose-800">
                {{ $errors->count() === 1 ? "Une erreur empêche l'enregistrement :" : "{$errors->count()} erreurs empêchent l'enregistrement :" }}
            </p>
            <ul class="mt-1.5 space-y-0.5 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
