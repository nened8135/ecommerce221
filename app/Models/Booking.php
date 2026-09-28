<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'flight_id',
        'booking_reference',
        'number_of_passengers',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Utilisateur ayant effectué la réservation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vol réservé
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Passagers de la réservation
     */
    public function passengers(): BelongsToMany
    {
        return $this->belongsToMany(
            Passenger::class,
            'booking_passengers'
        );
    }

    /**
     * Paiements de la réservation
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Bagages associés à la réservation
     */
    public function bookingBaggages(): HasMany
    {
        return $this->hasMany(BookingBaggage::class);
    }
}