<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        /*
         * One query for the whole page, grouped in PHP: the four sections
         * are four slices of the same ordered list, so asking the database
         * four times would be four round trips for one answer.
         */
        $members = TeamMember::public()
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->groupBy('department');

        return view('team', [
            'groups' => $members,
            'departments' => TeamMemberRequest::DEPARTMENTS,
            'counts' => collect(TeamMemberRequest::DEPARTMENTS)
                ->map(fn (string $label, string $key) => $members->get($key)?->count() ?? 0),
        ]);
    }
}
