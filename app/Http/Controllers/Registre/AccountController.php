<?php

namespace App\Http\Controllers\Registre;

use App\Http\Controllers\Controller;
use App\Models\Registre\AuditLog;
use App\Models\Registre\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The Registre's own accounts.
 *
 * Separate from the CMS accounts in every sense the plan asks for: a
 * different table, a different database, a different screen. Nothing
 * here can create a CMS account and nothing there can create one of
 * these.
 *
 * Who may use this screen is part of the matrice des autorisations the
 * Clinic has yet to approve — the plan gives the Administrateur
 * technique "l'administration technique des comptes et du système", but
 * the detail is not settled. Until it is, any signed-in account can, and
 * every change is written to the access log.
 *
 * Like the CMS, an account is disabled rather than deleted: a dossier
 * keeps its assignments and the log keeps pointing at a row that exists.
 */
class AccountController extends Controller
{
    public function index(): View
    {
        return view('registre.comptes.index', [
            'accounts' => User::orderByDesc('is_active')->orderBy('name')->get(),
            'roles' => User::roles(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:registre.registre_users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', Rule::in(array_keys(User::roles()))],
        ], [], $this->labels());

        $account = User::create($data + ['is_active' => true]);

        AuditLog::record('account.created', $account, [
            'name' => $account->name,
            'role' => $account->role,
        ]);

        return back()->with('registre_message', "Le compte de {$account->name} a été créé.");
    }

    public function update(Request $request, User $account): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('registre.registre_users', 'email')->ignore($account->id)],
            'role' => ['required', Rule::in(array_keys(User::roles()))],
        ], [], $this->labels());

        $account->update($data);

        AuditLog::record('account.updated', $account, ['name' => $account->name]);

        return back()->with('registre_message', "Le compte de {$account->name} a été modifié.");
    }

    public function password(Request $request, User $account): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [], $this->labels());

        $account->update(['password' => $data['password']]);

        // The new password is not logged, only that it was changed.
        AuditLog::record('account.password_reset', $account, ['name' => $account->name]);

        return back()->with('registre_message', "Le mot de passe de {$account->name} a été réinitialisé.");
    }

    public function toggle(User $account): RedirectResponse
    {
        $current = Auth::guard('registre')->user();

        // Disabling your own account closes the screen you would need to
        // put it back.
        if ($account->is($current)) {
            return back()->withErrors(['compte' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        // And the registre must never be left without a way in.
        if ($account->is_active && ! User::active()->whereKeyNot($account->id)->exists()) {
            return back()->withErrors([
                'compte' => "C'est le dernier compte actif du registre : il ne peut pas être désactivé.",
            ]);
        }

        $account->update(['is_active' => ! $account->is_active]);

        AuditLog::record($account->is_active ? 'account.enabled' : 'account.disabled', $account, [
            'name' => $account->name,
        ]);

        return back()->with('registre_message', $account->is_active
            ? "Le compte de {$account->name} a été réactivé."
            : "Le compte de {$account->name} a été désactivé.");
    }

    /** @return array<string, string> */
    private function labels(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse e-mail',
            'password' => 'mot de passe',
            'role' => 'profil',
        ];
    }
}
