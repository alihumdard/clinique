<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * Approved access level: « Administrateur IT : accès complet à la
 * plateforme, mais uniquement sur une base de données de test (données
 * fictives) — les données réelles des dossiers restent inaccessibles. »
 */
class TechnicalAccessTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private function account(string $role): User
    {
        return User::create([
            'name' => 'Compte',
            'email' => $role.'@registre.test',
            'password' => 'mot-de-passe-long',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    public function test_on_real_data_it_may_not_open_dossiers(): void
    {
        config(['clinique.registre.donnees_fictives' => false]);
        $it = $this->account(User::ROLE_TECHNICAL);

        $this->actingAs($it, 'registre')->get('/registre/dossiers')->assertForbidden();
        $this->actingAs($it, 'registre')->get('/registre/demandes')->assertForbidden();
        $this->actingAs($it, 'registre')->get('/registre')->assertRedirect('/registre/comptes');
    }

    public function test_on_real_data_it_still_manages_accounts(): void
    {
        config(['clinique.registre.donnees_fictives' => false]);

        $this->actingAs($this->account(User::ROLE_TECHNICAL), 'registre')
            ->get('/registre/comptes')
            ->assertOk()
            ->assertDontSee('Base de test');
    }

    public function test_on_the_test_database_it_opens_dossiers(): void
    {
        config(['clinique.registre.donnees_fictives' => true]);

        $this->actingAs($this->account(User::ROLE_TECHNICAL), 'registre')
            ->get('/registre/dossiers')
            ->assertOk()
            ->assertSee('Base de test');
    }

    public function test_the_coordinator_reads_real_dossiers(): void
    {
        config(['clinique.registre.donnees_fictives' => false]);

        $this->actingAs($this->account(User::ROLE_COORDINATOR), 'registre')
            ->get('/registre/dossiers')
            ->assertOk();
    }
}
