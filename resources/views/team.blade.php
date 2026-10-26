@extends('layouts.app')

@section('title', 'Notre équipe - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM est portee par une equipe d'enseignants encadrants, d'etudiants en droit et de professionnels partenaires.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="Notre équipe"
        subtitle="Une équipe engagée au service de la justice et des citoyens"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'La Clinique', 'url' => route('about')],
            ['label' => 'Équipe'],
        ]"
    />

    {{-- ============ INTRO + STATS PANEL ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div>
                <x-eyebrow color="amber">Une équipe pluridisciplinaire</x-eyebrow>
                <h2 class="mt-2 text-3xl font-bold leading-tight text-slate-900">
                    Des professionnels, des enseignants
                    <span class="block">et des étudiants engagés</span>
                </h2>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM est portée par une équipe d'enseignants encadrants,
                    d'étudiants en droit et de professionnels partenaires qui travaillent ensemble pour
                    offrir une assistance juridique gratuite et de qualité à la population.
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <div class="grid grid-cols-2 divide-x divide-y divide-slate-200">
                    @foreach ([
                        ['icon' => 'book',      'key' => 'enseignants',   'title' => 'Enseignants encadrants',    'sub' => 'Experts en droit'],
                        ['icon' => 'users',     'key' => 'etudiants',     'title' => 'Étudiants membres',         'sub' => 'Étudiants engagés'],
                        ['icon' => 'user-plus', 'key' => 'direction',     'title' => 'Direction',                 'sub' => 'Responsables de la Clinique'],
                        ['icon' => 'document',  'key' => 'administratif', 'title' => 'Personnel administratif',   'sub' => 'Membres de soutien'],
                    ] as $s)
                    <div class="flex min-w-0 flex-col items-start gap-2.5 p-3.5 sm:p-6 lg:flex-row lg:items-center lg:gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600 sm:h-14 sm:w-14">
                            <x-icon :name="$s['icon']" class="h-5 w-5 sm:h-7 sm:w-7" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs text-slate-500">{{ $s['title'] }}</p>
                            <p class="text-lg font-bold text-slate-900 sm:text-2xl">{{ $counts[$s['key']] ?? 0 }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $s['sub'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ DIRECTION ============ --}}
    @if(($groups['direction'] ?? collect())->isNotEmpty())
        <section class="bg-slate-50 py-12 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-eyebrow color="amber">Direction de la Clinique</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Responsables de la Clinique Juridique</h2>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach ($groups['direction'] as $member)
                        <x-profile-card
                            size="large"
                            :image-url="$member->hasPhoto() ? $member->photoUrl() : null"
                            :name="$member->name"
                            :role="$member->role"
                            :bio="$member->bio"
                            :email="$member->email"
                            :linkedin="$member->linkedin"
                            :initials="$member->initials()"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ENSEIGNANTS ENCADRANTS ============ --}}
    @if(($groups['enseignants'] ?? collect())->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
            <x-eyebrow color="amber">Enseignants encadrants</x-eyebrow>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Nos enseignants encadrants</h2>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($groups['enseignants'] as $member)
                    <x-profile-card
                        size="horizontal"
                        :image-url="$member->hasPhoto() ? $member->photoUrl() : null"
                        :name="$member->name"
                        :role="$member->role"
                        :bio="$member->bio"
                        :email="$member->email"
                        :initials="$member->initials()"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ ÉTUDIANTS ============ --}}
    @if(($groups['etudiants'] ?? collect())->isNotEmpty())
        <section class="bg-slate-50 py-12 sm:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <x-eyebrow color="amber">Nos étudiants</x-eyebrow>
                        <h2 class="mt-2 text-2xl font-bold text-slate-900">Les étudiants membres</h2>
                    </div>
                    <x-button :href="route('join')" variant="primary" class="self-start rounded-full! sm:self-auto">Rejoindre la Clinique</x-button>
                </div>

                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ($groups['etudiants'] as $member)
                        <x-profile-card
                            size="compact"
                            :image-url="$member->hasPhoto() ? $member->photoUrl() : null"
                            :name="$member->name"
                            :role="$member->role"
                            :bio="$member->bio"
                            :initials="$member->initials()"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PERSONNEL ADMINISTRATIF ============ --}}
    @if(($groups['administratif'] ?? collect())->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:py-16 sm:px-6 lg:px-8">
            <x-eyebrow color="amber">Personnel administratif et technique</x-eyebrow>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Notre équipe de soutien</h2>

            <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-3">
                @foreach ($groups['administratif'] as $member)
                    <x-profile-card
                        size="horizontal"
                        :image-url="$member->hasPhoto() ? $member->photoUrl() : null"
                        :name="$member->name"
                        :role="$member->role"
                        :bio="$member->bio"
                        :email="$member->email"
                        :initials="$member->initials()"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-12 sm:pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="users"
            title="Rejoignez notre équipe"
            button-label="Voir les opportunités"
            :button-href="route('presaisine.index')"
        >
            Vous êtes étudiant en droit et souhaitez vous engager ? Découvrez nos appels à candidatures.
        </x-inline-cta>
    </section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var indices = {};
    document.querySelectorAll('[data-carousel-prev], [data-carousel-next]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = this.dataset.carouselPrev || this.dataset.carouselNext;
            var container = document.getElementById('carousel-' + key);
            if (!container) return;
            var cards = Array.from(container.children);
            if (!cards.length) return;
            indices[key] = indices[key] || 0;
            indices[key] += this.dataset.carouselPrev !== undefined ? -1 : 1;
            indices[key] = Math.max(0, Math.min(indices[key], cards.length - 1));
            cards[indices[key]].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
        });
    });
});
</script>
@endpush
