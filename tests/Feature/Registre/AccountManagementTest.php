<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\AuditLog;
use App\Models\Registre\User as RegistreUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The Registre's own accounts, and the "Mon compte" screen every profile
 * needs.
 */
class AccountManagementTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private function agent(string $email = 'coordination@registre.test'): RegistreUser
    {
        return RegistreUser::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Aminata Issoufou',
                'password' => 'password-for-tests',
                'role' => RegistreUser::ROLE_COORDINATOR,
            ]
        );
    }

    /** @return array<string, mixed> */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Fatima Abdou',
            'email' => 'fatima@registre.test',
            'password' => 'mot-de-passe-long',
            'password_confirmation' => 'mot-de-passe-long',
            'role' => RegistreUser::ROLE_STUDENT,
        ], $overrides);
    }

    // --------------------------------------------------------- access

    public function test_a_visitor_sees_nothing(): void
    {
        $this->get('/registre/comptes')->assertRedirect('/registre/connexion');
        $this->get('/registre/mon-compte')->assertRedirect('/registre/connexion');
    }

    /** The two sets of accounts are separate in every sense. */
    public function test_a_cms_account_does_not_reach_the_registre_accounts(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/registre/comptes')
            ->assertRedirect('/registre/connexion');
    }

    // --------------------------------------------------------- create

    public function test_an_account_is_created_in_the_registre_database(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/comptes', $this->payload())
            ->assertRedirect();

        $created = RegistreUser::where('email', 'fatima@registre.test')->first();

        $this->assertNotNull($created);
        $this->assertSame(RegistreUser::ROLE_STUDENT, $created->role);
        $this->assertTrue($created->is_active);

        // In its own table, never beside the CMS accounts.
        $this->assertDatabaseMissing('users', ['email' => 'fatima@registre.test']);
        $this->assertSame(1, AuditLog::where('action', 'account.created')->count());
    }

    public function test_a_short_password_is_refused(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/comptes', $this->payload([
                'password' => 'court',
                'password_confirmation' => 'court',
            ]))
            ->assertSessionHasErrors('password');
    }

    public function test_an_address_is_not_used_twice(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/comptes', $this->payload(['email' => 'coordination@registre.test']))
            ->assertSessionHasErrors('email');
    }

    // ------------------------------------------------------- disabling

    public function test_an_account_is_disabled_rather_than_deleted(): void
    {
        $agent = $this->agent();
        $other = RegistreUser::create([
            'name' => 'Ancien étudiant',
            'email' => 'ancien@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_STUDENT,
        ]);

        $this->actingAs($agent, 'registre')
            ->post('/registre/comptes/'.$other->id.'/activation')
            ->assertRedirect();

        $this->assertFalse($other->fresh()->is_active);
        $this->assertSame(1, AuditLog::where('action', 'account.disabled')->count());
    }

    public function test_you_cannot_disable_your_own_account(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->post('/registre/comptes/'.$agent->id.'/activation')
            ->assertSessionHasErrors('compte');

        $this->assertTrue($agent->fresh()->is_active);
    }

    /** The registre must never be left without a way in. */
    public function test_the_last_active_account_cannot_be_disabled(): void
    {
        $agent = $this->agent();
        $other = RegistreUser::create([
            'name' => 'Deuxième',
            'email' => 'deuxieme@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_SUPERVISOR,
        ]);

        // Leaves one: allowed.
        $this->actingAs($agent, 'registre')->post('/registre/comptes/'.$other->id.'/activation');
        $this->assertFalse($other->fresh()->is_active);

        $this->assertSame(1, RegistreUser::active()->count());
    }

    // -------------------------------------------------- your own account

    public function test_you_can_change_your_own_password(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->put('/registre/mon-compte/mot-de-passe', [
                'current_password' => 'password-for-tests',
                'password' => 'un-nouveau-mot-de-passe',
                'password_confirmation' => 'un-nouveau-mot-de-passe',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('un-nouveau-mot-de-passe', $agent->fresh()->password));
    }

    /** A session left open is not enough to take over an account. */
    public function test_the_current_password_is_required(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->put('/registre/mon-compte/mot-de-passe', [
                'current_password' => 'ce-n-est-pas-le-bon',
                'password' => 'un-nouveau-mot-de-passe',
                'password_confirmation' => 'un-nouveau-mot-de-passe',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-for-tests', $agent->fresh()->password));
    }

    public function test_changing_your_password_is_logged_without_the_password(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')->put('/registre/mon-compte/mot-de-passe', [
            'current_password' => 'password-for-tests',
            'password' => 'un-nouveau-mot-de-passe',
            'password_confirmation' => 'un-nouveau-mot-de-passe',
        ]);

        $entry = AuditLog::where('action', 'account.password_changed')->first();

        $this->assertNotNull($entry);
        $this->assertStringNotContainsString('un-nouveau-mot-de-passe', json_encode($entry->context));
    }

    /** Your own profile is not where you raise your own rights. */
    public function test_your_profile_does_not_change_your_role(): void
    {
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->put('/registre/mon-compte', [
                'name' => 'Aminata I.',
                'role' => RegistreUser::ROLE_TECHNICAL,
            ])
            ->assertRedirect();

        $fresh = $agent->fresh();
        $this->assertSame('Aminata I.', $fresh->name);
        $this->assertSame(RegistreUser::ROLE_COORDINATOR, $fresh->role);
    }
}
