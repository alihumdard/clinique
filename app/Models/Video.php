<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'summary', 'video_url', 'provider', 'provider_id',
        'cover', 'duration', 'transcript', 'category_id',
        'status', 'published_at', 'allow_comments',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'allow_comments' => 'boolean',
            'views' => 'integer',
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

    /**
     * The embed address, built from what was parsed out of the pasted URL.
     * youtube-nocookie so nothing is set on the visitor before they press
     * play, which is also why the player loads only on click.
     */
    public function embedUrl(): ?string
    {
        if (! $this->provider_id) {
            return null;
        }

        return match ($this->provider) {
            'vimeo' => "https://player.vimeo.com/video/{$this->provider_id}?autoplay=1",
            default => "https://www.youtube-nocookie.com/embed/{$this->provider_id}?autoplay=1&rel=0",
        };
    }

    /**
     * The thumbnail shown before the visitor presses play. An uploaded
     * cover wins; otherwise the provider's own thumbnail saves the editor
     * a step, and only then do we fall back to a placeholder.
     */
    public function coverUrl(): string
    {
        if (filled($this->cover)) {
            return $this->imageUrl($this->cover, 'placeholders/video-featured');
        }

        return \App\Support\VideoLink::thumbnail($this->provider, $this->provider_id)
            ?? asset('images/placeholders/video-featured.jpg');
    }

    /** Minutes:seconds, as the cards show it. */
    public function durationLabel(): ?string
    {
        return $this->duration ?: null;
    }

    public function hasTranscript(): bool
    {
        return filled($this->transcript);
    }
}
