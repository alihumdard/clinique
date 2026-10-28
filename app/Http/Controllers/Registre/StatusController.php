<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierStatus;
use App\Services\Registre\DossierWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * Changing a dossier's status.
 *
 * Who may make which move is part of the matrice des autorisations the
 * Clinic has yet to approve. What is already enforced is the workflow
 * itself: only a declared transition, never past the supervisor's
 * validation, and always recorded.
 */
class StatusController extends Controller
{
    public function __construct(private DossierWorkflow $workflow) {}

    public function update(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'status_id' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'status_id' => 'statut',
            'note' => 'note',
        ]);

        $target = DossierStatus::find($data['status_id']);

        if (! $target) {
            return back()->withErrors(['status_id' => "Ce statut n'existe pas."]);
        }

        try {
            $this->workflow->moveTo($dossier, $target, Auth::guard('registre')->user(), $data['note'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status_id' => $e->getMessage()]);
        }

        return back()->with('registre_message', "Le dossier est passé au statut « {$target->label} ».");
    }
}
