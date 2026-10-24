<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Guide;
use App\Models\Report;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Site search across the published content.
 *
 * Deliberately a LIKE over a handful of columns rather than a search
 * engine: the plan's performance budget rules out a service of its own,
 * the corpus is a few hundred rows, and MySQL full-text would behave
 * differently from the SQLite the tests run on. If the corpus grows past
 * what this answers quickly, a full-text index on the same columns is the
 * next step and needs no change above this class.
 */
class SearchController extends Controller
{
    /** Content type => label shown on the filter and the result pill. */
    public const TYPES = [
        'actualites' => 'Actualité',
        'fiches' => 'Fiche pratique',
        'webtv' => 'Web TV',
        'activites' => 'Activité',
        'rapports' => 'Rapport',
    ];

    private const PER_PAGE = 10;

    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));
        $types = array_intersect((array) $request->query('type', []), array_keys(self::TYPES));

        /*
         * Searched once across every type, then counted and filtered from
         * that one set: the counts beside the filter have to cover types
         * the visitor has unticked, and running the search again per type
         * would be six passes for one answer.
         *
         * Nothing typed means the page explains itself rather than listing
         * everything the site holds.
         */
        $all = strlen($term) >= 2
            ? $this->search($term, array_keys(self::TYPES))
            : collect();

        $hits = $types
            ? $all->filter(fn (array $hit) => in_array($hit['key'], $types, true))->values()
            : $all;

        return view('search', [
            'query' => $term,
            // A single character would match almost everything, so it is
            // not searched — and the page says so rather than reporting
            // nought results as if the term had been looked for.
            'tooShort' => $term !== '' && strlen($term) < 2,
            'types' => self::TYPES,
            'activeTypes' => $types,
            'counts' => collect(self::TYPES)
                ->map(fn (string $label, string $key) => $all->where('key', $key)->count())
                ->all(),
            'results' => $this->paginate($hits, $request),
            'total' => $hits->count(),
        ]);
    }

    /**
     * Every match, newest first, as one list of plain arrays — the
     * five types have nothing in common as models, so the view is given
     * one shape rather than five.
     *
     * @param  array<int, string>  $types
     * @return Collection<int, array<string, mixed>>
     */
    private function search(string $term, array $types): Collection
    {
        $hits = collect();

        if (in_array('actualites', $types, true)) {
            $hits = $hits->concat(
                Article::public()
                    ->where(fn ($q) => $this->match($q, $term, ['title', 'excerpt', 'body']))
                    ->latest('published_at')
                    ->get()
                    ->map(fn (Article $a) => [
                        'key' => 'actualites',
                        'type' => self::TYPES['actualites'],
                        'title' => $a->title,
                        'excerpt' => $a->excerpt,
                        'imageUrl' => $a->imageUrl(),
                        'date' => $a->published_at,
                        'views' => $a->views,
                        'href' => route('news.show', $a),
                    ])
            );
        }

        if (in_array('fiches', $types, true)) {
            $hits = $hits->concat(
                Guide::public()
                    ->where(fn ($q) => $this->match($q, $term, ['title', 'description', 'body']))
                    ->latest('published_at')
                    ->get()
                    ->map(fn (Guide $g) => [
                        'key' => 'fiches',
                        'type' => self::TYPES['fiches'],
                        'title' => $g->title,
                        'excerpt' => $g->description,
                        'imageUrl' => $g->coverUrl(),
                        'date' => $g->published_at,
                        'views' => $g->downloads,
                        'href' => route('resources.guides.show', $g),
                    ])
            );
        }

        if (in_array('webtv', $types, true)) {
            $hits = $hits->concat(
                Video::public()
                    ->where(fn ($q) => $this->match($q, $term, ['title', 'summary', 'transcript']))
                    ->latest('published_at')
                    ->get()
                    ->map(fn (Video $v) => [
                        'key' => 'webtv',
                        'type' => self::TYPES['webtv'],
                        'title' => $v->title,
                        'excerpt' => $v->summary,
                        'imageUrl' => $v->coverUrl(),
                        'date' => $v->published_at,
                        'views' => $v->views,
                        'href' => route('webtv.show', $v),
                        'video' => true,
                    ])
            );
        }

        if (in_array('activites', $types, true)) {
            $hits = $hits->concat(
                Activity::public()
                    ->where(fn ($q) => $this->match($q, $term, ['title', 'excerpt', 'body', 'location']))
                    ->latest('held_on')
                    ->get()
                    ->map(fn (Activity $a) => [
                        'key' => 'activites',
                        'type' => self::TYPES['activites'],
                        'title' => $a->title,
                        'excerpt' => $a->excerpt,
                        'imageUrl' => $a->coverUrl(),
                        'date' => $a->held_on,
                        'href' => route('activities.index', ['categorie' => $a->category?->slug]),
                    ])
            );
        }

        if (in_array('rapports', $types, true)) {
            $hits = $hits->concat(
                Report::public()
                    ->where(fn ($q) => $this->match($q, $term, ['title', 'description']))
                    ->orderByDesc('year')
                    ->get()
                    ->map(fn (Report $r) => [
                        'key' => 'rapports',
                        'type' => self::TYPES['rapports'],
                        'title' => $r->title,
                        'excerpt' => $r->description,
                        'imageUrl' => $r->coverUrl(),
                        'date' => $r->published_at,
                        'views' => $r->downloads,
                        'href' => route('documents.report', $r),
                    ])
            );
        }

        // One ordering across the types, so a recent capsule can outrank an
        // older actualité rather than every actualité coming first.
        return $hits->sortByDesc(fn (array $hit) => $hit['date']?->timestamp ?? 0)->values();
    }

    /** @param  array<int, string>  $columns */
    private function match($query, string $term, array $columns): void
    {
        foreach ($columns as $i => $column) {
            $i === 0
                ? $query->where($column, 'like', "%{$term}%")
                : $query->orWhere($column, 'like', "%{$term}%");
        }
    }

    /**
     * The merged list, paginated by hand — it is a collection rather than
     * a query, so the paginator has to be told where it is.
     *
     * @param  Collection<int, array<string, mixed>>  $hits
     */
    private function paginate(Collection $hits, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $hits->forPage($page, self::PER_PAGE)->values(),
            $hits->count(),
            self::PER_PAGE,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
