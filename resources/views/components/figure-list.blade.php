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
                @if(($item['icon'] ?? null) === 'youtube')
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.56A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.56 9.38.56 9.38.56s7.5 0 9.38-.56a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
                @else
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                @endif
            </span>
            <div class="min-w-0">
                <dd class="text-xl font-bold leading-tight text-slate-900">{{ $item['value'] }}</dd>
                <dt class="text-xs text-slate-600">{{ $item['label'] }}</dt>
            </div>
        </div>
    @endforeach
</dl>
