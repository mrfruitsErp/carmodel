<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->string('codice_fiscale', 20)->nullable()->after('code');
            $table->string('piva', 30)->nullable()->after('codice_fiscale');
            $table->string('referente', 100)->nullable()->after('piva');
            $table->string('referente_email')->nullable()->after('referente');
            $table->string('referente_phone', 30)->nullable()->after('referente_email');
            $table->string('pec', 150)->nullable()->after('referente_phone');
            $table->string('codice_sdi', 10)->nullable()->after('pec');
        });
    }
    public function down(): void {
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->dropColumn(['codice_fiscale','piva','referente','referente_email','referente_phone','pec','codice_sdi']);
        });
    }
};