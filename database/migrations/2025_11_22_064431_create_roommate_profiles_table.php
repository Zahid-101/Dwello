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
        Schema::create('roommate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();

            $table->string('display_name');
            $table->unsignedTinyInteger('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();

            $table->string('preferred_city')->nullable();
            $table->string('preferred_location')->nullable();

            $table->date('move_in_date')->nullable();

            $table->boolean('is_smoker')->default(false);
            $table->boolean('has_pets')->default(false);

            $table->text('bio')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roommate_profiles');
    }
};
