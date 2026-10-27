<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * One of the thirteen dossier statuses.
 *
 * The labels are the Clinic's and are not yet approved; until they are,
 * every row carries is_provisional and the screens say so. No code
 * anywhere names a status by its label.
 */
class DossierStatus extends Model
{
    use OnRegistreConnection;

    protected $fillable = [
        'code', 'label', 'position', 'is_terminal',
        'requires_supervisor_validation', 'is_provisional',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_terminal' => 'boolean',
            'requires_supervisor_validation' => 'boolean',
            'is_provisional' => 'boolean',
        ];
    }

    /** The statuses a dossier in this one may move to. */
    public function allowedNext(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'dossier_status_transitions',
            'from_status_id',
            'to_status_id'
        );
    }

    /**
     * The status a dossier opens in: the first of the approved list.
     *
     * Which one that is belongs to the Clinic's ordering, so it is read
     * from the rows rather than named in the code. Null while no status
     * has been recorded at all, which callers must handle — opening a
     * dossier under an invented status is not an option.
     */
    public static function opening(): ?self
    {
        return static::query()->orderBy('position')->first();
    }

    /**
     * True while no transition at all has been approved. The Clinic has
     * not supplied them yet, and a screen that offers "any status to any
     * status" would be inventing the workflow, so it is asked rather
     * than assumed.
     */
    public static function transitionsAreUndefined(): bool
    {
        return ! static::query()->getConnection()->table('dossier_status_transitions')->exists();
    }
}
