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

        // Only a handful of logo files exist (placeholders — some from an
        // earlier content pass for partners this list no longer carries,
        // some supplied since for the Niger institutions). Assigned on the
        // closest name/category match where one exists; the rest cycle
        // through the same small set rather than show bare initials, since
        // every card should read as a real entry until the Clinic supplies
        // official artwork for each partner.
        $logos = ['ministere-justice-niger', 'uemf-niger', 'barreau-fes-niger', 'pndd', 'onu-femmes', 'oim', 'cicr', 'asf', 'amdh'];

        $partners = [
            // Institutions nationales
            ['Ministère de la Justice', 'Institutions nationales', 'Niger', 'ministere-justice-niger'],
            ['Université Abdou Moumouni', 'Institutions nationales', 'Niamey', 'uemf-niger'],
            ["Cour d'Appel de Niamey", 'Institutions nationales', 'Niger', null],
            ['Tribunal de Grande Instance', 'Institutions nationales', 'Niamey', null],
            ['Conseil Supérieur de la Magistrature', 'Institutions nationales', 'Niger', null],
            ['Ordre des Avocats du Niger', 'Institutions nationales', 'Niamey', 'barreau-fes-niger'],

            // Partenaires internationaux
            ['PNUD', 'Partenaires internationaux', 'Programme des Nations Unies pour le Développement', 'pndd'],
            ['UNICEF', 'Partenaires internationaux', "Fonds des Nations Unies pour l'enfance", 'onu-femmes'],
            ['Union Européenne', 'Partenaires internationaux', 'Délégation au Niger', null],
            ['AFD', 'Partenaires internationaux', 'Agence Française de Développement', null],
            ['OIM', 'Partenaires internationaux', 'Organisation Internationale pour les Migrations', 'oim'],
            ['CICR', 'Partenaires internationaux', 'Comité International de la Croix-Rouge', 'cicr'],

            // Société civile et ONG
            ['ASF', 'Société civile et ONG', 'Avocats Sans Frontières', 'asf'],
            ['Amnesty International', 'Société civile et ONG', null, 'amdh'],
            ['Human Rights Watch', 'Société civile et ONG', null, null],
            ['Search for Common Ground', 'Société civile et ONG', null, null],
            ['ALIMA', 'Société civile et ONG', 'The Alliance for International Medical Action', null],
            ['Plan International', 'Société civile et ONG', null, null],
        ];

        // Every partner still without a matched logo cycles through the
        // same set, so no card is left with bare initials.
        $cycle = 0;
        foreach ($partners as $i => &$row) {
            if ($row[3] === null) {
                $row[3] = $logos[$cycle % count($logos)];
                $cycle++;
            }
        }
        unset($row);

        // A second pass nudges any card (matched or cycled) that landed on
        // the same logo as its immediate predecessor — two neighbouring
        // cards repeating the same placeholder reads as a mistake even
        // though each is individually a reasonable match.
        for ($i = 1; $i < count($partners); $i++) {
            if ($partners[$i][3] === $partners[$i - 1][3]) {
                foreach ($logos as $alt) {
                    $clashesNext = isset($partners[$i + 1]) && $partners[$i + 1][3] === $alt;
                    if ($alt !== $partners[$i][3] && ! $clashesNext) {
                        $partners[$i][3] = $alt;
                        break;
                    }
                }
            }
        }

        foreach ($partners as $i => [$name, $rubrique, $description, $logo]) {
            Partner::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'logo' => $logo,
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
        // name, role, department, bio, email, photo
        $members = [
            ['Pr. Ibrahim Moussa', 'Directeur de la Clinique Juridique', 'direction', "Enseignant-chercheur à la Faculté des Sciences Juridiques et Politiques, spécialiste en droit public et en droits fondamentaux.", 'ibrahim.moussa@uam.ne', 'team/director-1'],
            ['Dr. Aïssatou Mahamadou', 'Directrice adjointe', 'direction', "Maître de conférences en droit privé, responsable de la coordination pédagogique et des activités de la Clinique.", 'aissatou.mahamadou@uam.ne', 'team/director-2'],

            ['Pr. Moussa Harouna', 'Droit pénal', 'enseignants', 'Encadrement des consultations et formations', 'moussa.harouna@uam.ne', 'team/teacher-1'],
            ['Dr. Halima Issa', 'Droit de la famille', 'enseignants', 'Suivi des dossiers et sensibilisation', 'halima.issa@uam.ne', 'team/teacher-2'],
            ['Dr. Ali Kountché', 'Droit administratif', 'enseignants', 'Appui juridique et veille légale', 'ali.kountche@uam.ne', 'team/teacher-3'],
            ['Pr. Mariama Souley', 'Droits humains', 'enseignants', 'Recherche et développement des ressources pédagogiques', 'mariama.souley@uam.ne', 'team/teacher-4'],

            ['Amadou Sani', 'Étudiant en Master 2', 'etudiants', 'Droit public', null, 'team/student-1'],
            ['Fatouma Idrissa', 'Étudiante en Master 2', 'etudiants', 'Droit privé', null, 'team/student-2'],
            ['Moussa Ali', 'Étudiant en Licence', 'etudiants', 'Droit pénal', null, 'team/student-3'],
            ['Aïcha Salissou', 'Étudiante en Licence', 'etudiants', 'Droit de la famille', null, 'team/student-4'],
            ['Ibrahim Tanko', 'Étudiant en Master 1', 'etudiants', 'Droit administratif', null, 'team/student-5'],
            ['Rokiatou Seyni', 'Étudiante en Licence', 'etudiants', 'Droits humains', null, 'team/student-6'],

            ['Mme Zarima Abdou', 'Secrétaire administrative', 'administratif', 'Gestion du courrier, accueil et coordination', 'zarima.abdou@uam.ne', 'team/staff-1'],
            ['M. Souleymane Ibrahim', 'Chargé de communication', 'administratif', 'Web, réseaux sociaux et événements', 'souleymane.ibrahim@uam.ne', 'team/staff-2'],
            ['Mme Ramatou Abdoul', 'Assistante administrative', 'administratif', 'Appui logistique et suivi des activités', 'ramatou.abdoul@uam.ne', 'team/staff-3'],
        ];

        $positions = [];

        foreach ($members as [$name, $role, $department, $bio, $email, $photo]) {
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
                    'photo' => $photo,
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
