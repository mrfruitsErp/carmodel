<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fascicoli', function (Blueprint $table) {
            $table->unsignedBigInteger('fleet_vehicle_id')->nullable()->after('riferimento_veicolo');
            $table->unsignedBigInteger('sale_vehicle_id')->nullable()->after('fleet_vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::table('fascicoli', function (Blueprint $table) {
            $table->dropColumn(['fleet_vehicle_id', 'sale_vehicle_id']);
        });
    }
};
