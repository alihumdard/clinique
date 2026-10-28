<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\Dossier;
use App\Models\Registre\DossierDocument;
use App\Models\Registre\DossierStatus;
use App\Models\Registre\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The dossiers: the list, one dossier, and the spreadsheet export.
 *
 * Who may see which dossier is the "matrice des autorisations" the
 * Clinic has yet to approve — a student clinician is to see "uniquement
 * les dossiers qui lui sont affectés". Until that matrix exists, every
 * signed-in account sees the list, and every consultation, download and
 * export is written to the access log, so the record of who saw what is
 * already being kept.
 */
class DossierController extends Controller
{
    public function index(Request $request): View
    {
        $dossiers = $this->filtered($request)
            ->with(['applicant', 'status'])
            ->withCount(['documents', 'assignments'])
            ->latest('opened_at')
            ->paginate(20)
            ->withQueryString();

        return view('registre.dossiers.index', [
            'dossiers' => $dossiers,
            'statuses' => DossierStatus::orderBy('position')->get(),
            'filters' => $request->only(['q', 'statut', 'urgence', 'retard']),
            'counts' => [
                'open' => Dossier::open()->count(),
                'overdue' => Dossier::overdue()->count(),
                'total' => Dossier::count(),
            ],
        ]);
    }

    /** 44. Créer un dossier, pour une personne reçue sur place. */
    public function create(): View
    {
        return view('registre.dossiers.create', [
            'domains' => \App\Models\LegalDomain::active()->get(),
        ]);
    }

    public function store(\App\Http\Requests\Registre\DossierRequest $request, \App\Services\Registre\DossierOpener $opener): \Illuminate\Http\RedirectResponse
    {
        try {
            $dossier = $opener->open($request->validated(), Auth::guard('registre')->user());
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['ouverture' => $e->getMessage()]);
        }

        return redirect()
            ->route('registre.dossiers.show', $dossier)
            ->with('registre_message', "Dossier {$dossier->reference} ouvert.");
    }

    public function show(Dossier $dossier): View
    {
        // Opening a case file is itself a sensitive action.
        AuditLog::record('dossier.viewed', $dossier, ['reference' => $dossier->reference]);

        return view('registre.dossiers.show', [
            'dossier' => $dossier->load([
                'applicant',
                'status',
                'documents.uploader',
                'deadlines',
                'assignments.user',
                'statusHistory.toStatus',
                'statusHistory.user',
            ]),
            'agents' => User::active()->orderBy('name')->get(),
            // Only the moves the Clinic allows from where the dossier
            // stands; the screen offers nothing the workflow refuses.
            'nextStatuses' => $dossier->status
                ? $dossier->status->allowedNext()->orderBy('position')->get()
                : collect(),
            'log' => AuditLog::with('user')
                ->where('subject_type', 'Dossier')
                ->where('subject_id', $dossier->id)
                ->latest('created_at')
                ->take(20)
                ->get(),
        ]);
    }

    /**
     * "Fournir la recherche, les filtres et l'export vers un tableur."
     *
     * Streamed rather than built in memory: the export is the whole
     * filtered list, and a year of dossiers should not have to fit in
     * one string before the first byte is sent.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)->with(['applicant', 'status']);

        AuditLog::record('dossier.exported', null, [
            'filters' => $request->only(['q', 'statut', 'urgence', 'retard']),
            'count' => (clone $query)->count(),
        ]);

        $filename = 'dossiers-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM: without it Excel reads the accents as mojibake.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Référence', 'Ouvert le', 'Demandeur', 'Téléphone',
                'Domaine', 'Objet', 'Statut', 'Urgence', 'Clôturé le',
            ], ';');

            $query->chunk(200, function ($dossiers) use ($handle) {
                foreach ($dossiers as $dossier) {
                    fputcsv($handle, [
                        $dossier->reference,
                        $dossier->opened_at?->format('d/m/Y'),
                        $dossier->applicant?->fullName(),
                        $dossier->applicant?->phone,
                        $dossier->legal_domain,
                        $dossier->subject,
                        $dossier->status?->label,
                        $dossier->urgency,
                        $dossier->closed_at?->format('d/m/Y'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * A document of a dossier. The file sits outside the web root and is
     * never linked to: the reader is checked and the download logged
     * before anything is sent.
     */
    public function document(Dossier $dossier, DossierDocument $document): StreamedResponse
    {
        abort_unless($document->dossier_id === $dossier->id, 404);
        abort_unless(Storage::disk('local')->exists($document->path), 404);

        AuditLog::record('document.downloaded', $dossier, [
            'reference' => $dossier->reference,
            'file' => $document->original_name,
        ]);

        return Storage::disk('local')->download($document->path, $document->original_name);
    }

    /** Shared by the list and the export, so both filter identically. */
    private function filtered(Request $request)
    {
        return Dossier::query()
            ->when($request->query('q'), function ($query, $term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('reference', 'like', "%{$term}%")
                        ->orWhere('subject', 'like', "%{$term}%")
                        ->orWhere('legal_domain', 'like', "%{$term}%")
                        ->orWhereHas('applicant', function ($applicant) use ($term) {
                            $applicant->where('last_name', 'like', "%{$term}%")
                                ->orWhere('first_name', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%");
                        });
                });
            })
            ->when($request->query('statut'), fn ($query, $id) => $query->where('status_id', $id))
            ->when($request->query('urgence'), fn ($query, $urgency) => $query->where('urgency', $urgency))
            ->when($request->boolean('retard'), fn ($query) => $query->overdue());
    }
}
