<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebTvController extends Controller
{
    public function index(Request $request): View
    {
        $slug = $request->query('categorie');

        $category = $slug
            ? Category::where('type', 'video')->where('slug', $slug)->first()
            : null;

        $query = Video::query()
            ->public()
            ->with('category')
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->latest('published_at');

        // The newest capsule leads the page; the grid carries the rest, so
        // the same video is never shown twice. Unfiltered only: inside a
        // rubrique the grid is the whole answer.
        $featured = $category ? null : (clone $query)->first();

        $videos = $query
            ->when($featured, fn ($q) => $q->whereKeyNot($featured->id))
            ->paginate(9)
            ->withQueryString();

        $categories = $this->rubriques();

        return view('webtv.index', [
            'featured' => $featured,
            'videos' => $videos,
            'categories' => $categories,
            'current' => $category,
            'totals' => [
                'videos' => Video::public()->count(),
                'views' => (int) Video::public()->sum('views'),
                'rubriques' => $categories->where('videos_count', '>', 0)->count(),
            ],
        ]);
    }

    /**
     * The rubriques, each with how many public capsules it holds and the
     * cover of its newest one. The cover comes from a select subquery, not
     * a relation, so the whole panel is still one query.
     */
    private function rubriques(): \Illuminate\Support\Collection
    {
        return Category::ofType('video')
            ->withCount(['videos' => fn ($q) => $q->public()])
            ->addSelect(['cover' => Video::select('cover')
                ->whereColumn('category_id', 'categories.id')
                ->public()
                ->latest('published_at')
                ->limit(1),
            ])
            ->get();
    }

    public function show(Video $video): View
    {
        // A draft or a scheduled capsule is not public yet, whoever has
        // the link. Admins preview from the admin side.
        abort_unless($video->isPublic(), 404);

        $video->increment('views');

        return view('webtv.show', [
            'video' => $video->load('category'),
            'more' => $this->related($video),
            // The rubrique read as a playlist, current capsule included so
            // the panel shows where the visitor is.
            'playlist' => $video->category_id
                ? Video::public()
                    ->where('category_id', $video->category_id)
                    ->latest('published_at')
                    ->take(6)
                    ->get()
                : collect(),
        ]);
    }

    /**
     * Capsules from the same rubrique, topped up with the newest others so
     * the panel is never half-empty just because a rubrique is small.
     */
    private function related(Video $video, int $limit = 3): \Illuminate\Support\Collection
    {
        $base = fn () => Video::public()->with('category')->whereKeyNot($video->id);

        $related = $video->category_id
            ? $base()->where('category_id', $video->category_id)->latest('published_at')->take($limit)->get()
            : collect();

        if ($related->count() >= $limit) {
            return $related;
        }

        return $related->concat(
            $base()
                ->whereNotIn('id', $related->pluck('id'))
                ->latest('published_at')
                ->take($limit - $related->count())
                ->get()
        );
    }
}
