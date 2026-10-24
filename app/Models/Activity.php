<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image',
        'location', 'held_on', 'category_id', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'held_on' => 'date',
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

    public function photos()
    {
        return $this->hasMany(ActivityPhoto::class)->orderBy('position');
    }

    public function coverUrl(): string
    {
        return $this->imageUrl($this->image, 'placeholders/news-caravan');
    }

    /**
     * Whether the activity has already taken place. The plan's "À venir" and
     * "Passées" views are this, not a status: an activity is published
     * either way, and the date is what moves it from one list to the other.
     */
    public function isUpcoming(): bool
    {
        return $this->held_on && $this->held_on->isFuture();
    }

    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('held_on')->where('held_on', '>=', now()->startOfDay());
    }

    public function scopePast($query)
    {
        return $query->whereNotNull('held_on')->where('held_on', '<', now()->startOfDay());
    }
}
