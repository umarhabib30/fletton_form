<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing Keap CRM fields to surveys table.
     * Keap billing address line 2 (inf_field_StreetAddress2).
     */
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'inf_field_StreetAddress2')) {
                $table->string('inf_field_StreetAddress2')->nullable()->after('inf_field_StreetAddress1');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('surveys', 'inf_field_StreetAddress2')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropColumn('inf_field_StreetAddress2');
            });
        }
    }
};
