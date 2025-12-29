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
        Schema::table('properties', function (Blueprint $table) {
            $table->index('city');
            $table->index('monthly_rent');
            $table->index('property_type');
        });

        Schema::table('roommate_profiles', function (Blueprint $table) {
            $table->index('preferred_city');
            $table->index('budget_min');
            $table->index('budget_max');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['monthly_rent']);
            $table->dropIndex(['property_type']);
        });

        Schema::table('roommate_profiles', function (Blueprint $table) {
            $table->dropIndex(['preferred_city']);
            $table->dropIndex(['budget_min']);
            $table->dropIndex(['budget_max']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
