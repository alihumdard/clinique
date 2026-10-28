<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Your own CMS account (module 4, "54. Profil utilisateur").
 *
 * The security plan asks that a password be changed at first sign-in,
 * which nobody could do while only an administrator could set one — and
 * would still know it afterwards.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): User
    {
        return User::factory()->editor()->create([
            'name' => 'Éditeur',
            'email' => 'editeur@example.test',
            'password' => 'password-for-tests',
        ]);
    }

    /** Not administrator-only: every account needs this one. */
    public function test_an_editor_can_open_their_own_account(): void
    {
        $this->actingAs($this->editor())
            ->get('/admin/mon-compte')
            ->assertOk()
            ->assertSee('Mon compte', escape: false);
    }

    public function test_a_visitor_cannot(): void
    {
        $this->get('/admin/mon-compte')->assertRedirect('/admin/login');
    }

    public function test_you_can_change_your_own_password(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->put('/admin/mon-compte/mot-de-passe', [
                'current_password' => 'password-for-tests',
                'password' => 'un-nouveau-mot-de-passe',
                'password_confirmation' => 'un-nouveau-mot-de-passe',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('un-nouveau-mot-de-passe', $editor->fresh()->password));
    }

    /** A session left open must not be enough to take over an account. */
    public function test_the_current_password_is_required(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->put('/admin/mon-compte/mot-de-passe', [
                'current_password' => 'ce-n-est-pas-le-bon',
                'password' => 'un-nouveau-mot-de-passe',
                'password_confirmation' => 'un-nouveau-mot-de-passe',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-for-tests', $editor->fresh()->password));
    }

    public function test_a_short_new_password_is_refused(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->put('/admin/mon-compte/mot-de-passe', [
                'current_password' => 'password-for-tests',
                'password' => 'court',
                'password_confirmation' => 'court',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_you_can_change_your_own_name(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->put('/admin/mon-compte', ['name' => 'Éditrice', 'email' => $editor->email])
            ->assertRedirect();

        $this->assertSame('Éditrice', $editor->fresh()->name);
    }

    /** Your own profile is not where you raise your own rights. */
    public function test_your_profile_does_not_change_your_role(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->put('/admin/mon-compte', [
                'name' => $editor->name,
                'email' => $editor->email,
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect();

        $this->assertFalse($editor->fresh()->isAdmin());
    }
}
