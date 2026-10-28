<?php

namespace Database\Seeders;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierAssignment;
use App\Models\Registre\DossierDeadline;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\DossierStatusChange;
use App\Models\Registre\User;
use App\Support\Registre\DossierNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fictitious dossiers, for development and review of the Registre.
 *
 * Nothing here is a real matter, and nothing here invents the Clinic's
 * process: the thirteen statuses are seeded as numbered placeholders,
 * flagged provisional, and no transition between them is declared. The
 * Clinic's approved labels and transitions replace them without a
 * migration.
 *
 * Run with: php artisan db:seed --class=RegistreDossierSeeder
 */
class RegistreDossierSeeder extends Seeder
{
    public function run(): void
    {
        $this->statuses();

        $statuses = DossierStatus::orderBy('position')->get();
        $coordinator = User::where('role', User::ROLE_COORDINATOR)->first();
        $supervisor = User::where('role', User::ROLE_SUPERVISOR)->first();
        $student = User::where('role', User::ROLE_STUDENT)->first();

        if ($statuses->isEmpty() || ! $coordinator) {
            $this->command?->warn('Comptes du registre absents : lancez RegistreSeeder d\'abord.');

            return;
        }

        $applicants = $this->applicants();

        $cases = [
            ['Droit de la famille', "Garde des enfants après séparation", 'sur_place', 'normale', 0, 12],
            ['Droit du travail', 'Licenciement sans préavis', 'en_ligne', 'elevee', 2, 9],
            ['Droit foncier', 'Litige sur une parcelle héritée', 'sur_place', 'normale', 3, 30],
            ['État civil', "Acte de naissance non établi", 'en_ligne', 'normale', 1, 5],
            ['Droit de la famille', 'Pension alimentaire impayée', 'sur_place', 'urgente', 4, 2],
            ['Droit pénal', "Plainte pour violences", 'sur_place', 'urgente', 5, 1],
            ['Droit du travail', "Salaires non versés", 'en_ligne', 'elevee', 6, 20],
            ['Succession', "Partage d'une succession", 'sur_place', 'normale', 7, 45],
        ];

        foreach ($cases as $i => [$domain, $subject, $source, $urgency, $statusIndex, $daysAgo]) {
            $applicant = $applicants[$i % count($applicants)];
            $status = $statuses[$statusIndex % $statuses->count()];
            $openedAt = now()->subDays($daysAgo);

            $dossier = Dossier::updateOrCreate(
                ['presaisine_reference' => 'DEMO-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'reference' => DossierNumber::next((int) $openedAt->year),
                    'applicant_id' => $applicant->id,
                    'legal_domain' => $domain,
                    'subject' => $subject,
                    'narrative' => "Exposé d'exemple pour la relecture du registre. Le contenu réel des dossiers n'est jamais fictif ni partagé.",
                    'question_1' => 'Quelles démarches puis-je engager ?',
                    'status_id' => $status->id,
                    'source' => $source,
                    'urgency' => $urgency,
                    'opened_at' => $openedAt,
                    'created_by' => $coordinator->id,
                ]
            );

            // One student and one supervisor on each, which is the
            // arrangement the plan describes.
            foreach ([[$student, DossierAssignment::ROLE_STUDENT], [$supervisor, DossierAssignment::ROLE_SUPERVISOR]] as [$agent, $role]) {
                if (! $agent) {
                    continue;
                }

                DossierAssignment::updateOrCreate(
                    ['dossier_id' => $dossier->id, 'user_id' => $agent->id, 'role' => $role],
                    ['assigned_by' => $coordinator->id, 'assigned_at' => $openedAt]
                );
            }

            // A couple of them overdue on purpose, so the "retards" view
            // has something to show.
            DossierDeadline::updateOrCreate(
                ['dossier_id' => $dossier->id, 'label' => 'Rappeler le demandeur'],
                [
                    'due_at' => $i % 3 === 0 ? now()->subDays(2) : now()->addDays(7),
                    'created_by' => $coordinator->id,
                ]
            );

            if ($dossier->statusHistory()->count() === 0) {
                DossierStatusChange::create([
                    'dossier_id' => $dossier->id,
                    'from_status_id' => null,
                    'to_status_id' => $status->id,
                    'user_id' => $coordinator->id,
                    'note' => "Ouverture du dossier (donnée d'exemple).",
                    'created_at' => $openedAt,
                ]);
            }

            AuditLog::firstOrCreate(
                [
                    'action' => 'dossier.created',
                    'subject_type' => 'Dossier',
                    'subject_id' => $dossier->id,
                ],
                [
                    'user_id' => $coordinator->id,
                    'context' => ['reference' => $dossier->reference],
                    'created_at' => $openedAt,
                ]
            );
        }
    }

    /**
     * Thirteen placeholders, and a provisional path between them.
     *
     * The labels stay numbered: inventing the Clinic's wording is what
     * the plan forbids, and a number cannot be mistaken for an approved
     * label. What is real is the shape — a dossier moves forward one
     * step at a time, can be sent back one step, and ends in one of two
     * terminal statuses.
     *
     * Position 11 is marked as the one the supervisor's validation
     * stands in front of, so the rule the plan demands can be seen
     * working. Which status that really is, the Clinic will say.
     *
     * When their list arrives, the labels and the transitions below are
     * replaced and nothing in the application changes.
     */
    private function statuses(): void
    {
        $statuses = [];

        for ($i = 1; $i <= 13; $i++) {
            $statuses[$i] = DossierStatus::updateOrCreate(
                ['code' => 'provisoire-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)],
                [
                    'label' => 'Statut provisoire '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'position' => $i,
                    // The last two end a dossier: one closes it, one
                    // ends it without a consultation.
                    'is_terminal' => $i >= 12,
                    'requires_supervisor_validation' => $i === 11,
                    'is_provisional' => true,
                ]
            );
        }

        $pairs = [];

        for ($i = 1; $i <= 11; $i++) {
            // One step forward.
            $pairs[] = [$i, $i + 1];

            // And one step back, because a dossier that cannot be
            // corrected is a dossier people work around.
            if ($i > 1) {
                $pairs[] = [$i, $i - 1];
            }

            // Abandoned at any point before the end.
            $pairs[] = [$i, 13];
        }

        foreach ($pairs as [$from, $to]) {
            DB::connection('registre')->table('dossier_status_transitions')->updateOrInsert(
                [
                    'from_status_id' => $statuses[$from]->id,
                    'to_status_id' => $statuses[$to]->id,
                ],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /** @return array<int, Applicant> */
    private function applicants(): array
    {
        $people = [
            ['Abdou', 'Hadiza', '+227 90 00 00 01', 'Niamey', false, null],
            ['Moussa', 'Ibrahim', '+227 90 00 00 02', 'Tillabéri', false, null],
            ['Souley', 'Ramatou', '+227 90 00 00 03', 'Dosso', true, 'UAM-2024-0411'],
            ['Garba', 'Oumarou', '+227 90 00 00 04', 'Zinder', false, null],
            ['Idrissa', 'Fatouma', '+227 90 00 00 05', 'Niamey', true, 'UAM-2023-1187'],
        ];

        return array_map(
            fn (array $p) => Applicant::updateOrCreate(
                ['phone' => $p[2]],
                [
                    'last_name' => $p[0],
                    'first_name' => $p[1],
                    'commune' => $p[3],
                    'is_uam_student' => $p[4],
                    'student_card_number' => $p[5],
                ]
            ),
            $people
        );
    }
}
