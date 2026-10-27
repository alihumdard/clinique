<?php

namespace App\Models\Registre;

use App\Models\Registre\Concerns\OnRegistreConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The person who brought the matter: walked into the permanence, or
 * submitted an online pré-saisine that was accepted.
 */
class Applicant extends Model
{
    use OnRegistreConnection;

    protected $fillable = [
        'last_name', 'first_name', 'phone', 'email',
        'commune', 'address', 'is_uam_student', 'student_card_number',
    ];

    protected function casts(): array
    {
        return ['is_uam_student' => 'boolean'];
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '').' '.$this->last_name);
    }
}
