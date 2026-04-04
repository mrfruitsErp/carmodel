<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('rental_number', 50);
            $table->foreignId('fleet_vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('claim_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('rental_type', ['breve_termine','lungo_termine','sostitutiva'])->default('sostitutiva');
            $table->date('start_date');
            $table->date('expected_end_date');
            $table->date('actual_end_date')->nullable();
            $table->unsignedInteger('km_start')->default(0);
            $table->unsignedInteger('km_end')->nullable();
            $table->unsignedInteger('km_included')->nullable();
            $table->decimal('km_extra_price', 6, 2)->default(0);
            $table->decimal('daily_rate', 8, 2)->default(0);
            $table->unsignedInteger('total_days')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('extra_charges', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(22);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['prenotato','attivo','scaduto','chiuso','annullato'])->default('prenotato');
            $table->unsignedTinyInteger('fuel_level_start')->default(100);
            $table->unsignedTinyInteger('fuel_level_end')->nullable();
            $table->text('damage_notes_start')->nullable();
            $table->text('damage_notes_end')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'rental_number']);
            $table->index('status');
            $table->index('expected_end_date');
        });
    }
    public function down(): void { Schema::dropIfExists('rentals'); }
};
