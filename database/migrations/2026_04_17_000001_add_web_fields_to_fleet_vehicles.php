<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fleet_vehicles', function (Blueprint $table) {
            $table->boolean('web_visible')->default(false)->after('notes');
            $table->boolean('booking_enabled')->default(false)->after('web_visible');
            $table->decimal('daily_rate_public', 8, 2)->nullable()->after('booking_enabled');
            $table->string('web_description', 500)->nullable()->after('daily_rate_public');
        });
    }

    public function down(): void
    {
        Schema::table('fleet_vehicles', function (Blueprint $table) {
            $table->dropColumn(['web_visible', 'booking_enabled', 'daily_rate_public', 'web_description']);
        });
    }
};
