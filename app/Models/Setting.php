<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['tenant_id','gruppo','chiave','valore','is_secret'];
    protected $casts = ['is_secret' => 'boolean'];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check()) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        // Mirror delle colonne legacy (key/value/group) sulle nuove (chiave/valore/gruppo)
        // così la riga è sempre coerente anche se qualche query legge ancora i nomi inglesi.
        static::saving(function ($setting) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'key')) {
                $setting->setAttribute('key', $setting->chiave);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'value')) {
                $setting->setAttribute('value', $setting->valore);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'group')) {
                $setting->setAttribute('group', $setting->gruppo);
            }
        });

        static::saved(function ($setting) {
            Cache::forget("settings_{$setting->tenant_id}");
        });
    }

    public static function get(string $chiave, mixed $default = null): mixed
    {
        // Se nessun utente è loggato (sito pubblico), usa il tenant_id del primo tenant
        $tenantId = auth()->user()?->tenant_id
            ?? Cache::remember('default_tenant_id', 3600, fn() =>
                static::withoutGlobalScope('tenant')->min('tenant_id') ?? 1
            );
        $settings = Cache::remember("settings_{$tenantId}", 3600, function () use ($tenantId) {
            return static::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->pluck('valore', 'chiave');
        });
        return $settings->get($chiave, $default);
    }

    public static function set(string $chiave, mixed $valore, string $gruppo = 'generale'): void
    {
        $tenantId = auth()->user()->tenant_id;
        static::withoutGlobalScope('tenant')->updateOrCreate(
            ['tenant_id' => $tenantId, 'chiave' => $chiave],
            ['valore' => $valore, 'gruppo' => $gruppo]
        );
        Cache::forget("settings_{$tenantId}");
    }

    public static function gruppi(): array
    {
        return [
            'generale'      => 'Generale',
            'mail'          => '📧 Mail & SMTP',
            'pec'           => '📮 PEC',
            'imap'          => '📥 Ricezione Mail (IMAP)',
            'calendar'      => '📅 Calendario & Google',
            'integrazioni'  => '🔌 Integrazioni API',
            'ai'            => '🤖 Intelligenza Artificiale',
            'sms'           => 'SMS Gateway',
            'fascicoli'     => 'Fascicoli',
            'documenti'     => 'Documenti',
            'notifiche'     => 'Notifiche',
            'privacy'       => 'Privacy & GDPR',
            'veicoli'       => 'Veicoli',
            'sito_web'      => '🌐 Sito Web',
        ];
    }

    public static function defaultPerGruppo(): array
    {
        return [
            'generale' => [
                'azienda_nome'      => '',
                'azienda_indirizzo' => '',
                'azienda_telefono'  => '',
                'azienda_email'     => '',
                'azienda_pec'       => '',
                'azienda_piva'      => '',
                'timezone'          => 'Europe/Rome',
            ],
            'mail' => [
                'mail_driver'       => 'smtp',
                'mail_host'         => 'smtp.legalmail.it',
                'mail_port'         => '587',
                'mail_encryption'   => 'tls',
                'mail_username'     => '',
                'mail_password'     => '',
                'mail_from_name'    => '',
                'mail_from_address' => '',
            ],
            'ai' => [
                'ai_api_key'  => '',
                'ai_model'    => 'claude-opus-4-5',
                'ai_provider' => 'anthropic',
            ],
            'sms' => [
                'sms_provider'       => 'self_hosted',
                'sms_api_key'        => '',
                'sms_mittente'       => 'CarModel',
                'otp_timeout_minuti' => '10',
                'otp_lunghezza'      => '6',
            ],
            'fascicoli' => [
                'link_scadenza_giorni' => '7',
                'upload_max_mb'        => '10',
                'upload_formati'       => 'jpg,jpeg,png,pdf',
                'notifica_email_admin' => '1',
            ],
            'documenti' => [
                'firma_modalita'        => 'self_hosted',
                'firma_provider'        => '',
                'firma_provider_key'    => '',
                'firma_cartacea_attiva' => '1',
            ],
            'notifiche' => [
                'notifica_campanellina' => '1',
                'notifica_email'        => '1',
            ],
            'privacy' => [
                'gdpr_versione' => '1.0',
                'gdpr_testo'    => 'Ai sensi del Regolamento UE 2016/679 (GDPR), La informiamo che i Suoi dati personali saranno trattati per le finalità connesse alla gestione del rapporto contrattuale.',
            ],
            'veicoli' => [
                'km_alert_soglia'        => '10000',
                'revisione_alert_giorni' => '30',
            ],
            'sito_web' => [
                // SEO globale
                'seo_site_title'         => 'AleCar S.r.l. - Vendita Auto e Noleggio Torino',
                'seo_site_description'   => 'AleCar S.r.l. Torino - Auto usate garantite e noleggio veicoli. Qualità, trasparenza e assistenza dedicata.',
                'seo_keywords'           => 'auto usate torino, noleggio auto torino, alecar',
                'seo_og_image'           => '',
                // Azienda
                'azienda_slogan'         => 'Auto selezionate e noleggio su misura',
                'azienda_descrizione'    => 'AleCar S.r.l. — veicoli usati garantiti, prezzi trasparenti e IVA esposta.',
                'azienda_telefono'       => '+39 327 807 2650',
                'azienda_email'          => 'alecarto7@gmail.com',
                'azienda_indirizzo'      => 'Via Ignazio Collino 29, 10100 Torino (TO)',
                'azienda_piva'           => '11352180019',
                'azienda_sdi'            => 'M5UXCR1',
                'azienda_whatsapp'       => '393278072650',
                'azienda_anno'           => '2018',
                // HERO
                'hero_titolo'            => 'Auto <span style="color:var(--orange)">selezionate</span><br>e noleggio<br>su misura',
                'hero_sottotitolo'       => 'AleCar S.r.l. — veicoli usati garantiti, prezzi trasparenti e IVA esposta. Noleggio breve e lungo termine con flotta sempre aggiornata.',
                'hero_badge'             => 'TORINO — DAL 2018',
                'hero_cta1_testo'        => 'Vedi auto in vendita',
                'hero_cta2_testo'        => 'Noleggio veicoli',
                'home_cta_label'         => 'Siamo qui per te',
                'home_cta_titolo'        => 'Hai domande? Scrivici',
                'home_cta_testo'         => 'Il nostro team risponde entro 24 ore. Chiamaci o inviaci un messaggio.',
                'home_cta_btn1'          => '+39 327 807 2650',
                'home_cta_btn2'          => 'Invia un messaggio',
                // VANTAGGI
                'vantaggi_titolo'        => 'Perché scegliere AleCar',
                'vantaggio_1_icon'       => '🔍',
                'vantaggio_1_titolo'     => 'Veicoli controllati',
                'vantaggio_1_desc'       => 'Ogni auto viene verificata e certificata prima della vendita',
                'vantaggio_2_icon'       => '💰',
                'vantaggio_2_titolo'     => 'Prezzi trasparenti',
                'vantaggio_2_desc'       => 'IVA sempre esposta, nessun costo nascosto',
                'vantaggio_3_icon'       => '📞',
                'vantaggio_3_titolo'     => 'Risposta in 24h',
                'vantaggio_3_desc'       => 'Rispondiamo a tutte le richieste entro un giorno lavorativo',
                'vantaggio_4_icon'       => '🚗',
                'vantaggio_4_titolo'     => 'Consegna a domicilio',
                'vantaggio_4_desc'       => 'Consegniamo il veicolo direttamente da te',
                // CHI SIAMO
                'chi_siamo_h1'           => 'Chi siamo',
                'chi_siamo_h2'           => 'La nostra storia',
                'chi_siamo_testo'        => 'AleCar S.r.l. nasce a Torino con la missione di rendere l\'acquisto e il noleggio di veicoli usati un\'esperienza trasparente, semplice e affidabile.',
                'chi_siamo_missione'     => 'La nostra missione è offrire veicoli selezionati di qualità con prezzi chiari e assistenza dedicata.',
                'chi_siamo_visione'      => 'Crediamo che ogni cliente meriti un\'esperienza d\'acquisto serena, senza sorprese.',
                // SERVIZI
                'servizi_h1'             => 'I nostri servizi',
                'servizi_h2'             => 'Tutto quello di cui hai bisogno',
                'servizi_intro'          => 'Offriamo una gamma completa di servizi automotive per soddisfare ogni esigenza.',
                // CONTATTI
                'contatti_h1'            => 'Contattaci',
                'contatti_h2'            => 'Siamo a tua disposizione',
                'contatti_intro'         => 'Hai domande su un\'auto o vuoi informazioni sul noleggio? Scrivici o chiamaci.',
                'contatti_maps_embed'    => '',
                // FOOTER
                'footer_descrizione'     => 'AleCar S.r.l. — Vendita auto usate selezionate e noleggio veicoli a Torino. Qualità garantita, prezzi trasparenti, assistenza dedicata.',
                // SOCIAL
                'social_facebook'        => '',
                'social_instagram'       => '',
                'social_tiktok'          => '',
                // COLORI
                'colore_primario'        => '#ff6b00',
                'colore_sfondo'          => '#0a0a0a',
                // ANALYTICS
                'google_analytics_id'    => '',
                'google_tag_manager'     => '',
                // LOGO
                'logo_url'               => '',
                'logo_favicon'           => '',
            ],
            'legale' => [
                'legal_privacy_title'          => '',
                'legal_privacy_desc'           => '',
                'legal_privacy_testo'          => '',
                'legal_cookie_title'           => '',
                'legal_cookie_desc'            => '',
                'legal_termini_vendita_title'  => '',
                'legal_termini_vendita_desc'   => '',
                'legal_termini_vendita_testo'  => '',
                'legal_termini_noleggio_title' => '',
                'legal_termini_noleggio_desc'  => '',
                'legal_termini_noleggio_testo' => '',
            ],
        ];
    }
}