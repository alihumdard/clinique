<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
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
    }
}
