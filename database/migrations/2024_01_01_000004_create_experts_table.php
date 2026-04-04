<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['perito','avvocato','medico_legale','consulente']);
            $table->string('name');
            $table->string('title', 50)->nullable();
            $table->string('company_name')->nullable();
            $table->foreignId('insurance_company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('phone2', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('fiscal_code', 20)->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->unsignedTinyInteger('rating')->default(3);
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'type']);
        });
    }
    public function down(): void { Schema::dropIfExists('experts'); }
};
