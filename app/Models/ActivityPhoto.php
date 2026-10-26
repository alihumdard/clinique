<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;

class ActivityPhoto extends Model
{
    protected $fillable = ['activity_id', 'path', 'caption', 'position'];

    protected function casts(): array
    {
        return ['position' => 'integer'];
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function url(): string
    {
        return Media::url($this->path, 'placeholders/news-caravan');
    }

    /** Decorative photos carry no caption, so alt text is empty on purpose. */
    public function alt(): string
    {
        return $this->caption ?? '';
    }
}
