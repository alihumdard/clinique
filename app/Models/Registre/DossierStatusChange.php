<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One move of a dossier from one status to another, with the moment and
 * the person who made it.
 *
 * Append-only: no updated_at, no update path, no delete path. A history
 * that can be rewritten is not a history.
 */
class DossierStatusChange extends Model
{
    use OnRegistreConnection;

    protected $table = 'dossier_status_history';

    public $timestamps = false;

    protected $fillable = ['dossier_id', 'from_status_id', 'to_status_id', 'user_id', 'note', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(DossierStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(DossierStatus::class, 'to_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
