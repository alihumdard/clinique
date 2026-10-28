{{-- Sticky topbar: drawer toggle, account, sign-out. Same shell as the
     administration's, so the two read as one product. --}}
@php $agent = auth('registre')->user(); @endphp

<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-sm">
    <div class="flex items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">

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

        {{-- Said in words rather than by a different palette: this is the
             confidential area, and every move in it is recorded. --}}
        <p class="hidden min-w-0 flex-1 items-center gap-2 text-xs text-slate-500 sm:flex">
            <x-icon name="lock-shield" class="h-4 w-4 shrink-0 text-amber-500" />
            <span class="truncate">Espace confidentiel — chaque consultation est enregistrée</span>
        </p>

        <div class="ml-auto flex items-center gap-2.5">
            <a href="{{ route('registre.profile') }}" title="Mon compte" class="flex items-center gap-2.5 rounded-lg px-1.5 py-1 hover:bg-slate-100">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-xs font-bold text-primary-700">
                    {{ mb_strtoupper(mb_substr($agent?->name ?? 'R', 0, 1)) }}
                </span>
                <span class="hidden leading-tight sm:block">
                    <span class="block max-w-40 truncate text-sm font-semibold text-slate-900">{{ $agent?->name }}</span>
                    <span class="block text-xs text-slate-400">{{ $agent?->roleLabel() }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('registre.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</header>
