<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Fix claims: vehicle_id e event_date nullable per import Wincar
        Schema::table('claims', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->change();
            $table->date('event_date')->nullable()->change();
        });

        // Fix experts: aggiunge tipo 'legale' per import avvocati Wincar
        Schema::table('experts', function (Blueprint $table) {
            $table->enum('type', ['perito','avvocato','medico_legale','consulente','legale','liquidatore'])->change();
        });

        // Fix customers: address più lunga per dati Wincar
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address', 1000)->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('claims', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable(false)->change();
            $table->date('event_date')->nullable(false)->change();
        });
        Schema::table('experts', function (Blueprint $table) {
            $table->enum('type', ['perito','avvocato','medico_legale','consulente'])->change();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address', 255)->nullable()->change();
        });
    }
};
