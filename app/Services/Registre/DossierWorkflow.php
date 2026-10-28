<?php

namespace App\Services\Registre;

use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\DossierStatusChange;
use App\Models\Registre\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Moves a dossier from one status to another.
 *
 * Three rules stand in the way, and all three are the plan's rather than
 * ours:
 *
 *   the move has to be one the Clinic allows — "des transitions
 *   autorisées" — so a status reached by typing an id is refused;
 *
 *   nothing reaches the status that returns the analysis to the
 *   applicant before the supervisor has validated it;
 *
 *   and every change is recorded "avec horodatage et identité de
 *   l'utilisateur", in a history that is only ever appended to.
 *
 * The statuses and the transitions themselves are rows. This class
 * never names one.
 */
class DossierWorkflow
{
    public function moveTo(Dossier $dossier, DossierStatus $target, User $agent, ?string $note = null): void
    {
        $current = $dossier->status;

        if ($current && $current->is($target)) {
            throw new RuntimeException('Ce dossier est déjà dans ce statut.');
        }

        /*
         * An allowed move, or none at all. While the Clinic has declared
         * no transition, every move is refused rather than permitted:
         * a workflow that allows everything is not a workflow, and
         * guessing one would be inventing their process.
         */
        if ($current && ! $current->allowedNext()->whereKey($target->getKey())->exists()) {
            throw new RuntimeException(
                "Ce passage n'est pas autorisé depuis le statut actuel."
            );
        }

        if ($target->requires_supervisor_validation && ! $dossier->returnableToApplicant()) {
            throw new RuntimeException(
                "L'analyse doit être validée par l'encadrant avant de passer à ce statut."
            );
        }

        DB::connection('registre')->transaction(function () use ($dossier, $current, $target, $agent, $note) {
            $dossier->forceFill([
                'status_id' => $target->id,
                // A terminal status closes the dossier; leaving one
                // reopens it, so the date follows the status rather than
                // being set by hand beside it.
                'closed_at' => $target->is_terminal ? now() : null,
                'returned_to_applicant_at' => $target->requires_supervisor_validation
                    ? now()
                    : $dossier->returned_to_applicant_at,
            ])->save();

            DossierStatusChange::create([
                'dossier_id' => $dossier->id,
                'from_status_id' => $current?->id,
                'to_status_id' => $target->id,
                'user_id' => $agent->id,
                'note' => $note,
                'created_at' => now(),
            ]);
        });

        AuditLog::record('status.changed', $dossier, [
            'reference' => $dossier->reference,
            'from' => $current?->label,
            'to' => $target->label,
        ]);
    }
}
