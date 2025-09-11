<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;
    protected $fillable=[
        'level1_base',
        'level2_base',
        'level3_base',
        'level4_base',
        'level1_market_percentage',
        'level2_market_percentage',
        'level3_market_percentage',
        'level4_market_percentage',
        'repair_cost',
        'aerial_chimney_cost',
        'insurance_cost',
        'thermal_image_cost',
        'listing_cost',
        'extra_sqft_cost',
        'extra_reception_room_cost',
        'extra_room_cost',
    ];
}
