<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fleet_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('plate', 20);
            $table->string('vin', 17)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('color', 50)->nullable();
            $table->enum('fuel_type', ['benzina','diesel','elettrico','ibrido','altro'])->nullable();
            $table->enum('category', ['A','B','C','D','E','F'])->default('B');
            $table->unsignedTinyInteger('seats')->default(5);
            $table->unsignedInteger('km_current')->default(0);
            $table->unsignedInteger('km_last_service')->default(0);
            $table->date('revision_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('insurance_company', 100)->nullable();
            $table->string('insurance_policy', 50)->nullable();
            $table->enum('status', ['disponibile','noleggiato','sostitutiva','manutenzione','dismissione'])->default('disponibile');
            $table->decimal('daily_rate', 8, 2)->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'plate']);
        });
    }
    public function down(): void { Schema::dropIfExists('fleet_vehicles'); }
};
