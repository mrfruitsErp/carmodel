<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinistro extends Model
{
    use SoftDeletes;

    protected $table = 'sinistri';

    protected $fillable = [
        'tenant_id', 'customer_id', 'vehicle_id',
        'numero_pratica', 'numero_sinistro', 'data_sinistro', 'data_ingresso', 'data_uscita',
        'targa', 'marca', 'modello', 'versione', 'colore', 'telaio', 'km',
        'insurance_company_id', 'compagnia_assicurazione', 'numero_polizza',
        'compagnia_controparte', 'targa_controparte', 'veicolo_controparte', 'conducente_controparte',
        'tipo_sinistro', 'stato', 'stato_wincar_id',
        'expert_id', 'perito_nome',
        'importo_manodopera', 'importo_ricambi', 'importo_materiali', 'importo_totale', 'importo_liquidato', 'iva_inclusa',
        'descrizione_danno', 'note',
        'ha_lesioni', 'ha_auto_sostitutiva', 'foto_count',
        'wincar_id',
    ];

    protected $casts = [
        'data_sinistro'    => 'date',
        'data_ingresso'    => 'date',
        'data_uscita'      => 'date',
        'iva_inclusa'      => 'boolean',
        'ha_lesioni'       => 'boolean',
        'ha_auto_sostitutiva' => 'boolean',
        'importo_manodopera'  => 'decimal:2',
        'importo_ricambi'     => 'decimal:2',
        'importo_materiali'   => 'decimal:2',
        'importo_totale'      => 'decimal:2',
        'importo_liquidato'   => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }

    public function righe(): HasMany
    {
        return $this->hasMany(SinistroRiga::class, 'sinistro_id');
    }

    public function lesioni(): HasMany
    {
        return $this->hasMany(Lesione::class, 'sinistro_id');
    }

    public function autoSostitutive(): HasMany
    {
        return $this->hasMany(SinistroAutoSostitutiva::class, 'sinistro_id');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getTipoSinistroBadgeAttribute(): string
    {
        return match($this->tipo_sinistro) {
            'R' => 'RCA',
            'K' => 'Kasko',
            'C' => 'CID',
            'D' => 'Danni',
            'P' => 'Privato',
            default => $this->tipo_sinistro ?? '—',
        };
    }

    public function getStatoColorAttribute(): string
    {
        return match($this->stato) {
            'aperto'        => 'orange',
            'in_lavorazione'=> 'blue',
            'chiuso'        => 'green',
            'sospeso'       => 'gray',
            default         => 'gray',
        };
    }
}