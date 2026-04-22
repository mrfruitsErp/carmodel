<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FascicoloToken extends Model
{
    protected $table = 'fascicolo_token';

    protected $fillable = [
        'tenant_id',
        'fascicolo_id',
        'referente_id',
        'token',
        'scadenza',
        'attivo',
        'gdpr_accettato_il',
        'gdpr_ip',
        'gdpr_versione',
        'otp_code',
        'otp_scadenza',
        'otp_tentativi',
        'otp_verificato_il',
    ];

    protected $casts = [
        'scadenza'          => 'datetime',
        'used_at'           => 'datetime',
        'attivo'            => 'boolean',
        'gdpr_accettato_il' => 'datetime',
        'otp_scadenza'      => 'datetime',
        'otp_verificato_il' => 'datetime',
    ];

    protected $hidden = ['otp_code'];

    public function fascicolo()
    {
        return $this->belongsTo(Fascicolo::class);
    }

    public function referente()
    {
        return $this->belongsTo(ClienteReferente::class, 'referente_id');
    }

    // ──────────────────────────────────────────
    // Genera nuovo token univoco
    // ──────────────────────────────────────────
    public static function genera(int $fascicoloId, int $tenantId, ?int $referenteId = null, ?int $giorniScadenza = null): self
    {
        $giorni = $giorniScadenza ?? (int) Setting::get('link_scadenza_giorni', 7);

        return static::create([
            'tenant_id'    => $tenantId,
            'fascicolo_id' => $fascicoloId,
            'referente_id' => $referenteId,
            'token'        => Str::random(64),
            'scadenza'     => $giorni > 0 ? now()->addDays($giorni) : null,
            'attivo'       => true,
        ]);
    }

    // ──────────────────────────────────────────
    // Genera e invia OTP email
    // ──────────────────────────────────────────
    public function generaOtp(): string
    {
        $lunghezza = (int) Setting::get('otp_lunghezza', 6);
        $timeout   = (int) Setting::get('otp_timeout_minuti', 10);
        $otp       = str_pad(random_int(0, pow(10, $lunghezza) - 1), $lunghezza, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code'      => bcrypt($otp),
            'otp_scadenza'  => now()->addMinutes($timeout),
            'otp_tentativi' => 0,
        ]);

        return $otp; // restituisce il codice in chiaro per invio email
    }

    public function verificaOtp(string $codice): bool
    {
        if ($this->otp_tentativi >= 5) return false;
        if ($this->otp_scadenza && $this->otp_scadenza->isPast()) return false;

        $this->increment('otp_tentativi');

        if (password_verify($codice, $this->otp_code)) {
            $this->update(['otp_verificato_il' => now(), 'otp_tentativi' => 0]);
            return true;
        }
        return false;
    }

    public function isValido(): bool
    {
        if (!$this->attivo) return false;
        if ($this->scadenza && $this->scadenza->isPast()) return false;
        return true;
    }

    public function isOtpVerificato(): bool
    {
        return !is_null($this->otp_verificato_il);
    }

    public function isGdprAccettato(): bool
    {
        return !is_null($this->gdpr_accettato_il);
    }

    public function accettaGdpr(string $ip): void
    {
        $this->update([
            'gdpr_accettato_il' => now(),
            'gdpr_ip'           => $ip,
            'gdpr_versione'     => Setting::get('gdpr_versione', '1.0'),
        ]);
    }
}