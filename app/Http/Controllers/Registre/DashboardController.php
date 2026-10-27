<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\Dossier;
use App\Models\Registre\PresaisineSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The Registre's landing screen.
 *
 * It carries the figures that say what needs attention now — requests
 * waiting, dossiers open, dossiers late — and says plainly what the rest
 * of module 3 is still waiting on from the Clinic.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('registre.dashboard', [
            'user' => Auth::guard('registre')->user(),
            'pendingSubmissions' => PresaisineSubmission::pending()->count(),
            'totalSubmissions' => PresaisineSubmission::count(),
            'openDossiers' => Dossier::open()->count(),
            'overdueDossiers' => Dossier::overdue()->count(),
        ]);
    }
}
