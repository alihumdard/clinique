<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $domain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->domain = Category::create([
            'type' => 'guide',
            'name' => 'Droit de la famille',
            'slug' => 'droit-de-la-famille',
            'tone' => 'blue',
        ]);
    }

    /** A real PDF, so MediaStorage's check of the file's own bytes passes. */
    private function pdf(string $name = 'fiche.pdf'): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($path, "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF");

        return new UploadedFile($path, $name, 'application/pdf', null, true);
    }

    /** @return array<string, mixed> */
    private function guidePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Guide pratique : droits de la famille',
            'description' => 'Un guide pour comprendre vos droits.',
            'body' => 'Contenu de la fiche.',
            'category_id' => $this->domain->id,
            'status' => Guide::STATUS_PUBLISHED,
            'document' => $this->pdf(),
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Fiches pratiques
    |--------------------------------------------------------------------------
    */

    public function test_the_guides_listing_requires_authentication(): void
    {
        $this->get('/admin/fiches-pratiques')->assertRedirect(route('admin.login'));
    }

    public function test_a_fiche_is_created_with_its_pdf_on_the_private_disk(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)
            ->post('/admin/fiches-pratiques', $this->guidePayload())
            ->assertRedirect(route('admin.guides'));

        $guide = Guide::firstWhere('slug', 'guide-pratique-droits-de-la-famille');

        $this->assertNotNull($guide);
        $this->assertSame('fiche.pdf', $guide->file_name);
        $this->assertGreaterThan(0, $guide->file_size);
        $this->assertStringStartsWith('documents/fiches-pratiques/', $guide->file_path);
        Storage::disk('local')->assertExists($guide->file_path);
        $this->assertNotNull($guide->published_at);
    }

    public function test_a_fiche_cannot_be_created_without_a_pdf(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/fiches-pratiques', $this->guidePayload(['document' => null]))
            ->assertSessionHasErrors('document');

        $this->assertSame(0, Guide::count());
    }

    public function test_updating_a_fiche_without_a_new_pdf_keeps_the_existing_one(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)->post('/admin/fiches-pratiques', $this->guidePayload());
        $guide = Guide::first();
        $originalPath = $guide->file_path;

        $this->actingAs($this->admin)
            ->put("/admin/fiches-pratiques/{$guide->id}", [
                'title' => 'Titre corrigé',
                'category_id' => $this->domain->id,
                'status' => Guide::STATUS_PUBLISHED,
            ])
            ->assertRedirect(route('admin.guides'));

        $guide->refresh();

        $this->assertSame('Titre corrigé', $guide->title);
        $this->assertSame($originalPath, $guide->file_path);
        Storage::disk('local')->assertExists($originalPath);
    }

    public function test_replacing_the_pdf_drops_the_old_file(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)->post('/admin/fiches-pratiques', $this->guidePayload());
        $guide = Guide::first();
        $originalPath = $guide->file_path;

        $this->actingAs($this->admin)->put("/admin/fiches-pratiques/{$guide->id}", [
            'title' => $guide->title,
            'status' => Guide::STATUS_PUBLISHED,
            'document' => $this->pdf('nouvelle-version.pdf'),
        ]);

        $guide->refresh();

        $this->assertNotSame($originalPath, $guide->file_path);
        Storage::disk('local')->assertMissing($originalPath);
        Storage::disk('local')->assertExists($guide->file_path);
    }

    public function test_a_fiche_goes_to_the_corbeille_and_can_be_restored(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)->post('/admin/fiches-pratiques', $this->guidePayload());
        $guide = Guide::first();

        $this->actingAs($this->admin)->delete("/admin/fiches-pratiques/{$guide->id}");
        $this->assertSoftDeleted($guide);
        // The corbeille is reversible, so the file must still be there.
        Storage::disk('local')->assertExists($guide->file_path);

        $this->actingAs($this->admin)->post("/admin/fiches-pratiques/{$guide->id}/restaurer");
        $this->assertNotSoftDeleted($guide->fresh());
    }

    public function test_a_permanent_delete_removes_the_row_and_the_pdf(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)->post('/admin/fiches-pratiques', $this->guidePayload());
        $guide = Guide::first();
        $path = $guide->file_path;
        $guide->delete();

        $this->actingAs($this->admin)->delete("/admin/fiches-pratiques/{$guide->id}/definitif");

        $this->assertDatabaseMissing('guides', ['id' => $guide->id]);
        Storage::disk('local')->assertMissing($path);
    }

    /*
    |--------------------------------------------------------------------------
    | Rapports annuels
    |--------------------------------------------------------------------------
    */

    /** @return array<string, mixed> */
    private function reportPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => "Rapport annuel d'activité 2024",
            'year' => 2024,
            'description' => "Le bilan de l'année.",
            'status' => Report::STATUS_PUBLISHED,
            'document' => $this->pdf('rapport-2024.pdf'),
        ], $overrides);
    }

    public function test_a_report_is_created_with_its_year(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)
            ->post('/admin/rapports-annuels', $this->reportPayload())
            ->assertRedirect(route('admin.reports'));

        $report = Report::first();

        $this->assertSame(2024, $report->year);
        $this->assertStringStartsWith('documents/rapports-annuels/', $report->file_path);
        Storage::disk('local')->assertExists($report->file_path);
    }

    public function test_a_year_before_the_clinic_existed_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/rapports-annuels', $this->reportPayload(['year' => 2010]))
            ->assertSessionHasErrors('year');
    }

    public function test_the_reports_listing_filters_on_year(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin)->post('/admin/rapports-annuels', $this->reportPayload());
        $this->actingAs($this->admin)->post('/admin/rapports-annuels', $this->reportPayload([
            'title' => "Rapport annuel d'activité 2023",
            'year' => 2023,
        ]));

        // The page's own form carries "Ex. Rapport annuel d'activité 2024"
        // as a placeholder, so the row count is what tells the two apart.
        $this->actingAs($this->admin)
            ->get('/admin/rapports-annuels?annee=2023')
            ->assertOk()
            ->assertSee('sur 1 rapports');

        $this->actingAs($this->admin)
            ->get('/admin/rapports-annuels')
            ->assertOk()
            ->assertSee('sur 2 rapports');
    }

    /** Reports have no category column; a crafted filter must not reach one. */
    public function test_a_category_filter_on_reports_is_ignored(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/rapports-annuels?categorie=4')
            ->assertOk();
    }
}
