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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('reviewee_id');
            $table->decimal('rating', 2, 1); // Allows ratings like 3.5, 4.0, etc.
            $table->text('comment')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewee_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent duplicate reviews for the same booking by the same reviewer
            $table->unique(['booking_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};