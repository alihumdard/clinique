{{--
    Multi-series line chart drawn as plain SVG.

    No charting library: the performance plan forbids remote scripts and a
    library would add ~60 kB plus a CSP exception for a panel that shows
    static figures. The path is computed in PHP at render time.

    $series: [['label' => ..., 'color' => '#hex', 'points' => [int, ...]], ...]
    $labels: x-axis captions, spread evenly across the plot.
--}}
@props([
    'series' => [],
    'labels' => [],
    'max' => null,
    'height' => 260,
])

@php
    // Viewbox units; the SVG scales to its container.
    $w = 720;
    $h = 260;
    $padL = 34;
    $padR = 10;
    $padT = 12;
    $padB = 28;

    $plotW = $w - $padL - $padR;
    $plotH = $h - $padT - $padB;

    $allPoints = collect($series)->flatMap(fn ($s) => $s['points'] ?? [])->all();
    $ceiling = $max ?? max(1, (int) ceil((max($allPoints ?: [1])) / 10) * 10);

    $gridLines = 4;

    /**
     * Maps a series to an SVG polyline point list.
     */
    $plot = function (array $points) use ($padL, $padT, $plotW, $plotH, $ceiling) {
        $count = max(1, count($points) - 1);

        return collect($points)
            ->map(function ($value, $i) use ($padL, $padT, $plotW, $plotH, $ceiling, $count) {
                $x = $padL + ($i / $count) * $plotW;
                $y = $padT + $plotH - (min($value, $ceiling) / $ceiling) * $plotH;

                return round($x, 1).','.round($y, 1);
            })
            ->implode(' ');
    };
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <svg
        viewBox="0 0 {{ $w }} {{ $h }}"
        class="h-auto w-full"
        style="max-height: {{ $height }}px"
        role="img"
        aria-label="Évolution des publications sur la période"
    >
        {{-- Horizontal grid and y-axis scale --}}
        @for ($i = 0; $i <= $gridLines; $i++)
            @php
                $y = $padT + ($i / $gridLines) * $plotH;
                $value = round($ceiling - ($i / $gridLines) * $ceiling);
            @endphp

            <line
                x1="{{ $padL }}" y1="{{ round($y, 1) }}"
                x2="{{ $w - $padR }}" y2="{{ round($y, 1) }}"
                stroke="currentColor" stroke-width="1"
                class="text-slate-200"
                stroke-dasharray="{{ $i === $gridLines ? '0' : '3 4' }}"
            />

            <text
                x="{{ $padL - 8 }}" y="{{ round($y + 4, 1) }}"
                text-anchor="end"
                class="fill-slate-400"
                style="font-size: 11px"
            >{{ $value }}</text>
        @endfor

        {{-- Series lines with their data points --}}
        @foreach ($series as $s)
            @php $pts = $plot($s['points'] ?? []); @endphp

            <polyline
                points="{{ $pts }}"
                fill="none"
                stroke="{{ $s['color'] }}"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            @foreach (explode(' ', $pts) as $pt)
                @php [$px, $py] = explode(',', $pt); @endphp
                <circle cx="{{ $px }}" cy="{{ $py }}" r="2.5" fill="{{ $s['color'] }}" />
            @endforeach
        @endforeach

        {{-- X-axis captions --}}
        @foreach ($labels as $i => $label)
            @php
                $count = max(1, count($labels) - 1);
                $x = $padL + ($i / $count) * $plotW;
            @endphp

            <text
                x="{{ round($x, 1) }}" y="{{ $h - 8 }}"
                text-anchor="middle"
                class="fill-slate-400"
                style="font-size: 11px"
            >{{ $label }}</text>
        @endforeach
    </svg>

    {{-- Legend doubles as the text alternative to the plotted lines --}}
    <ul class="mt-4 flex flex-wrap items-center justify-center gap-5">
        @foreach ($series as $s)
            <li class="flex items-center gap-2 text-xs text-slate-600">
                <span class="h-0.5 w-5 rounded" style="background-color: {{ $s['color'] }}" aria-hidden="true"></span>
                {{ $s['label'] }}
            </li>
        @endforeach
    </ul>
</div>
