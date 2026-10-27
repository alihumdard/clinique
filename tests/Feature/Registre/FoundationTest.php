<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\User as RegistreUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The foundation of the Registre numérique: a separate area, with its own
 * accounts in its own database.
 *
 * The plan makes this the first requirement of module 3 — "séparer le
 * registre du site public en matière de comptes, de contrôles d'accès et
 * de base de données" — so it is the first thing tested.
 */
class FoundationTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private function agent(string $role = RegistreUser::ROLE_COORDINATOR, bool $active = true): RegistreUser
    {
        return RegistreUser::create([
            'name' => 'Aminata Issoufou',
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
            'role' => $role,
            'is_active' => $active,
        ]);
    }

    public function test_the_registre_signs_in_on_its_own_connection(): void
    {
        $this->agent();

        $this->post('/registre/connexion', [
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
        ])->assertRedirect('/registre');

        $this->assertAuthenticatedAs(RegistreUser::first(), 'registre');

        // And the account is nowhere near the public site's table.
        $this->assertDatabaseMissing('users', ['email' => 'coordination@registre.test']);
        $this->assertSame('registre', (new RegistreUser)->getConnectionName());
    }

    /** The whole point of the separation. */
    public function test_a_cms_account_does_not_open_the_registre(): void
    {
        $cmsAdmin = User::factory()->create();

        $this->actingAs($cmsAdmin)
            ->get('/registre')
            ->assertRedirect('/registre/connexion');
    }

    /** And it holds the other way round. */
    public function test_a_registre_account_does_not_open_the_cms(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_a_visitor_is_sent_to_the_registre_sign_in(): void
    {
        $this->get('/registre')->assertRedirect('/registre/connexion');
    }

    public function test_a_disabled_account_cannot_sign_in(): void
    {
        $this->agent(active: false);

        $this->post('/registre/connexion', [
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('registre');
    }

    public function test_a_wrong_password_names_neither_field(): void
    {
        $this->agent();

        $response = $this->post('/registre/connexion', [
            'email' => 'coordination@registre.test',
            'password' => 'not-the-password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ]);
        $this->assertGuest('registre');
    }

    public function test_repeated_failures_are_throttled(): void
    {
        $this->agent();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/registre/connexion', [
                'email' => 'coordination@registre.test',
                'password' => 'not-the-password',
            ]);
        }

        $this->post('/registre/connexion', [
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('registre');
    }

    public function test_signing_in_records_the_moment(): void
    {
        $agent = $this->agent();
        $this->assertNull($agent->last_login_at);

        $this->post('/registre/connexion', [
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
        ]);

        $this->assertNotNull($agent->fresh()->last_login_at);
    }

    public function test_signing_out_ends_the_session(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/deconnexion')
            ->assertRedirect('/registre/connexion');

        $this->assertGuest('registre');
    }

    /** Shared workstations: thirty idle minutes and the session is gone. */
    public function test_an_idle_session_is_ended_after_thirty_minutes(): void
    {
        $this->actingAs($this->agent(), 'registre')->get('/registre')->assertOk();

        session(['registre.last_activity' => time() - (31 * 60)]);

        $this->get('/registre')->assertRedirect('/registre/connexion');
        $this->assertGuest('registre');
    }

    /** Confidential: the area must not be indexed, and must not leak a referrer. */
    public function test_the_registre_is_not_indexable(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->get('/registre')
            ->assertOk()
            ->assertSee('noindex, nofollow', escape: false)
            ->assertSee('no-referrer', escape: false);
    }

    /** No case table may ever appear in the public site's database. */
    public function test_the_registre_tables_are_not_in_the_public_database(): void
    {
        $this->assertTrue(Schema::connection('registre')->hasTable('registre_users'));
        $this->assertFalse(Schema::connection(config('database.default'))->hasTable('registre_users'));
    }
}
