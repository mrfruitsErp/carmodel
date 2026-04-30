<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Veicolo (uno dei tre tipi)
            $table->enum('vehicle_type', ['fleet','sale','customer'])->default('fleet');
            $table->unsignedBigInteger('fleet_vehicle_id')->nullable();
            $table->unsignedBigInteger('sale_vehicle_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable(); // veicolo cliente

            // Tipo movimento
            $table->enum('tipo', [
                'ritiro_cliente','consegna_cliente','trasferimento',
                'revisione','collaudo','perizia','noleggio',
                'sostitutiva','manutenzione','altro'
            ])->default('trasferimento');

            // Date/ora
            $table->dateTime('data_inizio');
            $table->dateTime('data_fine')->nullable();

            // Luoghi
            $table->string('luogo_partenza')->nullable();
            $table->string('indirizzo_partenza')->nullable();
            $table->string('luogo_arrivo')->nullable();
            $table->string('indirizzo_arrivo')->nullable();

            // Persone
            $table->foreignId('cliente_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('operatore_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('autista_id')->nullable()->constrained('users')->nullOnDelete();

            // Stato
            $table->enum('stato', ['programmato','in_corso','completato','annullato'])->default('programmato');

            // Collegamento ad altre entità
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->unsignedBigInteger('claim_id')->nullable();
            $table->unsignedBigInteger('fascicolo_id')->nullable();

            // Extra
            $table->string('titolo')->nullable();
            $table->text('note')->nullable();
            $table->integer('km_partenza')->nullable();
            $table->integer('km_arrivo')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id','data_inizio']);
            $table->index(['tenant_id','stato']);
            $table->index('fleet_vehicle_id');
            $table->index('sale_vehicle_id');
            $table->index('vehicle_id');
        });
    }

    public function down(): void { Schema::dropIfExists('vehicle_movements'); }
};
