<header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
            <x-icon-badge size="sm">
                <x-icon name="scale" class="h-5 w-5" />
            </x-icon-badge>
            <span class="leading-tight">
                <span class="block text-sm font-bold text-slate-900">Clinique Juridique</span>
                <span class="block text-xs font-medium text-slate-500">FSJP - UAM</span>
            </span>
        </a>

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Navigation principale">
            <a href="{{ route('home') }}" class="border-b-2 border-primary-600 pb-0.5 text-sm font-semibold text-primary-600">Accueil</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">À propos</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Nos services</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Actualités</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Ressources</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Web TV</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Nous rejoindre</a>
            <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600">Contact</a>
        </nav>

        <div class="hidden items-center gap-4 lg:flex">
            <button type="button" aria-label="Rechercher" class="text-slate-500 hover:text-primary-600">
                <x-icon name="search" class="h-5 w-5" />
            </button>
            <x-button href="#" variant="primary" class="!rounded-full">Pré-saisine</x-button>
        </div>

        <button
            type="button"
            id="mobile-menu-button"
            class="inline-flex items-center justify-center rounded-md p-2 text-slate-700 lg:hidden"
            aria-expanded="false"
            aria-controls="mobile-menu"
            aria-label="Ouvrir le menu"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <nav id="mobile-menu" class="hidden flex-col gap-1 border-t border-slate-200 px-4 py-3 lg:hidden" aria-label="Navigation mobile">
        <a href="{{ route('home') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-primary-600">Accueil</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">À propos</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Nos services</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Actualités</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Ressources</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Web TV</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Nous rejoindre</a>
        <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Contact</a>
        <x-button href="#" variant="primary" class="mt-1 w-full justify-center !rounded-full">Pré-saisine</x-button>
    </nav>
</header>
