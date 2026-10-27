<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Documents added to a dossier by the Clinic: the signed mandate, the
 * analysis, anything else the file needs.
 *
 * They go to the private disk outside the web root, like everything else
 * here, and a document is never deleted from a case file — a dossier
 * whose pieces can disappear is not a record of anything. Module 4's
 * retention rules are what remove them, by their own schedule.
 */
class DocumentController extends Controller
{
    public function store(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in([
                DossierDocument::KIND_SIGNED_MANDATE,
                DossierDocument::KIND_ANALYSIS,
                DossierDocument::KIND_ATTACHMENT,
                DossierDocument::KIND_OTHER,
            ])],
            // Same formats and size as the pré-saisine accepts, so a
            // member of the Clinic is not more constrained than a visitor.
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [], [
            'kind' => 'type de document',
            'file' => 'fichier',
        ]);

        $file = $request->file('file');

        $path = $file->store(
            sprintf('dossiers/%d/%s', $dossier->opened_at->year, $dossier->reference),
            'local'
        );

        $document = $dossier->documents()->create([
            'kind' => $data['kind'],
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'from_presaisine' => false,
            'uploaded_by' => Auth::guard('registre')->id(),
        ]);

        AuditLog::record('document.added', $dossier, [
            'reference' => $dossier->reference,
            'file' => $document->original_name,
            'kind' => $document->kind,
        ]);

        return back()->with('registre_message', 'Document ajouté au dossier.');
    }
}
