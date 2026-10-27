<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * An entry in the Registre's access and change log.
 *
 * Written through record(); nothing in the application updates or deletes
 * one. The context column carries what identifies the event, never the
 * confidential content itself.
 */
class AuditLog extends Model
{
    use OnRegistreConnection;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'ip_address', 'user_agent', 'context', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public static function record(string $action, ?Model $subject = null, array $context = [], ?Request $request = null): self
    {
        $request ??= request();

        return self::create([
            'user_id' => auth('registre')->id(),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request?->ip(),
            // Truncated: the column is indexed-adjacent metadata, not a
            // place for an unbounded header.
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 255) ?: null,
            'context' => $context ?: null,
            'created_at' => now(),
        ]);
    }
}
