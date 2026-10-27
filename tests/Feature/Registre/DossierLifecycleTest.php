<?php

namespace Tests\Feature\Registre;

use App\Models\LegalDomain;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierDocument;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\User as RegistreUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * Opening a dossier by hand, adding documents to it, and the analysis
 * with the supervisor's validation in front of the return to the
 * applicant.
 */
class DossierLifecycleTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        DossierStatus::create([
            'code' => 'provisoire-01',
            'label' => 'Statut provisoire 01',
            'position' => 1,
        ]);

        LegalDomain::create([
            'name' => 'Droit du travail',
            'slug' => 'droit-du-travail',
            'description' => 'Contrats et salaires.',
            'is_active' => true,
            'position' => 1,
        ]);
    }

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
            'last_name' => 'Garba',
            'first_name' => 'Oumarou',
            'phone' => '+227 97 00 11 22',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Licenciement sans préavis',
            'narrative' => 'Reçu à la permanence du jeudi.',
            'urgency' => Dossier::URGENCY_NORMAL,
        ], $overrides);
    }

    // ------------------------------------------------ walk-in dossier

    public function test_the_creation_screen_is_not_open_to_a_visitor(): void
    {
        $this->get('/registre/dossiers/nouveau')->assertRedirect('/registre/connexion');
    }

    public function test_a_dossier_can_be_opened_for_someone_received_on_site(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers', $this->payload())
            ->assertRedirect();

        $dossier = Dossier::first();

        $this->assertMatchesRegularExpression('/^CJ-\d{4}-\d{4}$/', $dossier->reference);
        $this->assertSame(Dossier::SOURCE_WALK_IN, $dossier->source);
        $this->assertSame('Oumarou Garba', $dossier->applicant->fullName());
        $this->assertSame('Licenciement sans préavis', $dossier->subject);
        // The opening is the first line of its history.
        $this->assertSame(1, $dossier->statusHistory()->count());
    }

    public function test_the_minimum_fields_are_required(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers', [])
            ->assertSessionHasErrors(['last_name', 'phone', 'legal_domain', 'subject', 'urgency']);

        $this->assertSame(0, Dossier::count());
    }

    public function test_a_dossier_cannot_be_opened_in_the_future(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers', $this->payload(['opened_at' => now()->addWeek()->toDateString()]))
            ->assertSessionHasErrors('opened_at');
    }

    /** The reference follows the opening date, not the day it was typed. */
    public function test_the_reference_follows_the_opening_year(): void
    {
        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers', $this->payload(['opened_at' => '2025-12-15']));

        $this->assertStringContainsString('-2025-', Dossier::first()->reference);
    }

    public function test_opening_a_dossier_is_logged(): void
    {
        $this->actingAs($this->agent(), 'registre')->post('/registre/dossiers', $this->payload());

        $this->assertSame(1, AuditLog::where('action', 'dossier.created')->count());
    }

    /** A dossier is never opened under an invented status. */
    public function test_opening_fails_while_no_status_is_recorded(): void
    {
        DossierStatus::query()->delete();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers', $this->payload())
            ->assertSessionHasErrors('ouverture');

        $this->assertSame(0, Dossier::count());
    }

    // ------------------------------------------------------ documents

    /**
     * Opened through the service rather than through the screen: a test
     * that signs in to build its fixture is still signed in afterwards,
     * which would quietly make a "visitor" test pass as an agent.
     */
    private function dossier(): Dossier
    {
        return app(\App\Services\Registre\DossierOpener::class)
            ->open($this->payload(), $this->agent());
    }

    public function test_a_document_can_be_added_to_a_dossier(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/documents', [
                'kind' => DossierDocument::KIND_SIGNED_MANDATE,
                'file' => UploadedFile::fake()->create('mandat.pdf', 50, 'application/pdf'),
            ])
            ->assertRedirect();

        $document = $dossier->documents()->first();

        $this->assertSame('mandat.pdf', $document->original_name);
        $this->assertSame(DossierDocument::KIND_SIGNED_MANDATE, $document->kind);
        $this->assertFalse($document->from_presaisine);
        // Private disk, under the dossier's own folder.
        $this->assertStringStartsWith('dossiers/', $document->path);
        Storage::disk('local')->assertExists($document->path);

        $this->assertSame(1, AuditLog::where('action', 'document.added')->count());
    }

    public function test_a_document_of_the_wrong_type_is_refused(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/documents', [
                'kind' => DossierDocument::KIND_OTHER,
                'file' => UploadedFile::fake()->create('script.exe', 10),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, $dossier->documents()->count());
    }

    // -------------------------------------- analysis and its validation

    public function test_an_analysis_is_saved_and_awaits_validation(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse', [
                'analysis' => "Le licenciement sans préavis ouvre droit à une indemnité.",
            ])
            ->assertRedirect();

        $dossier->refresh();

        $this->assertStringContainsString('indemnité', $dossier->analysis);
        $this->assertNotNull($dossier->analysis_submitted_at);
        $this->assertTrue($dossier->analysisAwaitsValidation());
        $this->assertFalse($dossier->isValidated());
    }

    /** The gate the plan requires, stated on the dossier itself. */
    public function test_the_return_to_the_applicant_waits_on_the_validation(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        $this->assertFalse($dossier->returnableToApplicant());

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse', ['analysis' => 'Analyse.']);

        // Submitted is not validated.
        $this->assertFalse($dossier->fresh()->returnableToApplicant());

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse/valider')
            ->assertRedirect();

        $this->assertTrue($dossier->fresh()->returnableToApplicant());
    }

    public function test_nothing_can_be_validated_before_it_is_submitted(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse/valider')
            ->assertSessionHasErrors('analysis');

        $this->assertNull($dossier->fresh()->analysis_validated_at);
    }

    public function test_a_validated_analysis_can_no_longer_be_rewritten(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse', ['analysis' => 'Première analyse.']);
        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse/valider');

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse', ['analysis' => 'Réécriture.'])
            ->assertSessionHasErrors('analysis');

        $this->assertSame('Première analyse.', $dossier->fresh()->analysis);
    }

    public function test_the_validation_is_logged_with_who_gave_it(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse', ['analysis' => 'Analyse.']);
        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/analyse/valider');

        $entry = AuditLog::where('action', 'analysis.validated')->first();

        $this->assertNotNull($entry);
        $this->assertSame($agent->id, $entry->user_id);
        $this->assertSame($dossier->fresh()->analysis_validated_by, $agent->id);
    }

    public function test_an_analysis_is_not_writable_by_a_visitor(): void
    {
        $dossier = $this->dossier();

        $this->post('/registre/dossiers/'.$dossier->reference.'/analyse', ['analysis' => 'Intrusion.'])
            ->assertRedirect('/registre/connexion');

        $this->assertNull($dossier->fresh()->analysis);
    }
}
