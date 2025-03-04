<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->text('description')->nullable();
            $table->string('car_model')->nullable();
            $table->string('city')->nullable();
            // You can store work days as a JSON array (e.g., ["Monday", "Tuesday",...])
            $table->json('work_days')->nullable();
            // Store work start and end times
            $table->time('work_start')->nullable();
            $table->time('work_end')->nullable();
            // Profile picture path
            $table->string('profile_picture')->nullable();
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('driver_profiles');
    }
};
