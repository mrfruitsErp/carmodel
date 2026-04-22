<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FascicoloDocumento extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'fascicolo_documenti';

    protected $fillable = [
        'tenant_id',
        'fascicolo_id',
        'catalogo_id',
        'nome',
        'obbligatorio',
        'richiede_firma',
        'richiede_upload',
        'modalita_firma',
        'stato',
        'firma_otp',
        'firma_otp_scadenza',
        'firmato_il',
        'firmato_da_nome',
        'firmato_da_ip',
        'firmato_da_user_agent',
        'caricato_il',
        'note_operatore',
        'note_cliente',
        'ordine',
    ];

    protected $casts = [
        'obbligatorio'       => 'boolean',
        'richiede_firma'     => 'boolean',
        'richiede_upload'    => 'boolean',
        'firma_otp_scadenza' => 'datetime',
        'firmato_il'         => 'datetime',
        'caricato_il'        => 'datetime',
    ];

    protected $hidden = ['firma_otp'];

    public function fascicolo()
    {
        return $this->belongsTo(Fascicolo::class);
    }

    public function catalogo()
    {
        return $this->belongsTo(DocumentoCatalogo::class, 'catalogo_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file_documento')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->singleFile();
    }

    public function isCompletato(): bool
    {
        return in_array($this->stato, ['caricato', 'firmato', 'verificato']);
    }

    public function generaFirmaOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'firma_otp'          => bcrypt($otp),
            'firma_otp_scadenza' => now()->addMinutes(15),
        ]);
        return $otp;
    }

    public function apponiFirma(string $otp, string $nome, string $ip, string $userAgent): bool
    {
        if (!$this->firma_otp_scadenza || $this->firma_otp_scadenza->isPast()) return false;
        if (!password_verify($otp, $this->firma_otp)) return false;

        $this->update([
            'stato'                 => 'firmato',
            'firmato_il'            => now(),
            'firmato_da_nome'       => $nome,
            'firmato_da_ip'         => $ip,
            'firmato_da_user_agent' => $userAgent,
        ]);
        return true;
    }
}