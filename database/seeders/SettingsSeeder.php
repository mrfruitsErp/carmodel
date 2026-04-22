<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = 1;
        $now = now();

        $settings = [
            // Generale
            ['gruppo' => 'generale', 'chiave' => 'azienda_nome',        'valore' => '',              'is_secret' => false],
            ['gruppo' => 'generale', 'chiave' => 'azienda_indirizzo',   'valore' => '',              'is_secret' => false],
            ['gruppo' => 'generale', 'chiave' => 'azienda_telefono',    'valore' => '',              'is_secret' => false],
            ['gruppo' => 'generale', 'chiave' => 'azienda_email',       'valore' => '',              'is_secret' => false],
            ['gruppo' => 'generale', 'chiave' => 'timezone',            'valore' => 'Europe/Rome',   'is_secret' => false],
            // SMS
            ['gruppo' => 'sms', 'chiave' => 'sms_provider',       'valore' => 'self_hosted', 'is_secret' => false],
            ['gruppo' => 'sms', 'chiave' => 'sms_api_key',        'valore' => '',            'is_secret' => true],
            ['gruppo' => 'sms', 'chiave' => 'sms_mittente',       'valore' => 'CarModel',    'is_secret' => false],
            ['gruppo' => 'sms', 'chiave' => 'otp_timeout_minuti', 'valore' => '10',          'is_secret' => false],
            ['gruppo' => 'sms', 'chiave' => 'otp_lunghezza',      'valore' => '6',           'is_secret' => false],
            // Fascicoli
            ['gruppo' => 'fascicoli', 'chiave' => 'link_scadenza_giorni', 'valore' => '7',                'is_secret' => false],
            ['gruppo' => 'fascicoli', 'chiave' => 'upload_max_mb',        'valore' => '10',               'is_secret' => false],
            ['gruppo' => 'fascicoli', 'chiave' => 'upload_formati',       'valore' => 'jpg,jpeg,png,pdf', 'is_secret' => false],
            ['gruppo' => 'fascicoli', 'chiave' => 'notifica_email_admin', 'valore' => '1',                'is_secret' => false],
            // Documenti
            ['gruppo' => 'documenti', 'chiave' => 'firma_modalita',        'valore' => 'self_hosted', 'is_secret' => false],
            ['gruppo' => 'documenti', 'chiave' => 'firma_provider',        'valore' => '',            'is_secret' => false],
            ['gruppo' => 'documenti', 'chiave' => 'firma_provider_key',    'valore' => '',            'is_secret' => true],
            ['gruppo' => 'documenti', 'chiave' => 'firma_cartacea_attiva', 'valore' => '1',           'is_secret' => false],
            // Notifiche
            ['gruppo' => 'notifiche', 'chiave' => 'notifica_campanellina', 'valore' => '1', 'is_secret' => false],
            ['gruppo' => 'notifiche', 'chiave' => 'notifica_email',        'valore' => '1', 'is_secret' => false],
            // Privacy
            ['gruppo' => 'privacy', 'chiave' => 'gdpr_versione', 'valore' => '1.0', 'is_secret' => false],
            ['gruppo' => 'privacy', 'chiave' => 'gdpr_testo',    'valore' => 'Ai sensi del Regolamento UE 2016/679 (GDPR) e del D.Lgs. 196/2003 come modificato dal D.Lgs. 101/2018, La informiamo che i Suoi dati personali saranno trattati per le finalità connesse alla gestione del rapporto contrattuale, alla gestione dei sinistri e delle riparazioni, nonché per adempimenti di legge. I dati non saranno ceduti a terzi senza Suo consenso, salvo obblighi di legge.', 'is_secret' => false],
            // Veicoli
            ['gruppo' => 'veicoli', 'chiave' => 'km_alert_soglia',        'valore' => '10000', 'is_secret' => false],
            ['gruppo' => 'veicoli', 'chiave' => 'revisione_alert_giorni', 'valore' => '30',    'is_secret' => false],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->insertOrIgnore([
                'tenant_id'  => $tenantId,
                'gruppo'     => $s['gruppo'],
                'chiave'     => $s['chiave'],
                'valore'     => $s['valore'],
                'is_secret'  => $s['is_secret'],
                // Colonne inglesi esistenti — le popoliamo per compatibilità
                'key'        => $s['chiave'],
                'value'      => $s['valore'],
                'group'      => $s['gruppo'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('✅ Settings inseriti: ' . count($settings) . ' voci');
    }
}