<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── SINISTRI (pratiche carrozzeria) ──────────────────────────────────
        Schema::create('sinistri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('customer_id')->nullable(); // proprietario veicolo
            $table->unsignedBigInteger('vehicle_id')->nullable();  // veicolo danneggiato

            // Identificativi
            $table->string('numero_pratica', 30)->nullable();      // F_NUMPRA Wincar
            $table->string('numero_sinistro', 30)->nullable();     // F_NUMSIN numero sinistro assicurazione
            $table->date('data_sinistro')->nullable();             // F_DATACA data accadimento
            $table->date('data_ingresso')->nullable();             // data ingresso in officina
            $table->date('data_uscita')->nullable();               // data uscita prevista/effettiva

            // Veicolo (dati al momento del sinistro, potrebbero differire dal veicolo)
            $table->string('targa', 15)->nullable();               // F_TARGAV
            $table->string('marca', 50)->nullable();               // F_DESMAR
            $table->string('modello', 100)->nullable();            // F_DESMOD
            $table->string('versione', 100)->nullable();           // F_DESVER
            $table->string('colore', 30)->nullable();              // F_DESCOL
            $table->string('telaio', 25)->nullable();              // F_TELAIO
            $table->integer('km')->nullable();                     // F_KIMVEI

            // Assicurazione proprietario
            $table->unsignedBigInteger('insurance_company_id')->nullable();
            $table->string('compagnia_assicurazione', 100)->nullable(); // F_DEASCL
            $table->string('numero_polizza', 50)->nullable();           // F_NUMPOL

            // Compagnia controparte
            $table->string('compagnia_controparte', 100)->nullable();   // F_DEASCO
            $table->string('targa_controparte', 15)->nullable();        // F_TARCON
            $table->string('veicolo_controparte', 100)->nullable();     // F_MACCON
            $table->string('conducente_controparte', 100)->nullable();  // F_NOMECO

            // Tipo e stato
            $table->string('tipo_sinistro', 5)->nullable();        // F_TIPSIN: R=RCA, K=Kasko, ecc
            $table->string('stato', 50)->default('aperto');        // aperto, in_lavorazione, chiuso, sospeso
            $table->unsignedBigInteger('stato_wincar_id')->nullable(); // FK a StatiPratica Wincar

            // Perito
            $table->unsignedBigInteger('expert_id')->nullable();   // perito assegnato
            $table->string('perito_nome', 100)->nullable();        // nome perito (cache)

            // Importi preventivo (da TESPRE)
            $table->decimal('importo_manodopera', 10, 2)->default(0);  // F_COSTOR
            $table->decimal('importo_ricambi', 10, 2)->default(0);     // F_TOTRIC
            $table->decimal('importo_materiali', 10, 2)->default(0);   // F_TOTMAT
            $table->decimal('importo_totale', 10, 2)->default(0);      // totale preventivo
            $table->decimal('importo_liquidato', 10, 2)->default(0);   // F_LIQUID da Pratica2
            $table->boolean('iva_inclusa')->default(false);             // F_CONIVA

            // Note e descrizione danno
            $table->text('descrizione_danno')->nullable();         // F_DESPER da TESPRE
            $table->text('note')->nullable();                      // F_NOTE da TESPRE

            // Flag lavorazione
            $table->boolean('ha_lesioni')->default(false);
            $table->boolean('ha_auto_sostitutiva')->default(false);
            $table->integer('foto_count')->default(0);             // F_FOTOSI

            // Wincar reference
            $table->integer('wincar_id')->nullable()->index();     // F_NUMPRA originale

            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('customer_id');
            $table->index('vehicle_id');
            $table->index(['tenant_id', 'numero_pratica']);
            $table->index(['tenant_id', 'targa']);
            $table->index(['tenant_id', 'stato']);
        });

        // ── RIGHE PREVENTIVO ─────────────────────────────────────────────────
        Schema::create('sinistri_righe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('sinistro_id');

            $table->string('tipo_riga', 5)->nullable();            // F_TIPRIG: MO=manodopera, RC=ricambi, ecc
            $table->integer('ordine')->default(0);                 // F_ORDINE
            $table->string('codice_articolo', 20)->nullable();     // F_CODART
            $table->string('descrizione', 100)->nullable();        // F_DESART
            $table->string('posizione', 10)->nullable();           // F_POSIZ (es. ANT, POST)
            $table->decimal('quantita', 8, 2)->default(1);        // F_QUANTI
            $table->decimal('prezzo', 10, 2)->default(0);         // F_PREZZO
            $table->decimal('sconto', 5, 2)->default(0);          // F_SCONTO
            $table->decimal('tempo_sr', 6, 2)->default(0);        // F_TEMPSR (scocca/ricambi)
            $table->decimal('tempo_la', 6, 2)->default(0);        // F_TEMPLA (laccatura)
            $table->decimal('tempo_ve', 6, 2)->default(0);        // F_TEMPVE (verniciatura)
            $table->decimal('tempo_me', 6, 2)->default(0);        // F_TEMPME (meccanica)
            $table->string('tipo_ricambio', 5)->nullable();        // F_TIPRIC: O=originale, A=alternativo

            $table->timestamps();

            $table->index(['tenant_id', 'sinistro_id']);
            $table->foreign('sinistro_id')->references('id')->on('sinistri')->onDelete('cascade');
        });

        // ── LESIONI PERSONALI ─────────────────────────────────────────────────
        Schema::create('lesioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('sinistro_id');

            $table->string('nome', 100)->nullable();               // F_LES___NOME
            $table->string('indirizzo', 100)->nullable();          // F_LES_INDIRI
            $table->string('citta', 50)->nullable();               // F_LES__CITTA
            $table->string('cap', 5)->nullable();                  // F_LES____CAP
            $table->string('provincia', 2)->nullable();            // F_LES_PROVIN
            $table->string('telefono', 50)->nullable();            // F_LES___TEL1
            $table->string('telefono2', 50)->nullable();           // F_LES___TEL2
            $table->string('email', 255)->nullable();              // F_LES__EMAIL
            $table->string('professione', 50)->nullable();         // F_LES_PROFES

            // Dati medici
            $table->date('data_referto')->nullable();              // F_LES_REFERT
            $table->integer('giorni_referto')->nullable();         // F_LES_GGREFE
            $table->date('data_guarigione')->nullable();           // F_LES_DATGUA
            $table->integer('giorni_temporanea')->nullable();      // F_LES_GGTEMP
            $table->boolean('postumi')->default(false);            // F_LES_POSTUM
            $table->integer('percentuale_postumi')->nullable();    // F_LES_PERPOS
            $table->string('ospedale', 100)->nullable();           // F_LES_RICOVE
            $table->date('ricovero_dal')->nullable();              // F_LES_RICDAL
            $table->date('ricovero_al')->nullable();               // F_LES__RICAL
            $table->boolean('medico_legale')->default(false);      // F_LES_MEDLEG
            $table->string('nome_medico', 100)->nullable();        // F_LES_NOMMED

            // Importi
            $table->decimal('totale_spese', 10, 2)->default(0);   // F_LES_TOTSPE
            $table->decimal('importo_offerta', 10, 2)->default(0); // F_LES_IMPOFF
            $table->decimal('importo_concordato', 10, 2)->default(0); // F_LES_IMPCON

            // Stato
            $table->string('stato', 50)->nullable();               // F_LES__STATO
            $table->text('note')->nullable();                      // F_LES___NOTE

            // Wincar reference
            $table->integer('wincar_id')->nullable();              // F_LES_CODLES

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'sinistro_id']);
            $table->foreign('sinistro_id')->references('id')->on('sinistri')->onDelete('cascade');
        });

        // ── AUTO SOSTITUTIVE (per sinistro) ───────────────────────────────────
        Schema::create('sinistri_auto_sostitutive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('sinistro_id');

            $table->string('targa', 15)->nullable();               // F_SOS__TARGA
            $table->string('marca_modello', 255)->nullable();      // F_SOS_MARMOD
            $table->string('telaio', 17)->nullable();              // F_SOS_TELAIO
            $table->string('gruppo', 10)->nullable();              // F_SOS_GRUPPO
            $table->date('data_inizio')->nullable();               // F_SOS_DATINI
            $table->date('data_fine')->nullable();                 // F_SOS_DATFIN
            $table->integer('km_inizio')->nullable();              // F_SOS_KIMINI
            $table->integer('km_fine')->nullable();                // F_SOS_KIMFIN
            $table->decimal('costo', 10, 2)->default(0);          // F_SOS__COSTO
            $table->string('numero_noleggio', 30)->nullable();     // F_SOS_NUMNOL
            $table->string('autorizzazione', 30)->nullable();      // F_SOS_AUTORI
            $table->string('conducente', 100)->nullable();         // F_SOS_NOMGUI
            $table->string('fornitore', 100)->nullable();          // F_SOS_FORNIT
            $table->text('motivo')->nullable();                    // F_SOS_MOTIVO

            // Wincar reference
            $table->integer('wincar_id')->nullable();              // F_SOS_CODSOS

            $table->timestamps();

            $table->index(['tenant_id', 'sinistro_id']);
            $table->foreign('sinistro_id')->references('id')->on('sinistri')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinistri_auto_sostitutive');
        Schema::dropIfExists('lesioni');
        Schema::dropIfExists('sinistri_righe');
        Schema::dropIfExists('sinistri');
    }
};