<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The analysis of a dossier, and the supervisor's validation of it.
 *
 * The plan makes the validation a gate: "empêcher le passage au statut
 * « analyse retournée au demandeur » tant que la validation du
 * superviseur n'est pas terminée". The gate itself lives on the dossier
 * (returnableToApplicant); what this controller does is move the
 * analysis through the two steps it has to pass first.
 *
 * Which accounts may write an analysis and which may validate one is the
 * matrice des autorisations the Clinic still has to approve. One rule is
 * enforced already, because it is in the plan rather than in the matrix:
 * the person who submitted an analysis does not validate their own.
 */
class AnalysisController extends Controller
{
    public function save(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'analysis' => ['required', 'string', 'max:20000'],
        ], [], ['analysis' => 'analyse']);

        if ($dossier->isValidated()) {
            return back()->withErrors([
                'analysis' => "Cette analyse a déjà été validée et ne peut plus être modifiée.",
            ]);
        }

        $dossier->forceFill([
            'analysis' => $data['analysis'],
            'analysis_submitted_at' => now(),
        ])->save();

        AuditLog::record('analysis.submitted', $dossier, ['reference' => $dossier->reference]);

        return back()->with('registre_message', "Analyse enregistrée et soumise à validation.");
    }

    public function validateAnalysis(Dossier $dossier): RedirectResponse
    {
        $agent = Auth::guard('registre')->user();

        if ($dossier->analysis_submitted_at === null) {
            return back()->withErrors(['analysis' => "Aucune analyse n'a encore été soumise."]);
        }

        if ($dossier->isValidated()) {
            return back()->withErrors(['analysis' => 'Cette analyse est déjà validée.']);
        }

        $dossier->forceFill([
            'analysis_validated_by' => $agent->id,
            'analysis_validated_at' => now(),
        ])->save();

        AuditLog::record('analysis.validated', $dossier, [
            'reference' => $dossier->reference,
            'validated_by' => $agent->name,
        ]);

        return back()->with('registre_message', 'Analyse validée.');
    }
}
