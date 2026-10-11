{{--
    Donut chart drawn as plain SVG, with the breakdown rendered beside it
    as a real list. Segments come from stroke-dasharray on concentric
    circles, so no charting library and no script are needed.

    $items: [['label' => ..., 'value' => int, 'color' => '#hex'], ...]
--}}
@props([
    'items' => [],
    'total' => null,
    'totalLabel' => 'Éléments',
])

@php
    $sum = array_sum(array_column($items, 'value')) ?: 1;
    $displayTotal = $total ?? $sum;

    // r chosen so the circumference is a round number to divide.
    $r = 60;
    $circumference = 2 * M_PI * $r;

    $offset = 0;
    $segments = [];

    foreach ($items as $item) {
        $fraction = $item['value'] / $sum;
        $length = $fraction * $circumference;

        $segments[] = [
            'color' => $item['color'],
            'dash' => round($length, 2).' '.round($circumference - $length, 2),
            // Negative offset walks each segment clockwise from 12 o'clock.
            'offset' => round(-$offset, 2),
            'percent' => round($fraction * 100),
        ];

        $offset += $length;
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-6 sm:flex-row sm:items-center']) }}>

    <div class="relative shrink-0">
        <svg viewBox="0 0 160 160" class="h-40 w-40" role="img" aria-label="Répartition du contenu par type">
            <g transform="rotate(-90 80 80)">
                <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="currentColor" stroke-width="22" class="text-slate-100" />

                @foreach ($segments as $segment)
                    <circle
                        cx="80" cy="80" r="{{ $r }}"
                        fill="none"
                        stroke="{{ $segment['color'] }}"
                        stroke-width="22"
                        stroke-dasharray="{{ $segment['dash'] }}"
                        stroke-dashoffset="{{ $segment['offset'] }}"
                    />
                @endforeach
            </g>
        </svg>

        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-2xl font-bold text-slate-900">{{ $displayTotal }}</span>
            <span class="text-xs text-slate-500">{{ $totalLabel }}</span>
        </div>
    </div>

    {{-- The list is the accessible equivalent of the chart, not a decoration --}}
    <dl class="min-w-0 flex-1 space-y-2.5">
        @foreach ($items as $i => $item)
            <div class="flex items-center gap-2.5">
                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $item['color'] }}" aria-hidden="true"></span>
                <dt class="min-w-0 flex-1 truncate text-sm text-slate-600">{{ $item['label'] }}</dt>
                <dd class="shrink-0 text-sm font-semibold text-slate-900">
                    {{ $item['value'] }}
                    <span class="font-normal text-slate-500">({{ $segments[$i]['percent'] }}%)</span>
                </dd>
            </div>
        @endforeach
    </dl>
</div>
