<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\PresaisineAttachment;
use App\Models\Registre\PresaisineSubmission;
use App\Services\Registre\PresaisineTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The online requests waiting to be looked at, and what becomes of them.
 *
 * Who may do this is part of the "matrice des autorisations" the Clinic
 * has yet to approve. Until it exists, any signed-in Registre account
 * can — and every action is written to the access log, so the record of
 * who did what is already being kept.
 */
class SubmissionController extends Controller
{
    public function __construct(private PresaisineTransfer $transfer) {}

    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $submissions = PresaisineSubmission::query()
            ->withCount('attachments')
            ->when(
                array_key_exists($status, PresaisineSubmission::statuses()),
                fn ($query) => $query->where('status', $status)
            )
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('registre.submissions.index', [
            'submissions' => $submissions,
            'counts' => PresaisineSubmission::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'filter' => $status,
        ]);
    }

    public function show(PresaisineSubmission $submission): View
    {
        // Reading someone's request is itself a sensitive action: "tenir
        // des journaux d'accès et de modification".
        AuditLog::record('presaisine.viewed', $submission, ['reference' => $submission->reference]);

        return view('registre.submissions.show', [
            'submission' => $submission->load('attachments', 'reviewer', 'dossier'),
        ]);
    }

    public function accept(PresaisineSubmission $submission): RedirectResponse
    {
        try {
            $dossier = $this->transfer->accept($submission, Auth::guard('registre')->user());
        } catch (RuntimeException $e) {
            return back()->withErrors(['transfert' => $e->getMessage()]);
        }

        return redirect()
            ->route('registre.submissions.show', $submission)
            ->with('registre_message', "Dossier {$dossier->reference} ouvert à partir de cette demande.");
    }

    public function refuse(Request $request, PresaisineSubmission $submission): RedirectResponse
    {
        $note = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ])['note'] ?? null;

        try {
            $this->transfer->refuse($submission, Auth::guard('registre')->user(), $note);
        } catch (RuntimeException $e) {
            return back()->withErrors(['transfert' => $e->getMessage()]);
        }

        return redirect()
            ->route('registre.submissions.show', $submission)
            ->with('registre_message', 'Demande refusée. Les pièces jointes ont été supprimées.');
    }

    /**
     * An intake attachment. It is never linked to directly: the file sits
     * outside the web root, the reader is checked, and the download is
     * logged before anything is sent.
     */
    public function attachment(PresaisineSubmission $submission, PresaisineAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->submission_id === $submission->id, 404);
        abort_if($attachment->isPurged(), 410, 'Cette pièce jointe a été supprimée du serveur de réception.');
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        AuditLog::record('presaisine.attachment.downloaded', $submission, [
            'reference' => $submission->reference,
            'file' => $attachment->original_name,
        ]);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
}
