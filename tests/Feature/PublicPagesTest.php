<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke coverage for the public site. Guards against a route, view or
 * shared component change silently breaking a page.
 */
class PublicPagesTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The actualités and Web TV pages read from the database; without a
        // row the listing is empty and the detail route has nothing to
        // resolve.
        \App\Models\Article::create([
            'title' => "Conférence sur l'accès au droit",
            'slug' => 'conference-sur-l-acces-au-droit',
            'excerpt' => 'Un aperçu de la conférence.',
            'status' => \App\Models\Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $caravanes = \App\Models\Category::create([
            'type' => 'activity',
            'name' => 'Caravanes juridiques',
            'slug' => 'caravanes-juridiques',
            'tone' => 'purple',
        ]);

        $activity = \App\Models\Activity::create([
            'title' => 'Caravane juridique à Tillabéri',
            'slug' => 'caravane-juridique-a-tillaberi',
            'excerpt' => "Une journée de conseil juridique.",
            'location' => 'Tillabéri',
            'held_on' => now()->subMonth(),
            'category_id' => $caravanes->id,
            'status' => \App\Models\Activity::STATUS_PUBLISHED,
            'published_at' => now()->subMonth(),
        ]);

        $activity->photos()->create([
            'path' => 'placeholders/news-caravan',
            'caption' => 'Caravane juridique en plein air',
            'position' => 1,
        ]);

        \App\Models\Guide::create([
            'title' => 'Droits de la famille',
            'slug' => 'droits-de-la-famille',
            'description' => 'Une fiche sur les droits de la famille.',
            'body' => 'Contenu de la fiche.',
            'file_path' => 'documents/fiches-pratiques/droits-de-la-famille.pdf',
            'file_name' => 'droits-de-la-famille.pdf',
            'file_size' => 2200000,
            'status' => \App\Models\Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDays(3),
        ]);

        \App\Models\Report::create([
            'title' => "Rapport annuel d'activité 2024",
            'slug' => 'rapport-annuel-d-activite-2024',
            'year' => 2024,
            'description' => "Le bilan de l'année.",
            'file_path' => 'documents/rapports-annuels/rapport-2024.pdf',
            'file_name' => 'rapport-2024.pdf',
            'file_size' => 4200000,
            'status' => \App\Models\Report::STATUS_PUBLISHED,
            'published_at' => now()->subMonth(),
        ]);

        \App\Models\Video::create([
            'title' => 'Caravane juridique à Tillabéri',
            'slug' => 'caravane-juridique-tillaberi',
            'summary' => 'Une capsule de terrain.',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
            'duration' => '12:35',
            'status' => \App\Models\Video::STATUS_PUBLISHED,
            'published_at' => now()->subDays(2),
        ]);
    }

    public function test_the_news_index_renders(): void
    {
        $this->get('/actualites')
            ->assertOk()
            ->assertSee('Actualit', escape: false);
    }

    public function test_a_published_article_renders(): void
    {
        $this->get('/actualites/conference-sur-l-acces-au-droit')
            ->assertOk()
            ->assertSee('Conférence', escape: false);
    }

    public function test_a_draft_article_is_not_public(): void
    {
        \App\Models\Article::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon',
            'status' => \App\Models\Article::STATUS_DRAFT,
        ]);

        $this->get('/actualites/brouillon')->assertNotFound();
    }

    /** Published, but dated ahead: not yet visible to anyone. */
    public function test_a_scheduled_article_is_not_public(): void
    {
        \App\Models\Article::create([
            'title' => 'À paraître',
            'slug' => 'a-paraitre',
            'status' => \App\Models\Article::STATUS_PUBLISHED,
            'published_at' => now()->addWeek(),
        ]);

        $this->get('/actualites/a-paraitre')->assertNotFound();
    }

    public function test_a_draft_capsule_is_not_public(): void
    {
        \App\Models\Video::create([
            'title' => 'Capsule en brouillon',
            'slug' => 'capsule-en-brouillon',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
            'status' => \App\Models\Video::STATUS_DRAFT,
        ]);

        $this->get('/web-tv/capsule-en-brouillon')->assertNotFound();
    }

    public function test_watching_a_capsule_counts_a_view(): void
    {
        $video = \App\Models\Video::firstWhere('slug', 'caravane-juridique-tillaberi');

        $this->get('/web-tv/caravane-juridique-tillaberi')->assertOk();

        $this->assertSame($video->views + 1, $video->fresh()->views);
    }

    public function test_a_draft_activity_is_not_on_the_public_site(): void
    {
        \App\Models\Activity::create([
            'title' => 'Atelier interne de préparation',
            'slug' => 'atelier-interne',
            'status' => \App\Models\Activity::STATUS_DRAFT,
        ]);

        $this->get('/activites')
            ->assertOk()
            ->assertDontSee('Atelier interne', escape: false);
    }

    /**
     * A draft activity's photos must not reach the gallery either: the
     * gallery reads photos through their activity, not on their own.
     */
    public function test_photos_of_a_draft_activity_are_not_in_the_gallery(): void
    {
        $draft = \App\Models\Activity::create([
            'title' => 'Atelier interne',
            'slug' => 'atelier-interne',
            'status' => \App\Models\Activity::STATUS_DRAFT,
            'category_id' => \App\Models\Category::create([
                'type' => 'activity',
                'name' => 'Ateliers',
                'slug' => 'ateliers',
                'tone' => 'green',
            ])->id,
        ]);

        $draft->photos()->create([
            'path' => 'placeholders/gallery-conference',
            'caption' => 'Photo interne confidentielle',
            'position' => 1,
        ]);

        $this->get('/galerie')
            ->assertOk()
            ->assertDontSee('Photo interne confidentielle', escape: false)
            ->assertDontSee('Ateliers', escape: false);
    }

    public function test_the_gallery_groups_photos_by_album(): void
    {
        $this->get('/galerie')
            ->assertOk()
            ->assertSee('Caravanes juridiques', escape: false)
            ->assertSee('Caravane juridique en plein air', escape: false);
    }

    public function test_the_activities_list_can_be_filtered_by_type(): void
    {
        $formations = \App\Models\Category::create([
            'type' => 'activity',
            'name' => 'Formations',
            'slug' => 'formations-activite',
            'tone' => 'blue',
        ]);

        \App\Models\Activity::create([
            'title' => 'Formation sur la médiation',
            'slug' => 'formation-sur-la-mediation',
            'category_id' => $formations->id,
            'held_on' => now()->subWeek(),
            'status' => \App\Models\Activity::STATUS_PUBLISHED,
            'published_at' => now()->subWeek(),
        ]);

        $this->get('/activites?categorie=formations-activite')
            ->assertOk()
            ->assertSee('Formation sur la médiation', escape: false)
            ->assertDontSee('Caravane juridique à Tillabéri', escape: false);
    }

    public function test_the_web_tv_list_can_be_filtered_by_rubrique(): void
    {
        $rubrique = \App\Models\Category::create([
            'type' => 'video',
            'name' => 'Formations',
            'slug' => 'formations',
            'tone' => 'green',
        ]);

        \App\Models\Video::create([
            'title' => 'Session de formation sur la médiation',
            'slug' => 'session-de-formation-sur-la-mediation',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
            'category_id' => $rubrique->id,
            'status' => \App\Models\Video::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/web-tv?categorie=formations')
            ->assertOk()
            ->assertSee('Session de formation', escape: false)
            // Inside a rubrique nothing is featured, so a capsule from
            // another rubrique must not appear at all.
            ->assertDontSee('Caravane juridique à Tillabéri', escape: false);
    }

    /**
     * Every public route from the Phase 2 page inventory.
     *
     * @return array<string, array{string, string}>
     */
    public static function publicRoutes(): array
    {
        return [
            'accueil' => ['/', 'Clinique Juridique'],
            'a propos' => ['/a-propos', 'propos'],
            'equipe' => ['/a-propos/equipe', 'quipe'],
            'partenaires' => ['/partenaires', 'partenaires'],
            'services' => ['/services', 'services'],
            'processus' => ['/services/processus-de-consultation', 'consultation'],
            'conditions' => ['/services/conditions-acces-gratuite', 'gratuit'],
            'tarifs' => ['/services/tarifs-administratifs', 'Tarifs'],
            'activites' => ['/activites', 'caravanes'],
            'galerie' => ['/galerie', 'Galerie'],
            'fiches pratiques' => ['/ressources/fiches-pratiques', 'Fiches pratiques'],
            'fiche detail' => ['/ressources/fiches-pratiques/droits-de-la-famille', 'famille'],
            'rapports' => ['/ressources/rapports-annuels', 'Rapports annuels'],
            'web tv' => ['/web-tv', 'Web TV'],
            'web tv detail' => ['/web-tv/caravane-juridique-tillaberi', 'Caravane'],
            // seeded in setUp, so these two exercise the real queries
            'rejoindre' => ['/rejoindre', 'candidatures'],
            'contact' => ['/contact', 'Contactez-nous'],
            'mentions legales' => ['/mentions-legales', 'Mentions'],
            'conditions utilisation' => ['/conditions-utilisation', 'Conditions'],
            'confidentialite' => ['/politique-confidentialite', 'confidentialit'],
            'recherche' => ['/recherche', 'recherche'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_renders(string $uri, string $expected): void
    {
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertSee($expected, escape: false);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_has_no_unresolved_icons(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        // An icon name missing from the x-icon map renders an empty path.
        $this->assertStringNotContainsString('<path d="" />', $html);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_has_exactly_one_h1(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        $this->assertSame(
            1,
            substr_count($html, '<h1'),
            "{$uri} must have exactly one <h1> for accessibility and SEO."
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_page_does_not_leak_php(string $uri): void
    {
        $html = $this->get($uri)->getContent();

        // An escaped double quote inside a Blade attribute breaks parsing
        // and spills raw PHP into the rendered output.
        $this->assertStringNotContainsString("=> '", $html);
        $this->assertStringNotContainsString('\\"', $html);
    }

    /**
     * The performance plan forbids loading a video player before the
     * visitor presses play (~1 Mo per idle embed).
     */
    public function test_video_pages_load_no_iframe_before_click(): void
    {
        foreach (['/web-tv', '/web-tv/caravane-juridique-tillaberi'] as $uri) {
            $this->assertStringNotContainsString(
                '<iframe',
                $this->get($uri)->getContent(),
                "{$uri} must not embed a player before the visitor clicks."
            );
        }
    }

    public function test_unknown_url_returns_404(): void
    {
        $this->get('/cette-page-nexiste-pas')->assertNotFound();
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }
}
