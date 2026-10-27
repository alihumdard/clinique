<?php

namespace App\Models\Registre;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * An account of the Registre numérique.
 *
 * Deliberately not App\Models\User: the two are different people in
 * different databases, and a single model would be one refactor away from
 * letting a CMS account sign in here.
 */
class User extends Authenticatable
{
    protected $connection = 'registre';

    protected $table = 'registre_users';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /*
    | The four profiles named in the plan. What each may do is the
    | "matrice des autorisations" the Clinic has yet to approve: these
    | constants record which profile an account holds, and no rule reads
    | them until that matrix exists.
    */
    public const ROLE_COORDINATOR = 'coordinateur';
    public const ROLE_SUPERVISOR = 'encadrant';
    public const ROLE_STUDENT = 'etudiant';
    public const ROLE_TECHNICAL = 'administrateur_technique';

    /** @return array<string, string> */
    public static function roles(): array
    {
        return [
            self::ROLE_COORDINATOR => 'Coordinateur',
            self::ROLE_SUPERVISOR => 'Superviseur / Encadrant',
            self::ROLE_STUDENT => 'Étudiant clinicien',
            self::ROLE_TECHNICAL => 'Administrateur technique',
        ];
    }

    public function roleLabel(): string
    {
        return self::roles()[$this->role] ?? $this->role;
    }

    /** A disabled account must not be able to sign in. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
