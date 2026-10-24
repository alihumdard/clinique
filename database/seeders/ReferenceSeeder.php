<?php

namespace Database\Seeders;

use App\Models\Call;
use App\Models\Category;
use App\Models\Fee;
use App\Models\LegalDomain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Appels à candidatures, the fee grid and the legal domains.
 *
 * The fee figures are the ones the design showed and remain to be
 * confirmed by the Clinic: the page itself says they are indicative and
 * subject to the University's decisions.
 */
class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $this->calls();
        $this->fees();
        $this->domains();
    }

    private function calls(): void
    {
        $types = collect([
            ['name' => 'Stages', 'tone' => 'blue'],
            ['name' => 'Volontariat', 'tone' => 'green'],
            ['name' => 'Consultance', 'tone' => 'purple'],
            ['name' => 'Partenariats', 'tone' => 'amber'],
            ['name' => 'Recherche', 'tone' => 'pink'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'call', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        // title, type, image, excerpt, closes in N days (null = no date)
        $calls = [
            [
                'Stage pour étudiants en droit', 'Stages', 'placeholders/join-call-stage',
                "La Clinique Juridique FSJP-UAM lance un appel à candidatures pour le recrutement de stagiaires étudiants en droit.",
                45,
            ],
            [
                'Volontaires pour les caravanes juridiques', 'Volontariat', 'placeholders/news-caravan',
                "Rejoignez notre équipe de volontaires pour l'organisation des caravanes juridiques dans les régions du Niger.",
                20,
            ],
            [
                'Consultant(e) en communication', 'Consultance', 'placeholders/join-call-consultance',
                "La Clinique Juridique FSJP-UAM recherche un(e) consultant(e) pour l'élaboration d'une stratégie de communication et de visibilité.",
                12,
            ],
            [
                'Partenaires pour les formations juridiques', 'Partenariats', 'placeholders/news-formation',
                "Appel à manifestation d'intérêt pour les partenaires techniques et financiers souhaitant soutenir nos activités de formation.",
                60,
            ],
            [
                'Assistant(e) de recherche', 'Recherche', 'placeholders/join-call-recherche',
                "Nous recrutons un(e) assistant(e) de recherche pour appuyer nos études et publications sur l'accès au droit et la justice.",
                30,
            ],
            // Past its date: this is what drops off the public list on its
            // own while staying in the admin as a record.
            [
                'Stagiaires 2024 (clôturé)', 'Stages', 'placeholders/about-team',
                "Appel clôturé. Conservé pour mémoire.",
                -30,
            ],
        ];

        foreach ($calls as [$title, $type, $image, $excerpt, $closesIn]) {
            Call::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => $excerpt,
                    'body' => "Contenu d'exemple de l'appel « {$title} ». Les conditions, pièces à fournir et modalités de candidature seront précisées par la Clinique.",
                    'image' => $image,
                    'category_id' => $types[$type]->id,
                    'closes_on' => now()->addDays($closesIn)->toDateString(),
                    'status' => 'published',
                    'published_at' => now()->subDays(max(1, 60 - abs($closesIn))),
                ]
            );
        }
    }

    private function fees(): void
    {
        // label, note, amount, amount_label
        $fees = [
            ['Frais de photocopie', 'Photocopie de documents (par page)', 50, null],
            ["Frais d'impression", 'Impression de documents (par page)', 100, null],
            ['Frais de timbre', 'Achat de timbres fiscaux (selon le type de document)', null, 'Selon tarif en vigueur'],
            ['Frais de déplacement', "Déplacement dans le cadre d'une enquête ou d'une démarche administrative (si nécessaire)", null, '5 000 - 15 000 FCFA'],
            ['Frais de correspondance', 'Envoi de courriers recommandés ou autres envois postaux', null, 'Selon tarif en vigueur'],
            ['Frais de certification', 'Certification ou légalisation de documents (si nécessaire)', null, '1 000 - 5 000 FCFA'],
            ['Autres frais spécifiques', 'Tout autre frais exceptionnel lié au traitement du dossier', null, 'Sur décision de la Clinique'],
        ];

        foreach ($fees as $i => [$label, $note, $amount, $amountLabel]) {
            Fee::updateOrCreate(
                ['label' => $label],
                [
                    'note' => $note,
                    'amount' => $amount,
                    'amount_label' => $amountLabel,
                    'currency' => 'FCFA',
                    'is_free' => false,
                    'is_visible' => true,
                    'position' => $i + 1,
                ]
            );
        }
    }

    private function domains(): void
    {
        /*
         * name, the plain-language wording the plan asks for, icon, tone.
         * "Droit de la famille" is the legal name; "Mariage, divorce,
         * enfants" is what a visitor recognises as their own situation.
         */
        $domains = [
            ['Droit de la famille', 'Mariage, divorce, filiation, enfants', 'users', 'blue'],
            ['Droit civil', 'Contrats, état civil, propriété, voisinage', 'scale', 'green'],
            ['Droit pénal', 'Plaintes, garde à vue, victimes', 'gavel', 'pink'],
            ['Droits humains', 'Discriminations, personnes vulnérables', 'heart', 'purple'],
            ['Droit administratif', "Démarches et décisions de l'administration", 'building', 'green'],
            ['Droit du travail', 'Contrat de travail, licenciement, salaires', 'handshake', 'purple'],
            ['Droit foncier', 'Terrains, titres de propriété, héritage foncier', 'map-pin', 'amber'],
            ['Droit des successions', 'Héritage, partage des biens', 'document', 'red'],
        ];

        foreach ($domains as $i => [$name, $plain, $icon, $tone]) {
            LegalDomain::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'plain_language' => $plain,
                    'icon' => $icon,
                    'tone' => $tone,
                    'is_active' => true,
                    'position' => $i + 1,
                ]
            );
        }
    }
}
