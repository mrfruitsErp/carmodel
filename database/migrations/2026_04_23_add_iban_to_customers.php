<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('codice_cliente', 20)->nullable()->after('id');
            $table->string('iban', 34)->nullable()->after('notes');
            $table->string('intestatario_iban', 255)->nullable()->after('iban');
        });
    }
    public function down(): void {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['codice_cliente', 'iban', 'intestatario_iban']);
        });
    }
};