<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The corbeille, and the trap it used to be.
 *
 * Deleting content moves it to a corbeille, and the restore and permanent
 * delete routes existed from the start — but nothing in the interface ever
 * offered them, so a deleted item could not be brought back by anyone
 * without access to the database. Worse, the deleted row still offered
 * "Modifier": the update route binds the model, SoftDeletes excludes a
 * deleted one, and the administrator who saved that form was answered with
 * a 404 on the public site, outside the administration entirely.
 */
class CorbeilleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function article(string $title): Article
    {
        return Article::create([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }

    private function trashedArticle(): Article
    {
        $article = $this->article('Note mise au rebut');
        $article->delete();

        return $article;
    }

    public function test_a_row_in_the_corbeille_offers_restore_and_permanent_delete(): void
    {
        $article = $this->trashedArticle();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.news', ['statut' => 'corbeille']));

        $response->assertOk();
        $response->assertSee(route('admin.news.restore', $article->id));
        $response->assertSee(route('admin.news.force-destroy', $article->id));
    }

    public function test_a_row_in_the_corbeille_does_not_offer_to_be_edited(): void
    {
        $article = $this->trashedArticle();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.news', ['statut' => 'corbeille']));

        // The dialog trigger is what sends the administrator to the update
        // route, and the update route cannot resolve a deleted row.
        $response->assertDontSee('data-open-dialog="edit-article-'.$article->id.'"', escape: false);
    }

    public function test_a_live_row_offers_the_ordinary_controls_and_not_the_corbeille_ones(): void
    {
        $article = $this->article('Note en ligne');

        $response = $this->actingAs($this->admin())->get(route('admin.news'));

        $response->assertSee('data-open-dialog="edit-article-'.$article->id.'"', escape: false);
        $response->assertDontSee(route('admin.news.restore', $article->id));
    }

    public function test_restoring_puts_the_row_back_in_the_listing(): void
    {
        $article = $this->trashedArticle();

        $this->actingAs($this->admin())
            ->post(route('admin.news.restore', $article->id))
            ->assertRedirect(route('admin.news'));

        $this->assertNull($article->fresh()->deleted_at);
    }

    public function test_a_permanent_delete_is_refused_to_an_editeur(): void
    {
        $article = $this->trashedArticle();

        $this->actingAs(User::factory()->editor()->create())
            ->delete(route('admin.news.force-destroy', $article->id))
            ->assertForbidden();

        $this->assertNotNull(Article::withTrashed()->find($article->id));
    }

    public function test_an_editeur_is_not_shown_the_permanent_delete(): void
    {
        $article = $this->trashedArticle();

        $response = $this->actingAs(User::factory()->editor()->create())
            ->get(route('admin.news', ['statut' => 'corbeille']));

        // Offering a control that answers 403 is worse than not offering it.
        $response->assertSee(route('admin.news.restore', $article->id));
        $response->assertDontSee(route('admin.news.force-destroy', $article->id));
    }

    public function test_an_administrator_can_purge_a_row_for_good(): void
    {
        $article = $this->trashedArticle();

        $this->actingAs($this->admin())
            ->delete(route('admin.news.force-destroy', $article->id))
            ->assertRedirect(route('admin.news'));

        $this->assertNull(Article::withTrashed()->find($article->id));
    }

    public function test_the_delete_confirmation_does_not_call_the_corbeille_irreversible(): void
    {
        $this->article('Note publiee');

        $response = $this->actingAs($this->admin())->get(route('admin.news'));

        $response->assertSee('à la corbeille ?', escape: false);
        $response->assertDontSee('Cette action est irréversible', escape: false);
    }

    public function test_the_permanent_delete_is_the_one_that_says_irreversible(): void
    {
        $this->trashedArticle();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.news', ['statut' => 'corbeille']));

        $response->assertSee('Cette action est irréversible', escape: false);
    }
}
