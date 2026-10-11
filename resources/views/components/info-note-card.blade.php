@props(['icon', 'title' => null, 'color' => 'blue'])

@php
    $colors = [
        'blue' => ['bg' => 'bg-primary-50', 'badge' => 'bg-primary-100 text-primary-600'],
        'amber' => ['bg' => 'bg-amber-50', 'badge' => 'bg-amber-100 text-amber-600'],
        'green' => ['bg' => 'bg-emerald-50', 'badge' => 'bg-emerald-100 text-emerald-600'],
    ];
    $c = $colors[$color] ?? $colors['blue'];
@endphp

<div class="flex items-start gap-4 rounded-xl {{ $c['bg'] }} p-6">
    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $c['badge'] }}">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <div>
        @if($title)
            <p class="font-semibold text-slate-900">{{ $title }}</p>
        @endif
        <p class="{{ $title ? 'mt-1 ' : '' }}text-sm leading-relaxed text-slate-600">{{ $slot }}</p>
    </div>
</div>
