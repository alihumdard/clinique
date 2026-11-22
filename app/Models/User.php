<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /** Submits articles and photos for coordinator review before publication. No registre access. */
    public const ROLE_MEMBRE = 'membre';

    /** Manages all CMS content and registre dossiers. Cannot change global settings or create admin accounts. */
    public const ROLE_COORDINATEUR = 'coordinateur';

    /** Full platform access. Cannot read real dossier data (enforced at registre level). */
    public const ROLE_ADMIN = 'admin';

    /** @deprecated Use ROLE_COORDINATEUR — kept so old 'editor' rows still resolve until migration runs. */
    public const ROLE_EDITOR = 'editor';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCoordinateur(): bool
    {
        return $this->role === self::ROLE_COORDINATEUR || $this->role === self::ROLE_EDITOR;
    }

    public function isMembre(): bool
    {
        return $this->role === self::ROLE_MEMBRE;
    }

    /** Coordinateur or administrateur: may publish, and validate what a membre submits. */
    public function managesContent(): bool
    {
        return $this->isAdmin() || $this->isCoordinateur();
    }

    /**
     * Which accounts this one may create or change. A coordinateur brings in
     * membres; only an administrateur hands out the two higher roles.
     *
     * @return list<string>
     */
    public function assignableRoles(): array
    {
        return match (true) {
            $this->isAdmin() => [self::ROLE_ADMIN, self::ROLE_COORDINATEUR, self::ROLE_MEMBRE],
            $this->isCoordinateur() => [self::ROLE_MEMBRE],
            default => [],
        };
    }

    public function roleLabel(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN        => 'Administrateur IT',
            self::ROLE_COORDINATEUR => 'Coordinateur',
            self::ROLE_MEMBRE       => 'Membre',
            default                 => 'Éditeur',
        };
    }

    /** A disabled account must not be able to sign in. */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * The Clinic must never be left without an administrator: the screen
     * that disables or demotes an account asks this first.
     */
    public static function otherActiveAdministratorsExist(self $besides): bool
    {
        return static::query()
            ->active()
            ->where('role', self::ROLE_ADMIN)
            ->whereKeyNot($besides->getKey())
            ->exists();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }
}
