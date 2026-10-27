<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierAssignment;
use App\Models\Registre\DossierDeadline;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\DossierStatusChange;
use App\Models\Registre\User as RegistreUser;
use App\Support\Registre\DossierNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * The Registre's schema: dossiers, their numbering, what hangs off them,
 * and the log that records what was done to them.
 */
class DossierSchemaTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private function makeStatus(array $attributes = []): DossierStatus
    {
        return DossierStatus::create(array_merge([
            'code' => 'provisoire-01',
            'label' => 'Statut provisoire 01',
            'position' => 1,
        ], $attributes));
    }

    private function dossier(array $attributes = []): Dossier
    {
        $applicant = Applicant::create([
            'last_name' => 'Abdou',
            'first_name' => 'Hadiza',
            'phone' => '+227 90 00 00 01',
        ]);

        return Dossier::create(array_merge([
            'reference' => DossierNumber::next(),
            'applicant_id' => $applicant->id,
            'legal_domain' => 'Droit de la famille',
            'subject' => 'Garde des enfants',
            'status_id' => ($attributes['status_id'] ?? null) ?: $this->makeStatus()->id,
            'opened_at' => now(),
        ], $attributes));
    }

    // ------------------------------------------------------- separation

    /** The confidential tables belong to the Registre's database alone. */
    public function test_the_dossier_tables_are_not_in_the_public_database(): void
    {
        $public = Schema::connection(config('database.default'));

        foreach (['dossiers', 'applicants', 'dossier_documents', 'audit_logs'] as $table) {
            $this->assertTrue(Schema::connection('registre')->hasTable($table), $table);
            $this->assertFalse($public->hasTable($table), $table.' must not exist publicly');
        }
    }

    public function test_every_registre_model_uses_the_registre_connection(): void
    {
        foreach ([Dossier::class, Applicant::class, DossierStatus::class, AuditLog::class, RegistreUser::class] as $model) {
            $this->assertSame('registre', (new $model)->getConnectionName(), $model);
        }
    }

    // -------------------------------------------------------- numbering

    public function test_a_reference_is_unique_and_sequential(): void
    {
        $first = DossierNumber::next(2026);
        $second = DossierNumber::next(2026);

        $this->assertSame('CJ-2026-0001', $first);
        $this->assertSame('CJ-2026-0002', $second);
    }

    /** The counter restarts each year, and the year is part of the number. */
    public function test_the_counter_restarts_each_year(): void
    {
        DossierNumber::next(2026);
        DossierNumber::next(2026);

        $this->assertSame('CJ-2027-0001', DossierNumber::next(2027));
        $this->assertSame('CJ-2026-0003', DossierNumber::next(2026));
    }

    /**
     * Counting existing dossiers would hand a deleted one's number to the
     * next dossier opened — two files quoting the same reference.
     */
    public function test_a_deleted_dossier_does_not_give_its_number_away(): void
    {
        $dossier = $this->dossier();
        $taken = $dossier->reference;
        $dossier->delete();

        $this->assertNotSame($taken, DossierNumber::next());
    }

    // ------------------------------------------------------- the dossier

    public function test_a_dossier_carries_its_applicant_status_and_people(): void
    {
        $status = $this->makeStatus();
        $dossier = $this->dossier(['status_id' => $status->id]);

        $agent = RegistreUser::create([
            'name' => 'Fatima Abdou',
            'email' => 'etudiant@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_STUDENT,
        ]);

        DossierAssignment::create([
            'dossier_id' => $dossier->id,
            'user_id' => $agent->id,
            'role' => DossierAssignment::ROLE_STUDENT,
            'assigned_at' => now(),
        ]);

        $dossier->refresh();

        $this->assertSame('Hadiza Abdou', $dossier->applicant->fullName());
        $this->assertSame($status->id, $dossier->status->id);
        $this->assertCount(1, $dossier->assignments);
        $this->assertSame('Fatima Abdou', $dossier->assignments->first()->user->name);
    }

    /** An assignment ends rather than disappearing: it is part of the history. */
    public function test_an_ended_assignment_is_kept_but_no_longer_current(): void
    {
        $dossier = $this->dossier();
        $agent = RegistreUser::create([
            'name' => 'Ancien étudiant',
            'email' => 'ancien@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_STUDENT,
        ]);

        DossierAssignment::create([
            'dossier_id' => $dossier->id,
            'user_id' => $agent->id,
            'role' => DossierAssignment::ROLE_STUDENT,
            'assigned_at' => now()->subMonth(),
            'ended_at' => now()->subWeek(),
        ]);

        $this->assertCount(1, $dossier->assignments);
        $this->assertCount(0, DossierAssignment::current()->get());
    }

    public function test_an_overdue_deadline_marks_the_dossier_late(): void
    {
        $late = $this->dossier();
        DossierDeadline::create([
            'dossier_id' => $late->id,
            'label' => 'Rappeler le demandeur',
            'due_at' => now()->subDay(),
        ]);

        $onTime = $this->dossier(['status_id' => $late->status_id]);
        DossierDeadline::create([
            'dossier_id' => $onTime->id,
            'label' => 'Audience',
            'due_at' => now()->addWeek(),
        ]);

        $this->assertSame([$late->id], Dossier::overdue()->pluck('id')->all());
    }

    public function test_a_completed_deadline_is_no_longer_late(): void
    {
        $dossier = $this->dossier();
        DossierDeadline::create([
            'dossier_id' => $dossier->id,
            'label' => 'Rappeler le demandeur',
            'due_at' => now()->subDay(),
            'completed_at' => now(),
        ]);

        $this->assertSame(0, Dossier::overdue()->count());
    }

    // ------------------------------------------------- statuses and log

    /**
     * The thirteen labels are the Clinic's. Nothing in the code may name
     * one, and no transition may be assumed before they are approved.
     */
    public function test_no_transition_is_assumed_before_the_clinic_approves_them(): void
    {
        $this->makeStatus();

        $this->assertTrue(DossierStatus::transitionsAreUndefined());
    }

    public function test_a_status_change_records_the_move_the_moment_and_the_person(): void
    {
        $from = $this->makeStatus();
        $to = $this->makeStatus(['code' => 'provisoire-02', 'label' => 'Statut provisoire 02', 'position' => 2]);
        $dossier = $this->dossier(['status_id' => $from->id]);

        $agent = RegistreUser::create([
            'name' => 'Aminata Issoufou',
            'email' => 'coordination@registre.test',
            'password' => 'password-for-tests',
            'role' => RegistreUser::ROLE_COORDINATOR,
        ]);

        DossierStatusChange::create([
            'dossier_id' => $dossier->id,
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
            'user_id' => $agent->id,
            'created_at' => now(),
        ]);

        $change = $dossier->statusHistory()->first();

        $this->assertSame($from->id, $change->fromStatus->id);
        $this->assertSame($to->id, $change->toStatus->id);
        $this->assertSame('Aminata Issoufou', $change->user->name);
        $this->assertNotNull($change->created_at);
    }

    public function test_the_log_records_what_was_done_and_to_what(): void
    {
        $dossier = $this->dossier();

        AuditLog::record('dossier.viewed', $dossier, ['reference' => $dossier->reference]);

        $entry = AuditLog::first();

        $this->assertSame('dossier.viewed', $entry->action);
        $this->assertSame('Dossier', $entry->subject_type);
        $this->assertSame($dossier->id, $entry->subject_id);
        $this->assertSame($dossier->reference, $entry->context['reference']);
        $this->assertNotNull($entry->created_at);
    }

    /** The log must outlive what it describes, or it erases its own evidence. */
    public function test_the_log_survives_the_deletion_of_its_subject(): void
    {
        $dossier = $this->dossier();
        AuditLog::record('dossier.created', $dossier);

        $dossier->forceDelete();

        $this->assertSame(1, AuditLog::count());
        $this->assertSame('Dossier', AuditLog::first()->subject_type);
    }
}
