<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabella clienti si chiama 'customers' nel DB
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'tipo_soggetto')) {
                $table->enum('tipo_soggetto', ['privato', 'azienda', 'impresa_individuale'])
                    ->default('privato')->after('id');
            }
            if (!Schema::hasColumn('customers', 'codice_fiscale')) {
                $table->string('codice_fiscale', 20)->nullable()->after('tipo_soggetto');
            }
            if (!Schema::hasColumn('customers', 'partita_iva')) {
                $table->string('partita_iva', 20)->nullable()->after('codice_fiscale');
            }
            if (!Schema::hasColumn('customers', 'pec')) {
                $table->string('pec', 150)->nullable();
            }
            if (!Schema::hasColumn('customers', 'codice_sdi')) {
                $table->string('codice_sdi', 10)->nullable();
            }
            if (!Schema::hasColumn('customers', 'ragione_sociale')) {
                $table->string('ragione_sociale')->nullable();
            }
        });

        // Tabella referenti aziendali
        if (!Schema::hasTable('cliente_referenti')) {
            Schema::create('cliente_referenti', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('cliente_id')->index();
                $table->string('nome');
                $table->string('cognome')->nullable();
                $table->string('email')->nullable();
                $table->string('telefono', 30)->nullable();
                $table->string('ruolo')->nullable();
                $table->boolean('is_principale')->default(false);
                $table->boolean('can_upload')->default(true);
                $table->json('sezioni_visibili')->nullable();
                $table->string('token_accesso', 64)->nullable()->unique();
                $table->string('codice_identificativo', 20)->nullable();
                $table->unsignedBigInteger('autorizzato_da')->nullable();
                $table->timestamp('autorizzato_il')->nullable();
                $table->boolean('attivo')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('cliente_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_referenti');
        Schema::table('customers', function (Blueprint $table) {
            $cols = ['tipo_soggetto','codice_fiscale','partita_iva','pec','codice_sdi','ragione_sociale'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};