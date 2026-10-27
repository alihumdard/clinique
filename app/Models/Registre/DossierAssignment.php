<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Who works on a dossier, and in what part.
 *
 * The part is recorded here rather than read from the account's profile:
 * the plan assigns "un ou plusieurs étudiants cliniciens et un
 * superviseur/encadrant" to a dossier, and who supervises one case is not
 * necessarily who supervises the next.
 */
class DossierAssignment extends Model
{
    use OnRegistreConnection;

    public const ROLE_STUDENT = 'etudiant';
    public const ROLE_SUPERVISOR = 'encadrant';

    protected $fillable = ['dossier_id', 'user_id', 'role', 'assigned_by', 'assigned_at', 'ended_at'];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Still working on it, as opposed to having worked on it. */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }
}
