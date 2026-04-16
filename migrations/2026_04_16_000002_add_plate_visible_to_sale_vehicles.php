<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->boolean('plate_visible')->default(true)->after('plate');
        });
    }

    public function down(): void
    {
        Schema::table('sale_vehicles', function (Blueprint $table) {
            $table->dropColumn('plate_visible');
        });
    }
};