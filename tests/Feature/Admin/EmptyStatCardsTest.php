<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * A figure of zero is not drawn.
 *
 * Four cards sat above every listing whatever the data, so a Clinic that
 * had just started met a row of zeros — "0 brouillons", "0 programmées",
 * "0 à la corbeille" — which says nothing except that the figures are
 * real. The row now holds only what there is.
 *
 * One card per screen is exempt: the figure the screen is about. A
 * listing with no cards at all above an empty table reads as a page that
 * failed to load rather than as a screen with nothing in it yet.
 */
class EmptyStatCardsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function article(string $title, string $status = Article::STATUS_PUBLISHED): Article
    {
        return Article::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'status' => $status,
            'published_at' => now()->subDay(),
        ]);
    }

    public function test_a_card_counting_nothing_is_not_rendered(): void
    {
        $this->article('Une actualité');

        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        // Nothing is in the corbeille and nothing is scheduled, so those
        // two cards are absent rather than showing a zero.
        $this->assertStringNotContainsString('À la corbeille', $html);
        $this->assertStringNotContainsString('Programmées', $html);
    }

    public function test_the_card_comes_back_as_soon_as_it_counts_something(): void
    {
        $this->article('Une actualité');
        $this->article('À jeter')->delete();

        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        $this->assertStringContainsString('À la corbeille', $html);
    }

    public function test_the_screens_own_figure_stays_at_zero(): void
    {
        // No articles at all. The listing still says what it is: a screen
        // that renders no cards above an empty table looks broken.
        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        $this->assertStringContainsString('Actualités publiées', $html);
    }

    public static function listings(): array
    {
        return [
            'actualités' => ['admin.news'],
            'web tv' => ['admin.webtv'],
            'équipe' => ['admin.team'],
            'partenaires' => ['admin.partners'],
            'activités' => ['admin.activities'],
            'fiches pratiques' => ['admin.guides'],
            'rapports annuels' => ['admin.reports'],
            'pages' => ['admin.pages'],
            'appels' => ['admin.calls'],
            'utilisateurs' => ['admin.users'],
            'messages' => ['admin.messages'],
            'newsletter' => ['admin.newsletter'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('listings')]
    public function test_every_empty_listing_still_shows_one_card(string $route): void
    {
        $html = $this->actingAs($this->admin())->get(route($route))->assertOk()->getContent();

        // The marker the cards share. At least one survives on a screen
        // with no data at all.
        $this->assertStringContainsString(
            'rounded-xl border border-slate-200 bg-white p-5 shadow-sm',
            $html,
            "{$route} renders no stat card at all when it is empty."
        );
    }
}
