<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        // A draft or a scheduled page is not public yet, whoever has the
        // link. Admins preview from the admin side.
        abort_unless($page->isPublic(), 404);

        return view('pages.show', ['page' => $page]);
    }
}
