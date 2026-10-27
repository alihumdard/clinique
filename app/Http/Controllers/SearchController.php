<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Category;
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

    /** Session key for the visitor's own recent search terms (no user accounts on the public site). */
    private const RECENT_SESSION_KEY = 'search.recent';

    private const RECENT_MAX = 5;

    /** Clears the visitor's own recent-search list and returns to the search page. */
    public function clearRecent(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->session()->forget(self::RECENT_SESSION_KEY);

        return redirect()->route('search');
    }

    /** Drops one term from the visitor's own recent-search list. */
    public function forgetRecent(Request $request): \Illuminate\Http\RedirectResponse
    {
        $term = (string) $request->input('term', '');

        $recent = collect($request->session()->get(self::RECENT_SESSION_KEY, []))
            ->reject(fn (string $t) => mb_strtolower($t) === mb_strtolower($term))
            ->values()
            ->all();

        $request->session()->put(self::RECENT_SESSION_KEY, $recent);

        return redirect()->route('search');
    }

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

        if (strlen($term) >= 2) {
            $this->rememberSearch($request, $term);
        }

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
            'popularCategories' => $this->popularCategories(),
            'recentSearches' => $request->session()->get(self::RECENT_SESSION_KEY, []),
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
                        'href' => route('activities.show', $a),
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

    /**
     * Pushes a search term to the front of the visitor's own recent-search
     * list, held in their session — no user accounts on the public site,
     * so this is the only "history" there can be, and it never leaves
     * their browser's session.
     */
    private function rememberSearch(Request $request, string $term): void
    {
        $recent = $request->session()->get(self::RECENT_SESSION_KEY, []);

        $recent = collect($recent)
            ->reject(fn (string $t) => mb_strtolower($t) === mb_strtolower($term))
            ->prepend($term)
            ->take(self::RECENT_MAX)
            ->values()
            ->all();

        $request->session()->put(self::RECENT_SESSION_KEY, $recent);
    }

    /**
     * The 5 actualités rubriques with the most published articles, for the
     * "Catégories populaires" sidebar — a real count rather than the fixed
     * list the page shipped with.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function popularCategories(): Collection
    {
        $tones = [
            'blue' => 'bg-primary-50 text-primary-700',
            'green' => 'bg-emerald-50 text-emerald-700',
            'amber' => 'bg-amber-50 text-amber-700',
            'purple' => 'bg-violet-50 text-violet-700',
            'red' => 'bg-rose-50 text-rose-700',
        ];

        return Category::ofType('article')
            ->withCount(['articles' => fn ($q) => $q->public()])
            // whereHas, not having: withCount is a select subquery, not an
            // aggregate, and SQLite refuses a HAVING clause on a query with
            // no GROUP BY. MySQL accepts it, so the page worked on the
            // server while every search test failed with a 500.
            ->whereHas('articles', fn ($q) => $q->public())
            ->orderByDesc('articles_count')
            ->take(5)
            ->get()
            ->map(fn (Category $c) => [
                'label' => $c->name,
                'count' => $c->articles_count,
                'color' => $tones[$c->tone] ?? $tones['blue'],
                'href' => route('news.index', ['categorie' => $c->slug]),
            ]);
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
