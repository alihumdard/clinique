{{--
    Tinted callout holding a short bulleted list ("À retenir", "Points
    essentiels", "Points clés"). Used throughout the fiches pratiques.
--}}
@props([
    'icon' => 'info',
    'title',
    'points' => [],
    'color' => 'blue',
])

@php
    $swatches = [
        'blue'  => ['bg' => 'bg-primary-50/60', 'badge' => 'bg-primary-100 text-primary-600'],
        'amber' => ['bg' => 'bg-amber-50', 'badge' => 'bg-amber-100 text-amber-600'],
        'green' => ['bg' => 'bg-emerald-50', 'badge' => 'bg-emerald-100 text-emerald-600'],
    ];
    $c = $swatches[$color] ?? $swatches['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-4 rounded-lg ' . $c['bg'] . ' p-5']) }}>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $c['badge'] }}">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>

    <div class="min-w-0">
        <p class="font-semibold text-slate-900">{{ $title }}</p>

        @if(count($points))
            <ul class="mt-2 space-y-1.5">
                @foreach ($points as $point)
                    <li class="flex gap-2 text-sm leading-relaxed text-slate-600">
                        <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-slate-400" aria-hidden="true"></span>
                        <span>{{ $point }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        {{ $slot }}
    </div>
</div>
