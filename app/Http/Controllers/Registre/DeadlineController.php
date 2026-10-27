<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierDeadline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Dates a dossier has to meet: "suivre l'urgence, les échéances et les
 * retards".
 */
class DeadlineController extends Controller
{
    public function store(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'due_at' => ['required', 'date'],
        ], [], [
            'label' => 'intitulé',
            'due_at' => 'date',
        ]);

        $dossier->deadlines()->create($data + ['created_by' => Auth::guard('registre')->id()]);

        AuditLog::record('deadline.created', $dossier, [
            'reference' => $dossier->reference,
            'label' => $data['label'],
        ]);

        return back()->with('registre_message', 'Échéance ajoutée.');
    }

    public function complete(Dossier $dossier, DossierDeadline $deadline): RedirectResponse
    {
        abort_unless($deadline->dossier_id === $dossier->id, 404);

        if ($deadline->completed_at === null) {
            $deadline->forceFill(['completed_at' => now()])->save();

            AuditLog::record('deadline.completed', $dossier, [
                'reference' => $dossier->reference,
                'label' => $deadline->label,
            ]);
        }

        return back()->with('registre_message', 'Échéance marquée comme faite.');
    }
}
