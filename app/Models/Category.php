<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['type', 'name', 'slug', 'tone', 'position'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type)->orderBy('position')->orderBy('name');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function guides()
    {
        return $this->hasMany(Guide::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    /**
     * A thumbnail for the rubrique, taken from whichever row the listing
     * query selected as representative. Null when none was selected, so
     * the panel that wants one asks for it and nothing else pays for it.
     */
    public function coverUrl(string $fallback = 'placeholders/video-featured'): string
    {
        return Media::url($this->cover ?? null, $fallback);
    }
}
