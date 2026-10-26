<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Partner;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function index(): View
    {
        /*
         * One query for the whole page: the sections are slices of the same
         * ordered list, grouped by rubrique in PHP rather than queried once
         * per section.
         */
        $partners = Partner::public()
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        $sections = Category::ofType('partner')
            ->get()
            ->map(fn (Category $category) => [
                'category' => $category,
                'partners' => $partners->where('category_id', $category->id)->values(),
            ])
            ->filter(fn (array $section) => $section['partners']->isNotEmpty())
            ->values();

        return view('partners', [
            'sections' => $sections,
            // Partners with no rubrique would otherwise never be shown.
            'unsorted' => $partners->whereNull('category_id')->values(),
            'total' => $partners->count(),
        ]);
    }
}
