<footer class="relative bg-navy-900 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <x-icon-badge size="sm" class="!bg-white/10 !text-white">
                        <x-icon name="scale" class="h-5 w-5" />
                    </x-icon-badge>
                    <span class="leading-tight">
                        <span class="block text-sm font-bold text-white">Clinique Juridique</span>
                        <span class="block text-xs font-medium text-slate-400">FSJP - UAM</span>
                    </span>
                </a>
                <p class="mt-4 text-sm text-slate-400">
                    Au service de la justice, de la communauté et de la formation des futurs professionnels du droit.
                </p>
                <x-social-links class="mt-5" />
            </div>

            <div>
                <h2 class="text-sm font-semibold text-white">Liens rapides</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Accueil</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white">À propos</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Nos services</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white">Actualités</a></li>
                    <li><a href="{{ route('resources') }}" class="hover:text-white">Ressources</a></li>
                    <li><a href="{{ route('webtv.index') }}" class="hover:text-white">Web TV</a></li>
                    <li><a href="{{ route('join') }}" class="hover:text-white">Nous rejoindre</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-white">Nos services</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('services') }}" class="hover:text-white">Consultations juridiques</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Information et orientation</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Accompagnement juridique</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Formations et sensibilisation</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white">Ressources juridiques</a></li>
                    <li><a href="{{ route('services.process') }}" class="hover:text-white">Processus de consultation</a></li>
                    <li><a href="{{ route('services.eligibility') }}" class="hover:text-white">Conditions d'accès</a></li>
                    <li><a href="{{ route('services.fees') }}" class="hover:text-white">Tarifs administratifs</a></li>
                </ul>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-white">Contact</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="flex items-start gap-2.5">
                        <x-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
                        <span>{{ config('clinique.contact.address.line1') }}<br>{{ config('clinique.contact.address.line2') }}, {{ config('clinique.contact.address.city') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <x-icon name="phone" class="h-4 w-4 shrink-0 text-slate-400" />
                        <a href="tel:{{ config('clinique.contact.phone.tel') }}" class="hover:text-white">{{ config('clinique.contact.phone.display') }}</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <a href="mailto:{{ config('clinique.contact.email') }}" class="hover:text-white">{{ config('clinique.contact.email') }}</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <x-icon name="clock" class="h-4 w-4 shrink-0 text-slate-400" />
                        <span>{{ config('clinique.hours.days') }} : {{ config('clinique.hours.time') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <x-icon name="whatsapp" class="h-4 w-4 shrink-0 text-slate-400" />
                        <a href="https://wa.me/{{ config('clinique.contact.whatsapp.number') }}" target="_blank" rel="noopener noreferrer" class="hover:text-white">WhatsApp</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} Clinique Juridique FSJP-UAM. Tous droits réservés.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('legal.notice') }}" class="hover:text-white">Mentions légales</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-white">Politique de confidentialité</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-white">Conditions d'utilisation</a>
            </div>
        </div>
    </div>

    <button
        type="button"
        id="back-to-top"
        aria-label="Retour en haut de page"
        class="absolute -top-6 right-6 flex h-12 w-12 items-center justify-center rounded-full bg-slate-700 text-white shadow-lg hover:bg-slate-600"
    >
        <x-icon name="arrow-up" class="h-5 w-5" />
    </button>
</footer>
