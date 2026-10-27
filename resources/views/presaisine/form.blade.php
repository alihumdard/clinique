@extends('layouts.app')

@section('title', 'Pré-saisine — votre demande - ' . config('app.name'))

@section('content')

    <x-page-hero
        image="placeholders/legal-scales-gavel"
        title="Votre demande"
        subtitle="Étape 2 sur 3 — décrivez votre situation."
        :breadcrumb="[
            ['label' => 'Accueil', 'url' => route('home')],
            ['label' => 'Pré-saisine', 'url' => route('presaisine.index')],
            ['label' => 'Votre demande'],
        ]"
    />

    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">

        @if($errors->any())
            <div role="alert" class="mb-8 rounded-lg border border-rose-200 bg-rose-50 p-5">
                <p class="text-sm font-semibold text-rose-900">Votre demande n'a pas pu être enregistrée :</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('presaisine.form.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- ============ IDENTITÉ ============ --}}
            <fieldset class="space-y-4">
                <legend class="text-base font-bold text-slate-900">Vos coordonnées</legend>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-field name="last_name" label="Nom" :required="true" :value="old('last_name', $draft['last_name'] ?? '')" />
                    <x-form-field name="first_name" label="Prénom" :value="old('first_name', $draft['first_name'] ?? '')" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-field name="phone" label="Téléphone" type="tel" :required="true" :value="old('phone', $draft['phone'] ?? '')" />
                    <x-form-field name="email" label="Adresse e-mail" type="email" :value="old('email', $draft['email'] ?? '')" hint="Pour recevoir un accusé de réception." />
                </div>

                <x-form-field name="commune" label="Commune / localité" :value="old('commune', $draft['commune'] ?? '')" />

                <label class="flex items-start gap-2.5 text-sm text-slate-700">
                    <input type="checkbox" name="is_uam_student" value="1" @checked(old('is_uam_student', $draft['is_uam_student'] ?? false)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Je suis étudiant(e) à l'Université Abdou Moumouni
                </label>

                <x-form-field name="student_card_number" label="Numéro de carte d'étudiant" :value="old('student_card_number', $draft['student_card_number'] ?? '')" hint="À renseigner uniquement si vous êtes étudiant(e) de l'UAM." />
            </fieldset>

            {{-- ============ LA DEMANDE ============ --}}
            <fieldset class="space-y-4">
                <legend class="text-base font-bold text-slate-900">Votre situation</legend>

                <div>
                    <label for="legal_domain" class="block text-sm font-semibold text-slate-700">
                        Domaine juridique <span class="text-rose-600">*</span>
                    </label>
                    <select
                        id="legal_domain"
                        name="legal_domain"
                        required
                        class="mt-1.5 w-full rounded-md border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option value="">Choisissez un domaine</option>
                        @foreach ($domains as $domain)
                            <option value="{{ $domain->name }}" @selected(old('legal_domain', $draft['legal_domain'] ?? '') === $domain->name)>
                                {{ $domain->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-form-field name="subject" label="Objet de votre demande" :required="true" :value="old('subject', $draft['subject'] ?? '')" hint="Une phrase : « litige avec mon employeur », « acte de naissance »…" />

                <div>
                    <label for="narrative" class="block text-sm font-semibold text-slate-700">Exposé de la situation</label>
                    <textarea
                        id="narrative"
                        name="narrative"
                        rows="6"
                        class="mt-1.5 w-full rounded-md border border-slate-200 px-3.5 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >{{ old('narrative', $draft['narrative'] ?? '') }}</textarea>
                    <p class="mt-1.5 text-xs text-slate-500">Les dates, les personnes concernées et ce qui a déjà été entrepris nous aident à vous répondre.</p>
                </div>
            </fieldset>

            {{-- ============ QUESTIONS ============ --}}
            <fieldset class="space-y-4">
                <legend class="text-base font-bold text-slate-900">Vos questions</legend>
                <p class="text-sm text-slate-500">Trois au maximum.</p>

                @foreach ([1, 2, 3] as $n)
                    <x-form-field name="question_{{ $n }}" label="Question {{ $n }}" :value="old('question_' . $n, $draft['question_' . $n] ?? '')" />
                @endforeach
            </fieldset>

            {{-- ============ PIÈCES JOINTES ============ --}}
            <fieldset class="space-y-3">
                <legend class="text-base font-bold text-slate-900">Pièces jointes</legend>

                <p class="text-sm text-slate-500">
                    {{ $attachments['max_files'] }} fichiers au maximum, {{ $attachments['max_size_kb'] / 1024 }} Mo chacun,
                    aux formats PDF, JPG ou PNG.
                </p>

                <input
                    type="file"
                    name="attachments[]"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100"
                >

                @if(count($files))
                    <ul class="space-y-1.5 text-sm text-slate-600">
                        @foreach ($files as $file)
                            <li class="flex items-center gap-2">
                                <x-icon name="document" class="h-4 w-4 shrink-0 text-slate-400" />
                                {{ $file['original_name'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </fieldset>

            {{-- ============ CONSENTEMENT ============ --}}
            <fieldset class="space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-5">
                <legend class="px-1 text-base font-bold text-slate-900">Traitement de vos données</legend>

                <label class="flex items-start gap-2.5 text-sm leading-relaxed text-slate-700">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <span>
                        J'autorise la Clinique Juridique FSJP-UAM à enregistrer et à traiter les
                        informations ci-dessus pour l'examen de ma demande, dans les conditions
                        décrites par la <a href="{{ route('legal.privacy') }}" class="font-semibold text-primary-600 hover:underline">politique de confidentialité</a>.
                    </span>
                </label>
            </fieldset>

            {{-- A field nobody sees and a script usually fills in. --}}
            <div class="hidden" aria-hidden="true">
                <label for="presaisine-website">Site web</label>
                <input type="text" id="presaisine-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="primary" type="submit">Vérifier ma demande</x-button>
                <x-button :href="route('presaisine.index')" variant="text" :arrow="false">Revenir à l'orientation</x-button>
            </div>
        </form>

    </section>

@endsection
