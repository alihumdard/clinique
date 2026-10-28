<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What a page says about itself: its canonical address, its title, the
 * language it declares, and the order of its headings.
 *
 * A browser recette found four of these wrong at once, and they share a
 * cause: metadata was written once, for a page with no parameters, and
 * every filtered or paginated variant inherited it.
 */
class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    private Category $rubrique;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rubrique = Category::create([
            'type' => 'article',
            'name' => 'Justice et citoyenneté',
            'slug' => 'justice-et-citoyennete',
            'tone' => 'primary',
        ]);

        // Enough to paginate: the listing shows six to a page.
        foreach (range(1, 8) as $i) {
            Article::create([
                'title' => "Article de recette {$i}",
                'slug' => "article-de-recette-{$i}",
                'excerpt' => 'Un résumé.',
                'category_id' => $this->rubrique->id,
                'status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subDays($i),
            ]);
        }
    }

    private function canonicalOf(string $url): string
    {
        preg_match(
            '~<link rel="canonical" href="([^"]+)"~',
            $this->get($url)->assertOk()->getContent(),
            $m
        );

        return $m[1] ?? '';
    }

    private function titleOf(string $url): string
    {
        preg_match('~<title>([^<]*)</title>~', $this->get($url)->assertOk()->getContent(), $m);

        return html_entity_decode($m[1] ?? '', ENT_QUOTES, 'UTF-8');
    }

    /*
     * ------------------------------------------------------------------
     * Canonical
     * ------------------------------------------------------------------
     */

    public function test_page_two_of_a_listing_is_its_own_canonical(): void
    {
        $this->assertStringEndsWith('/actualites', $this->canonicalOf('/actualites'));

        // Pointing page two at page one asks for page two to be dropped.
        $this->assertStringEndsWith('/actualites?page=2', $this->canonicalOf('/actualites?page=2'));
    }

    public function test_a_filtered_listing_is_its_own_canonical(): void
    {
        $this->assertStringEndsWith(
            '/actualites?categorie=justice-et-citoyennete',
            $this->canonicalOf('/actualites?categorie=justice-et-citoyennete')
        );
    }

    public function test_the_canonical_orders_its_parameters(): void
    {
        // The same two filters in either order are one page, not two.
        $this->assertSame(
            $this->canonicalOf('/actualites?categorie=justice-et-citoyennete&annee=2026'),
            $this->canonicalOf('/actualites?annee=2026&categorie=justice-et-citoyennete')
        );
    }

    public function test_the_canonical_drops_campaign_parameters(): void
    {
        $canonical = $this->canonicalOf(
            '/actualites?page=2&utm_source=facebook&utm_campaign=lancement&fbclid=abc'
        );

        $this->assertStringEndsWith('/actualites?page=2', $canonical);
        $this->assertStringNotContainsString('utm_', $canonical);
        $this->assertStringNotContainsString('fbclid', $canonical);
    }

    public function test_an_empty_parameter_does_not_reach_the_canonical(): void
    {
        $this->assertStringEndsWith('/actualites', $this->canonicalOf('/actualites?q='));
    }

    /*
     * ------------------------------------------------------------------
     * Title
     * ------------------------------------------------------------------
     */

    public function test_page_two_carries_its_number_in_the_title(): void
    {
        $first = $this->titleOf('/actualites');
        $second = $this->titleOf('/actualites?page=2');

        $this->assertNotSame($first, $second);
        $this->assertStringContainsString('page 2', $second);
    }

    public function test_a_filtered_listing_names_its_rubrique_in_the_title(): void
    {
        $this->assertStringContainsString(
            'Justice et citoyenneté',
            $this->titleOf('/actualites?categorie=justice-et-citoyennete')
        );
    }

    public function test_the_four_listings_with_filters_all_vary_their_title(): void
    {
        // Each of these shared one title across every filter and every
        // page of its pagination.
        foreach (['/actualites', '/activites', '/galerie', '/rejoindre'] as $listing) {
            $this->assertNotSame(
                $this->titleOf($listing),
                $this->titleOf($listing.'?page=2'),
                "{$listing} serves page two under the same title as page one"
            );
        }
    }

    /*
     * ------------------------------------------------------------------
     * Declared language
     * ------------------------------------------------------------------
     */

    public function test_the_document_declares_french_by_default(): void
    {
        $this->get('/')->assertOk()->assertSee('<html lang="fr"', escape: false);
    }

    public function test_the_document_declares_english_when_english_is_chosen(): void
    {
        // The page was telling a screen reader and a search engine that
        // its English interface was French.
        $this->withCookie(SetLocale::COOKIE, 'en')
            ->get('/')
            ->assertOk()
            ->assertSee('<html lang="en"', escape: false)
            ->assertSee('content="en_US"', escape: false);
    }

    /*
     * ------------------------------------------------------------------
     * Headings
     * ------------------------------------------------------------------
     */

    /**
     * @return list<int>
     */
    private function headingLevels(string $url): array
    {
        preg_match_all('~<h([1-6])[\s>]~', $this->get($url)->assertOk()->getContent(), $m);

        return array_map('intval', $m[1]);
    }

    public function test_no_public_page_skips_a_heading_level(): void
    {
        $pages = [
            '/', '/a-propos', '/a-propos/equipe', '/partenaires', '/actualites',
            '/actualites?categorie=justice-et-citoyennete', '/actualites?page=2',
            '/activites', '/galerie', '/rejoindre', '/contact', '/services',
            '/services/processus-de-consultation', '/services/conditions-acces-gratuite',
            '/services/tarifs-administratifs', '/ressources/fiches-pratiques',
            '/ressources/rapports-annuels', '/web-tv', '/pre-saisine',
            '/mentions-legales', '/conditions-utilisation', '/politique-confidentialite',
        ];

        foreach ($pages as $page) {
            $levels = $this->headingLevels($page);

            $this->assertNotEmpty($levels, "{$page} has no heading at all");
            $this->assertSame(1, $levels[0], "{$page} does not open on an h1");

            for ($i = 1; $i < count($levels); $i++) {
                $this->assertLessThanOrEqual(
                    1,
                    $levels[$i] - $levels[$i - 1],
                    sprintf(
                        '%s jumps from h%d to h%d at heading %d — [%s]',
                        $page, $levels[$i - 1], $levels[$i], $i + 1, implode('', $levels)
                    )
                );
            }
        }
    }

    public function test_every_public_page_has_exactly_one_h1(): void
    {
        foreach (['/', '/actualites', '/activites', '/contact', '/politique-confidentialite'] as $page) {
            $this->assertSame(
                1,
                count(array_filter($this->headingLevels($page), fn (int $l) => $l === 1)),
                "{$page} does not have exactly one h1"
            );
        }
    }

    /*
     * ------------------------------------------------------------------
     * Redirects
     * ------------------------------------------------------------------
     */

    public function test_the_resources_index_redirects_permanently(): void
    {
        // Structural, not a detour: 301, so the destination inherits the
        // standing of the address people already have.
        $this->get('/ressources')
            ->assertStatus(301)
            ->assertRedirect('/ressources/fiches-pratiques');
    }
}
