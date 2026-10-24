<?php

namespace Tests\Feature\Admin;

use App\Models\Call;
use App\Models\Category;
use App\Models\Fee;
use App\Models\LegalDomain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | Appels à candidatures
    |--------------------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    private function callPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Stage pour étudiants en droit',
            'excerpt' => 'Recrutement de stagiaires.',
            'closes_on' => now()->addMonth()->toDateString(),
            'status' => Call::STATUS_PUBLISHED,
        ], $overrides);
    }

    public function test_the_calls_screen_requires_authentication(): void
    {
        $this->get('/admin/appels')->assertRedirect(route('admin.login'));
    }

    public function test_a_call_is_created(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/appels', $this->callPayload())
            ->assertRedirect(route('admin.calls'));

        $call = Call::firstWhere('slug', 'stage-pour-etudiants-en-droit');

        $this->assertNotNull($call);
        $this->assertTrue($call->isOpen());
    }

    /**
     * The closing date is what drops a call off the public list, with no
     * action from the Clinic.
     */
    public function test_a_call_past_its_date_leaves_the_public_list_by_itself(): void
    {
        Call::create($this->callPayload([
            'slug' => 'ouvert',
            'title' => 'Appel encore ouvert',
            'closes_on' => now()->addWeek(),
            'published_at' => now()->subWeek(),
        ]));

        Call::create($this->callPayload([
            'slug' => 'cloture',
            'title' => 'Appel déjà clôturé',
            'closes_on' => now()->subWeek(),
            'published_at' => now()->subMonth(),
        ]));

        $this->get('/rejoindre')
            ->assertOk()
            ->assertSee('Appel encore ouvert', escape: false)
            ->assertDontSee('Appel déjà clôturé', escape: false);

        // Both stay in the admin, so the Clinic keeps the record.
        $this->actingAs($this->admin)
            ->get('/admin/appels')
            ->assertOk()
            ->assertSee('Appel déjà clôturé', escape: false);
    }

    public function test_a_call_without_a_date_stays_open(): void
    {
        Call::create($this->callPayload([
            'slug' => 'sans-date',
            'title' => 'Appel permanent',
            'closes_on' => null,
            'published_at' => now()->subWeek(),
        ]));

        $this->get('/rejoindre')
            ->assertOk()
            ->assertSee('Appel permanent', escape: false);
    }

    public function test_the_calls_list_can_be_filtered_by_type(): void
    {
        $stages = Category::create(['type' => 'call', 'name' => 'Stages', 'slug' => 'stages', 'tone' => 'blue']);
        $autre = Category::create(['type' => 'call', 'name' => 'Volontariat', 'slug' => 'volontariat', 'tone' => 'green']);

        Call::create($this->callPayload(['slug' => 'un-stage', 'title' => 'Un stage', 'category_id' => $stages->id, 'published_at' => now()->subDay()]));
        Call::create($this->callPayload(['slug' => 'un-volontariat', 'title' => 'Un volontariat', 'category_id' => $autre->id, 'published_at' => now()->subDay()]));

        $this->get('/rejoindre?type=stages')
            ->assertOk()
            ->assertSee('Un stage', escape: false)
            ->assertDontSee('Un volontariat', escape: false);
    }

    /*
    |--------------------------------------------------------------------------
    | Grille tarifaire
    |--------------------------------------------------------------------------
    */

    public function test_the_references_screen_requires_authentication(): void
    {
        $this->get('/admin/referentiels')->assertRedirect(route('admin.login'));
    }

    public function test_a_fee_is_created_and_shown_on_the_public_page(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/referentiels/tarifs', [
                'label' => 'Frais de photocopie',
                'note' => 'Par page',
                'amount' => 50,
                'is_visible' => '1',
            ])
            ->assertRedirect();

        $this->get('/services/tarifs-administratifs')
            ->assertOk()
            ->assertSee('Frais de photocopie', escape: false)
            ->assertSee('50 FCFA', escape: false);
    }

    /** Not every fee is a single figure. */
    public function test_a_fee_can_carry_a_text_amount(): void
    {
        Fee::create([
            'label' => 'Frais de timbre',
            'amount_label' => 'Selon tarif en vigueur',
            'is_visible' => true,
            'position' => 1,
        ]);

        $this->get('/services/tarifs-administratifs')
            ->assertOk()
            ->assertSee('Selon tarif en vigueur', escape: false);
    }

    public function test_a_free_service_reads_as_gratuit(): void
    {
        $fee = Fee::create(['label' => 'Consultation', 'is_free' => true, 'is_visible' => true]);

        $this->assertSame('Gratuit', $fee->amountForHumans());
    }

    public function test_a_hidden_fee_is_not_on_the_public_page(): void
    {
        Fee::create(['label' => 'Tarif interne', 'amount' => 999, 'is_visible' => false, 'position' => 1]);

        $this->get('/services/tarifs-administratifs')
            ->assertOk()
            ->assertDontSee('Tarif interne', escape: false);
    }

    public function test_a_fee_can_be_updated_and_deleted(): void
    {
        $fee = Fee::create(['label' => 'Frais divers', 'amount' => 100, 'is_visible' => true, 'position' => 1]);

        $this->actingAs($this->admin)->put("/admin/referentiels/tarifs/{$fee->id}", [
            'label' => 'Frais corrigés',
            'amount' => 200,
            'is_visible' => '1',
        ]);

        $this->assertSame('Frais corrigés', $fee->fresh()->label);

        $this->actingAs($this->admin)->delete("/admin/referentiels/tarifs/{$fee->id}");

        $this->assertDatabaseMissing('fees', ['id' => $fee->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Domaines du droit
    |--------------------------------------------------------------------------
    */

    public function test_a_domain_is_created_and_shown_on_both_pages(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/referentiels/domaines', [
                'name' => 'Droit de la famille',
                'plain_language' => 'Mariage, divorce, enfants',
                'icon' => 'users',
                'tone' => 'blue',
                'is_active' => '1',
            ])
            ->assertRedirect();

        foreach (['/', '/services'] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('Droit de la famille', escape: false)
                ->assertSee('Mariage, divorce, enfants', escape: false);
        }
    }

    public function test_an_inactive_domain_is_not_shown(): void
    {
        LegalDomain::create([
            'name' => 'Domaine en préparation',
            'slug' => 'domaine-en-preparation',
            'is_active' => false,
            'position' => 1,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Domaine en préparation', escape: false);
    }

    /** An icon outside the set would render an empty path. */
    public function test_an_unknown_icon_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/referentiels/domaines', [
                'name' => 'Droit test',
                'icon' => 'pas-une-icone',
            ])
            ->assertSessionHasErrors('icon');
    }

    /**
     * Module 3 stores the slug on a dossier, so a rename must not change
     * it — that would orphan those records.
     */
    public function test_renaming_a_domain_keeps_its_slug(): void
    {
        $domain = LegalDomain::create([
            'name' => 'Droit de la famille',
            'slug' => 'droit-de-la-famille',
            'is_active' => true,
            'position' => 1,
        ]);

        $this->actingAs($this->admin)->put("/admin/referentiels/domaines/{$domain->id}", [
            'name' => 'Droit de la famille et des personnes',
            'is_active' => '1',
        ]);

        $domain->refresh();

        $this->assertSame('Droit de la famille et des personnes', $domain->name);
        $this->assertSame('droit-de-la-famille', $domain->slug);
    }

    public function test_a_domain_can_be_deleted(): void
    {
        $domain = LegalDomain::create(['name' => 'Domaine', 'slug' => 'domaine', 'position' => 1]);

        $this->actingAs($this->admin)->delete("/admin/referentiels/domaines/{$domain->id}");

        $this->assertDatabaseMissing('legal_domains', ['id' => $domain->id]);
    }
}
