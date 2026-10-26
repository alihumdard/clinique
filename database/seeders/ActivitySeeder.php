<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Activités, caravanes and their galleries. The photos point at the
 * placeholder files under public/images rather than uploads, which is what
 * the review content does everywhere until the Clinic's own pictures
 * arrive in phase 4.
 */
class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $types = collect([
            ['name' => 'Caravanes juridiques', 'tone' => 'purple'],
            ['name' => 'Sensibilisations', 'tone' => 'pink'],
            ['name' => 'Formations', 'tone' => 'blue'],
            ['name' => 'Ateliers', 'tone' => 'green'],
            ['name' => 'Rencontres communautaires', 'tone' => 'amber'],
            ['name' => 'Événements officiels', 'tone' => 'red'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'activity', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        // title, type, image, location, held_on, status, gallery photos
        $activities = [
            [
                'Caravane juridique à Tillabéri', 'Caravanes juridiques', 'placeholders/news-caravan',
                'Tillabéri', '2025-09-28', 'published',
                [
                    ['placeholders/news-caravan', 'Caravane juridique en plein air à Tillabéri'],
                    ['placeholders/news-1', "Séance d'information avec les habitants"],
                    ['placeholders/gallery-caravan-team', "Équipe de la Clinique devant le véhicule de la caravane"],
                    ['placeholders/gallery-caravan-meeting', "Entretien individuel lors d'une permanence mobile"],
                    ['placeholders/gallery-caravan-assembly', "Assemblée communautaire lors d'une caravane juridique"],
                ],
            ],
            [
                'Caravane juridique à Zinder', 'Caravanes juridiques', 'placeholders/news-2',
                'Zinder', '2025-06-15', 'published',
                [
                    ['placeholders/news-2', 'Caravane juridique à Zinder'],
                    ['placeholders/news-3', 'Échange avec les participantes à la caravane'],
                ],
            ],
            [
                'Caravane juridique à Maradi', 'Caravanes juridiques', 'placeholders/news-caravan',
                'Maradi', '2025-05-20', 'published',
                [
                    ['placeholders/news-caravan', 'Permanence juridique à Maradi'],
                ],
            ],
            [
                'Session de sensibilisation des jeunes', 'Sensibilisations', 'placeholders/news-3',
                'Niamey', '2025-10-12', 'published',
                [
                    ['placeholders/news-3', 'Campagne de sensibilisation devant un public rural'],
                    ['placeholders/news-caravan', 'Sensibilisation des jeunes sur leurs droits'],
                    ['placeholders/news-2', "Équipe de sensibilisation de la Clinique"],
                ],
            ],
            [
                'Sensibilisation sur les droits des femmes', 'Sensibilisations', 'placeholders/gallery-scales-gavel-desk',
                'Niamey', '2025-06-08', 'published',
                [
                    ['placeholders/gallery-scales-gavel-desk', 'Atelier sur les droits des femmes'],
                ],
            ],
            [
                'Formation sur la médiation', 'Formations', 'placeholders/news-formation',
                'UAM Niamey', '2025-09-05', 'published',
                [
                    ['placeholders/news-formation', 'Formation des étudiants cliniciens en amphithéâtre'],
                    ['placeholders/gallery-conference', 'Atelier pratique encadré par un enseignant'],
                    ['placeholders/news-guide', 'Travaux de groupe pendant une session de formation'],
                ],
            ],
            [
                'Formation des étudiants en droit', 'Formations', 'placeholders/gallery-students-faculty',
                'UAM Niamey', '2025-05-28', 'published',
                [
                    ['placeholders/gallery-students-faculty', "Encadrement des étudiants de la Clinique"],
                ],
            ],
            [
                "Atelier sur l'aide juridique", 'Ateliers', 'placeholders/news-candidatures',
                'Tahoua', '2025-04-25', 'published',
                [
                    ['placeholders/news-candidatures', "Atelier de travail sur l'aide juridique"],
                    ['placeholders/gallery-conference', 'Restitution des travaux en atelier'],
                ],
            ],
            [
                'Rencontre avec les communautés', 'Rencontres communautaires', 'placeholders/news-partenariat',
                'Tillabéri', '2025-04-10', 'published',
                [
                    ['placeholders/news-partenariat', 'Rencontre avec les représentants communautaires'],
                    ['placeholders/news-1', 'Échange avec les autorités locales'],
                ],
            ],
            [
                'Journée portes ouvertes', 'Événements officiels', 'placeholders/about-hero',
                'Niamey', '2025-04-02', 'published',
                [
                    ['placeholders/about-hero', 'Cérémonie officielle en présence des autorités'],
                    ['placeholders/news-1', "Conférence sur l'accès au droit"],
                    ['placeholders/news-partenariat', 'Signature officielle de partenariat'],
                ],
            ],
            // Dated ahead: this is what fills the "À venir" view.
            [
                'Caravane juridique à Dosso', 'Caravanes juridiques', 'placeholders/news-caravan',
                'Dosso', '2026-12-05', 'published', [],
            ],
            [
                'Table ronde : justice et société', 'Événements officiels', 'placeholders/about-hero',
                'Niamey', '2026-11-20', 'published', [],
            ],
            // Not published: must not appear anywhere on the public site.
            [
                'Atelier interne de préparation', 'Ateliers', 'placeholders/gallery-conference',
                'Niamey', '2025-03-01', 'draft',
                [['placeholders/gallery-conference', 'Atelier interne']],
            ],
        ];

        foreach ($activities as [$title, $type, $image, $location, $heldOn, $status, $photos]) {
            /*
             * The publication date is when the announcement went up, which
             * is not the event date: an activity still to come is published
             * now and held later. Dating publication in the future would
             * instead make it a scheduled row, invisible to the public.
             */
            $publishedAt = $status === 'published'
                ? min($heldOn, now()->subDay()->toDateTimeString())
                : null;

            $activity = Activity::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => "Une activité d'exemple : « {$title} ». Le contenu réel de la Clinique sera intégré ultérieurement.",
                    'body' => "Contenu d'exemple de l'activité « {$title} ».",
                    'image' => $image,
                    'location' => $location,
                    'held_on' => $heldOn,
                    'category_id' => $types[$type]->id,
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]
            );

            // Re-seeding must not pile up duplicate photos.
            $activity->photos()->delete();

            foreach ($photos as $i => [$path, $caption]) {
                $activity->photos()->create([
                    'path' => $path,
                    'caption' => $caption,
                    'position' => $i + 1,
                ]);
            }
        }
    }
}
