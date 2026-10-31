<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * How many rows a listing shows.
     *
     * The table footer's "10 / 25 / 50 par page" was bound to nothing:
     * choosing 50 left the page on ten. It submits ?par_page= now, and
     * this is what reads it.
     *
     * It lives here rather than in ManagesContent because the messages
     * and newsletter screens paginate too and do not use that trait —
     * they hold the public's own data and share none of the publishing
     * behaviour the rest of the CMS does.
     *
     * Each screen keeps its own default: the newsletter shows 25 and the
     * partner grid 12, because the rows are not the same size. Only the
     * three sizes the control offers are honoured, so ?par_page=5000 is
     * not a way to ask the server for every row at once.
     */
    protected function perPage(Request $request, int $default = 10): int
    {
        $asked = (int) $request->query('par_page', 0);

        return in_array($asked, [10, 25, 50], true) ? $asked : $default;
    }
}
