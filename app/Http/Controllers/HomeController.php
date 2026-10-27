<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\LegalDomain;
use App\Models\Partner;
use App\Models\Video;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Everything the home page reads. It lives in one method because two
     * routes render that view: a second route that built the payload by
     * hand fell behind when a block was added, and /pre-saisine answered
     * 500 on every visit while the home page itself was fine.
     */
    public function index(): View
    {
        $videos = Video::public()
            ->with('category')
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('home', [
            'domains' => LegalDomain::active()->get(),

            'recentArticles' => Article::public()
                ->with('category')
                ->latest('published_at')
                ->take(3)
                ->get(),

            'homeActivities' => Activity::public()
                ->with('category')
                ->latest('held_on')
                ->take(3)
                ->get(),

            // The lead capsule is the newest one; the three beside it are
            // the ones after it, so no capsule is listed twice.
            'featuredVideo' => $videos->first(),
            'videos' => $videos->skip(1)->values(),

            // Six: the strip is a six-column grid, and the page links on
            // to the full list.
            'partners' => Partner::public()
                ->orderBy('position')
                ->orderBy('name')
                ->take(6)
                ->get(),
        ]);
    }

    /**
     * Reserved by Module 1 so the header and the CTAs resolve instead of
     * pointing at '#'. Module 3 replaces it with the orientation filter
     * and the request form; until then it answers with the home page
     * rather than a dead link.
     */
    public function presaisine(): View
    {
        return $this->index();
    }
}
