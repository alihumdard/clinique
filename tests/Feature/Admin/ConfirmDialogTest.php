<?php

namespace Tests\Feature\Admin;

use App\Models\Registre\User as RegistreUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The question an action asks before it runs.
 *
 * It used to be window.confirm(). That box belongs to the operating
 * system: it prints the address — "127.0.0.1:8123 says" — above the
 * question, cannot be styled, orders its buttons the OS way rather than
 * ours and labels them in the browser's language while everything around
 * it is in French. Beside these screens it read as a security warning
 * rather than as something the Clinic was asking.
 *
 * The replacement is one <dialog> per area, filled in by app.js from the
 * form that asked. PHPUnit cannot press it, so what is asserted here is
 * the markup it needs and the wording the forms carry.
 */
class ConfirmDialogTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    public static function adminScreens(): array
    {
        return [
            'actualités' => ['admin.news'],
            'web tv' => ['admin.webtv'],
            'équipe' => ['admin.team'],
            'partenaires' => ['admin.partners'],
            'activités' => ['admin.activities'],
            'référentiels' => ['admin.references'],
            'utilisateurs' => ['admin.users'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminScreens')]
    public function test_an_admin_screen_carries_the_confirmation_dialog(string $route): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route($route))
            ->assertOk()
            ->assertSee('id="confirm-dialog"', escape: false)
            ->assertSee('data-confirm-accept', escape: false)
            ->assertSee('data-confirm-cancel', escape: false);
    }

    public function test_the_registre_carries_the_same_dialog(): void
    {
        $user = RegistreUser::create([
            'name' => 'Coordination',
            'email' => 'coordination+'.uniqid().'@registre.test',
            'password' => bcrypt('secret-de-test'),
            'role' => 'coordinateur',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'registre')
            ->get(route('registre.dossiers'))
            ->assertOk()
            ->assertSee('id="confirm-dialog"', escape: false);
    }

    public function test_a_corbeille_delete_does_not_claim_to_be_irreversible(): void
    {
        // A row, so that there is a delete form on the page to carry the
        // wording: an empty listing renders none.
        \App\Models\Article::create([
            'title' => 'Une actualité',
            'slug' => 'une-actualite',
            'status' => \App\Models\Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $html = $this->actingAs(User::factory()->create())
            ->get(route('admin.news'))
            ->assertOk()
            ->getContent();

        // The row goes to the corbeille and can be restored from there.
        // "Cette action est irréversible" was true only while nothing
        // could be brought back, and it is the sentence that decides
        // which label the accept button takes.
        $this->assertStringContainsString('à la corbeille ?', $html);
        $this->assertStringContainsString('Vous pourrez le restaurer', $html);
    }

    public function test_the_public_site_has_no_admin_confirmation_dialog(): void
    {
        // It belongs to the administration; a public page has no action
        // that needs confirming.
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('id="confirm-dialog"', escape: false);
    }
}
