<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('job_number', 50);
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('job_type', ['carrozzeria','meccanica','detailing','tagliando','gomme','elettronica','altro'])->default('carrozzeria');
            $table->enum('status', ['attesa','in_lavorazione','attesa_ricambi','completato','consegnato','annullato'])->default('attesa');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->enum('priority', ['bassa','normale','alta','urgente'])->default('normale');
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->decimal('estimated_amount', 10, 2)->nullable();
            $table->decimal('actual_amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('technical_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'job_number']);
            $table->index('status');
            $table->index('vehicle_id');
            $table->index('expected_end_date');
        });
        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('work_order_items');
        Schema::dropIfExists('work_orders');
    }
};
