<?php

namespace Tests\Feature;

use App\Models\Registre\AuditLog;
use App\Models\Registre\PresaisineAttachment;
use App\Models\Registre\PresaisineSubmission;
use App\Support\Registre\PresaisineNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The retention rules of the module 4 checklist.
 *
 * What the command removes matters less than what it leaves: an accepted
 * request, a recent one, and anything to do with a dossier are all kept.
 */
class RetentionRulesTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function submission(string $status, int $monthsAgo, bool $withFile = false): PresaisineSubmission
    {
        $submission = PresaisineSubmission::create([
            'reference' => PresaisineNumber::next(),
            'last_name' => 'Abdou',
            'phone' => '+227 90 00 00 01',
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires',
            'status' => $status,
            'consent_at' => now()->subMonths($monthsAgo),
        ]);

        // created_at is not fillable; the age is what the rule reads.
        $submission->forceFill(['created_at' => now()->subMonths($monthsAgo)])->save();

        if ($withFile) {
            Storage::disk('local')->put('presaisine/2026/'.$submission->reference.'/piece.pdf', 'contenu');

            PresaisineAttachment::create([
                'submission_id' => $submission->id,
                'path' => 'presaisine/2026/'.$submission->reference.'/piece.pdf',
                'original_name' => 'piece.pdf',
                'mime_type' => 'application/pdf',
                'size' => 7,
            ]);
        }

        return $submission->fresh();
    }

    public function test_an_unaccepted_request_is_removed_after_six_months(): void
    {
        $old = $this->submission(PresaisineSubmission::STATUS_PENDING, 7);
        $recent = $this->submission(PresaisineSubmission::STATUS_PENDING, 2);

        $this->artisan('clinique:conservation')->assertSuccessful();

        $this->assertNull(PresaisineSubmission::find($old->id));
        $this->assertNotNull(PresaisineSubmission::find($recent->id));
    }

    public function test_a_refused_request_is_removed_too(): void
    {
        $refused = $this->submission(PresaisineSubmission::STATUS_REFUSED, 8);

        $this->artisan('clinique:conservation');

        $this->assertNull(PresaisineSubmission::find($refused->id));
    }

    /** An accepted request became a dossier; its retention is the dossier's. */
    public function test_an_accepted_request_is_kept(): void
    {
        $accepted = $this->submission(PresaisineSubmission::STATUS_ACCEPTED, 24);

        $this->artisan('clinique:conservation');

        $this->assertNotNull(PresaisineSubmission::find($accepted->id));
    }

    public function test_the_files_of_a_removed_request_go_with_it(): void
    {
        $old = $this->submission(PresaisineSubmission::STATUS_PENDING, 7, withFile: true);
        $path = $old->attachments()->first()->path;

        $this->artisan('clinique:conservation');

        Storage::disk('local')->assertMissing($path);
        $this->assertSame(0, PresaisineAttachment::where('submission_id', $old->id)->count());
    }

    public function test_the_log_is_kept_for_twelve_months(): void
    {
        $old = AuditLog::create([
            'action' => 'dossier.viewed',
            'created_at' => now()->subMonths(13),
        ]);

        $recent = AuditLog::create([
            'action' => 'dossier.viewed',
            'created_at' => now()->subMonths(6),
        ]);

        $this->artisan('clinique:conservation');

        $this->assertNull(AuditLog::find($old->id));
        $this->assertNotNull(AuditLog::find($recent->id));
    }

    /** Files from a form somebody started and never sent. */
    public function test_an_abandoned_draft_folder_is_swept(): void
    {
        Storage::disk('local')->put('presaisine/brouillons/abandonne/piece.pdf', 'contenu');

        // The fake disk reports "now", so the sweep needs a file older
        // than the cutoff: travelling forward ages it.
        $this->travel(8)->days();

        $this->artisan('clinique:conservation');

        Storage::disk('local')->assertMissing('presaisine/brouillons/abandonne/piece.pdf');
    }

    public function test_a_fresh_draft_folder_is_left_alone(): void
    {
        Storage::disk('local')->put('presaisine/brouillons/en-cours/piece.pdf', 'contenu');

        $this->artisan('clinique:conservation');

        Storage::disk('local')->assertExists('presaisine/brouillons/en-cours/piece.pdf');
    }

    /** Nothing is deleted silently, and nothing at all in a dry run. */
    public function test_a_dry_run_deletes_nothing(): void
    {
        $old = $this->submission(PresaisineSubmission::STATUS_PENDING, 7);

        $this->artisan('clinique:conservation --dry-run')
            ->expectsOutputToContain('Simulation')
            ->assertSuccessful();

        $this->assertNotNull(PresaisineSubmission::find($old->id));
    }
}
