{{--
    Status tabs above a management table ("Toutes / Publiées / Brouillons /
    Archivées"). Rendered as links so each view is addressable and the
    browser's back button behaves; Module 2 points them at real queries.

    $tabs: [['label' => ..., 'count' => int|null, 'active' => bool, 'href' => ...]]
--}}
@props(['tabs' => []])

<div {{ $attributes->merge(['class' => 'flex gap-6 overflow-x-auto border-b border-slate-200']) }}>
    @foreach ($tabs as $tab)
        @php $active = $tab['active'] ?? false; @endphp

        <a
            href="{{ $tab['href'] ?? url()->current() }}"
            @if($active) aria-current="page" @endif
            class="shrink-0 border-b-2 pb-3 text-sm transition-colors {{ $active ? 'border-primary-600 font-semibold text-primary-600' : 'border-transparent font-medium text-slate-500 hover:text-primary-600' }}"
        >
            {{ $tab['label'] }}@isset($tab['count']) ({{ $tab['count'] }})@endisset
        </a>
    @endforeach
</div>
