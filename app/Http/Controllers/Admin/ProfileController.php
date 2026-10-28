<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Your own CMS account (module 4, "54. Profil utilisateur").
 *
 * Not administrator-only: this is the one screen about accounts that
 * every account needs. The security plan asks that a password be
 * "renouvelé à la première connexion", and until now only an
 * administrator could set one — and would still know it afterwards.
 */
class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profil', ['account' => Auth::user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$account->id],
        ], [], ['name' => 'nom', 'email' => 'adresse e-mail']);

        $account->update($data);

        return back()->with('status', 'Votre profil a été mis à jour.');
    }

    public function password(Request $request): RedirectResponse
    {
        $account = Auth::user();

        $data = $request->validate([
            // The current password, because a session left open must not
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

        return back()->with('status', 'Votre mot de passe a été changé.');
    }
}
