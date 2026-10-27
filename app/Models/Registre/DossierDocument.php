<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Metadata for a file attached to a dossier.
 *
 * The file itself is written to a private disk outside the web root and
 * is never linked to directly: a controller checks the reader's rights,
 * logs the access and only then streams it.
 */
class DossierDocument extends Model
{
    use OnRegistreConnection;

    public const KIND_SIGNED_MANDATE = 'mandat_signe';
    public const KIND_ATTACHMENT = 'piece_jointe';
    public const KIND_ANALYSIS = 'analyse';
    public const KIND_OTHER = 'autre';

    protected $fillable = [
        'dossier_id', 'kind', 'path', 'original_name',
        'mime_type', 'size', 'from_presaisine', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'from_presaisine' => 'boolean',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function humanSize(): string
    {
        $mo = $this->size / 1048576;

        return $mo >= 1
            ? number_format($mo, 1, ',', ' ').' Mo'
            : max(1, (int) round($this->size / 1024)).' Ko';
    }
}
