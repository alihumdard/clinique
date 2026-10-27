<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The Registre's landing screen.
 *
 * Module 3 fills it with the dossier figures. It exists now because the
 * foundation needs somewhere to land after sign-in, and because it is
 * where the state of the module is stated plainly rather than implied by
 * empty screens.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('registre.dashboard', [
            'user' => Auth::guard('registre')->user(),
        ]);
    }
}
