<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The access and change log: "journal des activités / accès".
 *
 * Read-only, and that is the whole point. Nothing in the application
 * edits or deletes a line, including this screen.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $entries = AuditLog::query()
            ->with('user')
            ->when($request->query('action'), fn ($query, $action) => $query->where('action', $action))
            ->when($request->query('compte'), fn ($query, $id) => $query->where('user_id', $id))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('registre.journal.index', [
            'entries' => $entries,
            'actions' => AuditLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
            'agents' => User::orderBy('name')->get(),
            'filters' => $request->only(['action', 'compte']),
        ]);
    }
}
