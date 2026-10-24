<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;
use App\Models\Category;
use App\Models\Video;
use App\Services\MediaStorage;
use App\Support\VideoLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct(private readonly MediaStorage $media)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->query('statut');

        $videos = Video::query()
            ->with('category')
            // The corbeille is a view of its own, not a filter on the list.
            ->when($status === 'corbeille', fn ($q) => $q->onlyTrashed())
            ->when($status && $status !== 'corbeille', fn ($q) => $q->where('status', $status))
            ->when($request->query('categorie'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('summary', 'like', "%{$term}%");
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.webtv', [
            'videos' => $videos,
            'categories' => Category::ofType('video')->withCount('videos')->get(),
            'counts' => $this->counts(),
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

    /** Moves the capsule to the corbeille; the cover stays until it is purged. */
    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()
            ->route('admin.webtv')
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

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title'], $existing?->id);
        $data['published_at'] = $this->publicationDate($data, $existing);
        $data['allow_comments'] = $request->boolean('allow_comments');

        // Validation already refused an address we cannot embed, so this
        // cannot be null here.
        $parsed = VideoLink::parse($data['video_url']);
        $data['provider'] = $parsed['provider'];
        $data['provider_id'] = $parsed['provider_id'];

        return $data;
    }

    /** @return array<string, int> */
    private function counts(): array
    {
        return [
            'published' => Video::public()->count(),
            'views' => (int) Video::public()->sum('views'),
            'drafts' => Video::drafts()->count(),
            'trashed' => Video::onlyTrashed()->count(),
        ];
    }

    /**
     * A published capsule needs a date: without one it would never appear,
     * since the public list orders on it.
     */
    private function publicationDate(array $data, ?Video $existing = null): ?string
    {
        if ($data['status'] !== Video::STATUS_PUBLISHED) {
            return $data['published_at'] ?? $existing?->published_at;
        }

        return $data['published_at']
            ?? $existing?->published_at
            ?? now()->toDateTimeString();
    }

    private function uniqueSlug(?string $slug, string $title, ?int $ignore = null): string
    {
        $base = Str::slug($slug ?: $title) ?: 'video';
        $candidate = $base;
        $suffix = 2;

        while (Video::withTrashed()
            ->where('slug', $candidate)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))
            ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
