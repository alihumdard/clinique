<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuideRequest;
use App\Models\Category;
use App\Models\Guide;
use App\Services\MediaStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GuideController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $guides = $this->applyFilters(
            Guide::query()->with('category'),
            $request,
            ['title', 'description']
        )
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.guides', [
            'guides' => $guides,
            'categories' => Category::ofType('guide')->withCount('guides')->get(),
            'counts' => $this->publicationCounts(Guide::class) + [
                'downloads' => (int) Guide::public()->sum('downloads'),
            ],
            'filters' => $request->only(['statut', 'categorie', 'q']),
        ]);
    }

    public function store(GuideRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Guide::class, $data['slug'] ?? null, $data['title'], null, 'fiche');
        $data['published_at'] = $this->publicationDate($data);

        // Validation already required the file on a create. The uploaded
        // file itself is not a column, so it leaves the payload here.
        $data = array_merge($data, $this->storeDocument($request));
        unset($data['document']);

        if ($request->hasFile('cover')) {
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'fiches-pratiques');
        }

        $guide = Guide::create($data);

        return redirect()
            ->route('admin.guides')
            ->with('status', "La fiche pratique « {$guide->title} » a été enregistrée.");
    }

    public function update(GuideRequest $request, Guide $guide): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Guide::class, $data['slug'] ?? null, $data['title'], $guide->id, 'fiche');
        $data['published_at'] = $this->publicationDate($data, $guide);

        if ($request->hasFile('document')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $guide->file_path;
            $data = array_merge($data, $this->storeDocument($request));
            $this->media->deleteDocument($previous);
        }

        // Nothing uploaded means keep the file the row already has; either
        // way the uploaded file itself is not a column.
        unset($data['document']);

        if ($request->hasFile('cover')) {
            $previous = $guide->cover;
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'fiches-pratiques');
            $this->media->deleteImage($previous);
        }

        $guide->update($data);

        return redirect()
            ->route('admin.guides')
            ->with('status', "La fiche pratique « {$guide->title} » a été mise à jour.");
    }

    /**
     * The admin's own download, straight from the list — unlike the public
     * route, this one does not require the fiche to be published, so a
     * draft's PDF can still be checked before it goes live, and it does
     * not count toward the public download figure.
     */
    public function download(Guide $guide): StreamedResponse
    {
        abort_if(blank($guide->file_path), 404);

        $disk = Storage::disk('local');

        abort_unless(str_starts_with($guide->file_path, 'documents/'), 404);
        abort_if(str_contains($guide->file_path, '..'), 404);
        abort_unless($disk->exists($guide->file_path), 404);

        return $disk->download($guide->file_path, $guide->file_name ?: basename($guide->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Security-Policy' => "default-src 'none'; object-src 'none'",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** A copy as a draft, ready to adapt rather than written from scratch. */
    public function duplicate(Guide $guide): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($guide, Guide::class, 'title', 'fiche');

        return redirect()
            ->route('admin.guides')
            ->with('status', "« {$guide->title} » a été dupliquée en brouillon.");
    }

    /** Moves the fiche to the corbeille; both files stay until it is purged. */
    public function destroy(Guide $guide): RedirectResponse
    {
        $guide->delete();

        return redirect()
            ->route('admin.guides')
            ->with('status', "La fiche pratique « {$guide->title} » a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $guide = Guide::onlyTrashed()->findOrFail($id);
        $guide->restore();

        return redirect()
            ->route('admin.guides')
            ->with('status', "La fiche pratique « {$guide->title} » a été restaurée.");
    }

    /** Permanent: the row, its cover and its PDF all go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $guide = Guide::onlyTrashed()->findOrFail($id);
        $title = $guide->title;

        $this->media->deleteImage($guide->cover);
        $this->media->deleteDocument($guide->file_path);
        $guide->forceDelete();

        return redirect()
            ->route('admin.guides')
            ->with('status', "La fiche pratique « {$title} » a été supprimée définitivement.");
    }

    /**
     * The PDF goes to the private disk, so what the row keeps is where it
     * landed plus the name and size the pages display.
     *
     * @return array<string, mixed>
     */
    private function storeDocument(GuideRequest $request): array
    {
        if (! $request->hasFile('document')) {
            return [];
        }

        $stored = $this->media->storeDocument($request->file('document'), 'fiches-pratiques');

        return [
            'file_path' => $stored['path'],
            'file_name' => $stored['name'],
            'file_size' => $stored['size'],
        ];
    }
}
