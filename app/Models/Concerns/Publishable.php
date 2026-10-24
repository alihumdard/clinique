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
     * Whether this type publishes on a date.
     *
     * Content types do: an actualité or a capsule can be scheduled, and a
     * row dated next week must not appear until its date arrives. Others
     * are a list that is shown or hidden in an order the Clinic chooses —
     * partners and team members — and their tables carry no published_at
     * at all, so the date clauses must not be applied to them.
     */
    protected function publishesOnADate(): bool
    {
        return true;
    }

    /** Whether a single row can be hidden while still being published. */
    protected function hidesIndividualRows(): bool
    {
        return false;
    }

    /** What a visitor is allowed to see. */
    public function scopePublic(Builder $query): Builder
    {
        $query->where('status', self::STATUS_PUBLISHED);

        if ($this->publishesOnADate()) {
            $query->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
        }

        if ($this->hidesIndividualRows()) {
            $query->where('is_visible', true);
        }

        return $query;
    }

    public function scopeScheduled(Builder $query): Builder
    {
        if (! $this->publishesOnADate()) {
            // Nothing can be scheduled without a date to schedule it for.
            return $query->whereRaw('1 = 0');
        }

        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '>', now());
    }

    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function isPublic(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        if ($this->hidesIndividualRows() && ! $this->is_visible) {
            return false;
        }

        return ! $this->publishesOnADate()
            || is_null($this->published_at)
            || $this->published_at->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->publishesOnADate()
            && $this->status === self::STATUS_PUBLISHED
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
            // Published but hidden: the row exists and is correct, it is
            // simply not on the site, which is not the same as a draft.
            $this->hidesIndividualRows() && $this->status === self::STATUS_PUBLISHED && ! $this->is_visible => 'Masqué',
            $this->status === self::STATUS_PUBLISHED => 'Publié',
            $this->status === self::STATUS_ARCHIVED => 'Archivé',
            default => 'Brouillon',
        };
    }

    /**
     * A URL for the row's own image, wherever it was stored. The resolution
     * itself lives in the Media helper, because rows that are not content
     * types — a category's representative cover, for one — need it too.
     */
    public function imageUrl(?string $path = null, string $fallback = 'placeholders/news-1'): string
    {
        return \App\Support\Media::url($path ?? $this->image ?? null, $fallback);
    }

    public function statusTone(): string
    {
        if ($this->trashed()) {
            return 'red';
        }

        return match (true) {
            $this->isScheduled() => 'blue',
            $this->hidesIndividualRows() && $this->status === self::STATUS_PUBLISHED && ! $this->is_visible => 'slate',
            $this->status === self::STATUS_PUBLISHED => 'green',
            $this->status === self::STATUS_ARCHIVED => 'slate',
            default => 'amber',
        };
    }
}
