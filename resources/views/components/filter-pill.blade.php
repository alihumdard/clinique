@props(['active' => false, 'href' => null])

<a
    href="{{ $href ?? url()->current() }}"
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center whitespace-nowrap rounded-full px-3.5 py-1.5 text-sm font-medium transition-colors ' . ($active ? 'bg-primary-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50')]) }}
>
    {{ $slot }}
</a>
