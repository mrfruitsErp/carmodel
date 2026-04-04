<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('quote_number', 50);
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['bozza','inviato','accettato','rifiutato','scaduto'])->default('bozza');
            $table->enum('job_type', ['carrozzeria','meccanica','detailing','tagliando','gomme','altro'])->default('carrozzeria');
            $table->text('description')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(22);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('converted_to_job_id')->nullable(); // FK aggiunta dopo
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'quote_number']);
        });
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['manodopera','ricambio','materiale','servizio','altro'])->default('manodopera');
            $table->string('description', 500);
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
        });
    }
    public function down(): void {
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};