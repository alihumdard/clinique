{{-- Sticky admin topbar: drawer toggle, global search, alerts, account. --}}
<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-sm">
    <div class="flex items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">

        {{-- Mobile menu toggle --}}
        <button
            type="button"
            id="admin-sidebar-toggle"
            class="shrink-0 rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden"
            aria-label="Ouvrir le menu"
            aria-expanded="false"
            aria-controls="admin-sidebar"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{--
            Global search. Rendered only on a screen that declares where it
            searches, because the form had no action at all: it submitted to
            the current URL, and on the four screens whose controller never
            reads `q` — the dashboard you land on after signing in,
            paramètres, référentiels and comptes — the page simply
            re-rendered unchanged. A box that does nothing is worse than no
            box, so the screens that cannot search no longer show one.
        --}}
        @hasSection('search_action')
        <form role="search" method="GET" action="@yield('search_action')" class="relative min-w-0 flex-1 sm:max-w-sm">
            <label for="admin-search" class="sr-only">Rechercher</label>
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <x-icon name="search" class="h-4 w-4" />
            </span>
            <input
                type="search"
                id="admin-search"
                name="q"
                placeholder="@yield('search_placeholder', 'Rechercher...')"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            >
        </form>
        @endif

        {{-- Right actions --}}
        <div class="ml-auto flex shrink-0 items-center gap-1 sm:gap-2">

            {{--
                Unhandled contact messages, counted. The badge used to read
                a hard-coded "3" and link to the page you were already on,
                while /admin/notifications did not exist — three
                notifications nobody could open. It now counts something
                real and goes where that something is, and it disappears
                when there is nothing to report rather than inventing a
                figure. Administrators only: an éditeur is refused
                /admin/messages, so a badge pointing there would hand them
                a 403.
            --}}
            @if(auth()->user()?->isAdmin() && $unhandledMessages > 0)
                <a
                    href="{{ route('admin.messages') }}"
                    class="relative rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="{{ $unhandledMessages }} {{ $unhandledMessages === 1 ? 'message non traité' : 'messages non traités' }}"
                >
                    <x-icon name="bell" class="h-5 w-5" />
                    <span class="absolute right-1.5 top-1.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white" aria-hidden="true">{{ min($unhandledMessages, 9) }}</span>
                </a>

                {{-- Divider --}}
                <div class="mx-1 hidden h-6 w-px bg-slate-200 sm:block"></div>
            @endif

            {{-- User. A link, because this is where someone looks for
                 their own account. --}}
            <a href="{{ route('admin.profile') }}" title="Mon compte" class="flex items-center gap-2.5 rounded-lg px-1.5 py-1 hover:bg-slate-100">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-xs font-bold text-primary-700">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                </span>
                <div class="hidden leading-tight sm:block">
                    <p class="max-w-30 truncate text-sm font-semibold text-slate-900">{{ auth()->user()?->name ?? 'Administrateur' }}</p>
                    {{-- The account's real role: "Super Admin" was printed
                         for everyone, including an éditeur de contenu. --}}
                    <p class="text-xs text-slate-400">{{ auth()->user()?->roleLabel() }}</p>
                </div>
            </a>
        </div>
    </div>
</header>
