<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->string('plate', 20);
            $table->string('vin', 17)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('version', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('color', 50)->nullable();
            $table->enum('fuel_type', ['benzina','diesel','gpl','metano','elettrico','ibrido','altro'])->nullable();
            $table->unsignedInteger('km_current')->nullable();
            // Assicurazione
            $table->string('insurance_company', 100)->nullable();
            $table->string('insurance_policy', 50)->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('revision_expiry')->nullable();
            // Stato
            $table->enum('status', ['in_officina','pronto','consegnato','noleggio','sostitutiva','fermo'])->default('fermo');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'plate']);
            $table->index('customer_id');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicles'); }
};
