<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('payment_intent_id')->nullable()->after('payment_status');
            $table->decimal('amount', 8, 2)->nullable()->after('payment_intent_id');
        });
    }

    public function down() {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_intent_id', 'amount']);
        });
    }
};