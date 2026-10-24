<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * The list of legal domains. It feeds the public pages now and, in
 * Module 3, the pré-saisine form and the register — which is why the
 * plan asks the Clinic to keep it up to date in one place.
 */
class LegalDomain extends Model
{
    protected $fillable = ['name', 'slug', 'plain_language', 'icon', 'tone', 'is_active', 'position'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('position')->orderBy('name');
    }
}
