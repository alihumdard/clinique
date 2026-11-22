<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Approved access level: « Membre de la Clinique : peut soumettre des
 * articles et des photos, qui sont publiés uniquement après validation par
 * le coordinateur. »
 */
class MembreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function membre(string $email = 'membre@example.test'): User
    {
        return User::factory()->membre()->create(['email' => $email]);
    }

    private function coordinateur(): User
    {
        return User::factory()->coordinateur()->create();
    }

    public function test_a_membre_submission_waits_for_validation_whatever_status_was_posted(): void
    {
        $this->actingAs($this->membre())
            ->post('/admin/actualites', [
                'title' => 'Une actualité proposée',
                'status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->toDateString(),
            ])
            ->assertRedirect();

        $article = Article::where('slug', 'une-actualite-proposee')->firstOrFail();

        $this->assertSame(Article::STATUS_PENDING, $article->status);
        $this->assertNull($article->published_at);
        $this->assertFalse($article->isPublic());
    }

    public function test_a_pending_submission_is_not_on_the_public_site(): void
    {
        Article::create([
            'title' => 'En attente',
            'slug' => 'en-attente',
            'status' => Article::STATUS_PENDING,
        ]);

        $this->get('/actualites/en-attente')->assertNotFound();
    }

    public function test_a_membre_activity_and_its_photos_wait_for_validation(): void
    {
        $membre = $this->membre();

        $this->actingAs($membre)
            ->post('/admin/activites', [
                'title' => 'Caravane proposée',
                'status' => Activity::STATUS_PUBLISHED,
            ])
            ->assertRedirect();

        $activity = Activity::where('title', 'Caravane proposée')->firstOrFail();

        $this->assertSame(Activity::STATUS_PENDING, $activity->status);
        $this->assertSame($membre->id, (int) $activity->author_id);
    }

    public function test_the_coordinateur_publishes_a_pending_submission(): void
    {
        $article = Article::create([
            'title' => 'À valider',
            'slug' => 'a-valider',
            'status' => Article::STATUS_PENDING,
        ]);

        $this->actingAs($this->coordinateur())
            ->post("/admin/actualites/{$article->id}/statut", ['status' => Article::STATUS_PUBLISHED])
            ->assertRedirect();

        $this->assertTrue($article->fresh()->isPublic());
    }

    public function test_a_membre_may_not_publish(): void
    {
        $membre = $this->membre();
        $article = Article::create([
            'title' => 'La sienne',
            'slug' => 'la-sienne',
            'status' => Article::STATUS_PENDING,
            'author_id' => $membre->id,
        ]);

        $this->actingAs($membre)
            ->post("/admin/actualites/{$article->id}/statut", ['status' => Article::STATUS_PUBLISHED])
            ->assertForbidden();

        $this->assertSame(Article::STATUS_PENDING, $article->fresh()->status);
    }

    public function test_a_membre_edits_their_own_pending_submission(): void
    {
        $membre = $this->membre();
        $article = Article::create([
            'title' => 'Premier jet',
            'slug' => 'premier-jet',
            'status' => Article::STATUS_PENDING,
            'author_id' => $membre->id,
        ]);

        $this->actingAs($membre)
            ->put("/admin/actualites/{$article->id}", [
                'title' => 'Premier jet corrigé',
                'status' => Article::STATUS_PUBLISHED,
            ])
            ->assertRedirect();

        $this->assertSame('Premier jet corrigé', $article->fresh()->title);
        $this->assertSame(Article::STATUS_PENDING, $article->fresh()->status);
    }

    public function test_a_membre_may_not_edit_someone_elses_submission(): void
    {
        $other = $this->membre('autre@example.test');
        $article = Article::create([
            'title' => "D'un autre",
            'slug' => 'd-un-autre',
            'status' => Article::STATUS_PENDING,
            'author_id' => $other->id,
        ]);

        $this->actingAs($this->membre())
            ->put("/admin/actualites/{$article->id}", ['title' => 'Détourné', 'status' => 'pending'])
            ->assertForbidden();
    }

    public function test_a_membre_may_not_edit_once_published(): void
    {
        $membre = $this->membre();
        $article = Article::create([
            'title' => 'Déjà publiée',
            'slug' => 'deja-publiee',
            'status' => Article::STATUS_PUBLISHED,
            'author_id' => $membre->id,
        ]);

        $this->actingAs($membre)
            ->put("/admin/actualites/{$article->id}", ['title' => 'Modifiée', 'status' => 'pending'])
            ->assertForbidden();

        $this->assertSame('Déjà publiée', $article->fresh()->title);
    }

    public function test_a_membre_sees_only_their_own_submissions(): void
    {
        $membre = $this->membre();
        Article::create(['title' => 'Ma contribution', 'slug' => 'ma-contribution', 'status' => 'pending', 'author_id' => $membre->id]);
        Article::create(['title' => 'Article du coordinateur', 'slug' => 'article-coord', 'status' => 'draft']);

        $this->actingAs($membre)
            ->get('/admin/actualites')
            ->assertOk()
            ->assertSee('Ma contribution')
            ->assertDontSee('Article du coordinateur');
    }

    /** @return array<string, array{string}> */
    public static function coordinatorOnlyScreens(): array
    {
        return [
            'web tv' => ['/admin/web-tv'],
            'pages' => ['/admin/pages'],
            'équipe' => ['/admin/equipe'],
            'partenaires' => ['/admin/partenaires'],
            'fiches' => ['/admin/fiches-pratiques'],
            'rapports' => ['/admin/rapports-annuels'],
            'appels' => ['/admin/appels'],
            'messages' => ['/admin/messages'],
            'newsletter' => ['/admin/newsletter'],
            'utilisateurs' => ['/admin/utilisateurs'],
            'paramètres' => ['/admin/parametres'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('coordinatorOnlyScreens')]
    public function test_a_membre_may_not_open_other_screens(string $url): void
    {
        $this->actingAs($this->membre())->get($url)->assertForbidden();
    }

    public function test_the_activities_screen_renders_for_both_roles(): void
    {
        $membre = $this->membre();
        Activity::create(['title' => 'Sa caravane', 'slug' => 'sa-caravane', 'status' => 'pending', 'author_id' => $membre->id]);

        $this->actingAs($membre)
            ->get('/admin/activites')
            ->assertOk()
            ->assertSee('Sa caravane')
            ->assertSee('Envoyer pour validation');

        $this->actingAs($this->coordinateur())
            ->get('/admin/activites?statut=pending')
            ->assertOk()
            ->assertSee('Sa caravane')
            ->assertSee('En attente de validation');
    }

    public function test_a_membre_lands_on_their_contributions(): void
    {
        $this->actingAs($this->membre())->get('/admin')->assertRedirect('/admin/actualites');
    }

    public function test_the_dashboard_tells_the_coordinateur_what_is_waiting(): void
    {
        Article::create(['title' => 'En file', 'slug' => 'en-file', 'status' => 'pending']);

        $this->actingAs($this->coordinateur())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Contributions en attente de validation');
    }
}
