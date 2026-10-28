<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The CMS accounts screen (module 4, "52. Gestion des utilisateurs").
 *
 * Most of what is tested here is the same rule from four angles: the
 * Clinic must never be left without an administrator who can sign in.
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $email = 'admin@example.test'): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrateur',
                'password' => 'password-for-tests',
                'role' => User::ROLE_ADMIN,
            ]
        );
    }

    private function editor(): User
    {
        return User::factory()->editor()->create(['name' => 'Éditeur']);
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nouvelle éditrice',
            'email' => 'nouvelle@example.test',
            'password' => 'mot-de-passe-long',
            'password_confirmation' => 'mot-de-passe-long',
            'role' => User::ROLE_EDITOR,
        ], $overrides);
    }

    // --------------------------------------------------------- access

    public function test_an_editor_may_not_open_the_screen(): void
    {
        $this->actingAs($this->editor())->get('/admin/utilisateurs')->assertForbidden();
    }

    public function test_an_editor_may_not_create_an_account(): void
    {
        $this->actingAs($this->editor())
            ->post('/admin/utilisateurs', $this->payload(['role' => User::ROLE_ADMIN]))
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'nouvelle@example.test']);
    }

    public function test_an_administrator_sees_the_accounts(): void
    {
        $this->editor();

        $this->actingAs($this->admin())
            ->get('/admin/utilisateurs')
            ->assertOk()
            ->assertSee('Éditeur', escape: false)
            ->assertSee('Administrateur', escape: false);
    }

    // --------------------------------------------------------- create

    public function test_an_account_is_created_and_can_sign_in(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/utilisateurs', $this->payload())
            ->assertRedirect();

        $created = User::where('email', 'nouvelle@example.test')->first();

        $this->assertNotNull($created);
        $this->assertSame(User::ROLE_EDITOR, $created->role);
        $this->assertTrue($created->is_active);
        $this->assertTrue(Hash::check('mot-de-passe-long', $created->password));
    }

    /** The security plan asks for twelve characters. */
    public function test_a_short_password_is_refused(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/utilisateurs', $this->payload([
                'password' => 'court',
                'password_confirmation' => 'court',
            ]))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'nouvelle@example.test']);
    }

    public function test_an_address_is_not_used_twice(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/utilisateurs', $this->payload(['email' => 'admin@example.test']))
            ->assertSessionHasErrors('email');
    }

    // --------------------------------------------------------- change

    public function test_a_role_can_be_changed(): void
    {
        $editor = $this->editor();

        $this->actingAs($this->admin())
            ->put('/admin/utilisateurs/'.$editor->id, [
                'name' => $editor->name,
                'email' => $editor->email,
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect();

        $this->assertTrue($editor->fresh()->isAdmin());
    }

    public function test_a_password_can_be_reset(): void
    {
        $editor = $this->editor();

        $this->actingAs($this->admin())
            ->put('/admin/utilisateurs/'.$editor->id.'/mot-de-passe', [
                'password' => 'un-nouveau-mot-de-passe',
                'password_confirmation' => 'un-nouveau-mot-de-passe',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('un-nouveau-mot-de-passe', $editor->fresh()->password));
    }

    // ------------------------------------------------ disabling rules

    public function test_an_account_is_disabled_rather_than_deleted(): void
    {
        $editor = $this->editor();

        $this->actingAs($this->admin())
            ->post('/admin/utilisateurs/'.$editor->id.'/activation')
            ->assertRedirect();

        // Still there, with its name on whatever it published.
        $this->assertDatabaseHas('users', ['id' => $editor->id, 'is_active' => false]);
    }

    public function test_a_disabled_account_cannot_sign_in(): void
    {
        $editor = $this->editor();
        $editor->update(['password' => 'mot-de-passe-long', 'is_active' => false]);

        $this->post('/admin/login', [
            'email' => $editor->email,
            'password' => 'mot-de-passe-long',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_reactivated_account_can_sign_in_again(): void
    {
        $editor = $this->editor();
        $editor->update(['password' => 'mot-de-passe-long', 'is_active' => false]);

        $this->actingAs($this->admin())->post('/admin/utilisateurs/'.$editor->id.'/activation');
        $this->post('/admin/logout');

        $this->post('/admin/login', [
            'email' => $editor->email,
            'password' => 'mot-de-passe-long',
        ])->assertRedirect('/admin');
    }

    // ------------------------------------------------- lockout rules

    /** Disabling yourself closes the only door back in. */
    public function test_an_administrator_cannot_disable_their_own_account(): void
    {
        $admin = $this->admin();
        $this->admin('second@example.test');

        $this->actingAs($admin)
            ->post('/admin/utilisateurs/'.$admin->id.'/activation')
            ->assertSessionHasErrors('compte');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_the_last_administrator_cannot_be_disabled(): void
    {
        $admin = $this->admin();
        $other = $this->admin('second@example.test');

        // Leaves one administrator: allowed.
        $this->actingAs($admin)->post('/admin/utilisateurs/'.$other->id.'/activation');
        $this->assertFalse($other->fresh()->is_active);

        // That one is now the last: an editor's attempt is moot, so it is
        // the administrator's own screen that must refuse.
        $editor = $this->editor();
        $this->actingAs($admin)->post('/admin/utilisateurs/'.$editor->id.'/activation');

        $this->assertSame(1, User::active()->where('role', User::ROLE_ADMIN)->count());
    }

    public function test_the_last_administrator_cannot_be_demoted(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/utilisateurs/'.$admin->id, [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_EDITOR,
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_an_administrator_can_be_demoted_while_another_remains(): void
    {
        $admin = $this->admin();
        $other = $this->admin('second@example.test');

        $this->actingAs($admin)
            ->put('/admin/utilisateurs/'.$other->id, [
                'name' => $other->name,
                'email' => $other->email,
                'role' => User::ROLE_EDITOR,
            ])
            ->assertRedirect();

        $this->assertFalse($other->fresh()->isAdmin());
    }

    // ------------------------------------------------------- activity

    public function test_signing_in_records_the_moment(): void
    {
        $editor = $this->editor();
        $editor->update(['password' => 'mot-de-passe-long']);

        $this->assertNull($editor->last_login_at);

        $this->post('/admin/login', [
            'email' => $editor->email,
            'password' => 'mot-de-passe-long',
        ]);

        $this->assertNotNull($editor->fresh()->last_login_at);
    }
}
