<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('pickup_place')->nullable()->after('pickup_time');
            $table->string('destination')->nullable()->after('pickup_place');
        });
    }
    
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pickup_place', 'destination']);
        });
    }
};
