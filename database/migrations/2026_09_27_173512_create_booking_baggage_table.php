<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_baggage', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->foreignId('baggage_option_id')
                ->constrained('baggage_options')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('quantity')->default(1);

            // Prix enregistré au moment de la réservation
            $table->decimal('unit_price', 10, 2)->default(0);

            $table->timestamps();

            $table->unique([
                'booking_id',
                'baggage_option_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_baggage');
    }
};