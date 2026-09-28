<?php

namespace Database\Seeders;

use App\Models\BaggageOption;
use Illuminate\Database\Seeder;

class BaggageOptionSeeder extends Seeder
{
    public function run(): void
    {
        BaggageOption::insert([
            [
                'name' => 'Bagage cabine',
                'weight_kg' => 7,
                'price' => 0,
                'is_included' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bagage en soute',
                'weight_kg' => 23,
                'price' => 0,
                'is_included' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bagage supplémentaire',
                'weight_kg' => 23,
                'price' => 25000,
                'is_included' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bagage supplémentaire',
                'weight_kg' => 32,
                'price' => 40000,
                'is_included' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}