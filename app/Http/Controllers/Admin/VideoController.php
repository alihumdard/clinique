<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesContent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;
use App\Models\Category;
use App\Models\Video;
use App\Services\MediaStorage;
use App\Support\VideoLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    use ManagesContent;

    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $videos = $this->applyFilters(
            Video::query()->with('category'),
            $request,
            ['title', 'summary']
        )
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.webtv', [
            'videos' => $videos,
            'categories' => Category::ofType('video')->withCount('videos')->get(),
            'counts' => $this->publicationCounts(Video::class) + [
                'views' => (int) Video::public()->sum('views'),
            ],
            'filters' => $request->only(['statut', 'categorie', 'q']),
        ]);
    }

    public function store(VideoRequest $request): RedirectResponse
    {
        $data = $this->prepare($request);

        if ($request->hasFile('cover')) {
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'web-tv');
        }

        $video = Video::create($data);

        return redirect()
            ->route('admin.webtv')
            ->with('status', "La vidéo « {$video->title} » a été enregistrée.");
    }

    public function update(VideoRequest $request, Video $video): RedirectResponse
    {
        $data = $this->prepare($request, $video);

        if ($request->hasFile('cover')) {
            // Replace, then drop the old file — not the other way round, so
            // a failed upload cannot leave the row pointing at nothing.
            $previous = $video->cover;
            $data['cover'] = $this->media->storeImage($request->file('cover'), 'web-tv');
            $this->media->deleteImage($previous);
        }

        $video->update($data);

        return redirect()
            ->route('admin.webtv')
            ->with('status', "La vidéo « {$video->title} » a été mise à jour.");
    }

    /** A copy as a draft, ready to adapt rather than written from scratch. */
    public function duplicate(Video $video): RedirectResponse
    {
        $copy = $this->duplicateAsDraft($video, Video::class, 'title', 'video');

        return redirect()
            ->route('admin.webtv')
            ->with('status', "« {$video->title} » a été dupliquée en brouillon.");
    }

    /** Moves the capsule to the corbeille; the cover stays until it is purged. */
    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()
            ->back(fallback: route('admin.webtv'))
            ->with('status', "La vidéo « {$video->title} » a été mise à la corbeille.");
    }

    public function restore(int $id): RedirectResponse
    {
        $video = Video::onlyTrashed()->findOrFail($id);
        $video->restore();

        return redirect()
            ->route('admin.webtv')
            ->with('status', "La vidéo « {$video->title} » a été restaurée.");
    }

    /** Permanent: the row and its cover go for good. */
    public function forceDestroy(int $id): RedirectResponse
    {
        $video = Video::onlyTrashed()->findOrFail($id);
        $title = $video->title;

        $this->media->deleteImage($video->cover);
        $video->forceDelete();

        return redirect()
            ->route('admin.webtv')
            ->with('status', "La vidéo « {$title} » a été supprimée définitivement.");
    }

    /**
     * Everything the two writes share: the slug, the publication date and
     * the provider pair parsed out of the pasted address.
     */
    private function prepare(VideoRequest $request, ?Video $existing = null): array
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug(Video::class, $data['slug'] ?? null, $data['title'], $existing?->id, 'video');
        $data['published_at'] = $this->publicationDate($data, $existing);
        $data['allow_comments'] = $request->boolean('allow_comments');

        // Validation already refused an address we cannot embed, so this
        // cannot be null here.
        $parsed = VideoLink::parse($data['video_url']);
        $data['provider'] = $parsed['provider'];
        $data['provider_id'] = $parsed['provider_id'];

        return $data;
    }
}
