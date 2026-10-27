<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A case file.
 *
 * Opened either from an accepted online pré-saisine or by a member of the
 * Clinic for someone received at the permanence. Its status, its
 * assignments and its history are rows of their own, so that nothing
 * about how it moved is overwritten.
 */
class Dossier extends Model
{
    use OnRegistreConnection, SoftDeletes;

    public const SOURCE_WALK_IN = 'sur_place';
    public const SOURCE_ONLINE = 'en_ligne';

    public const URGENCY_NORMAL = 'normale';
    public const URGENCY_HIGH = 'elevee';
    public const URGENCY_CRITICAL = 'urgente';

    protected $fillable = [
        'reference', 'applicant_id', 'legal_domain', 'subject', 'narrative',
        'question_1', 'question_2', 'question_3', 'status_id', 'source',
        'presaisine_reference', 'urgency', 'analysis', 'opened_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'analysis_submitted_at' => 'datetime',
            'analysis_validated_at' => 'datetime',
            'returned_to_applicant_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(DossierStatus::class, 'status_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DossierAssignment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DossierDocument::class);
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(DossierDeadline::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(DossierStatusChange::class)->latest('created_at');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analysis_validated_by');
    }

    /** The supervisor's validation, which the return to the applicant waits on. */
    public function isValidated(): bool
    {
        return $this->analysis_validated_at !== null;
    }

    public function analysisAwaitsValidation(): bool
    {
        return $this->analysis_submitted_at !== null && ! $this->isValidated();
    }

    /**
     * The gate the plan requires: "empêcher le passage au statut
     * « analyse retournée au demandeur » tant que la validation du
     * superviseur n'est pas terminée".
     *
     * Asked here rather than enforced at the one place a status changes,
     * because that place is the transition engine the Clinic's approved
     * list and transitions are still awaited for. Whatever moves a
     * dossier to that status will have to ask this first, and the screens
     * already do.
     */
    public function returnableToApplicant(): bool
    {
        return $this->isValidated();
    }

    /** A deadline passed with nothing marked done. */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereHas('deadlines', function (Builder $deadline) {
            $deadline->whereNull('completed_at')->where('due_at', '<', now());
        });
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('closed_at');
    }
}
