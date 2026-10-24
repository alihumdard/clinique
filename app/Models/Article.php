<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image',
        'category_id', 'author_id', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Years that actually hold a published article, newest first, with a
     * count each — what the "archives par année" panel lists.
     */
    public static function archiveYears(): \Illuminate\Support\Collection
    {
        // YEAR() is MySQL's; SQLite spells it strftime. Picking the
        // expression from the driver keeps this working on both, which
        // is what lets the tests cover it at all.
        $year = match (static::query()->getConnection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', published_at) AS INTEGER)",
            'pgsql' => 'EXTRACT(YEAR FROM published_at)',
            default => 'YEAR(published_at)',
        };

        return static::query()
            ->public()
            ->selectRaw("{$year} as year, COUNT(*) as total")
            ->whereNotNull('published_at')
            ->groupBy('year')
            ->orderByDesc('year')
            ->get();
    }

    public function scopeInYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('published_at', $year);
    }
}
