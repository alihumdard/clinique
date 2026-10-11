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
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
            <div>
                <x-eyebrow>Une équipe pluridisciplinaire</x-eyebrow>
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

            <div class="grid grid-cols-2 gap-x-8 gap-y-6 rounded-xl border border-slate-200 bg-slate-50 p-6">
                <x-mini-stat icon="book" title="Enseignants encadrants" value="8+" subtitle="Experts en droit" />
                <x-mini-stat icon="users" title="Étudiants membres" value="50+" subtitle="Étudiants engagés" />
                <x-mini-stat icon="user-plus" title="Professionnels partenaires" value="10+" subtitle="Avocats et spécialistes" />
                <x-mini-stat icon="document" title="Personnel administratif" value="5+" subtitle="Membres de soutien" />
            </div>
        </div>
    </section>

    {{-- ============ DIRECTION ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-eyebrow>Direction de la Clinique</x-eyebrow>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Responsables de la Clinique Juridique</h2>

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-profile-card
                    size="large"
                    image="team/director-1"
                    name="Pr. Ibrahim Moussa"
                    role="Directeur de la Clinique Juridique"
                    bio="Enseignant-chercheur à la Faculté des Sciences Juridiques et Politiques, spécialiste en droit public et en droits fondamentaux."
                    email="ibrahim.moussa@uam.ne"
                    linkedin="#"
                />
                <x-profile-card
                    size="large"
                    image="team/director-2"
                    name="Dr. Aïssatou Mahamadou"
                    role="Directrice adjointe"
                    bio="Maître de conférences en droit privé, responsable de la coordination pédagogique et des activités de la Clinique."
                    email="aissatou.mahamadou@uam.ne"
                    linkedin="#"
                />
            </div>
        </div>
    </section>

    {{-- ============ ENSEIGNANTS ENCADRANTS (carousel row) ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-eyebrow>Enseignants encadrants</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Nos enseignants encadrants</h2>
            </div>
            <div class="hidden items-center gap-2 sm:flex">
                <button type="button" data-carousel-prev="teachers" aria-label="Précédent" class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button type="button" data-carousel-next="teachers" aria-label="Suivant" class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-600 text-white hover:bg-primary-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <div id="carousel-teachers" class="grid grid-cols-2 gap-5 overflow-x-auto sm:grid-cols-4 sm:overflow-visible">
            @foreach ([
                ['image' => 'team/teacher-1', 'name' => 'Pr. Moussa Harouna', 'role' => 'Droit pénal', 'bio' => 'Encadrement des consultations et formations', 'email' => 'moussa.harouna@uam.ne'],
                ['image' => 'team/teacher-2', 'name' => 'Dr. Halima Issa', 'role' => 'Droit de la famille', 'bio' => 'Suivi des dossiers et sensibilisation', 'email' => 'halima.issa@uam.ne'],
                ['image' => 'team/teacher-3', 'name' => 'Dr. Ali Kountché', 'role' => 'Droit administratif', 'bio' => 'Appui juridique et veille légale', 'email' => 'ali.kountche@uam.ne'],
                ['image' => 'team/teacher-4', 'name' => 'Pr. Mariama Souley', 'role' => 'Droits humains', 'bio' => 'Recherche et développement des ressources pédagogiques', 'email' => 'mariama.souley@uam.ne'],
            ] as $teacher)
                <x-profile-card
                    size="compact"
                    :image="$teacher['image']"
                    :name="$teacher['name']"
                    :role="$teacher['role']"
                    :bio="$teacher['bio']"
                    :email="$teacher['email']"
                />
            @endforeach
        </div>
    </section>

    {{-- ============ ÉTUDIANTS ============ --}}
    <section class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <x-eyebrow>Nos étudiants</x-eyebrow>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Les étudiants membres</h2>
                </div>
                <x-button :href="route('join')" variant="primary" class="!rounded-full">Voir tous les étudiants</x-button>
            </div>

            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ([
                    ['image' => 'team/student-1', 'name' => 'Amadou Sani', 'role' => 'Étudiant en Master 2', 'bio' => 'Droit public'],
                    ['image' => 'team/student-2', 'name' => 'Fatouma Idrissa', 'role' => 'Étudiante en Master 2', 'bio' => 'Droit privé'],
                    ['image' => 'team/student-3', 'name' => 'Moussa Ali', 'role' => 'Étudiant en Licence', 'bio' => 'Droit pénal'],
                    ['image' => 'team/student-4', 'name' => 'Aïcha Salissou', 'role' => 'Étudiante en Licence', 'bio' => 'Droit de la famille'],
                    ['image' => 'team/student-5', 'name' => 'Ibrahim Tanko', 'role' => 'Étudiant en Master 1', 'bio' => 'Droit administratif'],
                    ['image' => 'team/student-6', 'name' => 'Rokiatou Seyni', 'role' => 'Étudiante en Licence', 'bio' => 'Droits humains'],
                ] as $student)
                    <x-profile-card
                        size="compact"
                        :image="$student['image']"
                        :name="$student['name']"
                        :role="$student['role']"
                        :bio="$student['bio']"
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PERSONNEL ADMINISTRATIF ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-eyebrow>Personnel administratif et technique</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Notre équipe de soutien</h2>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
            @foreach ([
                ['image' => 'team/staff-1', 'name' => 'Mme Zarima Abdou', 'role' => 'Secrétaire administrative', 'bio' => 'Gestion du courrier, accueil et coordination', 'email' => 'zarima.abdou@uam.ne'],
                ['image' => 'team/staff-2', 'name' => 'M. Souleymane Ibrahim', 'role' => 'Chargé de communication', 'bio' => 'Web, réseaux sociaux et événements', 'email' => 'souleymane.ibrahim@uam.ne'],
                ['image' => 'team/staff-3', 'name' => 'Mme Ramatou Abdoul', 'role' => 'Assistante administrative', 'bio' => 'Appui logistique et suivi des activités', 'email' => 'ramatou.abdoul@uam.ne'],
            ] as $staff)
                <x-profile-card
                    size="compact"
                    :image="$staff['image']"
                    :name="$staff['name']"
                    :role="$staff['role']"
                    :bio="$staff['bio']"
                    :email="$staff['email']"
                />
            @endforeach
        </div>
    </section>

    {{-- ============ INLINE CTA ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            icon="user-plus"
            title="Rejoignez notre équipe"
            button-label="Voir les opportunités"
            :button-href="route('presaisine.index')"
        >
            Vous êtes étudiant en droit et souhaitez vous engager ? Découvrez nos appels à candidatures.
        </x-inline-cta>
    </section>

    {{-- ============ PARTENAIRES ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <x-eyebrow>Ils nous soutiennent</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Nos partenaires institutionnels</h2>
        <div class="mt-8">
            @include('partials.partners-grid')
        </div>
    </section>

@endsection
