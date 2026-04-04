<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('claim_number', 50);
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('insurance_company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expert_id')->nullable()->constrained()->nullOnDelete();
            // Tipo
            $table->enum('claim_type', ['rca','kasko','grandine','furto','incendio','altro'])->default('rca');
            // Evento
            $table->date('event_date');
            $table->string('event_location')->nullable();
            $table->text('event_description')->nullable();
            // Controparte
            $table->string('counterpart_plate', 20)->nullable();
            $table->string('counterpart_insurance', 100)->nullable();
            $table->string('counterpart_policy', 50)->nullable();
            // Polizza
            $table->string('policy_number', 50)->nullable();
            $table->date('policy_expiry')->nullable();
            // CID
            $table->boolean('cid_signed')->default(false);
            $table->date('cid_date')->nullable();
            $table->date('cid_expiry')->nullable();
            // Stato
            $table->enum('status', [
                'aperto','cid_presentato','perizia_attesa','perizia_effettuata',
                'in_riparazione','riparazione_completata','liquidazione_attesa',
                'liquidato','contestato','chiuso','archiviato'
            ])->default('aperto');
            // Importi
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->date('paid_date')->nullable();
            // Perizia
            $table->date('survey_date')->nullable();
            $table->text('survey_notes')->nullable();
            // Note
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'claim_number']);
            $table->index('status');
            $table->index('customer_id');
            $table->index('cid_expiry');
        });
    }
    public function down(): void { Schema::dropIfExists('claims'); }
};
