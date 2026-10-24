<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Partner;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Partners and team members.
 *
 * Two notes on the content. The mockups list Moroccan institutions and
 * partners; both planning documents place the Clinic at the Université
 * Abdou Moumouni in Niamey, so the names below are Nigerien. And the team
 * are invented people: the plan forbids real personal data during
 * development, and the Clinic's own list arrives in phase 4.
 */
class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $this->partners();
        $this->team();
    }

    private function partners(): void
    {
        $rubriques = collect([
            ['name' => 'Institutions nationales', 'tone' => 'blue'],
            ['name' => 'Partenaires internationaux', 'tone' => 'green'],
            ['name' => 'Société civile et ONG', 'tone' => 'purple'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'partner', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        $partners = [
            // Institutions nationales
            ['Ministère de la Justice', 'Institutions nationales', 'Niger'],
            ['Université Abdou Moumouni', 'Institutions nationales', 'Niamey'],
            ["Cour d'Appel de Niamey", 'Institutions nationales', 'Niger'],
            ['Tribunal de Grande Instance', 'Institutions nationales', 'Niamey'],
            ['Conseil Supérieur de la Magistrature', 'Institutions nationales', 'Niger'],
            ['Ordre des Avocats du Niger', 'Institutions nationales', 'Niamey'],

            // Partenaires internationaux
            ['PNUD', 'Partenaires internationaux', 'Programme des Nations Unies pour le Développement'],
            ['UNICEF', 'Partenaires internationaux', "Fonds des Nations Unies pour l'enfance"],
            ['Union Européenne', 'Partenaires internationaux', 'Délégation au Niger'],
            ['AFD', 'Partenaires internationaux', 'Agence Française de Développement'],
            ['OIM', 'Partenaires internationaux', 'Organisation Internationale pour les Migrations'],
            ['CICR', 'Partenaires internationaux', 'Comité International de la Croix-Rouge'],

            // Société civile et ONG
            ['ASF', 'Société civile et ONG', 'Avocats Sans Frontières'],
            ['Amnesty International', 'Société civile et ONG', null],
            ['Human Rights Watch', 'Société civile et ONG', null],
            ['Search for Common Ground', 'Société civile et ONG', null],
            ['ALIMA', 'Société civile et ONG', 'The Alliance for International Medical Action'],
            ['Plan International', 'Société civile et ONG', null],
        ];

        foreach ($partners as $i => [$name, $rubrique, $description]) {
            Partner::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'category_id' => $rubriques[$rubrique]->id,
                    'status' => 'published',
                    'is_visible' => true,
                    'position' => $i + 1,
                ]
            );
        }

        // One hidden row, so the Masqué state is visible during review.
        Partner::updateOrCreate(
            ['slug' => 'partenaire-en-cours-de-validation'],
            [
                'name' => 'Partenaire en cours de validation',
                'description' => 'Convention non encore signée.',
                'category_id' => $rubriques['Institutions nationales']->id,
                'status' => 'published',
                'is_visible' => false,
                'position' => 99,
            ]
        );
    }

    private function team(): void
    {
        // name, role, department, bio, email, photo placeholder
        $members = [
            ['Pr. Ibrahim Moussa', 'Directeur de la Clinique Juridique', 'direction', "Enseignant-chercheur à la Faculté des Sciences Juridiques et Politiques, spécialiste en droit public et en droits fondamentaux.", 'ibrahim.moussa@uam.ne'],
            ['Dr. Aïssatou Mahamadou', 'Directrice adjointe', 'direction', "Maître de conférences en droit privé, responsable de la coordination pédagogique et des activités de la Clinique.", 'aissatou.mahamadou@uam.ne'],

            ['Pr. Moussa Harouna', 'Droit pénal', 'enseignants', 'Encadrement des consultations et formations', 'moussa.harouna@uam.ne'],
            ['Dr. Halima Issa', 'Droit de la famille', 'enseignants', 'Suivi des dossiers et sensibilisation', 'halima.issa@uam.ne'],
            ['Dr. Ali Kountché', 'Droit administratif', 'enseignants', 'Appui juridique et veille légale', 'ali.kountche@uam.ne'],
            ['Pr. Mariama Souley', 'Droits humains', 'enseignants', 'Recherche et développement des ressources pédagogiques', 'mariama.souley@uam.ne'],

            ['Amadou Sani', 'Étudiant en Master 2', 'etudiants', 'Droit public', null],
            ['Fatouma Idrissa', 'Étudiante en Master 2', 'etudiants', 'Droit privé', null],
            ['Moussa Ali', 'Étudiant en Licence', 'etudiants', 'Droit pénal', null],
            ['Aïcha Salissou', 'Étudiante en Licence', 'etudiants', 'Droit de la famille', null],
            ['Ibrahim Tanko', 'Étudiant en Master 1', 'etudiants', 'Droit administratif', null],
            ['Rokiatou Seyni', 'Étudiante en Licence', 'etudiants', 'Droits humains', null],

            ['Mme Zarima Abdou', 'Secrétaire administrative', 'administratif', 'Gestion du courrier, accueil et coordination', 'zarima.abdou@uam.ne'],
            ['M. Souleymane Ibrahim', 'Chargé de communication', 'administratif', 'Web, réseaux sociaux et événements', 'souleymane.ibrahim@uam.ne'],
            ['Mme Ramatou Abdoul', 'Assistante administrative', 'administratif', 'Appui logistique et suivi des activités', 'ramatou.abdoul@uam.ne'],
        ];

        $positions = [];

        foreach ($members as [$name, $role, $department, $bio, $email]) {
            $positions[$department] = ($positions[$department] ?? 0) + 1;

            TeamMember::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'role' => $role,
                    'bio' => $bio,
                    'department' => $department,
                    'cohort' => $department === 'etudiants' ? '2025' : null,
                    'email' => $email,
                    'status' => 'published',
                    'is_visible' => true,
                    'position' => $positions[$department],
                ]
            );
        }

        // One hidden row: a member who has left is hidden, not deleted.
        TeamMember::updateOrCreate(
            ['slug' => 'ancien-membre'],
            [
                'name' => 'Ancien membre',
                'role' => 'Étudiant clinicien (promotion précédente)',
                'department' => 'etudiants',
                'cohort' => '2023',
                'status' => 'published',
                'is_visible' => false,
                'position' => 99,
            ]
        );
    }
}
