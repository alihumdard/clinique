<?php

namespace App\Models;

use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A member of the Clinic's team. These rows describe identifiable people:
 * the retention rules agreed with the Clinic apply, and a member who
 * leaves is hidden rather than left on the public page.
 */
class TeamMember extends Model
{
    use Publishable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'role', 'bio', 'photo', 'department', 'cohort',
        'email', 'linkedin', 'status', 'is_visible', 'position',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'position' => 'integer',
        ];
    }

    protected $hidden = ['email'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
