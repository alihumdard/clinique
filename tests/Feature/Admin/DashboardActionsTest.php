<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The row controls on the dashboard.
 *
 * Three faults, all with the same shape: a control that was drawn without
 * anywhere to send you. The dashboard lists recent articles but holds no
 * edit dialog, so Modifier fell back to href="#" and the page appeared to
 * reload; Aperçu did the same for a draft, which has no public page; and
 * Supprimer worked but its controller sent you to the actualités listing,
 * so deleting from the dashboard threw you into another screen.
 *
 * Modifier was first repaired by linking to the listing with ?ouvrir=,
 * which gave the control a destination but kept the shape of the third
 * fault: pressing it still replaced the screen being worked on. The
 * dialogs now live in a partial both screens include, and update()
 * returns to whichever one submitted the form.
 */
class DashboardActionsTest extends TestCase
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

    public function test_no_control_on_the_dashboard_points_at_nothing(): void
    {
        $this->article('Note publiee');
        $this->article('Note en brouillon', Article::STATUS_DRAFT);

        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        // href="#" reloads the page. From the person's seat the screen
        // simply refused to do anything.
        $this->assertStringNotContainsString('href="#"', $html);
        $this->assertStringNotContainsString('action="#"', $html);
    }

    public function test_modifier_on_the_dashboard_opens_a_dialog_on_the_dashboard(): void
    {
        $article = $this->article('Note a modifier');

        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        // The dialog is on this page. It used to be a link to the
        // actualités listing, which answered Modifier by replacing the
        // screen the administrator was working on — and left them there
        // once the dialog closed.
        $this->assertStringContainsString('id="edit-article-'.$article->id.'"', $html);
        $this->assertStringNotContainsString(
            route('admin.news', ['ouvrir' => 'edit-article-'.$article->id]),
            $html
        );
    }

    public function test_the_dashboards_edit_dialogs_sit_outside_the_table(): void
    {
        $this->article('Note a modifier');

        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        // A dialog is not valid content between tr elements: a browser
        // recovering from that parse lifts it out of the table and
        // corrupts the form inside it, without raising a console error.
        $table = Str::between($html, '<table', '</table>');

        $this->assertStringNotContainsString('<dialog', $table);
    }

    public function test_the_dialog_still_exists_on_the_listing(): void
    {
        $article = $this->article('Note a modifier');

        // Both screens render the same partial, so neither can lose it.
        $this->actingAs($this->admin())
            ->get(route('admin.news', ['ouvrir' => 'edit-article-'.$article->id]))
            ->assertOk()
            ->assertSee('id="edit-article-'.$article->id.'"', escape: false);
    }

    public function test_editing_from_the_dashboard_returns_to_the_dashboard(): void
    {
        $article = $this->article('Note a modifier');

        $this->actingAs($this->admin())
            ->from(route('admin.dashboard'))
            ->put(route('admin.news.update', $article), [
                'title' => 'Note modifiee',
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status');

        $this->assertSame('Note modifiee', $article->fresh()->title);
    }

    public function test_editing_from_the_listing_still_returns_to_the_listing(): void
    {
        $article = $this->article('Note a modifier');

        $this->actingAs($this->admin())
            ->from(route('admin.news'))
            ->put(route('admin.news.update', $article), [
                'title' => 'Note modifiee',
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.news'));
    }

    public function test_an_edit_with_nowhere_to_go_back_to_lands_on_the_listing(): void
    {
        $article = $this->article('Note a modifier');

        // Without a fallback, back() falls through to the public home page.
        $this->actingAs($this->admin())
            ->put(route('admin.news.update', $article), [
                'title' => 'Note modifiee',
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.news'));
    }

    public function test_a_draft_offers_no_preview_because_it_has_no_public_page(): void
    {
        $article = $this->article('Note en brouillon', Article::STATUS_DRAFT);

        $html = $this->actingAs($this->admin())->get(route('admin.dashboard'))->assertOk()->getContent();

        // The control is absent, not dead.
        $this->assertStringNotContainsString('Aperçu : Note en brouillon', $html);
        $this->assertStringNotContainsString(route('news.show', $article), $html);
    }

    public function test_a_published_article_does_offer_a_preview(): void
    {
        $article = $this->article('Note publiee');

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('news.show', $article), escape: false);
    }

    public function test_deleting_from_the_dashboard_returns_to_the_dashboard(): void
    {
        $article = $this->article('Note a supprimer');

        $this->actingAs($this->admin())
            ->from(route('admin.dashboard'))
            ->delete(route('admin.news.destroy', $article))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status');

        $this->assertNotNull($article->fresh()->deleted_at);
    }

    public function test_the_dashboard_can_show_a_confirmation(): void
    {
        $article = $this->article('Note a supprimer');

        // Every other admin screen renders the flash. The dashboard did
        // not, which went unnoticed while no action ever returned here.
        $this->actingAs($this->admin())
            ->from(route('admin.dashboard'))
            ->delete(route('admin.news.destroy', $article))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('a été mise à la corbeille', escape: false);
    }

    public function test_deleting_from_the_listing_still_returns_to_the_listing(): void
    {
        $article = $this->article('Note a supprimer');

        $this->actingAs($this->admin())
            ->from(route('admin.news'))
            ->delete(route('admin.news.destroy', $article))
            ->assertRedirect(route('admin.news'));
    }

    public function test_a_delete_with_nowhere_to_go_back_to_lands_on_the_listing(): void
    {
        $article = $this->article('Note a supprimer');

        // back() needs a previous URL. Without one it must not fall through
        // to the public home page.
        $this->actingAs($this->admin())
            ->delete(route('admin.news.destroy', $article))
            ->assertRedirect(route('admin.news'));
    }
}
