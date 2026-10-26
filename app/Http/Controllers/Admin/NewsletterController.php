<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The newsletter list.
 *
 * The plan's Phase 2 requirement is to collect addresses and hand them to
 * the Clinic as a file; sending the campaigns is not part of it, and the
 * schema holds subscribers only — no campaigns, no delivery statistics.
 * So this screen manages the list and exports it, rather than pretending
 * to send from here.
 */
class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $subscribers = NewsletterSubscriber::query()
            ->when($status === 'actifs', fn ($q) => $q->active())
            ->when($status === 'desinscrits', fn ($q) => $q->whereNotNull('unsubscribed_at'))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('email', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%");
                });
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.newsletter', [
            'subscribers' => $subscribers,
            'counts' => [
                'active' => NewsletterSubscriber::active()->count(),
                'unsubscribed' => NewsletterSubscriber::whereNotNull('unsubscribed_at')->count(),
                'month' => NewsletterSubscriber::active()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
                'all' => NewsletterSubscriber::count(),
            ],
            'filters' => $request->only(['statut', 'q']),
        ]);
    }

    /**
     * The list as a CSV the Clinic can load into whatever they send with.
     *
     * Streamed rather than built in memory, so a list that grows does not
     * become a memory limit, and only active subscribers are exported —
     * exporting someone who unsubscribed is how they get mailed again.
     */
    public function export(): StreamedResponse
    {
        $filename = 'abonnes-newsletter-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            // BOM, or Excel reads the accents as mojibake.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Email', 'Nom', "Date d'inscription"], ';');

            NewsletterSubscriber::active()
                ->orderBy('created_at')
                ->chunk(500, function ($chunk) use ($out) {
                    foreach ($chunk as $subscriber) {
                        fputcsv($out, [
                            $subscriber->email,
                            $subscriber->name,
                            $subscriber->created_at->format('d/m/Y'),
                        ], ';');
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Records that someone left, rather than deleting the row: the
     * retention rule is "jusqu'à désinscription", and a deleted row is
     * exactly what lets a later import re-subscribe them.
     */
    public function unsubscribe(NewsletterSubscriber $subscriber): RedirectResponse
    {
        if ($subscriber->isSubscribed()) {
            $subscriber->unsubscribe();
        }

        return back()->with('status', "{$subscriber->email} a été désinscrit de la newsletter.");
    }

    /** Permanent, for a request to be erased entirely. */
    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return back()->with('status', "{$email} a été supprimé définitivement de la liste.");
    }
}
