<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            // Base amounts
            $table->decimal('level1_base', 12, 2)->default(0);
            $table->decimal('level2_base', 12, 2)->default(0);
            $table->decimal('level3_base', 12, 2)->default(0);
            $table->decimal('level4_base', 12, 2)->default(0);

            // Percentages (store as 0–100, e.g. 12.5 for 12.5%)
            $table->double('level1_market_percentage')->default(0);
            $table->double('level2_market_percentage')->default(0);
            $table->double('level3_market_percentage')->default(0);
            $table->double('level4_market_percentage')->default(0);



            // Cost fields
            $table->decimal('repair_cost', 12, 2)->default(0);
            $table->decimal('aerial_chimney_cost', 12, 2)->default(0);
            $table->decimal('insurance_cost', 12, 2)->default(0);
            $table->decimal('thermal_image_cost', 12, 2)->default(0);
            $table->decimal('listing_cost', 12, 2)->default(0);
            $table->decimal('extra_sqft_cost', 12, 2)->default(0);
            $table->decimal('extra_reception_room_cost', 12, 2)->default(0);
            $table->decimal('extra_room_cost', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
