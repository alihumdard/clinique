<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Models\Report;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $reports = $this->applyFilters(
            Report::query(),
            $request,
            ['title', 'description'],
            hasCategory: false
        )
            // A report is identified by its year before its creation date.
            ->orderByDesc('year')
            ->latest('created_at')
            ->when($request->query('annee'), fn ($q, $year) => $q->where('year', $year))
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports', [
            'reports' => $reports,
            'years' => Report::query()->distinct()->orderByDesc('year')->pluck('year'),
            'counts' => $this->publicationCounts(Report::class) + [
                'downloads' => (int) Report::public()->sum('downloads'),
            ],
            'filters' => $request->only(['statut', 'annee', 'q']),
        ]);
    }

    public function store(ReportRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Report::class, $data['slug'] ?? null, $data['title'], null, 'rapport');
        $data['published_at'] = $this->publicationDate($data);

        // Validation already required the file on a create. The uploaded
        // file itself is not a column, so it leaves the payload here.
        $data = array_merge($data, $this->storeDocument($request));
        unset($data['document']);

        if ($request->hasFile('cover')) {
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'rapports-annuels');
        }

        $report = Report::create($data);

        return redirect()
            ->route('admin.reports')
            ->with('status', "Le rapport « {$report->title} » a été enregistré.");
    }

    public function update(ReportRequest $request, Report $report): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Report::class, $data['slug'] ?? null, $data['title'], $report->id, 'rapport');
        $data['published_at'] = $this->publicationDate($data, $report);

        if ($request->hasFile('document')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $report->file_path;
            $data = array_merge($data, $this->storeDocument($request));
            $this->media->deleteDocument($previous);
        }

        unset($data['document']);

        if ($request->hasFile('cover')) {
            $previous = $report->cover;
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'rapports-annuels');
            $this->media->deleteImage($previous);
        }

        $report->update($data);

        return redirect()
            ->route('admin.reports')
            ->with('status', "Le rapport « {$report->title} » a été mis à jour.");
    }

    /**
     * The admin's own download, straight from the list — unlike the public
     * route, this one does not require the report to be published, and it
     * does not count toward the public download figure.
     */
    public function download(Report $report): StreamedResponse
    {
        abort_if(blank($report->file_path), 404);

        $disk = Storage::disk('local');

        abort_unless(str_starts_with($report->file_path, 'documents/'), 404);
        abort_if(str_contains($report->file_path, '..'), 404);
        abort_unless($disk->exists($report->file_path), 404);

        return $disk->download($report->file_path, $report->file_name ?: basename($report->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Security-Policy' => "default-src 'none'; object-src 'none'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** A copy as a draft, ready to adapt rather than written from scratch. */
    public function duplicate(Report $report): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($report, Report::class, 'title', 'rapport');

        return redirect()
            ->route('admin.reports')
            ->with('status', "« {$report->title} » a été dupliqué en brouillon.");
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return redirect()
            ->back(fallback: route('admin.reports'))
            ->with('status', "Le rapport « {$report->title} » a été mis à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $report = Report::onlyTrashed()->findOrFail($id);
        $report->restore();

        return redirect()
            ->route('admin.reports')
            ->with('status', "Le rapport « {$report->title} » a été restauré.");
    }

    /** Permanent: the row, its cover and its PDF all go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $report = Report::onlyTrashed()->findOrFail($id);
        $title = $report->title;

        $this->media->deleteImage($report->cover);
        $this->media->deleteDocument($report->file_path);
        $report->forceDelete();

        return redirect()
            ->route('admin.reports')
            ->with('status', "Le rapport « {$title} » a été supprimé définitivement.");
    }

    /** @return array<string, mixed> */
    private function storeDocument(ReportRequest $request): array
    {
        if (! $request->hasFile('document')) {
            return [];
        }

        $stored = $this->media->storeDocument($request->file('document'), 'rapports-annuels');

        return [
            'file_path' => $stored['path'],
            'file_name' => $stored['name'],
            'file_size' => $stored['size'],
        ];
    }
}
