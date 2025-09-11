<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Prices',
            'active' => 'price',
            'price' => Price::find(1),
        ];
        return view('admin.price.index', $data);
    }

    public function updateOrCreate(Request $request)
    {
        $data = $request->validate([
            // Base amounts
            'level1_base' => 'nullable|numeric|min:0',
            'level2_base' => 'nullable|numeric|min:0',
            'level3_base' => 'nullable|numeric|min:0',
            'level4_base' => 'nullable|numeric|min:0',

            // Market percentages (0–100)
            'level1_market_percentage' => 'nullable|numeric|min:0|max:100',
            'level2_market_percentage' => 'nullable|numeric|min:0|max:100',
            'level3_market_percentage' => 'nullable|numeric|min:0|max:100',
            'level4_market_percentage' => 'nullable|numeric|min:0|max:100',

            // Other costs
            'repair_cost' => 'nullable|numeric|min:0',
            'aerial_chimney_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'thermal_image_cost' => 'nullable|numeric|min:0',
            'listing_cost' => 'nullable|numeric|min:0',
            'extra_sqft_cost' => 'nullable|numeric|min:0',
            'extra_reception_room_cost' => 'nullable|numeric|min:0',
            'extra_room_cost' => 'nullable|numeric|min:0',
        ]);

        // Normalize nulls to 0 so we don't store NULLs
        $payload = [
            'level1_base' => $data['level1_base'] ?? 0,
            'level2_base' => $data['level2_base'] ?? 0,
            'level3_base' => $data['level3_base'] ?? 0,
            'level4_base' => $data['level4_base'] ?? 0,

            'level1_market_percentage' => $data['level1_market_percentage'] ?? 0,
            'level2_market_percentage' => $data['level2_market_percentage'] ?? 0,
            'level3_market_percentage' => $data['level3_market_percentage'] ?? 0,
            'level4_market_percentage' => $data['level4_market_percentage'] ?? 0,

            'repair_cost' => $data['repair_cost'] ?? 0,
            'aerial_chimney_cost' => $data['aerial_chimney_cost'] ?? 0,
            'insurance_cost' => $data['insurance_cost'] ?? 0,
            'thermal_image_cost' => $data['thermal_image_cost'] ?? 0,
            'listing_cost' => $data['listing_cost'] ?? 0,
            'extra_sqft_cost' => $data['extra_sqft_cost'] ?? 0,
            'extra_reception_room_cost' => $data['extra_reception_room_cost'] ?? 0,
            'extra_room_cost' => $data['extra_room_cost'] ?? 0,
        ];

        Price::updateOrCreate(['id' => 1], $payload);

        return back()->with('success', 'Price settings saved successfully.');
    }
}
