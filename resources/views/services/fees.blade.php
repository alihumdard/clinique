@extends('layouts.app')

@section('title', 'Tarifs administratifs - ' . config('app.name'))
@section('meta_description', "La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite. Seuls certains frais administratifs, strictement encadres, peuvent etre demandes.")

@section('content')

    <x-page-hero
        image="placeholders/about-hero"
        title="Tarifs administratifs"
        subtitle="Des frais administratifs transparents et accessibles"
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Nos services', 'url' => route('services')],
            ['label' => 'Tarifs administratifs'],
        ]"
    />

    {{-- ============ PRINCIPE GENERAL + A RETENIR ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-eyebrow>Principe général</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                    Des frais administratifs limités et encadrés
                </h2>
                <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-600">
                    La Clinique Juridique FSJP-UAM offre une assistance juridique gratuite. Seuls certains
                    frais administratifs, strictement encadrés, peuvent être demandés pour couvrir des coûts
                    spécifiques liés au traitement des dossiers (copies, déplacements, timbres, etc.). Ces
                    tarifs sont fixés de manière transparente et approuvés par l'Université.
                </p>
            </div>

            <div class="rounded-xl bg-primary-50/60 p-6">
                <div class="flex items-center gap-2">
                    <x-icon-badge size="sm"><x-icon name="info" class="h-5 w-5" /></x-icon-badge>
                    <h3 class="text-base font-bold text-slate-900">À retenir</h3>
                </div>

                <div class="mt-5 space-y-3.5">
                    <x-check-item>Les consultations et l'assistance juridique sont gratuites.</x-check-item>
                    <x-check-item>Les frais administratifs sont limités et justifiés.</x-check-item>
                    <x-check-item>Aucun frais n'est demandé sans information préalable.</x-check-item>
                    <x-check-item>Un reçu est délivré pour tout paiement.</x-check-item>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ GRILLE DES TARIFS ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-eyebrow>Grille des tarifs</x-eyebrow>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Détail des tarifs administratifs</h2>

        <p class="mt-6 flex items-center gap-1.5 text-xs text-slate-500 sm:hidden">
            <x-icon name="arrow-right" class="h-3.5 w-3.5 shrink-0" />
            Faites glisser le tableau pour voir les montants
        </p>

        <div class="relative mt-2 sm:mt-6">
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">N°</th>
                        <th scope="col" class="px-5 py-3">Nature de la prestation</th>
                        <th scope="col" class="px-5 py-3">Description</th>
                        <th scope="col" class="px-5 py-3">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ([
                        ['1', 'Frais de photocopie', 'Photocopie de documents (par page)', '50 FCFA'],
                        ['2', "Frais d'impression", 'Impression de documents (par page)', '100 FCFA'],
                        ['3', 'Frais de timbre', 'Achat de timbres fiscaux (selon le type de document)', 'Selon tarif en vigueur'],
                        ['4', 'Frais de déplacement', "Déplacement dans le cadre d'une enquête ou d'une démarche administrative (si nécessaire)", '5 000 - 15 000 FCFA'],
                        ['5', 'Frais de correspondance', 'Envoi de courriers recommandés ou autres envois postaux', 'Selon tarif en vigueur'],
                        ['6', 'Frais de certification', 'Certification ou légalisation de documents (si nécessaire)', '1 000 - 5 000 FCFA'],
                        ['7', 'Autres frais spécifiques', 'Tout autre frais exceptionnel lié au traitement du dossier', 'Sur décision de la Clinique'],
                    ] as $row)
                        <tr class="bg-white">
                            <td class="px-5 py-4 text-slate-500">{{ $row[0] }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $row[1] }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $row[2] }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $row[3] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-white to-transparent sm:hidden" aria-hidden="true"></div>
        </div>

        <div class="mt-6">
            <x-notice-banner icon="document" lead="Important :">
                Ces tarifs sont donnés à titre indicatif et peuvent être révisés en fonction des décisions de
                l'Université et des tarifs officiels en vigueur. Tout frais demandé fait l'objet d'une
                information préalable et d'un reçu officiel.
            </x-notice-banner>
        </div>
    </section>

    {{-- ============ PAIEMENT + TRANSPARENCE ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-info-note-card icon="wallet" title="Paiement et reçu" color="blue">
                Tout paiement de frais administratifs donne lieu à la délivrance d'un reçu officiel de la
                Clinique Juridique FSJP-UAM. Aucun paiement ne doit être effectué sans reçu.
            </x-info-note-card>
            <x-info-note-card icon="shield" title="Transparence et contrôle" color="green">
                Les frais administratifs sont strictement encadrés et font l'objet d'un contrôle interne.
                En cas de question ou de réclamation, vous pouvez nous contacter.
            </x-info-note-card>
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-eyebrow>Questions fréquentes</x-eyebrow>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Questions sur les tarifs administratifs</h2>
            </div>
            <x-button href="{{ route('services') }}#faq" variant="text" class="self-start shrink-0 sm:self-auto">Voir toutes les questions</x-button>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
            <x-faq-item question="Les consultations sont-elles vraiment gratuites ?">
                Oui, toutes les consultations et l'assistance juridique de base sont entièrement gratuites.
            </x-faq-item>
            <x-faq-item question="Comment puis-je payer les frais administratifs ?">
                Les frais administratifs se règlent directement auprès de la Clinique, contre délivrance d'un reçu officiel.
            </x-faq-item>
            <x-faq-item question="Pourquoi certains frais sont-ils demandés ?">
                Ces frais couvrent uniquement des coûts réels (copies, timbres, déplacements) et non la prestation juridique elle-même.
            </x-faq-item>
            <x-faq-item question="Un reçu est-il toujours délivré ?">
                Oui, aucun paiement n'est accepté sans la délivrance immédiate d'un reçu officiel.
            </x-faq-item>
            <x-faq-item question="Comment sont fixés les tarifs ?">
                Les tarifs sont fixés de manière transparente et approuvés par l'Université Abdou Moumouni de Niamey.
            </x-faq-item>
            <x-faq-item question="Que faire en cas de désaccord sur un montant ?">
                Vous pouvez contacter la Clinique directement pour toute question ou réclamation concernant un frais demandé.
            </x-faq-item>
            <x-faq-item question="Puis-je obtenir un remboursement ?">
                En cas d'erreur de facturation, contactez la Clinique avec votre reçu pour une régularisation.
            </x-faq-item>
            <x-faq-item question="Les tarifs sont-ils les mêmes pour tous ?">
                Oui, la grille tarifaire s'applique de manière identique à tous les usagers, sans exception.
            </x-faq-item>
        </div>
    </section>

    {{-- ============ CTA (dark) ============ --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <x-inline-cta
            variant="dark"
            icon="paper-plane"
            title="Besoin d'une assistance juridique ?"
            button-label="Faire une pré-saisine"
            :button-href="route('presaisine.index')"
        >
            Nos services sont gratuits et accessibles à tous. Remplissez notre formulaire de pré-saisine
            pour être orienté vers le bon service.
        </x-inline-cta>
    </section>

@endsection
