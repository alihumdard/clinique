{{--
    Bandeau d'état du service — the strip the Clinic switches on to tell
    visitors the permanence is open, closed, or running under an exception
    (jour férié, caravane en région). Required by the Phase 2 plan:
    "Bandeau d'état du service — Activer, désactiver et modifier le message".

    Module 2 reads these from CMS settings; the defaults below come from
    config/clinique.php so the strip already shows real hours.
--}}
@props([
    // null means "ask the Clinic's setting", which is the normal case.
    // A caller may still pass a state, which is what the tests do.
    'state' => null,
    'message' => null,
])

@php
    /*
     * The state comes from Paramètres du site, not from the template.
     *
     * It was written in as state="open", so the strip announced
     * "Permanences ouvertes" every day of the year — including the days
     * nobody is there. It is a claim about something real, and the only
     * people who know it are the Clinic's, so they set it. Absent, it
     * stays open, which is what the page said before and what is true
     * most of the time.
     */
    $state ??= \App\Support\SiteSettings::values()['service.state'] ?? 'open';
@endphp

@php
    $states = [
        'open' => [
            'panel' => 'bg-emerald-50 border-emerald-200',
            'dot' => 'bg-emerald-500',
            'text' => 'text-emerald-900',
            'label' => 'Permanences ouvertes',
        ],
        'closed' => [
            'panel' => 'bg-slate-100 border-slate-200',
            'dot' => 'bg-slate-400',
            'text' => 'text-slate-700',
            'label' => 'Permanences fermées',
        ],
        'notice' => [
            'panel' => 'bg-amber-50 border-amber-200',
            'dot' => 'bg-amber-500',
            'text' => 'text-amber-900',
            'label' => 'Information importante',
        ],
    ];

    $s = $states[$state] ?? $states['open'];

    $default = sprintf(
        '%s : %s · %s',
        config('clinique.hours.days'),
        config('clinique.hours.time'),
        config('clinique.hours.note')
    );
@endphp

<div {{ $attributes->merge(['class' => 'border-b ' . $s['panel']]) }} role="status">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm sm:px-6 lg:px-8">
        {{-- The dot is decorative; the label carries the state in words. --}}
        <span class="flex items-center gap-2 font-semibold {{ $s['text'] }}">
            <span class="h-2 w-2 shrink-0 rounded-full {{ $s['dot'] }}" aria-hidden="true"></span>
            {{ $s['label'] }}
        </span>

        <span class="{{ $s['text'] }} opacity-90">{{ $message ?? $default }}</span>

        <a
            href="{{ route('contact') }}"
            class="ml-auto hidden shrink-0 items-center gap-1 font-semibold underline-offset-2 hover:underline {{ $s['text'] }} sm:inline-flex"
        >
            Nous joindre
            <x-icon name="arrow-right" class="h-3.5 w-3.5" />
        </a>
    </div>
</div>
