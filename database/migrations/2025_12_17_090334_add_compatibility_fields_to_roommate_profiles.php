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
        Schema::table('roommate_profiles', function (Blueprint $table) {
            // Preferences / Deal-breakers
            $table->boolean('pref_no_smoker')->default(0);
            $table->boolean('pref_pets_ok')->default(1);
            $table->boolean('pref_same_gender_only')->default(0);
            $table->boolean('pref_visitors_ok')->default(1);
            $table->boolean('pref_substance_free_required')->default(0);
            $table->boolean('uses_substances')->default(0);

            // Lifestyle scales (1-5)
            $table->unsignedTinyInteger('cleanliness')->nullable();
            $table->unsignedTinyInteger('noise_tolerance')->nullable();
            $table->unsignedTinyInteger('sleep_schedule')->nullable();
            $table->unsignedTinyInteger('study_focus')->nullable();
            $table->unsignedTinyInteger('social_level')->nullable();

            // Meta
            $table->enum('schedule_type', ['morning', 'night', 'mixed'])->nullable();
            $table->string('occupation_field')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roommate_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'pref_no_smoker',
                'pref_pets_ok',
                'pref_same_gender_only',
                'pref_visitors_ok',
                'pref_substance_free_required',
                'uses_substances',
                'cleanliness',
                'noise_tolerance',
                'sleep_schedule',
                'study_focus',
                'social_level',
                'schedule_type',
                'occupation_field',
            ]);
        });
    }
};
