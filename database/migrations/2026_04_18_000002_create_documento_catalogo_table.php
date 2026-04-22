<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catalogo master documenti — configurato da admin
        Schema::create('documento_catalogo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('nome');
            $table->text('descrizione')->nullable();
            $table->enum('tipo_soggetto', ['privato', 'azienda', 'entrambi'])->default('entrambi');
            $table->json('sezioni_collegate'); // ['noleggio','sinistro','riparazione',...]
            $table->boolean('richiede_firma')->default(false);
            $table->boolean('richiede_upload')->default(false);
            $table->enum('modalita_firma', ['self_hosted', 'provider_esterno', 'entrambi'])->default('self_hosted');
            $table->text('template_testo')->nullable(); // testo documento generabile
            $table->boolean('obbligatorio_default')->default(false);
            $table->integer('ordine')->default(0);
            $table->boolean('attivo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_catalogo');
    }
};