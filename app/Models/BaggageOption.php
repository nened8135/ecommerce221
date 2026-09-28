<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaggageOption extends Model
{
    protected $fillable = [
        'name',
        'weight_kg',
        'price',
        'is_included',
        'is_active',
    ];

    protected $casts = [
        'weight_kg' => 'integer',
        'price' => 'decimal:2',
        'is_included' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Réservations utilisant cette option de bagage
     */
    public function bookingBaggages(): HasMany
    {
        return $this->hasMany(BookingBaggage::class);
    }
}