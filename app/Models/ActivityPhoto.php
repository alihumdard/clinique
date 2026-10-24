<?php

namespace App\Models;

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
}
