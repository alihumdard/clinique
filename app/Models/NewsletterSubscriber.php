<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A newsletter subscriber.
 *
 * The retention rule is "jusqu'à désinscription", so leaving is recorded
 * rather than the row being deleted: that is what stops a later import
 * quietly re-subscribing someone who asked to be removed.
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'token', 'confirmed_at', 'unsubscribed_at'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected $hidden = ['token'];

    protected static function booted(): void
    {
        // Every subscriber needs a token: it is what lets an unsubscribe
        // link work without the person signing in anywhere.
        static::creating(function (self $subscriber) {
            $subscriber->token ??= Str::random(48);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('unsubscribed_at');
    }

    public function isSubscribed(): bool
    {
        return is_null($this->unsubscribed_at);
    }

    public function unsubscribe(): void
    {
        $this->forceFill(['unsubscribed_at' => now()])->save();
    }
}
