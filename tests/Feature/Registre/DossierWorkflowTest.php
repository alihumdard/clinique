<?php

namespace Tests\Feature\Registre;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\User as RegistreUser;
use App\Services\Registre\DossierWorkflow;
use App\Support\Registre\DossierNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\RefreshesRegistreDatabase;
use Tests\TestCase;

/**
 * Moving a dossier from one status to another.
 *
 * The labels in play are provisional and the tests never name one: what
 * is tested is the shape the plan describes — only a declared move,
 * never past the supervisor's validation, always recorded.
 */
class DossierWorkflowTest extends TestCase
{
    use RefreshDatabase, RefreshesRegistreDatabase;

    private DossierStatus $first;
    private DossierStatus $second;
    private DossierStatus $gated;
    private DossierStatus $closing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->first = $this->makeStatus(1);
        $this->second = $this->makeStatus(2);
        $this->gated = $this->makeStatus(3, ['requires_supervisor_validation' => true]);
        $this->closing = $this->makeStatus(4, ['is_terminal' => true]);

        $this->allow($this->first, $this->second);
        $this->allow($this->second, $this->gated);
        $this->allow($this->second, $this->closing);
    }

    private function makeStatus(int $position, array $attributes = []): DossierStatus
    {
        return DossierStatus::create(array_merge([
            'code' => 'provisoire-'.str_pad((string) $position, 2, '0', STR_PAD_LEFT),
            'label' => 'Statut provisoire '.str_pad((string) $position, 2, '0', STR_PAD_LEFT),
            'position' => $position,
        ], $attributes));
    }

    private function allow(DossierStatus $from, DossierStatus $to): void
    {
        DB::connection('registre')->table('dossier_status_transitions')->insert([
            'from_status_id' => $from->id,
            'to_status_id' => $to->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

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

    private function dossier(?DossierStatus $status = null): Dossier
    {
        $applicant = Applicant::create([
            'last_name' => 'Abdou',
            'phone' => '+227 90 11 22 33',
        ]);

        return Dossier::create([
            'reference' => DossierNumber::next(),
            'applicant_id' => $applicant->id,
            'legal_domain' => 'Droit du travail',
            'subject' => 'Salaires non versés',
            'status_id' => ($status ?? $this->first)->id,
            'opened_at' => now(),
        ]);
    }

    // ------------------------------------------------- allowed moves

    public function test_a_declared_move_is_made_and_recorded(): void
    {
        $dossier = $this->dossier();
        $agent = $this->agent();

        app(DossierWorkflow::class)->moveTo($dossier, $this->second, $agent, 'Dossier complété.');

        $this->assertSame($this->second->id, $dossier->fresh()->status_id);

        $change = $dossier->statusHistory()->first();
        $this->assertSame($this->first->id, $change->from_status_id);
        $this->assertSame($this->second->id, $change->to_status_id);
        $this->assertSame($agent->id, $change->user_id);
        $this->assertSame('Dossier complété.', $change->note);
        $this->assertNotNull($change->created_at);
    }

    /** A status reached by typing an id is not a workflow. */
    public function test_an_undeclared_move_is_refused(): void
    {
        $dossier = $this->dossier();

        $this->expectException(RuntimeException::class);
        app(DossierWorkflow::class)->moveTo($dossier, $this->closing, $this->agent());
    }

    public function test_the_dossier_keeps_its_status_when_a_move_is_refused(): void
    {
        $dossier = $this->dossier();

        try {
            app(DossierWorkflow::class)->moveTo($dossier, $this->closing, $this->agent());
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame($this->first->id, $dossier->fresh()->status_id);
        $this->assertSame(0, $dossier->statusHistory()->count());
    }

    public function test_moving_to_the_status_it_already_has_is_refused(): void
    {
        $dossier = $this->dossier();

        $this->expectException(RuntimeException::class);
        app(DossierWorkflow::class)->moveTo($dossier, $this->first, $this->agent());
    }

    // ------------------------------------------- the supervisor's gate

    /**
     * "Empêcher le passage au statut « analyse retournée au demandeur »
     * tant que la validation du superviseur n'est pas terminée."
     */
    public function test_the_gated_status_is_refused_before_validation(): void
    {
        $dossier = $this->dossier($this->second);
        $agent = $this->agent();

        // Even an analysis that was written and submitted is not enough.
        $dossier->forceFill(['analysis' => 'Analyse.', 'analysis_submitted_at' => now()])->save();

        $this->expectException(RuntimeException::class);
        app(DossierWorkflow::class)->moveTo($dossier->fresh(), $this->gated, $agent);
    }

    public function test_the_gated_status_opens_once_the_analysis_is_validated(): void
    {
        $dossier = $this->dossier($this->second);
        $agent = $this->agent();

        $dossier->forceFill([
            'analysis' => 'Analyse.',
            'analysis_submitted_at' => now(),
            'analysis_validated_by' => $agent->id,
            'analysis_validated_at' => now(),
        ])->save();

        app(DossierWorkflow::class)->moveTo($dossier->fresh(), $this->gated, $agent);

        $fresh = $dossier->fresh();
        $this->assertSame($this->gated->id, $fresh->status_id);
        // And the moment it went back to the applicant is recorded.
        $this->assertNotNull($fresh->returned_to_applicant_at);
    }

    // ----------------------------------------------------- closing

    public function test_a_terminal_status_closes_the_dossier(): void
    {
        $dossier = $this->dossier($this->second);

        app(DossierWorkflow::class)->moveTo($dossier, $this->closing, $this->agent());

        $this->assertNotNull($dossier->fresh()->closed_at);
        $this->assertSame(0, Dossier::open()->count());
    }

    // ------------------------------------------------------ the screen

    public function test_the_screen_offers_only_the_declared_moves(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->get('/registre/dossiers/'.$dossier->reference)
            ->assertOk()
            ->assertSee('Statut provisoire 02', escape: false)
            // Not reachable from here, so not offered.
            ->assertDontSee('Statut provisoire 04', escape: false);
    }

    public function test_a_refused_move_from_the_screen_says_why(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/statut', [
                'status_id' => $this->closing->id,
            ])
            ->assertSessionHasErrors('status_id');

        $this->assertSame($this->first->id, $dossier->fresh()->status_id);
    }

    public function test_a_move_from_the_screen_is_logged(): void
    {
        $dossier = $this->dossier();

        $this->actingAs($this->agent(), 'registre')
            ->post('/registre/dossiers/'.$dossier->reference.'/statut', [
                'status_id' => $this->second->id,
                'note' => 'Pièces reçues.',
            ])
            ->assertRedirect();

        $this->assertSame(1, AuditLog::where('action', 'status.changed')->count());
        $this->assertSame($this->second->id, $dossier->fresh()->status_id);
    }

    public function test_a_visitor_cannot_move_a_dossier(): void
    {
        $dossier = $this->dossier();

        $this->post('/registre/dossiers/'.$dossier->reference.'/statut', [
            'status_id' => $this->second->id,
        ])->assertRedirect('/registre/connexion');

        $this->assertSame($this->first->id, $dossier->fresh()->status_id);
    }
}
