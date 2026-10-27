<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A date a dossier has to meet: a hearing, a filing, a call back. */
class DossierDeadline extends Model
{
    use OnRegistreConnection;

    protected $fillable = ['dossier_id', 'label', 'due_at', 'completed_at', 'created_by'];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function isOverdue(): bool
    {
        return $this->completed_at === null && $this->due_at->isPast();
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNull('completed_at')->where('due_at', '<', now());
    }
}
