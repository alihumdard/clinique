{{--
    Sidebar navigation shared by the mandatory legal pages. Keeping the list
    here means adding a legal page touches one file, not three.
--}}
@props(['current' => null])

@php
    $links = [
        ['key' => 'notice',        'label' => 'Mentions légales',             'icon' => 'document', 'route' => 'legal.notice'],
        ['key' => 'terms',         'label' => "Conditions d'utilisation",      'icon' => 'document', 'route' => 'legal.terms'],
        ['key' => 'privacy',       'label' => 'Politique de confidentialité',  'icon' => 'shield',   'route' => 'legal.privacy'],
        ['key' => 'cookies',       'label' => 'Politique des cookies',         'icon' => 'info',     'route' => null],
        ['key' => 'accessibility', 'label' => 'Accessibilité',                 'icon' => 'users',    'route' => null],
        ['key' => 'credits',       'label' => 'Crédits',                       'icon' => 'users',    'route' => null],
        ['key' => 'contact',       'label' => 'Contact',                       'icon' => 'mail',     'route' => 'contact'],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl bg-slate-50']) }} aria-label="Pages légales">
    <ul>
        @foreach ($links as $link)
            @php
                $isCurrent = $current === $link['key'];
                // Pages not yet built fall back to the legal notice so the
                // nav never carries a dead link.
                $href = $link['route'] ? route($link['route']) : route('legal.notice');
            @endphp

            <li>
                <a
                    href="{{ $href }}"
                    @if($isCurrent) aria-current="page" @endif
                    class="flex items-center gap-3 px-5 py-3.5 text-sm {{ $isCurrent ? 'bg-primary-600 font-semibold text-white' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    <x-icon :name="$link['icon']" class="h-5 w-5 shrink-0 {{ $isCurrent ? 'text-white' : 'text-slate-400' }}" />
                    {{ $link['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
