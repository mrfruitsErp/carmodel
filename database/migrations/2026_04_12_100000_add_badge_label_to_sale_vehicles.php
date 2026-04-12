<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->string('badge_label', 40)->nullable()->after('price_negotiable');
        });
    }

    public function down(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->dropColumn('badge_label');
        });
    }
};