<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('document_number', 50);
            $table->enum('document_type', ['fattura','ddt','nota_credito','ricevuta','proforma']);
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rental_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(22);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_status', ['da_pagare','pagata','parziale','scaduta','stornata'])->default('da_pagare');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['contanti','bonifico','carta','assegno','altro'])->nullable();
            $table->enum('sdi_status', ['da_inviare','inviata','accettata','rifiutata','non_applicabile'])->default('non_applicabile');
            $table->string('sdi_id', 100)->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'document_number']);
            $table->index('payment_status');
        });
        Schema::create('document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->string('description', 500);
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('vat_percent', 5, 2)->default(22);
            $table->decimal('total_price', 10, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
        });
    }
    public function down(): void {
        Schema::dropIfExists('document_items');
        Schema::dropIfExists('documents');
    }
};
