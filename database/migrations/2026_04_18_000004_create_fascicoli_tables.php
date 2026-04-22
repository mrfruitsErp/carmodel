<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fascicoli')) {
            Schema::create('fascicoli', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('cliente_id')->index();
                $table->unsignedBigInteger('operatore_id')->nullable();
                $table->enum('tipo_pratica', ['noleggio','sinistro','riparazione','perizia','auto_sostitutiva','lesioni_personali','vendita_auto','altro'])->default('noleggio');
                $table->enum('stato', ['bozza','link_inviato','gdpr_accettato','in_compilazione','completato','verificato','archiviato'])->default('bozza');
                $table->string('titolo')->nullable();
                $table->text('note')->nullable();
                $table->nullableMorphs('pratica');
                $table->date('data_inizio')->nullable();
                $table->date('data_fine')->nullable();
                $table->string('riferimento_veicolo')->nullable();
                $table->timestamp('completato_il')->nullable();
                $table->timestamp('notifica_operatore_il')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('cliente_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('fascicolo_token')) {
            Schema::create('fascicolo_token', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('fascicolo_id')->index();
                $table->unsignedBigInteger('referente_id')->nullable();
                $table->string('token', 64)->unique();
                $table->timestamp('scadenza')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->boolean('attivo')->default(true);
                $table->timestamp('gdpr_accettato_il')->nullable();
                $table->string('gdpr_ip', 45)->nullable();
                $table->string('gdpr_versione', 20)->nullable();
                $table->string('otp_code', 255)->nullable();
                $table->timestamp('otp_scadenza')->nullable();
                $table->integer('otp_tentativi')->default(0);
                $table->timestamp('otp_verificato_il')->nullable();
                $table->timestamps();
                $table->foreign('fascicolo_id')->references('id')->on('fascicoli')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('fascicolo_documenti')) {
            Schema::create('fascicolo_documenti', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('fascicolo_id')->index();
                $table->unsignedBigInteger('catalogo_id')->nullable();
                $table->string('nome');
                $table->boolean('obbligatorio')->default(false);
                $table->boolean('richiede_firma')->default(false);
                $table->boolean('richiede_upload')->default(false);
                $table->enum('modalita_firma', ['self_hosted','provider_esterno','cartacea'])->default('self_hosted');
                $table->enum('stato', ['richiesto','caricato','firmato','verificato','rifiutato'])->default('richiesto');
                $table->string('firma_otp', 255)->nullable();
                $table->timestamp('firma_otp_scadenza')->nullable();
                $table->timestamp('firmato_il')->nullable();
                $table->string('firmato_da_nome')->nullable();
                $table->string('firmato_da_ip', 45)->nullable();
                $table->string('firmato_da_user_agent')->nullable();
                $table->timestamp('caricato_il')->nullable();
                $table->text('note_operatore')->nullable();
                $table->text('note_cliente')->nullable();
                $table->integer('ordine')->default(0);
                $table->timestamps();
                $table->foreign('fascicolo_id')->references('id')->on('fascicoli')->onDelete('cascade');
                $table->foreign('catalogo_id')->references('id')->on('documento_catalogo')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fascicolo_documenti');
        Schema::dropIfExists('fascicolo_token');
        Schema::dropIfExists('fascicoli');
    }
};