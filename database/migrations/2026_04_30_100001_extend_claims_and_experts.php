<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Aggiungi tipi mancanti agli esperti ──────────────────────────────
        Schema::table('experts', function (Blueprint $table) {
            // Modifica enum per aggiungere nuovi tipi
            \DB::statement("ALTER TABLE experts MODIFY type ENUM(
                'perito','avvocato','legale','liquidatore','medico_legale',
                'consulente','carrozziere','officina','soccorso_stradale','altro'
            ) NOT NULL");

            if (!Schema::hasColumn('experts', 'orario_disponibilita')) {
                $table->string('orario_disponibilita')->nullable()->after('phone2');
            }
        });

        // ── Aggiungi campi mancanti ai sinistri ─────────────────────────────
        Schema::table('claims', function (Blueprint $table) {
            // Liquidatore (esperto di tipo liquidatore)
            if (!Schema::hasColumn('claims', 'liquidatore_id')) {
                $table->foreignId('liquidatore_id')->nullable()->after('expert_id')
                    ->constrained('experts')->nullOnDelete();
            }
            // N° sinistro compagnia assicurativa
            if (!Schema::hasColumn('claims', 'numero_sinistro_compagnia')) {
                $table->string('numero_sinistro_compagnia', 100)->nullable()->after('claim_number');
            }
            // Importi dettagliati
            if (!Schema::hasColumn('claims', 'importo_richiesto')) {
                $table->decimal('importo_richiesto', 10, 2)->nullable()->after('estimated_amount');
            }
            if (!Schema::hasColumn('claims', 'importo_concordato')) {
                $table->decimal('importo_concordato', 10, 2)->nullable()->after('importo_richiesto');
            }
            if (!Schema::hasColumn('claims', 'importo_perizia')) {
                $table->decimal('importo_perizia', 10, 2)->nullable()->after('importo_concordato');
            }
            // Costo orario perizia
            if (!Schema::hasColumn('claims', 'costo_ora_mo')) {
                $table->decimal('costo_ora_mo', 8, 2)->nullable()->after('importo_perizia');
            }
            if (!Schema::hasColumn('claims', 'costo_ora_materiali')) {
                $table->decimal('costo_ora_materiali', 8, 2)->nullable()->after('costo_ora_mo');
            }
            if (!Schema::hasColumn('claims', 'ore_lavoro')) {
                $table->decimal('ore_lavoro', 8, 2)->nullable()->after('costo_ora_materiali');
            }
            // Noleggio e traino
            if (!Schema::hasColumn('claims', 'noleggio_importo')) {
                $table->decimal('noleggio_importo', 10, 2)->nullable()->after('ore_lavoro');
            }
            if (!Schema::hasColumn('claims', 'noleggio_giorni')) {
                $table->unsignedSmallInteger('noleggio_giorni')->nullable()->after('noleggio_importo');
            }
            if (!Schema::hasColumn('claims', 'traino_importo')) {
                $table->decimal('traino_importo', 10, 2)->nullable()->after('noleggio_giorni');
            }
            // Fermo tecnico
            if (!Schema::hasColumn('claims', 'fermo_tecnico_giorni')) {
                $table->unsignedSmallInteger('fermo_tecnico_giorni')->nullable()->after('traino_importo');
            }
            if (!Schema::hasColumn('claims', 'fermo_tecnico_importo')) {
                $table->decimal('fermo_tecnico_importo', 10, 2)->nullable()->after('fermo_tecnico_giorni');
            }
            // Onorari
            if (!Schema::hasColumn('claims', 'onorario_percentuale')) {
                $table->decimal('onorario_percentuale', 5, 2)->nullable()->after('fermo_tecnico_importo');
            }
            // Recupero IVA
            if (!Schema::hasColumn('claims', 'recupera_iva')) {
                $table->boolean('recupera_iva')->default(false)->after('onorario_percentuale');
            }
            // Codice fiscale danneggiato
            if (!Schema::hasColumn('claims', 'danneggiato_cf')) {
                $table->string('danneggiato_cf', 20)->nullable()->after('counterpart_plate');
            }
            // Concordato SI/NO
            if (!Schema::hasColumn('claims', 'concordato')) {
                $table->boolean('concordato')->nullable()->after('importo_concordato');
            }
            // Scadenze sinistro (10/35/60 gg)
            if (!Schema::hasColumn('claims', 'scadenza_nomina_perito')) {
                $table->date('scadenza_nomina_perito')->nullable()->after('cid_expiry');
            }
            if (!Schema::hasColumn('claims', 'scadenza_chiusura_perito')) {
                $table->date('scadenza_chiusura_perito')->nullable()->after('scadenza_nomina_perito');
            }
            if (!Schema::hasColumn('claims', 'scadenza_chiusura_totale')) {
                $table->date('scadenza_chiusura_totale')->nullable()->after('scadenza_chiusura_perito');
            }
            // Valore commerciale veicolo
            if (!Schema::hasColumn('claims', 'valore_commerciale')) {
                $table->decimal('valore_commerciale', 10, 2)->nullable()->after('scadenza_chiusura_totale');
            }
            // Riferimento gestore / intermediario
            if (!Schema::hasColumn('claims', 'riferimento_gestore')) {
                $table->string('riferimento_gestore', 200)->nullable()->after('valore_commerciale');
            }
            // IBAN per bonifico liquidazione
            if (!Schema::hasColumn('claims', 'iban_liquidazione')) {
                $table->string('iban_liquidazione', 50)->nullable()->after('riferimento_gestore');
            }
            // Beneficiario bonifica (es. Ellezeta SNC)
            if (!Schema::hasColumn('claims', 'beneficiario_liquidazione')) {
                $table->string('beneficiario_liquidazione', 200)->nullable()->after('iban_liquidazione');
            }
        });

        // ── Diario comunicazioni sinistro ────────────────────────────────────
        if (!Schema::hasTable('claim_diary')) {
            Schema::create('claim_diary', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('claim_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->date('data_evento');
                $table->enum('tipo', ['nota','chiamata','mail_inviata','mail_ricevuta','pec_inviata','pec_ricevuta','incontro','sollecito','pagamento','altro'])->default('nota');
                $table->string('oggetto', 300)->nullable();
                $table->text('testo');
                $table->decimal('importo', 10, 2)->nullable(); // per voci economiche
                $table->timestamps();
                $table->index(['claim_id', 'data_evento']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_diary');

        Schema::table('claims', function (Blueprint $table) {
            $cols = ['liquidatore_id','numero_sinistro_compagnia','importo_richiesto',
                'importo_concordato','importo_perizia','costo_ora_mo','costo_ora_materiali',
                'ore_lavoro','noleggio_importo','noleggio_giorni','traino_importo',
                'fermo_tecnico_giorni','fermo_tecnico_importo','onorario_percentuale',
                'recupera_iva','danneggiato_cf','concordato','scadenza_nomina_perito',
                'scadenza_chiusura_perito','scadenza_chiusura_totale','valore_commerciale',
                'riferimento_gestore','iban_liquidazione','beneficiario_liquidazione'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('claims', $col)) $table->dropColumn($col);
            }
        });

        Schema::table('experts', function (Blueprint $table) {
            if (Schema::hasColumn('experts', 'orario_disponibilita')) {
                $table->dropColumn('orario_disponibilita');
            }
        });
    }
};
