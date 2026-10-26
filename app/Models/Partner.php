<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'website',
        'category_id', 'status', 'is_visible', 'position',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
     * Partners are a list the Clinic orders and shows or hides, not a
     * timeline: the table carries is_visible and position, and no
     * published_at.
     */
    protected function publishesOnADate(): bool
    {
        return false;
    }

    protected function hidesIndividualRows(): bool
    {
        return true;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function logoUrl(): string
    {
        return $this->imageUrl($this->logo, 'placeholders/partner-placeholder');
    }
}
