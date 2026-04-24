<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', [
                'libretto','polizza','revisione','bollo',
                'cid','perizia','atto_vendita','altro'
            ]);
            $table->string('nome')->nullable();
            $table->date('data_emissione')->nullable();
            $table->date('data_scadenza')->nullable();
            $table->text('note')->nullable();
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['vehicle_id','tipo']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('vehicle_documents');
    }
};