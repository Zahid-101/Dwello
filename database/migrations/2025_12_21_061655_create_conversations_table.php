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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['property', 'roommate']);
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');

            // Unique indexes to prevent duplicate conversations
            // Property conv: unique(type, property_id, user_one_id, user_two_id)
            $table->unique(['type', 'property_id', 'user_one_id', 'user_two_id'], 'conversations_property_unique');

            // Roommate conv: unique(type, user_one_id, user_two_id) where property_id is null
            // MySQL unique index ignores NULLs, so for strict uniqueness we can rely on application logic 
            // or just add a composite index. 
            // Letting application logic handle "one roommate chat per pair" is safer across DB engines,
            // but we can add a specific index on (type, user_one_id, user_two_id) too.
            $table->index(['type', 'user_one_id', 'user_two_id']);

            // Efficient sorting/lookup
            $table->index(['user_one_id', 'last_message_at']);
            $table->index(['user_two_id', 'last_message_at']);
            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
