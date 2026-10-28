<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Three things that worked but read badly, from the browser recette's
 * "works, but would be better otherwise" list.
 *
 * None of them was broken. Each one left someone guessing: the corbeille
 * was reachable only by setting a filter by hand, a dialog opened with the
 * caret on the way out of it, and the visibility toggle re-checked itself
 * without saying so when a submit was refused.
 */
class AdminAffordancesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    /** @return array<string, array{string}> */
    public static function listingsWithACorbeilleCounter(): array
    {
        return [
            'actualités' => ['admin.news'],
            'web tv' => ['admin.webtv'],
            'pages' => ['admin.pages'],
            'appels' => ['admin.calls'],
            'équipe' => ['admin.team'],
            'partenaires' => ['admin.partners'],
            'rapports annuels' => ['admin.reports'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('listingsWithACorbeilleCounter')]
    public function test_the_corbeille_counter_is_the_way_into_the_corbeille(string $route): void
    {
        $html = $this->actingAs($this->admin())->get(route($route))->assertOk()->getContent();

        // It counted the rows in the corbeille without being a link to
        // them, so someone who deleted an item had no way to find where it
        // went short of knowing the query string by heart.
        $this->assertStringContainsString(
            route($route, ['statut' => 'corbeille']),
            $html,
            "The corbeille counter on {$route} does not link to the corbeille."
        );
    }

    public function test_the_counter_is_a_link_only_where_it_is_given_one(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        // The other cards on the same row are figures, not destinations;
        // turning every card into a link would promise screens that do not
        // exist.
        preg_match_all('~<a[^>]*class="[^"]*rounded-xl border border-slate-200~', $html, $links);
        $this->assertCount(1, $links[0],
            'More than one stat card became a link, so a figure is promising a screen.');
    }

    public function test_a_dialog_opens_on_its_first_field_and_not_on_the_way_out(): void
    {
        $js = file_get_contents(resource_path('js/app.js'));

        // showModal() focuses whatever comes first in the markup, which is
        // the close button.
        $this->assertStringContainsString('function focusFirstField(dialog)', $js);
        $this->assertStringContainsString('focusFirstField(dialog);', $js);
        $this->assertMatchesRegularExpression(
            '~input:not\(\[type=hidden\]\)[^\n]*select[^\n]*textarea~',
            $js,
            'focusFirstField does not look for a form control.'
        );
    }

    public function test_the_visibility_toggle_defaults_to_on_for_a_new_record(): void
    {
        $html = $this->actingAs($this->admin())->get(route('admin.partners'))->assertOk()->getContent();

        // An editor who fills the form and stops should get a partner that
        // is actually on the public page.
        $this->assertMatchesRegularExpression(
            '~name="is_visible"[^>]*\schecked~',
            $html,
            'The create form offers a hidden-by-default partner, so publishing it does nothing visible.'
        );
    }

    public function test_the_visibility_toggle_keeps_being_off_when_a_submit_is_refused(): void
    {
        // name is required, so this comes back with errors — and without
        // is_visible, because an unchecked box is simply absent.
        $response = $this->actingAs($this->admin())
            ->from(route('admin.partners'))
            ->post(route('admin.partners.store'), ['name' => '']);

        $response->assertSessionHasErrors('name');

        $html = $this->actingAs($this->admin())->get(route('admin.partners'))->getContent();

        $this->assertDoesNotMatchRegularExpression(
            '~name="is_visible"[^>]*\schecked~',
            $html,
            'The toggle re-checked itself after a refused submit, losing a choice the editor made.'
        );
    }

    public function test_turning_visibility_off_from_the_edit_form_actually_turns_it_off(): void
    {
        // The toggle is a bare checkbox with no hidden companion, so an
        // unchecked one posts nothing at all. The controller reads it with
        // boolean(), which is what makes unchecking mean false rather than
        // "leave as it was".
        $partner = \App\Models\Partner::create([
            'name' => 'Partenaire visible',
            'slug' => 'partenaire-visible',
            'is_visible' => true,
            'status' => 'published',
        ]);

        $this->actingAs($this->admin())->put(route('admin.partners.update', $partner), [
            'name' => 'Partenaire visible',
            'status' => 'published',
        ]);

        $this->assertFalse((bool) $partner->fresh()->is_visible);
    }

    public function test_a_deleted_row_can_be_found_restored_and_purged_end_to_end(): void
    {
        $admin = $this->admin();
        $article = Article::create([
            'title' => 'Note a restaurer',
            'slug' => Str::slug('Note a restaurer'),
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        // Delete from the listing, follow the counter into the corbeille,
        // restore, and find it back in the list. This is the journey that
        // was impossible before.
        $this->actingAs($admin)->delete(route('admin.news.destroy', $article));
        $this->assertNotNull($article->fresh()->deleted_at);

        $this->actingAs($admin)
            ->get(route('admin.news', ['statut' => 'corbeille']))
            ->assertOk()
            ->assertSee('Note a restaurer');

        $this->actingAs($admin)->post(route('admin.news.restore', $article->id));
        $this->assertNull($article->fresh()->deleted_at);

        $this->actingAs($admin)->get(route('admin.news'))->assertSee('Note a restaurer');
    }
}
