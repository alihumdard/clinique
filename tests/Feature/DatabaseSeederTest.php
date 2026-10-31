<?php

namespace Tests\Feature;

use App\Models\LegalDomain;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What `php artisan migrate --seed` leaves behind.
 *
 * The README tells whoever sets this up to run that one command. For a
 * while it created the two accounts and nothing else, so a fresh checkout
 * arrived at a site whose pré-saisine could not be sent: `legal_domain`
 * is required and its select is built from `legal_domains`, which was
 * empty. Every other test seeds its own fixtures, so none of them went
 * anywhere near the seeder and the suite stayed green.
 *
 * These tests stand in for the person following the README.
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_documented_seed_command_fills_the_legal_domains_the_presaisine_needs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(
            0,
            LegalDomain::query()->where('is_active', true)->count(),
            'The pré-saisine form cannot be submitted without an active legal domain to choose.'
        );
    }

    public function test_the_presaisine_form_offers_a_domain_after_the_documented_seed(): void
    {
        $this->seed(DatabaseSeeder::class);

        $domain = LegalDomain::query()->where('is_active', true)->first();

        $this->withSession(['presaisine.orientation' => ['eligible' => true]])
            ->get(route('presaisine.form'))
            ->assertOk()
            ->assertSee($domain->name, escape: false);
    }

    public function test_the_documented_seed_creates_both_cms_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::query()->where('role', User::ROLE_ADMIN)->count());
        $this->assertSame(1, User::query()->where('role', User::ROLE_EDITOR)->count());
    }

    public function test_seeding_twice_updates_the_fixtures_rather_than_duplicating_them(): void
    {
        $this->seed(DatabaseSeeder::class);
        $afterFirst = LegalDomain::query()->count();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(
            $afterFirst,
            LegalDomain::query()->count(),
            'The seeders are written with updateOrCreate so a second run is safe.'
        );
    }
}
