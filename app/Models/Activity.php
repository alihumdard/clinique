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
}
