<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingBaggage extends Model
{
    protected $table = 'booking_baggage';

    protected $fillable = [
        'booking_id',
        'baggage_option_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function baggageOption(): BelongsTo
    {
        return $this->belongsTo(BaggageOption::class);
    }
}