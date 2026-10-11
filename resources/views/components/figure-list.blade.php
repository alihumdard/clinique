{{--
    Vertical list of figures with coloured icon tiles ("Chiffres clés",
    "Notre galerie en chiffres"). Sits inside a tinted sidebar panel.

    $items: ['icon','value','label','color'] where color keys $swatches.
--}}
@props([
    'items' => [],
])

@php
    $swatches = [
        'blue'   => 'bg-primary-100 text-primary-600',
        'green'  => 'bg-emerald-100 text-emerald-600',
        'amber'  => 'bg-amber-100 text-amber-600',
        'red'    => 'bg-rose-100 text-rose-600',
        'purple' => 'bg-violet-100 text-violet-600',
    ];
@endphp

<dl {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @foreach ($items as $item)
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $swatches[$item['color'] ?? 'blue'] ?? $swatches['blue'] }}">
                <x-icon :name="$item['icon']" class="h-5 w-5" />
            </span>
            <div class="min-w-0">
                <dd class="text-xl font-bold leading-tight text-slate-900">{{ $item['value'] }}</dd>
                <dt class="text-xs text-slate-600">{{ $item['label'] }}</dt>
            </div>
        </div>
    @endforeach
</dl>
