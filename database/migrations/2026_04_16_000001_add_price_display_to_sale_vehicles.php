<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->boolean('price_visible')->default(true)->after('asking_price');
            $table->string('price_label', 60)->nullable()->after('price_visible');
        });
    }

    public function down(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->dropColumn(['price_visible', 'price_label']);
        });
    }
};