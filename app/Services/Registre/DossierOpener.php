<?php

namespace App\Services\Registre;

use App\Models\Registre\Applicant;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\DossierStatusChange;
use App\Models\Registre\User;
use App\Support\Registre\DossierNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Opens a dossier for someone received at the permanence.
 *
 * The same ending as a transferred pré-saisine: an applicant, a numbered
 * dossier, the first line of its history and a log entry. Only the
 * source differs, which is why both paths read the opening status the
 * same way rather than naming one.
 */
class DossierOpener
{
    /** @param  array<string, mixed>  $data */
    public function open(array $data, User $agent): Dossier
    {
        $status = DossierStatus::opening();

        if (! $status) {
            throw new RuntimeException(
                "Aucun statut de dossier n'est enregistré : la liste approuvée par la Clinique est attendue."
            );
        }

        $dossier = DB::connection('registre')->transaction(function () use ($data, $agent, $status) {
            $applicant = Applicant::create([
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'commune' => $data['commune'] ?? null,
                'is_uam_student' => (bool) ($data['is_uam_student'] ?? false),
                'student_card_number' => $data['student_card_number'] ?? null,
            ]);

            $openedAt = ! empty($data['opened_at']) ? \Illuminate\Support\Carbon::parse($data['opened_at']) : now();

            $dossier = Dossier::create([
                // The year of the opening date, not of today: a dossier
                // recorded in January for a consultation held in December
                // belongs to December's series.
                'reference' => DossierNumber::next((int) $openedAt->year),
                'applicant_id' => $applicant->id,
                'legal_domain' => $data['legal_domain'],
                'subject' => $data['subject'],
                'narrative' => $data['narrative'] ?? null,
                'question_1' => $data['question_1'] ?? null,
                'question_2' => $data['question_2'] ?? null,
                'question_3' => $data['question_3'] ?? null,
                'status_id' => $status->id,
                'source' => Dossier::SOURCE_WALK_IN,
                'urgency' => $data['urgency'] ?? Dossier::URGENCY_NORMAL,
                'opened_at' => $openedAt,
                'created_by' => $agent->id,
            ]);

            DossierStatusChange::create([
                'dossier_id' => $dossier->id,
                'from_status_id' => null,
                'to_status_id' => $status->id,
                'user_id' => $agent->id,
                'note' => 'Ouverture sur place.',
                'created_at' => now(),
            ]);

            return $dossier;
        });

        AuditLog::record('dossier.created', $dossier, [
            'reference' => $dossier->reference,
            'source' => Dossier::SOURCE_WALK_IN,
        ]);

        return $dossier;
    }
}
