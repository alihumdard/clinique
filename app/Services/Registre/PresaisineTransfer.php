<?php

namespace App\Services\Registre;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierDocument;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\DossierStatusChange;
use App\Models\Registre\PresaisineSubmission;
use App\Models\Registre\User;
use App\Support\Registre\DossierNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Turns an accepted pré-saisine into a dossier.
 *
 * This is the "transfert" of the plan: "créer des dossiers à partir des
 * demandes en ligne admissibles", followed by "supprimer les pièces
 * jointes du serveur de réception après transfert sécurisé".
 *
 * The order of operations matters. The files are copied to the dossier
 * inside the transaction and the intake copies are removed only once it
 * has committed: deleting first would lose them for good if anything
 * after it failed.
 */
class PresaisineTransfer
{
    public function accept(PresaisineSubmission $submission, User $agent): Dossier
    {
        // A submission becomes one dossier and no more: a second click,
        // a double submit or a replayed request must not open another.
        if ($submission->status === PresaisineSubmission::STATUS_ACCEPTED) {
            throw new RuntimeException('Cette demande a déjà été acceptée.');
        }

        $status = DossierStatus::opening();

        if (! $status) {
            throw new RuntimeException(
                "Aucun statut de dossier n'est enregistré : la liste approuvée par la Clinique est attendue."
            );
        }

        [$dossier, $copied] = DB::connection('registre')->transaction(function () use ($submission, $agent, $status) {
            $applicant = Applicant::create([
                'last_name' => $submission->last_name,
                'first_name' => $submission->first_name,
                'phone' => $submission->phone,
                'email' => $submission->email,
                'commune' => $submission->commune,
                'is_uam_student' => $submission->is_uam_student,
                'student_card_number' => $submission->student_card_number,
            ]);

            $dossier = Dossier::create([
                'reference' => DossierNumber::next(),
                'applicant_id' => $applicant->id,
                'legal_domain' => $submission->legal_domain,
                'subject' => $submission->subject,
                'narrative' => $submission->narrative,
                'question_1' => $submission->question_1,
                'question_2' => $submission->question_2,
                'question_3' => $submission->question_3,
                'status_id' => $status->id,
                'source' => Dossier::SOURCE_ONLINE,
                'presaisine_reference' => $submission->reference,
                'opened_at' => now(),
                'created_by' => $agent->id,
            ]);

            $copied = [];

            foreach ($submission->attachments()->whereNull('purged_at')->get() as $attachment) {
                $destination = sprintf(
                    'dossiers/%d/%s/%s',
                    $dossier->opened_at->year,
                    $dossier->reference,
                    basename($attachment->path)
                );

                // Copy, not move: the intake file is removed after the
                // transaction has committed, never before.
                Storage::disk('local')->copy($attachment->path, $destination);

                DossierDocument::create([
                    'dossier_id' => $dossier->id,
                    'kind' => DossierDocument::KIND_ATTACHMENT,
                    'path' => $destination,
                    'original_name' => $attachment->original_name,
                    'mime_type' => $attachment->mime_type,
                    'size' => $attachment->size,
                    'from_presaisine' => true,
                    'uploaded_by' => $agent->id,
                ]);

                $copied[] = $attachment;
            }

            // The opening of the dossier is the first line of its history.
            DossierStatusChange::create([
                'dossier_id' => $dossier->id,
                'from_status_id' => null,
                'to_status_id' => $status->id,
                'user_id' => $agent->id,
                'note' => 'Ouverture depuis la pré-saisine '.$submission->reference.'.',
                'created_at' => now(),
            ]);

            $submission->forceFill([
                'status' => PresaisineSubmission::STATUS_ACCEPTED,
                'reviewed_by' => $agent->id,
                'reviewed_at' => now(),
                'dossier_id' => $dossier->id,
            ])->save();

            return [$dossier, $copied];
        });

        /*
         * Committed. Now the intake copies go — "supprimer du serveur de
         * réception après transfert sécurisé". The row stays, with the
         * moment its file was removed, so what arrived is still on record.
         */
        foreach ($copied as $attachment) {
            Storage::disk('local')->delete($attachment->path);
            $attachment->forceFill(['purged_at' => now()])->save();
        }

        AuditLog::record('presaisine.accepted', $submission, [
            'submission' => $submission->reference,
            'dossier' => $dossier->reference,
            'attachments' => count($copied),
        ]);

        AuditLog::record('dossier.created', $dossier, [
            'reference' => $dossier->reference,
            'source' => Dossier::SOURCE_ONLINE,
        ]);

        return $dossier;
    }

    /**
     * A request the Clinic does not take on. No dossier is created, and
     * the intake attachments are removed: there is nothing left to
     * transfer them to.
     */
    public function refuse(PresaisineSubmission $submission, User $agent, ?string $note = null): void
    {
        if ($submission->status !== PresaisineSubmission::STATUS_PENDING) {
            throw new RuntimeException('Cette demande a déjà été traitée.');
        }

        $submission->forceFill([
            'status' => PresaisineSubmission::STATUS_REFUSED,
            'reviewed_by' => $agent->id,
            'reviewed_at' => now(),
            'review_note' => $note,
        ])->save();

        foreach ($submission->attachments()->whereNull('purged_at')->get() as $attachment) {
            Storage::disk('local')->delete($attachment->path);
            $attachment->forceFill(['purged_at' => now()])->save();
        }

        AuditLog::record('presaisine.refused', $submission, [
            'submission' => $submission->reference,
        ]);
    }
}
