@props(['active' => false, 'count', 'href' => '#'])

<a
    href="{{ $href }}"
    class="flex items-center justify-between rounded-md px-3 py-2 text-sm {{ $active ? 'bg-primary-50 font-semibold text-primary-700' : 'text-slate-600 hover:bg-slate-50' }}"
>
    <span>{{ $slot }}</span>
    <span class="text-xs {{ $active ? 'text-primary-600' : 'text-slate-400' }}">{{ $count }}</span>
</a>
