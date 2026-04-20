<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SinistroRiga extends Model
{
    protected $table = 'sinistri_righe';

    protected $fillable = [
        'tenant_id', 'sinistro_id',
        'tipo_riga', 'ordine', 'codice_articolo', 'descrizione', 'posizione',
        'quantita', 'prezzo', 'sconto',
        'tempo_sr', 'tempo_la', 'tempo_ve', 'tempo_me',
        'tipo_ricambio',
    ];

    protected $casts = [
        'quantita' => 'decimal:2',
        'prezzo'   => 'decimal:2',
        'sconto'   => 'decimal:2',
        'tempo_sr' => 'decimal:2',
        'tempo_la' => 'decimal:2',
        'tempo_ve' => 'decimal:2',
        'tempo_me' => 'decimal:2',
    ];

    public function sinistro(): BelongsTo
    {
        return $this->belongsTo(Sinistro::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo_riga) {
            'MO' => 'Manodopera',
            'RC' => 'Ricambio',
            'VN' => 'Verniciatura',
            'MC' => 'Meccanica',
            'SR' => 'Scocca',
            default => $this->tipo_riga ?? '—',
        };
    }

    public function getTotaleAttribute(): float
    {
        return round($this->prezzo * $this->quantita * (1 - $this->sconto / 100), 2);
    }
}