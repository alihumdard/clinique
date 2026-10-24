<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JoinController extends Controller
{
    public function index(Request $request): View
    {
        $slug = $request->query('type');

        $category = $slug
            ? Category::where('type', 'call')->where('slug', $slug)->first()
            : null;

        /*
         * Open calls only: a call past its closing date drops off the
         * public list on its own, which is what the date is for. The
         * closed ones stay in the admin so the Clinic keeps the record.
         */
        $calls = Call::open()
            ->with('category')
            ->when($category, fn ($q) => $q->where('category_id', $category->id))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%");
                });
            })
            // Closing soonest first: the one a visitor needs to act on.
            ->orderByRaw('closes_on is null')
            ->orderBy('closes_on')
            ->paginate(10)
            ->withQueryString();

        return view('join', [
            'calls' => $calls,
            'categories' => Category::ofType('call')
                ->withCount(['calls' => fn ($q) => $q->open()])
                ->get(),
            'current' => $category,
            'filters' => $request->only(['type', 'q']),
            'total' => Call::open()->count(),
        ]);
    }
}
