<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Module 2 asks for "l'authentification administrateur et les
 * autorisations des éditeurs de contenu". Every signed-in account used to
 * reach every screen: an éditeur could rewrite the site settings, edit the
 * référentiels other screens choose from, read the personal data left by
 * the public and delete a row for good.
 */
class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): User
    {
        return User::create([
            'name' => 'Éditeur',
            'email' => 'editeur@example.test',
            'password' => 'password-for-tests',
            'role' => User::ROLE_EDITOR,
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Administrateur',
            'email' => 'admin@example.test',
            'password' => 'password-for-tests',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /** @return array<string, array{string}> */
    public static function administratorOnlyScreens(): array
    {
        return [
            'paramètres' => ['/admin/parametres'],
            'référentiels' => ['/admin/referentiels'],
            'messages de contact' => ['/admin/messages'],
            'newsletter' => ['/admin/newsletter'],
            'export newsletter' => ['/admin/newsletter/export'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('administratorOnlyScreens')]
    public function test_an_editor_may_not_open_an_administrator_screen(string $url): void
    {
        $this->actingAs($this->editor())->get($url)->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('administratorOnlyScreens')]
    public function test_an_administrator_may_open_it(string $url): void
    {
        $this->actingAs($this->admin())->get($url)->assertOk();
    }

    public function test_an_editor_may_not_change_the_site_settings(): void
    {
        $this->actingAs($this->editor())
            ->put('/admin/parametres', ['contact_email' => 'detourne@example.test'])
            ->assertForbidden();

        $this->assertDatabaseMissing('settings', ['value' => 'detourne@example.test']);
    }

    /** Permanent deletion empties the corbeille for good. */
    public function test_an_editor_may_not_delete_a_row_for_good(): void
    {
        $article = Article::create([
            'title' => 'À supprimer',
            'slug' => 'a-supprimer',
            'status' => Article::STATUS_DRAFT,
        ]);
        $article->delete();

        $this->actingAs($this->editor())
            ->delete("/admin/actualites/{$article->id}/definitif")
            ->assertForbidden();

        $this->assertNotNull(Article::withTrashed()->find($article->id));
    }

    /** The point of the role: content work is still fully open. */
    public function test_an_editor_may_write_and_publish_content(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->get('/admin/actualites')
            ->assertOk();

        $this->actingAs($editor)
            ->post('/admin/actualites', [
                'title' => 'Une actualité rédigée par un éditeur',
                'status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('articles', ['slug' => 'une-actualite-redigee-par-un-editeur']);
    }

    /** And the screens they cannot open are not offered to them either. */
    public function test_the_sidebar_hides_the_administration_group_from_an_editor(): void
    {
        $this->actingAs($this->editor())
            ->get('/admin')
            ->assertOk()
            ->assertDontSee('Paramètres du site', escape: false)
            ->assertDontSee('Référentiels', escape: false)
            ->assertSee('Actualités', escape: false);

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Paramètres du site', escape: false);
    }

    /** An account created without a role stated gets the lesser of the two. */
    public function test_a_new_account_defaults_to_editor(): void
    {
        $user = User::create([
            'name' => 'Sans rôle',
            'email' => 'sans-role@example.test',
            'password' => 'password-for-tests',
        ]);

        $this->assertFalse($user->fresh()->isAdmin());
    }
}
