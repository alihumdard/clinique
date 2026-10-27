<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A request left through the online pré-saisine.
 *
 * Received by the public site, stored here. It becomes a dossier only
 * when someone at the Clinic accepts it.
 */
class PresaisineSubmission extends Model
{
    use OnRegistreConnection;

    public const STATUS_PENDING = 'en_attente';
    public const STATUS_ACCEPTED = 'acceptee';
    public const STATUS_REFUSED = 'refusee';

    protected $fillable = [
        'reference', 'last_name', 'first_name', 'phone', 'email', 'commune',
        'is_uam_student', 'student_card_number', 'legal_domain', 'subject',
        'narrative', 'question_1', 'question_2', 'question_3', 'orientation',
        'status', 'consent_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_uam_student' => 'boolean',
            'orientation' => 'array',
            'consent_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PresaisineAttachment::class, 'submission_id');
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '').' '.$this->last_name);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /** @return array<string, string> */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_ACCEPTED => 'Acceptée',
            self::STATUS_REFUSED => 'Refusée',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }
}
