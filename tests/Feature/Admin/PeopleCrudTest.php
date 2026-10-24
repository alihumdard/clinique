<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Partner;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Partners and team members publish differently from the content types:
 * their tables carry is_visible and position and no published_at, so a row
 * is shown or hidden rather than dated.
 */
class PeopleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $rubrique;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->rubrique = Category::create([
            'type' => 'partner',
            'name' => 'Institutions nationales',
            'slug' => 'institutions-nationales',
            'tone' => 'blue',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Publication model
    |--------------------------------------------------------------------------
    */

    /** The scopes must not reach for a column these tables do not have. */
    public function test_the_public_scope_works_without_a_publication_date(): void
    {
        Partner::create($this->partnerPayload(['slug' => 'visible']));
        Partner::create($this->partnerPayload(['slug' => 'masque', 'name' => 'Masqué', 'is_visible' => false]));
        Partner::create($this->partnerPayload(['slug' => 'brouillon', 'name' => 'Brouillon', 'status' => Partner::STATUS_DRAFT]));

        $this->assertSame(1, Partner::public()->count());
        $this->assertSame(1, Partner::drafts()->count());
        // Nothing can be scheduled without a date to schedule it for.
        $this->assertSame(0, Partner::scheduled()->count());
    }

    public function test_a_hidden_row_reads_as_masque_rather_than_draft(): void
    {
        $partner = Partner::create($this->partnerPayload(['slug' => 'masque', 'is_visible' => false]));

        $this->assertSame('Masqué', $partner->statusLabel());
        $this->assertFalse($partner->isPublic());
        $this->assertFalse($partner->isScheduled());
    }

    /*
    |--------------------------------------------------------------------------
    | Partenaires
    |--------------------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    private function partnerPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ministère de la Justice',
            'description' => 'Niger',
            'website' => 'https://www.justice.ne',
            'category_id' => $this->rubrique->id,
            'status' => Partner::STATUS_PUBLISHED,
            'is_visible' => true,
        ], $overrides);
    }

    public function test_the_partners_listing_requires_authentication(): void
    {
        $this->get('/admin/partenaires')->assertRedirect(route('admin.login'));
    }

    public function test_a_partner_is_created_and_placed_at_the_end(): void
    {
        Partner::create($this->partnerPayload(['slug' => 'premier', 'name' => 'Premier', 'position' => 4]));

        $this->actingAs($this->admin)
            ->post('/admin/partenaires', $this->partnerPayload())
            ->assertRedirect(route('admin.partners'));

        $partner = Partner::firstWhere('slug', 'ministere-de-la-justice');

        $this->assertNotNull($partner);
        $this->assertSame(5, $partner->position);
        $this->assertTrue($partner->is_visible);
    }

    /**
     * A partner's site ends up in an href, so only http and https are
     * accepted — a javascript: address must never get that far.
     */
    public function test_a_website_that_is_not_http_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/partenaires', $this->partnerPayload(['website' => 'javascript:alert(1)']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, Partner::count());
    }

    /** The mockup lists SVG for logos; SVG can carry script, so it is refused. */
    public function test_an_svg_logo_is_refused(): void
    {
        Storage::fake('public');

        $svg = UploadedFile::fake()->createWithContent(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $this->actingAs($this->admin)
            ->post('/admin/partenaires', $this->partnerPayload(['logo' => $svg]))
            ->assertSessionHasErrors('logo');

        $this->assertSame(0, Partner::count());
    }

    public function test_a_partner_can_be_hidden_and_shown_again(): void
    {
        $partner = Partner::create($this->partnerPayload(['slug' => 'ministere']));

        $this->actingAs($this->admin)->post("/admin/partenaires/{$partner->id}/visibilite");
        $this->assertFalse($partner->fresh()->is_visible);
        $this->assertSame(0, Partner::public()->count());

        $this->actingAs($this->admin)->post("/admin/partenaires/{$partner->id}/visibilite");
        $this->assertTrue($partner->fresh()->is_visible);
        $this->assertSame(1, Partner::public()->count());
    }

    public function test_a_hidden_partner_is_not_on_the_public_page(): void
    {
        Partner::create($this->partnerPayload(['slug' => 'visible']));
        Partner::create($this->partnerPayload([
            'slug' => 'masque',
            'name' => 'Partenaire en cours de validation',
            'is_visible' => false,
        ]));

        $this->get('/partenaires')
            ->assertOk()
            ->assertSee('Ministère de la Justice', escape: false)
            ->assertDontSee('en cours de validation', escape: false);
    }

    public function test_a_partner_goes_to_the_corbeille_and_can_be_restored(): void
    {
        $partner = Partner::create($this->partnerPayload(['slug' => 'ministere']));

        $this->actingAs($this->admin)->delete("/admin/partenaires/{$partner->id}");
        $this->assertSoftDeleted($partner);

        $this->actingAs($this->admin)->post("/admin/partenaires/{$partner->id}/restaurer");
        $this->assertNotSoftDeleted($partner->fresh());
    }

    /*
    |--------------------------------------------------------------------------
    | Équipe
    |--------------------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    private function memberPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Dr. Halima Issa',
            'role' => 'Droit de la famille',
            'bio' => 'Suivi des dossiers et sensibilisation.',
            'department' => 'enseignants',
            'email' => 'halima.issa@uam.ne',
            'status' => TeamMember::STATUS_PUBLISHED,
            'is_visible' => true,
        ], $overrides);
    }

    public function test_the_team_listing_requires_authentication(): void
    {
        $this->get('/admin/equipe')->assertRedirect(route('admin.login'));
    }

    public function test_a_member_is_created_in_their_group(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/equipe', $this->memberPayload())
            ->assertRedirect(route('admin.team'));

        $member = TeamMember::firstWhere('slug', 'dr-halima-issa');

        $this->assertNotNull($member);
        $this->assertSame('enseignants', $member->department);
        $this->assertSame(1, $member->position);
    }

    /** Positions count within a group, not across the whole team. */
    public function test_positions_are_counted_within_each_group(): void
    {
        $this->actingAs($this->admin)->post('/admin/equipe', $this->memberPayload());
        $this->actingAs($this->admin)->post('/admin/equipe', $this->memberPayload([
            'name' => 'Pr. Ibrahim Moussa',
            'department' => 'direction',
        ]));

        $this->assertSame(1, TeamMember::firstWhere('department', 'enseignants')->position);
        $this->assertSame(1, TeamMember::firstWhere('department', 'direction')->position);
    }

    public function test_a_group_outside_the_published_list_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/equipe', $this->memberPayload(['department' => 'inconnu']))
            ->assertSessionHasErrors('department');
    }

    public function test_a_linkedin_address_must_be_https(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/equipe', $this->memberPayload(['linkedin' => 'http://linkedin.com/in/x']))
            ->assertSessionHasErrors('linkedin');
    }

    /**
     * A member who leaves is hidden rather than left on the public page,
     * which is what the plan asks for.
     */
    public function test_a_hidden_member_is_not_on_the_public_page(): void
    {
        TeamMember::create($this->memberPayload(['slug' => 'visible']));
        TeamMember::create($this->memberPayload([
            'slug' => 'partie',
            'name' => 'Ancien membre',
            'is_visible' => false,
        ]));

        $this->get('/a-propos/equipe')
            ->assertOk()
            ->assertSee('Dr. Halima Issa', escape: false)
            ->assertDontSee('Ancien membre', escape: false);
    }

    public function test_a_member_without_a_photo_shows_their_initials(): void
    {
        TeamMember::create($this->memberPayload(['slug' => 'sans-photo']));

        $this->get('/a-propos/equipe')
            ->assertOk()
            // "Halima Issa" with no photo renders as HI.
            ->assertSee('>HI<', escape: false);
    }

    public function test_a_permanent_delete_removes_the_member_and_their_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/equipe', $this->memberPayload([
            'photo' => UploadedFile::fake()->image('portrait.jpg', 400, 400),
        ]));

        $member = TeamMember::first();
        $path = $member->photo;
        $member->delete();

        $this->actingAs($this->admin)->delete("/admin/equipe/{$member->id}/definitif");

        $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
