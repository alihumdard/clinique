<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierAssignment;
use App\Models\Registre\DossierDeadline;
use App\Models\Registre\DossierDocument;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\User as RegistreUser;
use App\Support\Registre\DossierNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The dossier screens: the list and its filters, one dossier, the
 * assignments and deadlines on it, the documents, the export and the
 * access log.
 */
class DossierScreensTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private DossierStatus $status;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->status = DossierStatus::create([
            'code' => 'provisoire-01',
            'label' => 'Statut provisoire 01',
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

    private function dossier(array $attributes = [], array $applicant = []): Dossier
    {
        $person = Applicant::create(array_merge([
            'last_name' => 'Abdou',
            'first_name' => 'Hadiza',
            'phone' => '+227 90 11 22 33',
        ], $applicant));

        return Dossier::create(array_merge([
            'reference' => DossierNumber::next(),
            'applicant_id' => $person->id,
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'status_id' => $this->status->id,
            'opened_at' => now(),
        ], $attributes));
    }

    // ------------------------------------------------------- the list

    public function test_the_list_is_not_open_to_a_visitor(): void
    {
        $this->get('/registre/dossiers')->assertRedirect('/registre/connexion');
    }

    public function test_the_list_shows_the_dossiers(): void
    {
        $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->get('/registre/dossiers')
            ->assertOk()
            ->assertSee('Salaires non versés', escape: false)
            ->assertSee('Hadiza Abdou', escape: false);
    }

    public function test_the_search_matches_a_reference_a_name_and_a_phone(): void
    {
        $wanted = $this->dossier(['subject' => 'Litige foncier'], ['last_name' => 'Souley', 'phone' => '+227 96 00 00 11']);
        $other = $this->dossier(['subject' => 'Autre affaire'], ['last_name' => 'Garba', 'phone' => '+227 97 00 00 22']);

        foreach ([$wanted->reference, 'Souley', '96 00 00 11'] as $term) {
            $this->actingAs($this->agent(), 'registre')
                ->get('/registre/dossiers?q='.urlencode($term))
                ->assertOk()
                ->assertSee('Litige foncier', escape: false)
                ->assertDontSee('Autre affaire', escape: false);
        }
    }

    public function test_the_late_filter_keeps_only_overdue_dossiers(): void
    {
        $late = $this->dossier(['subject' => 'Dossier en retard']);
        DossierDeadline::create(['dossier_id' => $late->id, 'label' => 'Audience', 'due_at' => now()->subWeek()]);

        $onTime = $this->dossier(['subject' => 'Dossier à jour']);
        DossierDeadline::create(['dossier_id' => $onTime->id, 'label' => 'Audience', 'due_at' => now()->addWeek()]);

        $this->actingAs($this->agent(), 'registre')
            ->get('/registre/dossiers?retard=1')
            ->assertOk()
            ->assertSee('Dossier en retard', escape: false)
            ->assertDontSee('Dossier à jour', escape: false);
    }

    // ---------------------------------------------------- one dossier

    public function test_a_dossier_shows_its_request_people_and_history(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        DossierAssignment::create([
            'dossier_id' => $dossier->id,
            'user_id' => $agent->id,
            'role' => DossierAssignment::ROLE_STUDENT,
            'assigned_at' => now(),
        ]);

        $this->actingAs($agent, 'registre')
            ->get('/registre/dossiers/'.$dossier->reference)
            ->assertOk()
            ->assertSee('Salaires non versés', escape: false)
            ->assertSee('Aminata Issoufou', escape: false)
            // The provisional status is declared on the dossier itself.
            ->assertSee('provisoire', escape: false);
    }

    public function test_opening_a_dossier_is_logged(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')->get('/registre/dossiers/'.$dossier->reference);

        $this->assertSame(1, AuditLog::where('action', 'dossier.viewed')->count());
    }

    // ---------------------------------------------------- assignments

    public function test_someone_can_be_assigned_and_the_assignment_ended(): void
    {
        $dossier = $this->dossier();
        $coordinator = $this->agent();
        $student = RegistreUser::create([
            'name' => 'Fatima Abdou',
            'email' => 'etudiant@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_STUDENT,
        ]);

        $this->actingAs($coordinator, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/affectations', [
                'user_id' => $student->id,
                'role' => DossierAssignment::ROLE_STUDENT,
            ])
            ->assertRedirect();

        $assignment = $dossier->assignments()->first();
        $this->assertSame($student->id, $assignment->user_id);

        $this->actingAs($coordinator, 'registre')
            ->post("/registre/dossiers/{$dossier->reference}/affectations/{$assignment->id}/terminer")
            ->assertRedirect();

        // Ended, not deleted: who worked on it stays on the record.
        $this->assertNotNull($assignment->fresh()->ended_at);
        $this->assertSame(1, $dossier->assignments()->count());
    }

    public function test_the_same_person_is_not_assigned_twice_in_the_same_role(): void
    {
        $dossier = $this->dossier();
        $coordinator = $this->agent();

        $payload = ['user_id' => $coordinator->id, 'role' => DossierAssignment::ROLE_SUPERVISOR];

        $this->actingAs($coordinator, 'registre')->post('/registre/dossiers/'.$dossier->reference.'/affectations', $payload);
        $this->actingAs($coordinator, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/affectations', $payload)
            ->assertSessionHasErrors('user_id');

        $this->assertSame(1, $dossier->assignments()->count());
    }

    // ------------------------------------------------------ deadlines

    public function test_a_deadline_can_be_added_and_marked_done(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/echeances', [
                'label' => 'Rappeler le demandeur',
                'due_at' => now()->addDays(3)->toDateString(),
            ])
            ->assertRedirect();

        $deadline = $dossier->deadlines()->first();
        $this->assertSame('Rappeler le demandeur', $deadline->label);

        $this->actingAs($agent, 'registre')
            ->post("/registre/dossiers/{$dossier->reference}/echeances/{$deadline->id}/faite")
            ->assertRedirect();

        $this->assertNotNull($deadline->fresh()->completed_at);
    }

    // ------------------------------------------------------ documents

    public function test_a_document_is_not_served_to_a_visitor(): void
    {
        $dossier = $this->dossier();
        Storage::disk('local')->put('dossiers/2026/x/piece.pdf', 'contenu');

        $document = DossierDocument::create([
            'dossier_id' => $dossier->id,
            'kind' => DossierDocument::KIND_ATTACHMENT,
            'path' => 'dossiers/2026/x/piece.pdf',
            'original_name' => 'piece.pdf',
            'mime_type' => 'application/pdf',
            'size' => 7,
        ]);

        $this->get("/registre/dossiers/{$dossier->reference}/document/{$document->id}")
            ->assertRedirect('/registre/connexion');
    }

    public function test_a_document_download_is_logged(): void
    {
        $dossier = $this->dossier();
        Storage::disk('local')->put('dossiers/2026/x/piece.pdf', 'contenu');

        $document = DossierDocument::create([
            'dossier_id' => $dossier->id,
            'kind' => DossierDocument::KIND_ATTACHMENT,
            'path' => 'dossiers/2026/x/piece.pdf',
            'original_name' => 'piece.pdf',
            'mime_type' => 'application/pdf',
            'size' => 7,
        ]);

        $this->actingAs($this->agent(), 'registre')
            ->get("/registre/dossiers/{$dossier->reference}/document/{$document->id}")
            ->assertOk();

        $this->assertSame(1, AuditLog::where('action', 'document.downloaded')->count());
    }

    /** A document of another dossier is not reachable through this one. */
    public function test_a_document_of_another_dossier_is_not_served(): void
    {
        $mine = $this->dossier();
        $other = $this->dossier(['subject' => 'Autre']);

        $document = DossierDocument::create([
            'dossier_id' => $other->id,
            'kind' => DossierDocument::KIND_ATTACHMENT,
            'path' => 'dossiers/2026/y/piece.pdf',
            'original_name' => 'piece.pdf',
            'mime_type' => 'application/pdf',
            'size' => 7,
        ]);

        $this->actingAs($this->agent(), 'registre')
            ->get("/registre/dossiers/{$mine->reference}/document/{$document->id}")
            ->assertNotFound();
    }

    // --------------------------------------------------------- export

    public function test_the_export_returns_a_csv_of_the_filtered_list(): void
    {
        $this->dossier(['subject' => 'Litige foncier'], ['last_name' => 'Souley']);
        $this->dossier(['subject' => 'Autre affaire'], ['last_name' => 'Garba']);

        $response = $this->actingAs($this->agent(), 'registre')
            ->get('/registre/dossiers/export?q=Souley')
            ->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Référence', $csv);
        $this->assertStringContainsString('Litige foncier', $csv);
        $this->assertStringNotContainsString('Autre affaire', $csv);
    }

    public function test_an_export_is_logged(): void
    {
        $this->dossier();

        $this->actingAs($this->agent(), 'registre')->get('/registre/dossiers/export')->streamedContent();

        $this->assertSame(1, AuditLog::where('action', 'dossier.exported')->count());
    }

    public function test_the_export_is_not_open_to_a_visitor(): void
    {
        $this->get('/registre/dossiers/export')->assertRedirect('/registre/connexion');
    }

    // -------------------------------------------------------- journal

    public function test_the_journal_lists_what_was_done(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        $this->actingAs($agent, 'registre')->get('/registre/dossiers/'.$dossier->reference);

        $this->actingAs($agent, 'registre')
            ->get('/registre/journal')
            ->assertOk()
            ->assertSee('dossier.viewed', escape: false)
            ->assertSee('Aminata Issoufou', escape: false);
    }

    public function test_the_journal_is_not_open_to_a_visitor(): void
    {
        $this->get('/registre/journal')->assertRedirect('/registre/connexion');
    }
}
