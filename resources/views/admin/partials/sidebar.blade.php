{{--
    Admin sidebar: fixed on desktop, slide-over drawer on mobile.

    The list is grouped rather than flat: sixteen items in one run forced a
    scroll on a laptop screen and gave the eye nothing to anchor on. The
    groups below match how the work divides — publishing, people, the site
    itself — so a screen is found by its group first.

    The nav lives here once, so adding a screen touches one file.
--}}
@php
    /*
     * Each group is [label, items]. An item may carry `children`, and the
     * group expands while one of its routes is active. Screens still to be
     * built resolve to the dashboard rather than producing a dead link.
     */
    /*
     * The twelve screens the Phase 2 plan lists, grouped by the work they
     * belong to. Items are added here only once their screen exists —
     * Médias, Candidatures, Utilisateurs and Commentaires were in the
     * dashboard mockup but are not in the approved inventory, and linking
     * to them raised a 500.
     */
    $navGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Tableau de bord', 'icon' => 'home', 'filled' => true, 'route' => 'admin.dashboard'],
            ],
        ],
        [
            'label' => 'Contenus',
            'items' => [
                ['label' => 'Actualités',       'icon' => 'document',    'route' => 'admin.news'],
                ['label' => 'Web TV',           'icon' => 'play-circle', 'filled' => true, 'route' => 'admin.webtv'],
                ['label' => 'Fiches pratiques', 'icon' => 'book',        'route' => 'admin.guides'],
                ['label' => 'Rapports annuels', 'icon' => 'chart-bar',   'route' => 'admin.reports'],
                [
                    'label' => 'Pages',
                    'icon' => 'folder',
                    'route' => 'admin.pages',
                ],
            ],
        ],
        [
            'label' => 'Terrain',
            'items' => [
                [
                    'label' => 'Appels à candidatures',
                    'icon' => 'user-plus',
                    'route' => 'admin.calls',
                ],
                [
                    'label' => 'Activités & caravanes',
                    'icon' => 'calendar',
                    'route' => 'admin.activities',
                    'children' => [
                        ['label' => 'Toutes les activités', 'route' => 'admin.activities'],
                        ['label' => 'Ajouter une activité', 'route' => 'admin.activities', 'dialog' => 'create-activity'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Personnes',
            'items' => [
                ['label' => 'Équipe',      'icon' => 'users',     'route' => 'admin.team'],
                ['label' => 'Partenaires', 'icon' => 'globe', 'route' => 'admin.partners'],
            ],
        ],
        [
            'label' => 'Administration',
            'items' => [
                ['label' => 'Messages de contact', 'icon' => 'chat',    'route' => 'admin.messages'],
                ['label' => 'Newsletter',         'icon' => 'mail',     'route' => 'admin.newsletter'],
                ['label' => 'Référentiels',       'icon' => 'folder',   'route' => 'admin.references'],
                ['label' => 'Paramètres du site', 'icon' => 'settings', 'route' => 'admin.settings'],
            ],
        ],
    ];

    // Resolves a route name to a URL, falling back to the dashboard while
    // a screen is still being built.
    $linkTo = fn (string $name) => Route::has($name) ? route($name) : route('admin.dashboard');

    /*
     * A child may open its parent screen's "Ajouter" dialog rather than
     * linking to a screen of its own — the dialog isn't a page, so the
     * link navigates with ?ouvrir=<dialog-id> (read by app.js on load)
     * instead of an anchor, and does not claim the active state.
     */
    $childHref = fn (array $child) => $linkTo($child['route'])
        .(isset($child['dialog']) ? '?ouvrir='.$child['dialog'] : '');
@endphp

{{-- Backdrop, shown only while the mobile drawer is open --}}
<div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

<aside
    id="admin-sidebar"
    {{-- h-screen, not inset-y-0: an explicit height is what gives the nav
         below a real box to scroll inside. --}}
    class="admin-sidebar fixed left-0 top-0 z-40 flex h-screen w-72 -translate-x-full flex-col shadow-xl transition-transform duration-200 lg:w-64 lg:translate-x-0 lg:shadow-none"
    aria-label="Navigation de l'administration"
>
    {{-- Brand: fixed height so the nav below gets the rest --}}
    <div class="flex shrink-0 items-center gap-3 border-b border-white/10 px-5 py-4">
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex min-w-0 items-center gap-3">
            <img
                src="{{ asset('images/brand/logo.png') }}"
                alt=""
                class="h-10 w-10 shrink-0 rounded-full bg-white/10 object-cover"
                width="40" height="40"
            >
            <span class="min-w-0 leading-tight text-white">
                <span class="block truncate text-sm font-bold tracking-wide">CLINIQUE JURIDIQUE</span>
                <span class="block truncate text-sm font-bold tracking-wide">FSJP - UAM</span>
            </span>
        </a>

        {{-- Close control for the mobile drawer; the backdrop also closes it --}}
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

    {{--
        The nav scrolls on its own when the list outruns the viewport;
        overscroll-contain stops the page behind it scrolling too. The
        wrapper is relative so the fade below can hint there's more to
        scroll to — on a phone/tablet drawer the thin custom scrollbar
        alone wasn't a strong enough cue that "Administration" existed.
    --}}
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
                            @class([
                                'admin-nav-link group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition',
                                'is-active' => $isOpen,
                            ])
                        >
                            @if(!empty($item['filled']) && $item['icon'] === 'home')
                                {{-- Filled house: the shared `home` path is outline-only --}}
                                <svg class="admin-nav-icon h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 11-1.06 1.06l-1.17-1.17V19.5a1.5 1.5 0 01-1.5 1.5h-3a.75.75 0 01-.75-.75V15a.75.75 0 00-.75-.75h-1.5a.75.75 0 00-.75.75v5.25a.75.75 0 01-.75.75h-3a1.5 1.5 0 01-1.5-1.5v-7.07l-1.17 1.17a.75.75 0 11-1.06-1.06l8.69-8.69z" />
                                </svg>
                            @elseif(!empty($item['filled']) && $item['icon'] === 'play-circle')
                                {{--
                                    Filled play button: the shared play-circle path is
                                    outline-only. The triangle is fixed to the sidebar's
                                    own dark navy rather than white, so it still reads
                                    against the circle once .is-active forces the circle
                                    itself (currentColor) to white.
                                --}}
                                <svg class="admin-nav-icon h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M10 8.5l6 3.5-6 3.5z" fill="#0b1f38" />
                                </svg>
                            @else
                                <x-icon
                                    :name="$item['icon']"
                                    class="admin-nav-icon h-5 w-5 shrink-0"
                                />
                            @endif
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>

                            @if(count($children))
                                <x-icon
                                    name="chevron-up"
                                    class="h-4 w-4 shrink-0 transition-transform {{ $isOpen ? '' : 'rotate-180' }}"
                                />
                            @endif
                        </a>

                        @if(count($children) && $isOpen)
                            <ul class="mt-1 space-y-0.5 border-l border-white/10 pl-3 ml-5">
                                @foreach ($children as $child)
                                    @php $childActive = Route::is($child['route']) && ! isset($child['dialog']); @endphp
                                    <li>
                                        <a
                                            href="{{ $childHref($child) }}"
                                            @if($childActive) aria-current="page" @endif
                                            @class([
                                                'admin-nav-sublink flex items-center gap-2.5 rounded-md px-3 py-2 text-sm transition',
                                                'is-active' => $childActive,
                                            ])
                                        >
                                            <span class="admin-nav-dot h-1.5 w-1.5 shrink-0 rounded-full" aria-hidden="true"></span>
                                            <span class="min-w-0 truncate">{{ $child['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach
        </nav>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-[#0b1f38] to-transparent" aria-hidden="true"></div>
    </div>

    {{-- Footer: fixed, never scrolls away from the sign-out control --}}
    <div class="shrink-0 border-t border-white/10">
        <a
            href="{{ route('home') }}"
            target="_blank"
            rel="noopener"
            class="admin-nav-link flex items-center gap-2.5 px-5 py-3 text-sm transition"
        >
            <x-icon name="globe" class="admin-nav-icon h-4 w-4 shrink-0" />
            Retour au site public
            <svg class="ml-auto h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </a>

        <div class="flex items-center gap-3 border-t border-white/10 px-5 py-3.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">
                {{ strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 1)) }}
            </span>

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">{{ auth()->user()?->name ?? 'Administrateur' }}</p>
                <p class="truncate text-xs text-slate-300">{{ auth()->user()?->email }}</p>
            </div>

            {{-- POST: signing out changes state, so it must not be reachable by a link --}}
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button
                    type="submit"
                    aria-label="Se déconnecter"
                    class="shrink-0 rounded-lg p-1.5 text-slate-200 transition hover:bg-white/10 hover:text-rose-300"
                >
                    <x-icon name="logout" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</aside>
