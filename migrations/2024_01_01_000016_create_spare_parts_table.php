<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 100)->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->enum('unit', ['pz','lt','kg','ml','m','set','altro'])->default('pz');
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->string('location', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
        });
        Schema::create('spare_part_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained()->restrictOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('movement_type', ['carico','scarico','reso','inventario']);
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('notes', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void {
        Schema::dropIfExists('spare_part_movements');
        Schema::dropIfExists('spare_parts');
    }
};
