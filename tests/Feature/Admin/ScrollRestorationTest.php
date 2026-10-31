<?php

namespace Tests\Feature\Admin;

use App\Models\Registre\User as RegistreUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The hook that keeps an administrator's place across a row action.
 *
 * Supprimer, Dupliquer and Masquer post, and the controller answers with
 * a redirect — so the browser loads a fresh document at the top. On the
 * dashboard the recent-articles panel sits below a chart, and on a long
 * listing the row may be hundreds of pixels down: acting on a row threw
 * the administrator back to the top, away from the row and from the
 * confirmation that it had worked.
 *
 * `initAdminScrollRestore` in app.js writes the offset before the form
 * leaves and restores it on the next load of the same path. It finds the
 * forms with `form.closest('.admin-shell')`, which is why that class on
 * the body is a contract rather than styling: rename it and every screen
 * below silently goes back to jumping. PHPUnit cannot run the script, so
 * what is asserted here is the contract it depends on.
 */
class ScrollRestorationTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    public static function adminScreens(): array
    {
        return [
            'tableau de bord' => ['admin.dashboard'],
            'actualités' => ['admin.news'],
            'web tv' => ['admin.webtv'],
            'partenaires' => ['admin.partners'],
            'équipe' => ['admin.team'],
            'activités' => ['admin.activities'],
            'fiches pratiques' => ['admin.guides'],
            'rapports annuels' => ['admin.reports'],
            'pages' => ['admin.pages'],
            'appels' => ['admin.calls'],
            'utilisateurs' => ['admin.users'],
            'référentiels' => ['admin.references'],
            'messages' => ['admin.messages'],
            'newsletter' => ['admin.newsletter'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminScreens')]
    public function test_an_admin_screen_carries_the_shell_the_hook_looks_for(string $route): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route($route))
            ->assertOk()
            ->assertSee('admin-shell', escape: false);
    }

    public static function registreScreens(): array
    {
        return [
            'tableau de bord' => ['registre.dashboard'],
            'dossiers' => ['registre.dossiers'],
            'demandes' => ['registre.submissions'],
            'comptes' => ['registre.accounts'],
            'journal' => ['registre.journal'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('registreScreens')]
    public function test_a_registre_screen_carries_the_same_shell(string $route): void
    {
        // The registre reuses the admin components deliberately, so it
        // inherits this behaviour and must keep the class that drives it.
        $user = RegistreUser::create([
            'name' => 'Coordination',
            'email' => 'coordination+'.uniqid().'@registre.test',
            'password' => bcrypt('secret-de-test'),
            'role' => 'coordinateur',
            'is_active' => true,
        ]);

        $this->actingAs($user, 'registre')
            ->get(route($route))
            ->assertOk()
            ->assertSee('admin-shell', escape: false);
    }

    public function test_the_public_site_is_left_alone(): void
    {
        // The hook is scoped to the administration on purpose: a public
        // form has nothing to restore and the newsletter sign-up sits in
        // the footer, where holding the offset would hide the answer.
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('admin-shell', escape: false);
    }
}
