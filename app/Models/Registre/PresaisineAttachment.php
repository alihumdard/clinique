<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A file attached to a pré-saisine, held on the intake's private disk.
 *
 * The row outlives the file: once the submission has become a dossier
 * and the file has been copied there, the intake copy is removed and
 * purged_at records when — "supprimer du serveur de réception après
 * transfert sécurisé" — while what arrived stays on the record.
 */
class PresaisineAttachment extends Model
{
    use OnRegistreConnection;

    protected $fillable = ['submission_id', 'path', 'original_name', 'mime_type', 'size', 'purged_at'];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'purged_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(PresaisineSubmission::class, 'submission_id');
    }

    public function isPurged(): bool
    {
        return $this->purged_at !== null;
    }

    public function humanSize(): string
    {
        $mo = $this->size / 1048576;

        return $mo >= 1
            ? number_format($mo, 1, ',', ' ').' Mo'
            : max(1, (int) round($this->size / 1024)).' Ko';
    }
}
