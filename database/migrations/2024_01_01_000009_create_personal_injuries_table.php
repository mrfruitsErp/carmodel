<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personal_injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('injury_number', 50);
            $table->foreignId('claim_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('lawyer_id')->nullable()->constrained('experts')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('experts')->nullOnDelete();
            $table->string('injury_type')->nullable();
            $table->text('injury_description')->nullable();
            $table->string('icd_code', 20)->nullable();
            $table->enum('status', [
                'aperta','visita_medica','perizia_medica','trattativa',
                'accordo','liquidata','contenzioso','chiusa'
            ])->default('aperta');
            $table->date('medical_visit_date')->nullable();
            $table->date('medical_report_date')->nullable();
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->decimal('agreed_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'injury_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('personal_injuries'); }
};
