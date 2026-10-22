@props(['icon', 'title', 'color' => 'primary', 'link' => true, 'compact' => false, 'href' => null])

@php
    $colors = [
        'primary' => 'bg-primary-50 text-primary-600',
        'amber' => 'bg-amber-50 text-amber-500',
        'green' => 'bg-emerald-50 text-emerald-600',
        'purple' => 'bg-violet-50 text-violet-600',
        'red' => 'bg-rose-50 text-rose-600',
        'teal' => 'bg-teal-50 text-teal-600',
    ];
@endphp

<div class="group flex items-start gap-4 rounded-lg border border-slate-200 bg-white transition-all duration-200 hover:-translate-y-1 hover:border-primary-200 hover:bg-primary-50/40 hover:shadow-md {{ $compact ? 'p-5' : 'p-6' }}">
    <span class="flex {{ $compact ? 'h-10 w-10' : 'h-12 w-12' }} shrink-0 items-center justify-center rounded-full {{ $colors[$color] ?? $colors['primary'] }}">
        <x-icon :name="$icon" class="{{ $compact ? 'h-5 w-5' : 'h-6 w-6' }}" />
    </span>
    <div class="min-w-0">
        <h3 class="{{ $compact ? 'text-sm' : 'text-base' }} font-semibold text-slate-900">{{ $title }}</h3>
        <p class="mt-1.5 {{ $compact ? 'text-xs' : 'text-sm' }} leading-relaxed text-slate-600">{{ $slot }}</p>
        @if($link)
            <a href="{{ $href ?? route('services') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                En savoir plus
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        @endif
    </div>
</div>
