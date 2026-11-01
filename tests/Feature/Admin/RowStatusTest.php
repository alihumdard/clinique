<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Changing a row's status from the listing.
 *
 * Publishing used to mean opening the edit dialog, finding the field
 * among a dozen others and saving the whole form for one column. The
 * listing already showed the status in its own column; it now changes it
 * there.
 *
 * The control is a select that posts its own form. It was first written
 * as onchange="this.form.requestSubmit()" and never ran once: the
 * Content-Security-Policy here is script-src 'self', which forbids an
 * inline handler, so the select changed and nothing was saved. The
 * handler lives in app.js now and the markup carries data-submit-on-change.
 */
class RowStatusTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function article(string $title, string $status = Article::STATUS_DRAFT, $publishedAt = null): Article
    {
        return Article::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'status' => $status,
            'published_at' => $publishedAt,
        ]);
    }

    public function test_a_row_can_be_published_from_the_listing(): void
    {
        $article = $this->article('Une actualité');

        $this->actingAs($this->admin())
            ->from(route('admin.news'))
            ->post(route('admin.news.status', $article), ['status' => Article::STATUS_PUBLISHED])
            ->assertRedirect(route('admin.news'))
            ->assertSessionHas('status');

        $this->assertSame(Article::STATUS_PUBLISHED, $article->fresh()->status);
    }

    public function test_publishing_for_the_first_time_gives_the_row_a_date(): void
    {
        $article = $this->article('Sans date');
        $this->assertNull($article->published_at);

        $this->actingAs($this->admin())
            ->post(route('admin.news.status', $article), ['status' => Article::STATUS_PUBLISHED]);

        // Every public listing orders and filters on this column, so a
        // published row without one would never appear.
        $this->assertNotNull($article->fresh()->published_at);
    }

    public function test_a_row_that_has_been_published_before_keeps_its_date(): void
    {
        $was = now()->subYear()->startOfDay();
        $article = $this->article('Déjà parue', Article::STATUS_PUBLISHED, $was);

        $this->actingAs($this->admin())
            ->post(route('admin.news.status', $article), ['status' => Article::STATUS_DRAFT]);
        $this->actingAs($this->admin())
            ->post(route('admin.news.status', $article), ['status' => Article::STATUS_PUBLISHED]);

        // Re-publishing is not re-dating: the article came out last year
        // and saying otherwise would move it to the top of the listing.
        $this->assertTrue($was->equalTo($article->fresh()->published_at));
    }

    public function test_a_status_that_does_not_exist_is_refused(): void
    {
        $article = $this->article('Une actualité');

        $this->actingAs($this->admin())
            ->from(route('admin.news'))
            ->post(route('admin.news.status', $article), ['status' => 'supprimé'])
            ->assertSessionHasErrors('status');

        $this->assertSame(Article::STATUS_DRAFT, $article->fresh()->status);
    }

    public function test_the_select_carries_no_inline_handler(): void
    {
        $this->article('Une actualité');

        $html = $this->actingAs($this->admin())->get(route('admin.news'))->assertOk()->getContent();

        // script-src 'self' blocks an inline handler outright, and the
        // browser reports it in the console rather than failing loudly —
        // so the control simply did nothing.
        $this->assertStringContainsString('data-submit-on-change', $html);
        $this->assertStringNotContainsString('onchange=', $html);
    }

    public function test_a_deleted_row_offers_no_status_control(): void
    {
        $this->article('À jeter')->delete();

        $html = $this->actingAs($this->admin())
            ->get(route('admin.news', ['statut' => 'corbeille']))
            ->assertOk()
            ->getContent();

        /*
         * A row in the corbeille offers Restaurer and nothing else.
         *
         * The class is what identifies this control rather than its name
         * or its data attribute: every edit dialog on the page carries a
         * status field of its own, and the rows-per-page select in the
         * footer carries data-submit-on-change.
         */
        $this->assertStringNotContainsString('rounded-full border py-1', $html);
        $this->assertStringContainsString('Corbeille', $html);
    }
}
