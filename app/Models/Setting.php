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
        static::saved(function ($setting) {
            Cache::forget("settings_{$setting->tenant_id}");
        });
    }

    public static function get(string $chiave, mixed $default = null): mixed
    {
        $tenantId = auth()->user()?->tenant_id ?? 0;
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
            'generale'  => 'Generale',
            'mail'      => 'Mail & SMTP',
            'ai'        => 'Intelligenza Artificiale',
            'sms'       => 'SMS Gateway',
            'fascicoli' => 'Fascicoli',
            'documenti' => 'Documenti',
            'notifiche' => 'Notifiche',
            'privacy'   => 'Privacy & GDPR',
            'veicoli'   => 'Veicoli',
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
        ];
    }
}