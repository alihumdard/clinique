{{--
    Registre sidebar.

    Same shell as the administration's — same classes, same drawer
    behaviour, same active state — because it is the same product. What
    tells the two apart is the badge below the name, not a different
    palette: someone with both open should know which one they are in
    without having to read the page, and that is a one-line distinction,
    not a reason to look like a different piece of software.
--}}
@php
    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Tableau de bord', 'icon' => 'home', 'filled' => true, 'route' => 'registre.dashboard'],
            ],
        ],
        [
            'label' => 'Dossiers',
            'items' => [
                ['label' => 'Demandes en ligne', 'icon' => 'mail', 'route' => 'registre.submissions'],
                ['label' => 'Dossiers', 'icon' => 'folder', 'route' => 'registre.dossiers'],
            ],
        ],
        [
            'label' => 'Suivi',
            'items' => [
                ['label' => "Journal d'accès", 'icon' => 'lock-shield', 'route' => 'registre.journal'],
            ],
        ],
        [
            'label' => 'Comptes',
            'items' => [
                ['label' => 'Comptes du registre', 'icon' => 'users', 'route' => 'registre.accounts'],
                ['label' => 'Mon compte', 'icon' => 'user', 'route' => 'registre.profile'],
            ],
        ],
    ];
@endphp

<div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

<aside
    id="admin-sidebar"
    class="admin-sidebar fixed left-0 top-0 z-40 flex h-screen w-72 -translate-x-full flex-col shadow-xl transition-transform duration-200 lg:w-64 lg:translate-x-0 lg:shadow-none"
    aria-label="Navigation du registre"
>
    <div class="flex shrink-0 items-center gap-3 border-b border-white/10 px-5 py-4">
        <span class="flex min-w-0 items-center gap-3">
            <img
                src="{{ asset('images/brand/logo.png') }}"
                alt=""
                class="h-10 w-10 shrink-0 rounded-full bg-white/10 object-cover"
                width="40" height="40"
            >
            <span class="min-w-0 leading-tight text-white">
                <span class="block truncate text-sm font-bold tracking-wide">CLINIQUE JURIDIQUE</span>
                <span class="block truncate text-sm font-bold tracking-wide">FSJP - UAM</span>
                {{-- The one mark that says which area this is. --}}
                <span class="mt-1 inline-block rounded bg-amber-400/20 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-300">
                    Registre numérique
                </span>
            </span>
        </span>

        <button
            type="button"
            id="admin-sidebar-close"
            class="ml-auto shrink-0 rounded-lg p-1.5 text-slate-200 transition hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Fermer le menu"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="relative min-h-0 flex-1">
        <nav class="admin-sidebar-scroll h-full overflow-y-auto overscroll-contain px-3 py-4">
            @foreach ($navGroups as $group)
                @if($group['label'])
                    <p class="px-3 pb-2 pt-4 text-[11px] font-bold uppercase tracking-wider text-slate-300">
                        {{ $group['label'] }}
                    </p>
                @endif

                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php $isActive = Route::is($item['route'].'*'); @endphp

                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @if($isActive) aria-current="page" @endif
                                @class([
                                    'admin-nav-link group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition',
                                    'is-active' => $isActive,
                                ])
                            >
                                @if(!empty($item['filled']) && $item['icon'] === 'home')
                                    <svg class="admin-nav-icon h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 11-1.06 1.06l-1.17-1.17V19.5a1.5 1.5 0 01-1.5 1.5h-3a.75.75 0 01-.75-.75V15a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v5.25a.75.75 0 01-.75.75h-3a1.5 1.5 0 01-1.5-1.5v-7.07l-1.17 1.17a.75.75 0 11-1.06-1.06l8.69-8.69z" />
                                    </svg>
                                @else
                                    <x-icon :name="$item['icon']" class="admin-nav-icon h-5 w-5 shrink-0" />
                                @endif

                                <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>
    </div>

    <div class="shrink-0 border-t border-white/10 px-3 py-3">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm">
            <x-icon name="globe" class="admin-nav-icon h-5 w-5 shrink-0" />
            <span class="truncate">Retour au site public</span>
            <x-icon name="link" class="ml-auto h-3.5 w-3.5 shrink-0 opacity-60" />
        </a>
    </div>
</aside>
