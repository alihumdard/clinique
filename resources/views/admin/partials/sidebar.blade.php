{{--
    Admin sidebar: fixed on desktop, slide-over drawer on mobile. The nav
    list lives here once, so adding a management screen touches one file.
--}}
@php
    /*
     * Dashboard sidebar, in the order the approved mockup shows it.
     * Screens still to be built point at the dashboard for now rather
     * than producing a dead link.
     */
    /*
     * Dashboard sidebar, in the order the approved mockup shows it.
     * An item may carry `children`; the group expands while one of its
     * routes is active. Screens still to be built resolve to the
     * dashboard rather than producing a dead link.
     */
    $nav = [
        ['label' => 'Tableau de bord',        'icon' => 'home',        'route' => 'admin.dashboard'],
        ['label' => 'Actualités',             'icon' => 'document',    'route' => 'admin.news'],
        ['label' => 'Web TV',                 'icon' => 'play-circle', 'route' => 'admin.webtv'],
        ['label' => 'Fiches pratiques',       'icon' => 'book',        'route' => 'admin.guides'],
        [
            'label' => 'Activités & caravanes',
            'icon' => 'calendar',
            'route' => 'admin.activities',
            'children' => [
                ['label' => 'Toutes les activités', 'route' => 'admin.activities'],
                ['label' => 'Ajouter une activité', 'route' => 'admin.activities.create'],
                ['label' => 'Galerie photos',       'route' => 'admin.gallery'],
            ],
        ],
        ['label' => 'Appels à candidatures',  'icon' => 'megaphone',   'route' => 'admin.calls'],
        ['label' => 'Candidatures',           'icon' => 'users',       'route' => 'admin.applications'],
        ['label' => 'Galerie photos',         'icon' => 'image',       'route' => 'admin.gallery'],
        [
            'label' => 'Pages',
            'icon' => 'folder',
            'route' => 'admin.pages',
            'children' => [
                ['label' => 'Toutes les pages', 'route' => 'admin.pages'],
                ['label' => 'Ajouter une page', 'route' => 'admin.pages.create'],
                ['label' => 'Catégories',       'route' => 'admin.pages.categories'],
            ],
        ],
        ['label' => 'Médias',                 'icon' => 'image',       'route' => 'admin.media'],
        ['label' => 'Rapports annuels',       'icon' => 'document',    'route' => 'admin.reports'],
        ['label' => 'Partenaires',            'icon' => 'handshake',   'route' => 'admin.partners'],
        ['label' => 'Utilisateurs',           'icon' => 'users',       'route' => 'admin.users'],
        ['label' => 'Commentaires',           'icon' => 'chat',        'route' => 'admin.comments'],
        ['label' => 'Paramètres du site',     'icon' => 'settings',    'route' => 'admin.settings'],
        ['label' => 'Newsletter',             'icon' => 'mail',        'route' => 'admin.newsletter'],
    ];

    // Resolves a route name to a URL, falling back to the dashboard while
    // a screen is still being built.
    $linkTo = fn (string $name) => Route::has($name) ? route($name) : route('admin.dashboard');
@endphp

{{-- Backdrop, shown only while the mobile drawer is open --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy-900 transition-transform duration-200 lg:translate-x-0"
    aria-label="Navigation de l'administration"
>
    <div class="flex items-center gap-3 px-5 py-5">
        <img src="{{ asset('images/brand/logo.png') }}" alt="Clinique Juridique FSJP - UAM" class="h-10 w-10 shrink-0 rounded-full object-cover" width="40" height="40">
        <span class="min-w-0 leading-tight text-white">
            <span class="block text-sm font-bold tracking-wide">CLINIQUE JURIDIQUE</span>
            <span class="block text-sm font-bold tracking-wide">FSJP - UAM</span>
            <span class="mt-1 block text-[10px] leading-snug text-slate-400">
                {{ config('clinique.faculty') }}
            </span>
        </span>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 pb-4">
        <ul class="space-y-1">
            @foreach ($nav as $item)
                @php
                    $children = $item['children'] ?? [];
                    $isActive = Route::is($item['route']);
                    // A parent counts as open while any of its children is active.
                    $isOpen = $isActive || collect($children)->contains(fn ($c) => Route::is($c['route']));
                @endphp

                <li>
                    <a
                        href="{{ $linkTo($item['route']) }}"
                        @if($isActive) aria-current="page" @endif
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm {{ $isOpen ? 'bg-primary-600 font-semibold text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}"
                    >
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                        <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>

                        @if(count($children))
                            <x-icon
                                name="chevron-up"
                                class="h-4 w-4 shrink-0 transition-transform {{ $isOpen ? '' : 'rotate-180' }}"
                            />
                        @endif
                    </a>

                    @if(count($children) && $isOpen)
                        <ul class="mt-1 space-y-0.5 rounded-lg bg-white/5 py-1.5">
                            @foreach ($children as $child)
                                @php $childActive = Route::is($child['route']); @endphp
                                <li>
                                    <a
                                        href="{{ $linkTo($child['route']) }}"
                                        @if($childActive) aria-current="page" @endif
                                        class="flex items-center gap-2.5 py-2 pl-11 pr-3 text-sm {{ $childActive ? 'font-semibold text-white' : 'text-slate-300 hover:text-white' }}"
                                    >
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $childActive ? 'bg-white' : 'bg-slate-500' }}" aria-hidden="true"></span>
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="border-t border-white/10 px-5 py-4">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-slate-300 hover:text-white">
            Retour au site public
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </a>
    </div>

    <div class="flex items-center gap-3 border-t border-white/10 px-5 py-4">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-white">
            <x-icon name="users" class="h-5 w-5" />
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()?->name ?? 'Administrateur' }}</p>
            <p class="truncate text-xs text-slate-400">{{ auth()->user()?->email }}</p>
        </div>

        {{-- POST: signing out changes state, so it must not be reachable by a link --}}
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" aria-label="Se déconnecter" class="shrink-0 text-slate-400 hover:text-white">
                <x-icon name="logout" class="h-5 w-5" />
            </button>
        </form>
    </div>
</aside>
