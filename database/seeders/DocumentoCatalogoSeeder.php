<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentoCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;
        $now = now();

        $documenti = [
            // SEMPRE — tutte le pratiche
            [
                'nome'                => 'Informativa Privacy GDPR',
                'descrizione'         => 'Informativa trattamento dati personali ai sensi art. 13 GDPR 2016/679',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','perizia','auto_sostitutiva','lesioni_personali','vendita_auto','altro']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 1,
            ],
            [
                'nome'                => 'Documento di Identita (fronte)',
                'descrizione'         => 'Carta identita o passaporto - lato fronte',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','perizia','auto_sostitutiva','lesioni_personali','vendita_auto','altro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 2,
            ],
            [
                'nome'                => 'Documento di Identita (retro)',
                'descrizione'         => 'Carta identita - lato retro',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','perizia','auto_sostitutiva','lesioni_personali','vendita_auto','altro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 3,
            ],
            [
                'nome'                => 'Tessera Sanitaria',
                'descrizione'         => 'Tessera sanitaria / codice fiscale',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','lesioni_personali','auto_sostitutiva']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 4,
            ],
            // AZIENDE
            [
                'nome'                => 'Visura Camerale',
                'descrizione'         => 'Visura camerale aggiornata (non oltre 6 mesi)',
                'tipo_soggetto'       => 'azienda',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','vendita_auto','altro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 5,
            ],
            [
                'nome'                => 'Documento Identita Legale Rappresentante',
                'descrizione'         => 'CI o passaporto del legale rappresentante aziendale',
                'tipo_soggetto'       => 'azienda',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','vendita_auto','altro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 6,
            ],
            [
                'nome'                => 'Accordo Quadro Servizi CarModel',
                'descrizione'         => 'Contratto master per aziende clienti',
                'tipo_soggetto'       => 'azienda',
                'sezioni_collegate'   => json_encode(['noleggio','riparazione','auto_sostitutiva','vendita_auto']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 7,
            ],
            [
                'nome'                => 'Delega Operativa Referenti',
                'descrizione'         => 'Documento che autorizza i referenti aziendali ad operare per conto azienda',
                'tipo_soggetto'       => 'azienda',
                'sezioni_collegate'   => json_encode(['noleggio','sinistro','riparazione','auto_sostitutiva','vendita_auto']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 8,
            ],
            // NOLEGGIO
            [
                'nome'                => 'Patente di Guida (fronte)',
                'descrizione'         => 'Patente di guida - lato fronte',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio','auto_sostitutiva']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 10,
            ],
            [
                'nome'                => 'Patente di Guida (retro)',
                'descrizione'         => 'Patente di guida - lato retro',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio','auto_sostitutiva']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 11,
            ],
            [
                'nome'                => 'Contratto di Noleggio',
                'descrizione'         => 'Contratto firmato per il noleggio del veicolo',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 12,
            ],
            [
                'nome'                => 'Autorizzazione Addebito / Cauzione',
                'descrizione'         => 'Autorizzazione addebito carta di credito o pagamento cauzione',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 13,
            ],
            [
                'nome'                => 'Verbale Consegna Veicolo',
                'descrizione'         => 'Verbale di consegna con stato del veicolo e km',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio','auto_sostitutiva']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 14,
            ],
            [
                'nome'                => 'Verbale Restituzione Veicolo',
                'descrizione'         => 'Verbale di restituzione con stato del veicolo e km',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['noleggio','auto_sostitutiva']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 15,
            ],
            // SINISTRO
            [
                'nome'                => 'CID / Constatazione Amichevole',
                'descrizione'         => 'Modulo CID compilato e firmato da entrambe le parti',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 20,
            ],
            [
                'nome'                => 'Mandato di Gestione Sinistro',
                'descrizione'         => 'Mandato che autorizza CarModel a gestire la pratica sinistro',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 21,
            ],
            [
                'nome'                => 'Procura a Trattare con Assicurazione',
                'descrizione'         => 'Procura speciale per trattare con la compagnia assicurativa',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 22,
            ],
            [
                'nome'                => 'Cessione del Credito',
                'descrizione'         => 'Atto di cessione del credito risarcitorio',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 23,
            ],
            [
                'nome'                => 'Foto Danni Veicolo',
                'descrizione'         => 'Fotografie dei danni al veicolo (minimo 4 foto)',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro','riparazione','perizia']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 24,
            ],
            [
                'nome'                => 'Verbale Polizia / Carabinieri',
                'descrizione'         => 'Copia del verbale delle forze ordine (se presente)',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 25,
            ],
            // RIPARAZIONE
            [
                'nome'                => 'Autorizzazione alla Riparazione',
                'descrizione'         => 'Documento firmato che autorizza esecuzione dei lavori',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['riparazione']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 30,
            ],
            [
                'nome'                => 'Accettazione Preventivo',
                'descrizione'         => 'Preventivo firmato per accettazione dei costi',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['riparazione','perizia']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 31,
            ],
            [
                'nome'                => 'Libretto di Circolazione',
                'descrizione'         => 'Copia del libretto del veicolo',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['riparazione','perizia','vendita_auto']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 32,
            ],
            // LESIONI PERSONALI
            [
                'nome'                => 'Autorizzazione Raccolta Dati Sanitari',
                'descrizione'         => 'Consenso esplicito al trattamento dati sanitari (art. 9 GDPR)',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['lesioni_personali']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 40,
            ],
            [
                'nome'                => 'Referti Medici / Pronto Soccorso',
                'descrizione'         => 'Copia referti medici relativi alle lesioni riportate',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['lesioni_personali']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 41,
            ],
            [
                'nome'                => 'Mandato Legale',
                'descrizione'         => 'Procura al legale per la gestione della pratica',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['lesioni_personali']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 42,
            ],
            [
                'nome'                => 'Foto Lesioni',
                'descrizione'         => 'Fotografie delle lesioni fisiche riportate',
                'tipo_soggetto'       => 'privato',
                'sezioni_collegate'   => json_encode(['lesioni_personali']),
                'richiede_firma'      => 0,
                'richiede_upload'     => 1,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 43,
            ],
            // VENDITA AUTO
            [
                'nome'                => 'Proposta di Acquisto',
                'descrizione'         => 'Proposta acquisto firmata dal cliente',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['vendita_auto']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 50,
            ],
            [
                'nome'                => 'Contratto di Vendita',
                'descrizione'         => 'Contratto definitivo di compravendita del veicolo',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['vendita_auto']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 1,
                'ordine'              => 51,
            ],
            // TRASVERSALI
            [
                'nome'                => 'Liberatoria Fotografica',
                'descrizione'         => 'Autorizzazione utilizzo foto del veicolo/danni',
                'tipo_soggetto'       => 'entrambi',
                'sezioni_collegate'   => json_encode(['sinistro','riparazione','perizia','vendita_auto']),
                'richiede_firma'      => 1,
                'richiede_upload'     => 0,
                'modalita_firma'      => 'self_hosted',
                'obbligatorio_default'=> 0,
                'ordine'              => 60,
            ],
        ];

        $inseriti = 0;
        $saltati  = 0;

        foreach ($documenti as $doc) {
            // Controlla se esiste già per nome + tenant
            $esiste = DB::table('documento_catalogo')
                ->where('tenant_id', $tenantId)
                ->where('nome', $doc['nome'])
                ->exists();

            if (!$esiste) {
                DB::table('documento_catalogo')->insert(array_merge($doc, [
                    'tenant_id'  => $tenantId,
                    'attivo'     => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
                $inseriti++;
            } else {
                $saltati++;
            }
        }

        $this->command->info("Catalogo documenti: {$inseriti} inseriti, {$saltati} gia esistenti.");
    }
}