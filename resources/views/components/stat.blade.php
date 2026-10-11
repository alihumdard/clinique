@props([
    'value',
    'label',
    'variant' => 'card',
])

@if($variant === 'overlay')
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 text-white']) }}>
        {{ $slot }}
        <span class="text-sm font-medium leading-tight">{{ $label }}</span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4']) }}>
        {{ $slot }}
        <div>
            <p class="text-xl font-bold text-slate-900">{{ $value }}</p>
            <p class="text-xs text-slate-500">{{ $label }}</p>
        </div>
    </div>
@endif
