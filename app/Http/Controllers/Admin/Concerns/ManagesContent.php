<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * The parts every content-type screen in the CMS does the same way.
 *
 * Each type has its own controller because its fields and uploads differ,
 * but the listing filters, the slug rule, the publication date and the KPI
 * counts are identical by design — the editor should not meet a different
 * convention on each screen. Written once here so they cannot drift apart
 * as the remaining types are added.
 */
trait ManagesContent
{
    /**
     * Status, category and search, applied the same way on every listing.
     * The corbeille is a view of its own, not a filter on the list.
     *
     * @param  array<int, string>  $searchColumns
     * @param  bool  $hasCategory  false for a type with no category column,
     *                             so a crafted ?categorie= cannot reach a
     *                             column that does not exist.
     */
    protected function applyFilters(Builder $query, Request $request, array $searchColumns, bool $hasCategory = true): Builder
    {
        $status = $request->query('statut');

        return $query
            ->when($status === 'corbeille', fn ($q) => $q->onlyTrashed())
            ->when($status && $status !== 'corbeille', fn ($q) => $q->where('status', $status))
            ->when($hasCategory && $request->query('categorie'), fn ($q) => $q->where('category_id', $request->query('categorie')))
            ->when($request->query('q'), function ($q, $term) use ($searchColumns) {
                $q->where(function ($inner) use ($searchColumns, $term) {
                    foreach ($searchColumns as $i => $column) {
                        $i === 0
                            ? $inner->where($column, 'like', "%{$term}%")
                            : $inner->orWhere($column, 'like', "%{$term}%");
                    }
                });
            });
    }

    /**
     * The four KPI figures above a listing.
     *
     * @param  class-string<Model>  $model
     * @return array<string, int>
     */
    protected function publicationCounts(string $model): array
    {
        return [
            'published' => $model::public()->count(),
            'drafts' => $model::drafts()->count(),
            'scheduled' => $model::scheduled()->count(),
            'trashed' => $model::onlyTrashed()->count(),
        ];
    }

    /**
     * A published row needs a date: without one it would never appear,
     * since every public listing orders and filters on it.
     *
     * @param  array<string, mixed>  $data
     */
    protected function publicationDate(array $data, ?Model $existing = null): ?string
    {
        if (($data['status'] ?? null) !== 'published') {
            return $data['published_at'] ?? $existing?->published_at;
        }

        return $data['published_at']
            ?? $existing?->published_at
            ?? now()->toDateTimeString();
    }

    /**
     * A slug no other row holds, trashed rows included — a restored row
     * must not collide with one published in the meantime.
     *
     * @param  class-string<Model>  $model
     */
    protected function uniqueSlug(string $model, ?string $slug, string $title, ?int $ignore = null, string $fallback = 'element'): string
    {
        $base = \Illuminate\Support\Str::slug($slug ?: $title) ?: $fallback;
        $candidate = $base;
        $suffix = 2;

        while ($model::withTrashed()
            ->where('slug', $candidate)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))
            ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
