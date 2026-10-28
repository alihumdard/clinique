<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Your own account (module 4, "54. Profil utilisateur").
 *
 * The security plan asks that a password be "renouvelé à la première
 * connexion", which until now nobody could do: only an administrator
 * could set one, and they would know it afterwards. A password someone
 * else chose and still knows is not a password.
 */
class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('registre.profil', [
            'account' => Auth::guard('registre')->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = Auth::guard('registre')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ], [], ['name' => 'nom']);

        $account->update($data);

        return back()->with('registre_message', 'Votre profil a été mis à jour.');
    }

    public function password(Request $request): RedirectResponse
    {
        $account = Auth::guard('registre')->user();

        $data = $request->validate([
            // The current password, because a left-open session must not
            // be enough to lock the rightful holder out of their account.
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [], [
            'current_password' => 'mot de passe actuel',
            'password' => 'nouveau mot de passe',
        ]);

        if (! Hash::check($data['current_password'], $account->password)) {
            return back()->withErrors(['current_password' => 'Ce mot de passe ne correspond pas.']);
        }

        $account->update(['password' => $data['password']]);

        AuditLog::record('account.password_changed', $account, ['name' => $account->name]);

        return back()->with('registre_message', 'Votre mot de passe a été changé.');
    }
}
