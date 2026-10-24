<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'label', 'note', 'amount', 'amount_label', 'currency',
        'is_free', 'position', 'is_visible',
    ];

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

    /**
     * "5 000 FCFA", the word the plan uses for a free service, or whatever
     * the Clinic wrote where the fee is not a single figure — a range, or
     * "Selon tarif en vigueur".
     */
    public function amountForHumans(): string
    {
        if ($this->is_free) {
            return 'Gratuit';
        }

        if (filled($this->amount_label)) {
            return $this->amount_label;
        }

        if (is_null($this->amount)) {
            return '—';
        }

        return number_format($this->amount, 0, ',', ' ').' '.$this->currency;
    }
}
