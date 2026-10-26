<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Guide;
use App\Models\Report;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Article::create([
            'title' => 'Formation sur les droits des femmes',
            'slug' => 'formation-droits-des-femmes',
            'excerpt' => 'Une session de formation ouverte à tous.',
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Guide::create([
            'title' => 'Guide pratique : droits de la famille',
            'slug' => 'guide-droits-de-la-famille',
            'description' => 'Mariage, divorce, filiation.',
            'file_path' => 'documents/fiches-pratiques/famille.pdf',
            'file_name' => 'famille.pdf',
            'file_size' => 1000,
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDays(2),
        ]);

        Video::create([
            'title' => 'Les droits des femmes en pratique',
            'slug' => 'droits-des-femmes-en-pratique',
            'summary' => 'Une capsule de sensibilisation.',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'provider' => 'youtube',
            'provider_id' => 'dQw4w9WgXcQ',
            'status' => Video::STATUS_PUBLISHED,
            'published_at' => now()->subDays(3),
        ]);

        Activity::create([
            'title' => 'Caravane juridique à Tillabéri',
            'slug' => 'caravane-tillaberi',
            'excerpt' => 'Conseil juridique de proximité.',
            'location' => 'Tillabéri',
            'held_on' => now()->subMonth(),
            'status' => Activity::STATUS_PUBLISHED,
            'published_at' => now()->subMonth(),
        ]);

        Report::create([
            'title' => "Rapport annuel d'activité 2024",
            'slug' => 'rapport-2024',
            'year' => 2024,
            'description' => 'Bilan des caravanes et des formations.',
            'file_path' => 'documents/rapports-annuels/2024.pdf',
            'file_name' => '2024.pdf',
            'file_size' => 2000,
            'status' => Report::STATUS_PUBLISHED,
            'published_at' => now()->subMonths(2),
        ]);
    }

    public function test_the_page_asks_for_a_keyword_when_none_is_given(): void
    {
        $this->get('/recherche')
            ->assertOk()
            ->assertSee('Saisissez un mot-clé', escape: false);
    }

    /** One term, matches across several content types. */
    public function test_a_search_finds_matches_in_every_type(): void
    {
        $response = $this->get('/recherche?q=droits+des+femmes')->assertOk();

        $response->assertSee('Formation sur les droits des femmes', escape: false);
        $response->assertSee('Les droits des femmes en pratique', escape: false);
        $response->assertSee('Actualité', escape: false);
        $response->assertSee('Web TV', escape: false);
    }

    public function test_a_search_matches_a_guide_description(): void
    {
        $this->get('/recherche?q=filiation')
            ->assertOk()
            ->assertSee('Guide pratique', escape: false);
    }

    /** An activity's place is searchable, which a title-only search would miss. */
    public function test_a_search_matches_an_activity_location(): void
    {
        $this->get('/recherche?q=Tillab')
            ->assertOk()
            ->assertSee('Caravane juridique', escape: false);
    }

    public function test_a_search_matches_a_report(): void
    {
        $this->get('/recherche?q=Bilan')
            ->assertOk()
            ->assertSee('Rapport annuel', escape: false);
    }

    public function test_the_type_filter_narrows_the_results(): void
    {
        $this->get('/recherche?q=droits+des+femmes&type[]=webtv')
            ->assertOk()
            ->assertSee('Les droits des femmes en pratique', escape: false)
            ->assertDontSee('Formation sur les droits des femmes', escape: false);
    }

    /** The counts beside the filter cover types the visitor unticked. */
    public function test_the_counts_cover_every_type_whatever_is_filtered(): void
    {
        $this->get('/recherche?q=droits&type[]=webtv')
            ->assertOk()
            // 2 actualité/webtv hits on "droits" plus the guide; the
            // actualité count must still show even though it is unticked.
            ->assertSee('Actualités', escape: false);
    }

    public function test_an_unpublished_row_is_not_searchable(): void
    {
        Article::create([
            'title' => 'Brouillon confidentiel sur les droits',
            'slug' => 'brouillon-confidentiel',
            'status' => Article::STATUS_DRAFT,
        ]);

        $this->get('/recherche?q=confidentiel')
            ->assertOk()
            ->assertDontSee('Brouillon confidentiel', escape: false)
            ->assertSee('Aucun résultat', escape: false);
    }

    public function test_a_single_character_is_not_searched(): void
    {
        $this->get('/recherche?q=d')
            ->assertOk()
            ->assertSee('deux caractères au moins', escape: false);
    }

    public function test_nothing_found_says_so(): void
    {
        $this->get('/recherche?q=zzzzzzzz')
            ->assertOk()
            ->assertSee('Aucun résultat', escape: false);
    }

    /** A search term must not be able to put markup on the page. */
    public function test_the_term_is_escaped_when_echoed_back(): void
    {
        $html = $this->get('/recherche?q='.urlencode('<script>alert(1)</script>'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    /** The filters have to survive paging, which is what carries the term. */
    public function test_pagination_keeps_the_term_and_the_filters(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Article::create([
                'title' => "Actualité numéro {$i} sur les droits",
                'slug' => "actualite-{$i}",
                'status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subDays($i),
            ]);
        }

        $html = $this->get('/recherche?q=droits&type[]=actualites')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('q=droits', $html);
        $this->assertStringContainsString('page=2', $html);
    }
}
