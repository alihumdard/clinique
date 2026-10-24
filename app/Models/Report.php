<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'year', 'description', 'cover',
        'file_path', 'file_name', 'file_size', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'year' => 'integer',
            'file_size' => 'integer',
            'downloads' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function fileSizeForHumans(): string
    {
        return number_format($this->file_size / 1048576, 1, ',', ' ').' Mo';
    }
}
