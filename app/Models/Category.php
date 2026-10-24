<?php

namespace App\Models;

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
}
