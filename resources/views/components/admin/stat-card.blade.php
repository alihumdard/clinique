{{--
    Tinted KPI card used across the admin dashboards: icon, figure, label
    and an optional trend line.
--}}
@props([
    'icon',
    'value',
    'label',
    'trend' => null,
    'note' => null,
    'color' => 'blue',
])

@php
    $swatches = [
        'blue'   => ['panel' => 'bg-primary-50/70', 'badge' => 'bg-primary-100 text-primary-600'],
        'green'  => ['panel' => 'bg-emerald-50/70', 'badge' => 'bg-emerald-100 text-emerald-600'],
        'amber'  => ['panel' => 'bg-amber-50/70',   'badge' => 'bg-amber-100 text-amber-600'],
        'purple' => ['panel' => 'bg-violet-50/70',  'badge' => 'bg-violet-100 text-violet-600'],
        'red'    => ['panel' => 'bg-rose-50/70',    'badge' => 'bg-rose-100 text-rose-600'],
    ];
    $c = $swatches[$color] ?? $swatches['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl ' . $c['panel'] . ' p-5']) }}>
    <div class="flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $c['badge'] }}">
            <x-icon :name="$icon" class="h-6 w-6" />
        </span>

        <div class="min-w-0">
            <p class="text-2xl font-bold leading-tight text-slate-900">{{ $value }}</p>
            <p class="mt-0.5 text-sm font-medium text-slate-700">{{ $label }}</p>

            @if($trend)
                {{-- The arrow carries the direction too, so the meaning does
                     not rely on colour alone. --}}
                <p class="mt-2 flex items-center gap-1 text-xs font-semibold text-emerald-600">
                    <span aria-hidden="true">&#8599;</span>
                    {{ $trend }}
                </p>
            @endif

            @if($note)
                <p class="mt-0.5 text-xs text-slate-500">{{ $note }}</p>
            @endif
        </div>
    </div>
</div>
