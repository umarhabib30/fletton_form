<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Price::create([
            'level1_base' => 349,
            'level2_base' => 450,
            'level3_base' => 550,
            'level4_base' => 1050,

            'level1_market_percentage' => 0,
            'level2_market_percentage' => 0.0003,
            'level3_market_percentage' => 0.0004,
            'level4_market_percentage' => 0.0005,

            'repair_cost' => 300,
            'aerial_chimney_cost' => 200,
            'insurance_cost' => 200,
            'thermal_image_cost' => 250,
            'listing_cost' => 100,
            'extra_sqft_cost' => 1.75,
            'extra_reception_room_cost' => 50,
            'extra_room_cost' => 100,
        ]);
    }
}
