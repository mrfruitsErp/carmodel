<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SinistroAutoSostitutiva extends Model
{
    protected $table = 'sinistri_auto_sostitutive';

    protected $fillable = [
        'tenant_id', 'sinistro_id',
        'targa', 'marca_modello', 'telaio', 'gruppo',
        'data_inizio', 'data_fine',
        'km_inizio', 'km_fine',
        'costo', 'numero_noleggio', 'autorizzazione',
        'conducente', 'fornitore', 'motivo',
        'wincar_id',
    ];

    protected $casts = [
        'data_inizio' => 'date',
        'data_fine'   => 'date',
        'costo'       => 'decimal:2',
    ];

    public function sinistro(): BelongsTo
    {
        return $this->belongsTo(Sinistro::class);
    }

    public function getGiorniAttribute(): int
    {
        if (!$this->data_inizio || !$this->data_fine) return 0;
        return $this->data_inizio->diffInDays($this->data_fine);
    }

    public function getKmPercorsiAttribute(): int
    {
        return max(0, ($this->km_fine ?? 0) - ($this->km_inizio ?? 0));
    }
}