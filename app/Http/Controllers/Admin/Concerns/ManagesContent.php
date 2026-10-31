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
    protected function applyFilters(Builder $query, Request $request, array $searchColumns, bool $hasCategory = true, string $dateColumn = 'created_at'): Builder
    {
        $status = $request->query('statut');
        $period = $request->query('periode');

        return $query
            ->when($status === 'corbeille', fn ($q) => $q->onlyTrashed())
            ->when($status && $status !== 'corbeille', fn ($q) => $q->where('status', $status))
            ->when($hasCategory && $request->query('categorie'), fn ($q) => $q->where('category_id', $request->query('categorie')))
            ->when($period === 'mois', fn ($q) => $q->where($dateColumn, '>=', now()->startOfMonth()))
            ->when($period === 'trimestre', fn ($q) => $q->where($dateColumn, '>=', now()->subMonths(3)))
            ->when($period === 'annee', fn ($q) => $q->where($dateColumn, '>=', now()->startOfYear()))
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
     * The KPI figures for a list type — partners, team members — where a
     * row is shown or hidden rather than scheduled.
     *
     * @param  class-string<Model>  $model
     * @return array<string, int>
     */
    protected function listCounts(string $model): array
    {
        return [
            'published' => $model::public()->count(),
            'hidden' => $model::query()
                ->where('status', 'published')
                ->where('is_visible', false)
                ->count(),
            'drafts' => $model::drafts()->count(),
            'trashed' => $model::onlyTrashed()->count(),
        ];
    }

    /**
     * Replace, or take away, a row's image.
     *
     * Three things can happen to an image on an edit, and the order
     * matters. A new file replaces the old one, and the old file is
     * dropped only once the new one is stored — the other way round, a
     * failed upload would leave the row pointing at nothing. The remove
     * checkbox clears the column and deletes the file. Neither present
     * means the image is left exactly as it was, which is why this
     * returns the data untouched rather than writing a null.
     *
     * $request->boolean(): an unchecked checkbox posts nothing at all,
     * so its absence has to be read as false here rather than as "no
     * opinion" — the same rule the visibility toggles follow.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function applyImage(Request $request, array $data, ?Model $existing, string $field, string $folder): array
    {
        if ($request->hasFile($field)) {
            $previous = $existing?->{$field};
            $data[$field] = $this->media->storeImage($request->file($field), $folder);
            $this->media->deleteImage($previous);

            return $data;
        }

        if ($request->boolean('remove_'.$field) && $existing?->{$field}) {
            $this->media->deleteImage($existing->{$field});
            $data[$field] = null;
        }

        return $data;
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
     * A copy of a row as a draft: every column except the primary key,
     * the slug (regenerated so it doesn't collide with the original) and
     * the publication date (a copy is not yet published, whatever the
     * original's status was).
     *
     * @param  class-string<Model>  $model
     */
    protected function duplicateAsDraft(Model $source, string $model, string $titleColumn = 'title', string $fallbackSlug = 'element'): Model
    {
        $data = $source->getAttributes();

        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);

        $data[$titleColumn] = ($data[$titleColumn] ?? '') . ' (copie)';
        $data['status'] = 'draft';

        // A list type (partners, team members) has no published_at column
        // at all: only clear it when the source row already carried one,
        // so create() is never handed a key no column answers to.
        if (array_key_exists('published_at', $data)) {
            $data['published_at'] = null;
        }

        $data['slug'] = $this->uniqueSlug($model, null, $data[$titleColumn], null, $fallbackSlug);

        return $model::create($data);
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
