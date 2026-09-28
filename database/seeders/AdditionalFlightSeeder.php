<?php

namespace Database\Seeders;

use App\Models\Flight;
use Illuminate\Database\Seeder;

class AdditionalFlightSeeder extends Seeder
{
    public function run(): void
    {
        $flights = [
            // Air France — Dakar → Paris
            [
                'airline_id' => 2,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 2,
                'flight_number' => 'AF719',
                'departure_at' => '2026-10-15 10:00:00',
                'arrival_at' => '2026-10-15 16:45:00',
                'duration_minutes' => 405,
                'stops' => 0,
                'price' => 320000,
                'available_seats' => 180,
                'is_active' => true,
            ],

            // Royal Air Maroc — Dakar → Paris
            [
                'airline_id' => 3,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 2,
                'flight_number' => 'AT500',
                'departure_at' => '2026-10-15 11:30:00',
                'arrival_at' => '2026-10-15 19:00:00',
                'duration_minutes' => 450,
                'stops' => 0,
                'price' => 210000,
                'available_seats' => 160,
                'is_active' => true,
            ],

            // Turkish Airlines — Dakar → Paris
            [
                'airline_id' => 4,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 2,
                'flight_number' => 'TK501',
                'departure_at' => '2026-10-15 14:30:00',
                'arrival_at' => '2026-10-15 22:00:00',
                'duration_minutes' => 450,
                'stops' => 0,
                'price' => 290000,
                'available_seats' => 150,
                'is_active' => true,
            ],

            // Ethiopian Airlines — Dakar → Paris
            [
                'airline_id' => 6,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 2,
                'flight_number' => 'ET901',
                'departure_at' => '2026-10-15 17:00:00',
                'arrival_at' => '2026-10-16 00:30:00',
                'duration_minutes' => 450,
                'stops' => 0,
                'price' => 280000,
                'available_seats' => 140,
                'is_active' => true,
            ],

            // Emirates — Dakar → Dubaï
            [
                'airline_id' => 5,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 8,
                'flight_number' => 'EK701',
                'departure_at' => '2026-10-15 18:30:00',
                'arrival_at' => '2026-10-16 04:30:00',
                'duration_minutes' => 600,
                'stops' => 0,
                'price' => 400000,
                'available_seats' => 170,
                'is_active' => true,
            ],
        ];

        foreach ($flights as $flight) {
            Flight::updateOrCreate(
                ['flight_number' => $flight['flight_number']],
                $flight
            );
        }
    }
}