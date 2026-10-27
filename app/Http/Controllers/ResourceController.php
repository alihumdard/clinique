<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function guides(Request $request): View
    {
        $slug = $request->query('domaine');

        $domain = $slug
            ? Category::where('type', 'guide')->where('slug', $slug)->first()
            : null;

        $guides = Guide::query()
            ->public()
            ->with('category')
            ->when($domain, fn ($q) => $q->where('category_id', $domain->id))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        // The domain name is visible right above this search
                        // box, so a term matching it (e.g. "Droit civil")
                        // should also match, not come back empty.
                        ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$term}%"));
                });
            })
            // The three orders the page offers; anything else is the default.
            ->when($request->query('tri') === 'consultees', fn ($q) => $q->orderByDesc('downloads'))
            ->when($request->query('tri') === 'alphabetique', fn ($q) => $q->orderBy('title'))
            ->when(! in_array($request->query('tri'), ['consultees', 'alphabetique'], true), fn ($q) => $q->latest('published_at'))
            ->paginate(9)
            ->withQueryString();

        return view('resources.guides', [
            'guides' => $guides,
            'domains' => Category::ofType('guide')
                ->withCount(['guides' => fn ($q) => $q->public()])
                ->get(),
            'current' => $domain,
            'filters' => $request->only(['domaine', 'q', 'tri']),
            // "Fiches les plus consultées" is the download counter the
            // document controller keeps, not a separate figure.
            'popular' => Guide::public()->orderByDesc('downloads')->take(5)->get(),
            'totals' => [
                'guides' => Guide::public()->count(),
                'downloads' => (int) Guide::public()->sum('downloads'),
            ],
        ]);
    }

    public function guide(Guide $guide): View
    {
        // A draft or a scheduled fiche is not public yet, whoever has the
        // link. Admins preview from the admin side.
        abort_unless($guide->isPublic(), 404);

        return view('resources.guide-show', [
            'guide' => $guide->load('category'),
            'more' => Guide::public()
                ->with('category')
                ->whereKeyNot($guide->id)
                ->when($guide->category_id, fn ($q) => $q->where('category_id', $guide->category_id))
                ->latest('published_at')
                ->take(4)
                ->get(),
            // The sidebar carries the same domain list as the index.
            'domains' => Category::ofType('guide')
                ->withCount(['guides' => fn ($q) => $q->public()])
                ->get(),
            'totalGuides' => Guide::public()->count(),
        ]);
    }

    public function reports(): View
    {
        return view('resources.reports', [
            // A report is identified by its year, so that is the order.
            'reports' => Report::public()
                ->orderByDesc('year')
                ->paginate(10),
            'totals' => [
                'reports' => Report::public()->count(),
                'downloads' => (int) Report::public()->sum('downloads'),
            ],
        ]);
    }
}
