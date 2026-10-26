<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'body', 'image', 'category_id',
        'status', 'published_at', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_system' => 'boolean',
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
}
