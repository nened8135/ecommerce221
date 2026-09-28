<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        Flight::insert([
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 3,
                'flight_number' => 'HC201',
                'departure_at' => '2026-10-15 09:00:00',
                'arrival_at' => '2026-10-15 12:00:00',
                'price' => 150000,
                'available_seats' => 120,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 4,
                'flight_number' => 'HC301',
                'departure_at' => '2026-10-15 10:30:00',
                'arrival_at' => '2026-10-15 16:00:00',
                'price' => 180000,
                'available_seats' => 140,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 5,
                'flight_number' => 'HC401',
                'departure_at' => '2026-10-15 13:00:00',
                'arrival_at' => '2026-10-15 20:30:00',
                'price' => 320000,
                'available_seats' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 6,
                'flight_number' => 'HC501',
                'departure_at' => '2026-10-15 14:00:00',
                'arrival_at' => '2026-10-15 20:30:00',
                'price' => 300000,
                'available_seats' => 130,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 7,
                'flight_number' => 'HC601',
                'departure_at' => '2026-10-15 16:00:00',
                'arrival_at' => '2026-10-16 05:30:00',
                'price' => 450000,
                'available_seats' => 160,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'airline_id' => 1,
                'departure_airport_id' => 1,
                'arrival_airport_id' => 8,
                'flight_number' => 'HC701',
                'departure_at' => '2026-10-15 18:00:00',
                'arrival_at' => '2026-10-16 04:30:00',
                'price' => 400000,
                'available_seats' => 170,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}