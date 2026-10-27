<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityPhoto;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $slug = $request->query('categorie');

        $category = $slug
            ? Category::where('type', 'activity')->where('slug', $slug)->first()
            : null;

        $activities = Activity::query()
            ->public()
            ->with('category')
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->orderByDesc('held_on')
            ->paginate(9)
            ->withQueryString();

        return view('activities', [
            'activities' => $activities,
            'categories' => Category::ofType('activity')
                ->withCount(['activities' => fn ($q) => $q->public()])
                ->get(),
            'current' => $category,
            // The strip above the gallery link: the newest photos, whichever
            // activity they belong to.
            'strip' => $this->latestPhotos(5),
        ]);
    }

    public function show(Activity $activity): View
    {
        abort_unless($activity->isPublic(), 404);

        return view('activities.show', [
            'activity' => $activity->load(['category', 'photos']),
            'recent' => Activity::public()
                ->whereKeyNot($activity->id)
                ->orderByDesc('held_on')
                ->take(4)
                ->get(),
            'related' => Activity::public()
                ->where('id', '!=', $activity->id)
                ->when($activity->category_id, fn ($q) => $q->where('category_id', $activity->category_id))
                ->orderByDesc('held_on')
                ->take(3)
                ->get(),
        ]);
    }

    /**
     * The gallery, grouped into albums by the activity's own rubrique —
     * which is what the design calls an album.
     */
    public function gallery(Request $request): View
    {
        $slug = $request->query('album');

        $category = $slug
            ? Category::where('type', 'activity')->where('slug', $slug)->first()
            : null;

        // One query for every album's photo count, rather than one per album.
        $counts = $this->photoCounts();

        $albums = Category::ofType('activity')
            ->when($category, fn ($q) => $q->whereKey($category->id))
            ->with(['activities' => fn ($q) => $q->public()->orderByDesc('held_on')->with('photos')])
            ->get()
            ->map(fn (Category $album) => [
                'category' => $album,
                // Photos of every public activity in the rubrique, newest
                // activity first, trimmed to what the design shows.
                'photos' => $album->activities
                    ->flatMap(fn (Activity $activity) => $activity->photos)
                    ->take(6),
                'total' => $counts[$album->id] ?? 0,
            ])
            ->filter(fn (array $album) => $album['photos']->isNotEmpty())
            ->values();

        return view('gallery', [
            'albums' => $albums,
            'index' => $this->albumIndex($counts),
            'current' => $category,
            'totals' => [
                'photos' => $counts->sum(),
                'activities' => Activity::public()->count(),
                'albums' => $counts->filter()->count(),
            ],
        ]);
    }

    /**
     * The album list beside the gallery: every rubrique that holds at least
     * one photo, with its count and a thumbnail. The thumbnail comes from a
     * select subquery, so the whole panel is still one query.
     *
     * @param  \Illuminate\Support\Collection<int, int>  $counts
     * @return \Illuminate\Support\Collection<int, array{category: Category, total: int, cover: string}>
     */
    private function albumIndex(\Illuminate\Support\Collection $counts): \Illuminate\Support\Collection
    {
        return Category::ofType('activity')
            ->addSelect(['cover' => ActivityPhoto::select('activity_photos.path')
                ->join('activities', 'activities.id', '=', 'activity_photos.activity_id')
                ->whereColumn('activities.category_id', 'categories.id')
                ->whereNull('activities.deleted_at')
                ->where('activities.status', Activity::STATUS_PUBLISHED)
                ->orderByDesc('activity_photos.id')
                ->limit(1),
            ])
            ->get()
            ->map(fn (Category $c) => [
                'category' => $c,
                'total' => $counts[$c->id] ?? 0,
                'cover' => $c->coverUrl('placeholders/news-caravan'),
            ])
            ->filter(fn (array $row) => $row['total'] > 0)
            ->values();
    }

    /** @return \Illuminate\Support\Collection<int, ActivityPhoto> */
    private function latestPhotos(int $limit): \Illuminate\Support\Collection
    {
        return ActivityPhoto::query()
            ->whereHas('activity', fn ($q) => $q->public())
            ->latest('id')
            ->take($limit)
            ->get();
    }

    /**
     * Photos of public activities, counted per rubrique.
     *
     * @return \Illuminate\Support\Collection<int, int> keyed by category id
     */
    private function photoCounts(): \Illuminate\Support\Collection
    {
        return ActivityPhoto::query()
            ->join('activities', 'activities.id', '=', 'activity_photos.activity_id')
            ->whereNull('activities.deleted_at')
            ->where('activities.status', Activity::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('activities.published_at')
                    ->orWhere('activities.published_at', '<=', now());
            })
            ->whereNotNull('activities.category_id')
            ->groupBy('activities.category_id')
            ->selectRaw('activities.category_id as category_id, COUNT(*) as total')
            ->pluck('total', 'category_id');
    }
}
