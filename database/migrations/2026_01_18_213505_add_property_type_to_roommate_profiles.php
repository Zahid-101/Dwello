<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('roommate_profiles', function (Blueprint $table) {
            $table->string('preferred_property_type')->nullable()->after('preferred_city'); // room, apartment, house
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roommate_profiles', function (Blueprint $table) {
            $table->dropColumn('preferred_property_type');
        });
    }
};
