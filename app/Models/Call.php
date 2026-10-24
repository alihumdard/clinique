<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Call extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image',
        'category_id', 'closes_on', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'closes_on' => 'date',
            'published_at' => 'datetime',
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

    /** Still accepting applications: published and not past its date. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->public()->where(function (Builder $q) {
            $q->whereNull('closes_on')->orWhereDate('closes_on', '>=', today());
        });
    }

    public function isOpen(): bool
    {
        return $this->isPublic() && (is_null($this->closes_on) || ! $this->closes_on->isPast());
    }
}
