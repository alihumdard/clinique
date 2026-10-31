@props([
    'icon',
    'value',
    'label',
    'trend' => null,
    'note' => null,
    'color' => 'blue',
    // Makes the card the way in to what it counts. The corbeille was
    // reachable only by setting the statut filter by hand, which did not
    // matter while nothing could be restored from it, and does now.
    'href' => null,
    // Set on the one or two cards per screen that carry the figure the
    // screen is about — "Actualités publiées" on the actualités listing.
    // Those stay at zero, because an empty listing saying nothing at all
    // reads as a page that failed to load.
    'keepAtZero' => false,
])

@php
    /*
     * A card counting nothing is not drawn.
     *
     * Four cards sat above every listing whatever the data, so a fresh
     * Clinic met a row of zeros — "0 brouillons", "0 à la corbeille",
     * "0 reçus cette semaine" — which says nothing except that the
     * figures are real. The row now holds what there is, and the screen
     * is quiet until there is something to report.
     *
     * Numeric zero only: a card is kept when its value is a string the
     * caller meant to show, such as a date or "—".
     */
    $isEmpty = ! $keepAtZero
        && (is_int($value) || is_float($value) || (is_string($value) && ctype_digit($value)))
        && (int) $value === 0;

    // return, not an @if around the markup: the card's own tag is written
    // as <{{ $href ? 'a' : 'div' }}>, and Blade loses track of a
    // conditional wrapped around a tag whose name it has to compile.
    if ($isEmpty) {
        return;
    }

    $swatches = [
        'blue'   => ['accent' => 'bg-primary-500',  'badge' => 'bg-primary-50 text-primary-600',  'trend' => 'text-primary-600'],
        'green'  => ['accent' => 'bg-emerald-500',  'badge' => 'bg-emerald-50 text-emerald-600',  'trend' => 'text-emerald-600'],
        'amber'  => ['accent' => 'bg-amber-500',    'badge' => 'bg-amber-50 text-amber-600',      'trend' => 'text-amber-600'],
        'purple' => ['accent' => 'bg-violet-500',   'badge' => 'bg-violet-50 text-violet-600',    'trend' => 'text-violet-600'],
        'red'    => ['accent' => 'bg-rose-500',     'badge' => 'bg-rose-50 text-rose-600',        'trend' => 'text-rose-600'],
    ];
    $c = $swatches[$color] ?? $swatches['blue'];

    $shell = 'relative block overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm'
        .($href ? ' transition hover:border-slate-300 hover:shadow-md' : '');
@endphp

<{{ $href ? 'a' : 'div' }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => $shell]) }}
>
    {{-- Colored accent bar on top --}}
    <div class="absolute inset-x-0 top-0 h-1 {{ $c['accent'] }}"></div>

    <div class="pt-1">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $c['badge'] }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>

        <div class="mt-3 min-w-0">
            <p class="text-2xl font-bold leading-tight text-slate-900">{{ $value }}</p>
            <p class="mt-0.5 text-sm font-medium text-slate-600">{{ $label }}</p>

            @if($trend || $note)
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    @if($trend)
                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold {{ $c['trend'] }}">
                            <span aria-hidden="true">&#8599;</span>{{ $trend }}
                        </span>
                    @endif
                    @if($note)
                        <span class="text-xs text-slate-400">{{ $note }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</{{ $href ? 'a' : 'div' }}>
