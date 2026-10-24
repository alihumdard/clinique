<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = ['label', 'note', 'amount', 'currency', 'is_free', 'position', 'is_visible'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'is_free' => 'boolean',
            'is_visible' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true)->orderBy('position');
    }

    /** "5 000 FCFA", or the word the plan uses for a free service. */
    public function amountForHumans(): string
    {
        if ($this->is_free || is_null($this->amount)) {
            return 'Gratuit';
        }

        return number_format($this->amount, 0, ',', ' ').' '.$this->currency;
    }
}
