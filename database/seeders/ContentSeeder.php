<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Fictitious content for development and review. The plan forbids real
 * Clinic data during development, and the Clinic's own first-version
 * content arrives in phase 4.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Actualités', 'tone' => 'purple'],
            ['name' => 'Événements', 'tone' => 'blue'],
            ['name' => 'Formation', 'tone' => 'purple'],
            ['name' => 'Ressources', 'tone' => 'amber'],
            ['name' => 'Web TV', 'tone' => 'amber'],
            ['name' => 'Appels à candidatures', 'tone' => 'pink'],
            ['name' => 'Rapports', 'tone' => 'green'],
            ['name' => 'Institution', 'tone' => 'blue'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'article', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ));

        $byName = $categories->keyBy('name');
        $author = User::first();

        $articles = [
            ['Lancement de la caravane juridique à Zinder', 'Événements', 'placeholders/news-caravan', '2025-06-10 14:32', 'published'],
            ['Formation sur les droits des femmes', 'Formation', 'placeholders/about-legal', '2025-06-08 11:20', 'published'],
            ['Nouvelle fiche pratique disponible', 'Ressources', 'placeholders/news-guide', '2025-06-05 09:15', 'published'],
            ['Appel à candidatures – Stagiaires 2025', 'Appels à candidatures', 'placeholders/about-team', '2025-06-01 12:10', 'published'],
            ['La Clinique FSJP-UAM renforce ses partenariats', 'Institution', 'placeholders/about-hero', '2025-05-28 16:45', 'published'],
            ['Publication du rapport annuel 2024', 'Rapports', 'placeholders/news-partenariat', '2025-05-25 10:22', 'published'],
            ['Nouvelle capsule Web TV : Accès à la justice', 'Web TV', 'placeholders/video-featured', null, 'draft'],
            ['Clinique juridique présente au forum universitaire', 'Événements', 'placeholders/news-3', '2025-05-16 13:18', 'published'],
            ['Guide pratique : droits des étudiants', 'Ressources', 'placeholders/article-highlight-1', '2025-05-12 09:40', 'published'],
            ['Conférence sur la justice sociale', 'Actualités', 'placeholders/news-1', '2026-12-08 11:05', 'published'],
            ['Caravane juridique à Tillabéri', 'Événements', 'placeholders/news-caravan', '2024-09-28 10:00', 'published'],
            ['Session de sensibilisation des jeunes', 'Formation', 'placeholders/news-3', '2024-10-12 14:00', 'published'],
            ['Rencontre avec les autorités locales', 'Institution', 'placeholders/news-2', '2023-11-15 09:30', 'published'],
            ['Atelier sur la médiation communautaire', 'Formation', 'placeholders/news-formation', '2023-06-20 15:00', 'published'],
            ['Bilan des activités 2022', 'Rapports', 'placeholders/article-highlight-2', '2022-12-18 11:00', 'published'],
        ];

        foreach ($articles as [$title, $category, $image, $publishedAt, $status]) {
            Article::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => "Un aperçu de l'actualité « {$title} » de la Clinique Juridique FSJP-UAM.",
                    'body' => "Contenu d'exemple. Le contenu réel de la Clinique sera intégré ultérieurement.",
                    'image' => $image,
                    'category_id' => $byName[$category]->id,
                    'author_id' => $author?->id,
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]
            );
        }

        $this->videos();
    }

    /**
     * Web TV capsules. Real addresses are not used: the ids below are
     * fictitious, so a seeded capsule shows the click-to-load poster and
     * never embeds someone else's video.
     */
    private function videos(): void
    {
        $rubriques = collect([
            ['name' => 'Émissions', 'tone' => 'blue'],
            ['name' => 'Caravanes juridiques', 'tone' => 'amber'],
            ['name' => 'Sensibilisations', 'tone' => 'purple'],
            ['name' => 'Formations', 'tone' => 'green'],
            ['name' => 'Témoignages', 'tone' => 'red'],
            ['name' => 'Interviews', 'tone' => 'slate'],
            ['name' => 'Événements officiels', 'tone' => 'pink'],
        ])->map(fn (array $c, int $i) => Category::updateOrCreate(
            ['type' => 'video', 'slug' => Str::slug($c['name'])],
            ['name' => $c['name'], 'tone' => $c['tone'], 'position' => $i]
        ))->keyBy('name');

        $videos = [
            ["Lancement des activités de la Clinique Juridique FSJP-UAM", 'Événements officiels', 'placeholders/video-featured', '12:35', 2450, '2025-06-12 10:00', 'published'],
            ["Caravane juridique à Tillabéri : accès au droit pour les populations", 'Caravanes juridiques', 'placeholders/news-caravan', '8:24', 1250, '2025-05-28 09:00', 'published'],
            ["Les droits des femmes : paroles d'étudiantes", 'Sensibilisations', 'placeholders/news-3', '6:18', 980, '2025-05-12 14:00', 'published'],
            ['Session de formation sur la médiation', 'Formations', 'placeholders/news-formation', '15:42', 1420, '2025-04-05 11:00', 'published'],
            ['Entretien avec le Doyen de la FSJP sur la Clinique Juridique', 'Interviews', 'placeholders/webtv-interview', '10:36', 2100, '2025-03-20 16:00', 'published'],
            ["Témoignages : l'impact des caravanes juridiques", 'Témoignages', 'placeholders/news-2', '7:50', 1780, '2025-02-15 10:30', 'published'],
            ['Accès au droit : enjeux et perspectives', 'Émissions', 'placeholders/news-1', '9:12', 1560, '2025-01-03 12:00', 'published'],
            ['Table ronde : justice et développement local', 'Événements officiels', 'placeholders/about-hero', '12:20', 1320, '2024-11-25 15:00', 'published'],
            ['Protection des enfants : que dit la loi ?', 'Sensibilisations', 'placeholders/news-candidatures', '6:45', 940, '2024-10-18 09:45', 'published'],
            ['Les mécanismes de règlement des conflits', 'Formations', 'placeholders/news-partenariat', '11:08', 1110, '2024-09-10 13:00', 'published'],
            ['Capsule : comprendre la procédure civile', 'Émissions', 'placeholders/article-highlight-1', '5:40', 0, null, 'draft'],
            ['Rencontre avec les chefs traditionnels', 'Interviews', 'placeholders/about-team', '14:02', 0, '2026-12-01 10:00', 'published'],
        ];

        foreach ($videos as [$title, $rubrique, $cover, $duration, $views, $publishedAt, $status]) {
            // Only the lead capsule carries a transcription, which is how
            // the plan describes it: "lorsqu'elle existe".
            $transcript = $title === $videos[0][0]
                ? "Transcription d'exemple de la capsule « {$title} ». La transcription réelle sera fournie par la Clinique."
                : null;

            // A plausible 11-character id without pointing at a real video.
            $id = substr(str_replace('-', '_', Str::slug($title)) . 'aaaaaaaaaaa', 0, 11);

            $video = Video::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'summary' => "Capsule d'exemple : « {$title} ». Le contenu réel de la Clinique sera intégré ultérieurement.",
                    'video_url' => "https://www.youtube.com/watch?v={$id}",
                    'provider' => 'youtube',
                    'provider_id' => $id,
                    'cover' => $cover,
                    'duration' => $duration,
                    'transcript' => $transcript,
                    'category_id' => $rubriques[$rubrique]->id,
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]
            );

            // views is a counter, not editor input, so it is not fillable.
            $video->forceFill(['views' => $views])->save();
        }
    }
}
