<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A message from the public contact form, distinct from the pré-saisine.
 *
 * These rows hold personal data a visitor typed in. `consent_at` records
 * the explicit agreement the form asks for, and the retention rules
 * agreed with the Clinic run from `created_at`.
 */
class ContactMessage extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_HANDLED = 'handled';

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'consent_at', 'status', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'consent_at' => 'datetime',
            'handled_at' => 'datetime',
        ];
    }

    // Never serialised out of the application: kept for abuse handling only.
    protected $hidden = ['ip_address'];

    public function scopeUnhandled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function markHandled(User $user): void
    {
        $this->forceFill([
            'status' => self::STATUS_HANDLED,
            'handled_by' => $user->id,
            'handled_at' => now(),
        ])->save();
    }
}
