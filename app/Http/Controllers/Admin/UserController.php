<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The CMS accounts (module 4, "52. Gestion des utilisateurs").
 *
 * Administrator-only, like everything else that changes who can do what.
 *
 * An account is disabled, never deleted: deleting one would blank
 * author_id on everything it ever published, and the Clinic would lose
 * the answer to "who wrote this". Someone who leaves keeps their name on
 * their work and loses their way in.
 *
 * Four rules stand between this screen and a Clinic that has locked
 * itself out. They are all the same rule from different angles: there
 * must always be one administrator left who can sign in.
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', [
            'users' => User::orderByDesc('is_active')
                ->orderBy('name')
                ->get(),
            'counts' => [
                'total' => User::count(),
                'admins' => User::active()->where('role', User::ROLE_ADMIN)->count(),
                'editors' => User::active()->where('role', User::ROLE_EDITOR)->count(),
                'disabled' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            // Twelve characters, as the security plan requires of a
            // production account.
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_EDITOR])],
        ], [], $this->labels());

        User::create($data + ['is_active' => true]);

        return back()->with('status', "Le compte de {$data['name']} a été créé.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_EDITOR])],
        ], [], $this->labels());

        // Demoting the last administrator leaves nobody able to reach the
        // settings, the référentiels — or this screen.
        if ($user->isAdmin()
            && $data['role'] !== User::ROLE_ADMIN
            && ! User::otherActiveAdministratorsExist($user)) {
            return back()->withErrors([
                'role' => "Ce compte est le dernier administrateur actif : son rôle ne peut pas être changé.",
            ]);
        }

        $user->update($data);

        return back()->with('status', "Le compte de {$user->name} a été modifié.");
    }

    /**
     * A new password, set by an administrator — for someone who has lost
     * theirs. The holder is told to change it at first sign-in, which is
     * what the security plan asks of every account.
     */
    public function password(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [], $this->labels());

        $user->update(['password' => $data['password']]);

        return back()->with('status', "Le mot de passe de {$user->name} a été réinitialisé.");
    }

    public function toggle(User $user): RedirectResponse
    {
        // Disabling your own account signs you out of a screen only you
        // could have used to put it back.
        if ($user->is($this->current())) {
            return back()->withErrors(['compte' => "Vous ne pouvez pas désactiver votre propre compte."]);
        }

        if ($user->is_active
            && $user->isAdmin()
            && ! User::otherActiveAdministratorsExist($user)) {
            return back()->withErrors([
                'compte' => "Ce compte est le dernier administrateur actif : il ne peut pas être désactivé.",
            ]);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('status', $user->is_active
            ? "Le compte de {$user->name} a été réactivé."
            : "Le compte de {$user->name} a été désactivé.");
    }

    private function current(): User
    {
        return Auth::user();
    }

    /** @return array<string, string> */
    private function labels(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse e-mail',
            'password' => 'mot de passe',
            'role' => 'rôle',
        ];
    }
}
