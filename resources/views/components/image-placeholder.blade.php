{{-- Temporary placeholder until real photos are supplied by the Clinic. Pure CSS, zero HTTP weight. --}}
@props(['label' => ''])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 text-center']) }}>
    @if($label)
        <span class="px-4 text-xs font-medium text-slate-500">{{ $label }}</span>
    @endif
</div>
