<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'body', 'cover',
        'file_path', 'file_name', 'file_size',
        'category_id', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'file_size' => 'integer',
            'downloads' => 'integer',
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

    /** "2,1 Mo" — the French decimal comma, as the pages show it. */
    public function fileSizeForHumans(): string
    {
        $mb = $this->file_size / 1048576;

        return number_format($mb, 1, ',', ' ').' Mo';
    }
}
