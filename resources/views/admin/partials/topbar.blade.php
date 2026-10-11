{{-- Sticky admin topbar: drawer toggle, global search, alerts, account. --}}
<header class="sticky top-0 z-20 flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 lg:px-8">

    <button
        type="button"
        id="admin-sidebar-toggle"
        class="shrink-0 rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
        aria-label="Ouvrir le menu"
        aria-expanded="false"
        aria-controls="admin-sidebar"
    >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <form role="search" class="relative min-w-0 flex-1 sm:max-w-xl">
        <label for="admin-search" class="sr-only">Rechercher dans l'administration</label>

        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            <x-icon name="search" class="h-5 w-5" />
        </span>

        <input
            type="search"
            id="admin-search"
            name="q"
            placeholder="@yield('search_placeholder', 'Rechercher une actualité, une vidéo, un utilisateur...')"
            class="w-full rounded-lg border border-slate-200 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
        >
    </form>

    <div class="ml-auto flex shrink-0 items-center gap-3">
        <a
            href="{{ route('admin.dashboard') }}"
            class="relative rounded-md p-2 text-slate-500 hover:bg-slate-100"
            aria-label="Notifications : 3 non lues"
        >
            <x-icon name="bell" class="h-5 w-5" />
            <span class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white" aria-hidden="true">3</span>
        </a>

        <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <x-icon name="users" class="h-5 w-5" />
            </span>
            <span class="hidden min-w-0 leading-tight sm:block">
                <span class="block truncate text-sm font-semibold text-slate-900">{{ auth()->user()?->name ?? 'Administrateur' }}</span>
                <span class="block truncate text-xs text-slate-500">Super Admin</span>
            </span>
        </div>
    </div>
</header>
