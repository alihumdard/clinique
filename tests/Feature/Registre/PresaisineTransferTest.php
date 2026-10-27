<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\PresaisineAttachment;
use App\Models\Registre\PresaisineSubmission;
use App\Models\Registre\User as RegistreUser;
use App\Services\Registre\PresaisineTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The transfer: an accepted pré-saisine becomes a dossier, and the files
 * it arrived with leave the intake server.
 */
class PresaisineTransferTest extends TestCase
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
    }

    /** firstOrCreate: a test may need the same agent more than once. */
    private function agent(): RegistreUser
    {
        return RegistreUser::firstOrCreate(
            ['email' => 'coordination@registre.test'],
            [
                'name' => 'Aminata Issoufou',
                'password' => 'password-for-tests',
                'role' => RegistreUser::ROLE_COORDINATOR,
            ]
        );
    }

    private function submission(bool $withAttachment = false): PresaisineSubmission
    {
        $submission = PresaisineSubmission::create([
            'reference' => 'PS-2026-0001',
            'last_name' => 'Abdou',
            'first_name' => 'Hadiza',
            'phone' => '+227 90 11 22 33',
            'email' => 'hadiza@example.test',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'narrative' => 'Trois mois sans salaire.',
            'question_1' => "Puis-je saisir l'inspection du travail ?",
            'orientation' => ['urgence' => 'non'],
            'consent_at' => now(),
        ]);

        if ($withAttachment) {
            Storage::disk('local')->put('presaisine/2026/PS-2026-0001/contrat.pdf', 'contenu');

            PresaisineAttachment::create([
                'submission_id' => $submission->id,
                'path' => 'presaisine/2026/PS-2026-0001/contrat.pdf',
                'original_name' => 'contrat.pdf',
                'mime_type' => 'application/pdf',
                'size' => 7,
            ]);
        }

        return $submission->fresh();
    }

    // ---------------------------------------------------------- accept

    public function test_accepting_opens_a_dossier_carrying_the_request(): void
    {
        $submission = $this->submission();

        $dossier = app(PresaisineTransfer::class)->accept($submission, $this->agent());

        $this->assertMatchesRegularExpression('/^CJ-\d{4}-\d{4}$/', $dossier->reference);
        $this->assertSame('Salaires non versés', $dossier->subject);
        $this->assertSame('Droit du travail', $dossier->legal_domain);
        $this->assertSame(Dossier::SOURCE_ONLINE, $dossier->source);
        $this->assertSame('PS-2026-0001', $dossier->presaisine_reference);
        $this->assertSame('Hadiza Abdou', $dossier->applicant->fullName());
        $this->assertSame('+227 90 11 22 33', $dossier->applicant->phone);
    }

    public function test_the_submission_is_marked_accepted_and_linked(): void
    {
        $submission = $this->submission();
        $agent = $this->agent();

        $dossier = app(PresaisineTransfer::class)->accept($submission, $agent);
        $submission->refresh();

        $this->assertSame(PresaisineSubmission::STATUS_ACCEPTED, $submission->status);
        $this->assertSame($dossier->id, $submission->dossier_id);
        $this->assertSame($agent->id, $submission->reviewed_by);
        $this->assertNotNull($submission->reviewed_at);
    }

    public function test_the_opening_is_the_first_line_of_the_history(): void
    {
        $submission = $this->submission();

        $dossier = app(PresaisineTransfer::class)->accept($submission, $this->agent());
        $first = $dossier->statusHistory()->first();

        $this->assertNull($first->from_status_id);
        $this->assertSame($dossier->status_id, $first->to_status_id);
        $this->assertStringContainsString('PS-2026-0001', $first->note);
    }

    /** "Supprimer les pièces jointes du serveur de réception après transfert." */
    public function test_an_attachment_moves_to_the_dossier_and_leaves_the_intake(): void
    {
        $submission = $this->submission(withAttachment: true);

        $dossier = app(PresaisineTransfer::class)->accept($submission, $this->agent());

        $document = $dossier->documents()->first();
        $this->assertSame('contrat.pdf', $document->original_name);
        $this->assertTrue($document->from_presaisine);
        Storage::disk('local')->assertExists($document->path);

        // Gone from intake, but the record of what arrived stays.
        $attachment = $submission->attachments()->first();
        Storage::disk('local')->assertMissing($attachment->path);
        $this->assertNotNull($attachment->fresh()->purged_at);
        $this->assertSame('contrat.pdf', $attachment->original_name);
    }

    public function test_a_submission_cannot_open_two_dossiers(): void
    {
        $submission = $this->submission();
        $agent = $this->agent();

        app(PresaisineTransfer::class)->accept($submission, $agent);

        $this->expectException(RuntimeException::class);
        app(PresaisineTransfer::class)->accept($submission->fresh(), $agent);
    }

    /** A dossier is never opened under an invented status. */
    public function test_accepting_fails_while_no_status_is_recorded(): void
    {
        DossierStatus::query()->delete();

        $this->expectException(RuntimeException::class);
        app(PresaisineTransfer::class)->accept($this->submission(), $this->agent());
    }

    public function test_the_transfer_is_written_to_the_access_log(): void
    {
        app(PresaisineTransfer::class)->accept($this->submission(), $this->agent());

        $this->assertSame(1, AuditLog::where('action', 'presaisine.accepted')->count());
        $this->assertSame(1, AuditLog::where('action', 'dossier.created')->count());
    }

    // ---------------------------------------------------------- refuse

    public function test_refusing_opens_no_dossier_and_removes_the_files(): void
    {
        $submission = $this->submission(withAttachment: true);
        $path = $submission->attachments()->first()->path;

        app(PresaisineTransfer::class)->refuse($submission, $this->agent(), 'Hors domaine.');

        $this->assertSame(0, Dossier::count());
        $this->assertSame(PresaisineSubmission::STATUS_REFUSED, $submission->fresh()->status);
        $this->assertSame('Hors domaine.', $submission->fresh()->review_note);
        Storage::disk('local')->assertMissing($path);
        $this->assertNotNull($submission->attachments()->first()->purged_at);
    }

    public function test_a_handled_request_cannot_be_refused_again(): void
    {
        $submission = $this->submission();
        $agent = $this->agent();

        app(PresaisineTransfer::class)->refuse($submission, $agent);

        $this->expectException(RuntimeException::class);
        app(PresaisineTransfer::class)->refuse($submission->fresh(), $agent);
    }

    // --------------------------------------------------- the screens

    public function test_the_inbox_lists_requests_and_opens_one(): void
    {
        $submission = $this->submission();

        $this->actingAs($this->agent(), 'registre')
            ->get('/registre/demandes')
            ->assertOk()
            ->assertSee('PS-2026-0001', escape: false)
            ->assertSee('Hadiza Abdou', escape: false);

        $this->actingAs($this->agent(), 'registre')
            ->get('/registre/demandes/'.$submission->reference)
            ->assertOk()
            ->assertSee('Salaires non versés', escape: false);
    }

    public function test_reading_a_request_is_logged(): void
    {
        $submission = $this->submission();

        $this->actingAs($this->agent(), 'registre')->get('/registre/demandes/'.$submission->reference);

        $this->assertSame(1, AuditLog::where('action', 'presaisine.viewed')->count());
    }

    public function test_accepting_from_the_screen_opens_the_dossier(): void
    {
        $submission = $this->submission();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/demandes/'.$submission->reference.'/accepter')
            ->assertRedirect('/registre/demandes/'.$submission->reference);

        $this->assertSame(1, Dossier::count());
    }

    /** The intake files are not public: no account, no file. */
    public function test_an_attachment_is_not_served_to_a_visitor(): void
    {
        $submission = $this->submission(withAttachment: true);
        $attachment = $submission->attachments()->first();

        $this->get("/registre/demandes/{$submission->reference}/piece/{$attachment->id}")
            ->assertRedirect('/registre/connexion');
    }

    public function test_an_attachment_download_is_logged(): void
    {
        $submission = $this->submission(withAttachment: true);
        $attachment = $submission->attachments()->first();

        $this->actingAs($this->agent(), 'registre')
            ->get("/registre/demandes/{$submission->reference}/piece/{$attachment->id}")
            ->assertOk();

        $this->assertSame(1, AuditLog::where('action', 'presaisine.attachment.downloaded')->count());
    }

    /** Once transferred, the intake copy is gone and says so. */
    public function test_a_purged_attachment_is_no_longer_downloadable(): void
    {
        $submission = $this->submission(withAttachment: true);
        $attachment = $submission->attachments()->first();

        app(PresaisineTransfer::class)->accept($submission, $this->agent());

        $this->actingAs($this->agent(), 'registre')
            ->get("/registre/demandes/{$submission->reference}/piece/{$attachment->id}")
            ->assertStatus(410);
    }
}
