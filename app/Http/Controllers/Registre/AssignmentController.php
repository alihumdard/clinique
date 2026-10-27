<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierAssignment;
use App\Models\Registre\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Who works on a dossier: "affecter un ou plusieurs étudiants cliniciens
 * et un superviseur/encadrant".
 *
 * An assignment is ended, never deleted — who worked on a dossier and
 * when is part of its history, and a dossier that lost its record of
 * that would be a dossier nobody can account for.
 */
class AssignmentController extends Controller
{
    public function store(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer'],
            'role' => ['required', Rule::in([DossierAssignment::ROLE_STUDENT, DossierAssignment::ROLE_SUPERVISOR])],
        ], [], [
            'user_id' => 'personne',
            'role' => 'rôle',
        ]);

        $agent = User::active()->find($data['user_id']);

        if (! $agent) {
            return back()->withErrors(['user_id' => "Ce compte n'existe pas ou n'est plus actif."]);
        }

        // Already on it in that part: adding them twice would double
        // every count without changing who is working on the dossier.
        $already = $dossier->assignments()
            ->current()
            ->where('user_id', $agent->id)
            ->where('role', $data['role'])
            ->exists();

        if ($already) {
            return back()->withErrors(['user_id' => 'Cette personne est déjà affectée à ce dossier dans ce rôle.']);
        }

        $dossier->assignments()->create([
            'user_id' => $agent->id,
            'role' => $data['role'],
            'assigned_by' => Auth::guard('registre')->id(),
            'assigned_at' => now(),
        ]);

        AuditLog::record('assignment.created', $dossier, [
            'reference' => $dossier->reference,
            'assigned' => $agent->name,
            'role' => $data['role'],
        ]);

        return back()->with('registre_message', "{$agent->name} a été affecté(e) au dossier.");
    }

    public function end(Dossier $dossier, DossierAssignment $assignment): RedirectResponse
    {
        abort_unless($assignment->dossier_id === $dossier->id, 404);

        if ($assignment->ended_at === null) {
            $assignment->forceFill(['ended_at' => now()])->save();

            AuditLog::record('assignment.ended', $dossier, [
                'reference' => $dossier->reference,
                'assigned' => $assignment->user?->name,
            ]);
        }

        return back()->with('registre_message', 'Affectation terminée.');
    }
}
