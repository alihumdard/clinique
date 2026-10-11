{{--
    Sidebar category row: label, optional colour-coded icon, and a count.
    The icon is opt-in so the plain news-page list keeps its current look.
--}}
@props([
    'active' => false,
    'count',
    'href' => null,
    'icon' => null,
    'color' => 'blue',
])

@php
    $swatches = [
        'blue'   => 'bg-primary-50 text-primary-600',
        'green'  => 'bg-emerald-50 text-emerald-600',
        'amber'  => 'bg-amber-50 text-amber-600',
        'red'    => 'bg-rose-50 text-rose-600',
        'purple' => 'bg-violet-50 text-violet-600',
        'pink'   => 'bg-pink-50 text-pink-600',
        'slate'  => 'bg-slate-100 text-slate-600',
    ];
@endphp

<a
    href="{{ $href ?? url()->current() }}"
    class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm {{ $active ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-slate-50' }}"
>
    @if($icon)
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded {{ $swatches[$color] ?? $swatches['blue'] }}">
            <x-icon :name="$icon" class="h-3.5 w-3.5" />
        </span>
    @endif

    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>

    <span class="shrink-0 text-xs {{ $active ? 'text-primary-600' : 'text-slate-400' }}">{{ $count }}</span>
</a>
