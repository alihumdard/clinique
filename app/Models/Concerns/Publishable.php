<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared publication behaviour. Every content type in the CMS carries the
 * same four states, so the rule for "what the public may see" is written
 * once here rather than once per model, where one of them would drift.
 */
trait Publishable
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * What a visitor is allowed to see: published, and not dated in the
     * future. A row scheduled for next week is published in the admin's
     * sense but must not appear on the site until its date arrives.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '>', now());
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function isPublic(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && (is_null($this->published_at) || $this->published_at->isPast());
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at
            && $this->published_at->isFuture();
    }

    /** The label the admin tables show, which separates the two above. */
    public function statusLabel(): string
    {
        if ($this->trashed()) {
            return 'Corbeille';
        }

        return match (true) {
            $this->isScheduled() => 'Programmée',
            $this->status === self::STATUS_PUBLISHED => 'Publié',
            $this->status === self::STATUS_ARCHIVED => 'Archivé',
            default => 'Brouillon',
        };
    }

    public function statusTone(): string
    {
        if ($this->trashed()) {
            return 'red';
        }

        return match (true) {
            $this->isScheduled() => 'blue',
            $this->status === self::STATUS_PUBLISHED => 'green',
            $this->status === self::STATUS_ARCHIVED => 'slate',
            default => 'amber',
        };
    }
}
