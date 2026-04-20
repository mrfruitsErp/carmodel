<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesione extends Model
{
    use SoftDeletes;

    protected $table = 'lesioni';

    protected $fillable = [
        'tenant_id', 'sinistro_id',
        'nome', 'indirizzo', 'citta', 'cap', 'provincia',
        'telefono', 'telefono2', 'email', 'professione',
        'data_referto', 'giorni_referto', 'data_guarigione', 'giorni_temporanea',
        'postumi', 'percentuale_postumi',
        'ospedale', 'ricovero_dal', 'ricovero_al',
        'medico_legale', 'nome_medico',
        'totale_spese', 'importo_offerta', 'importo_concordato',
        'stato', 'note',
        'wincar_id',
    ];

    protected $casts = [
        'data_referto'       => 'date',
        'data_guarigione'    => 'date',
        'ricovero_dal'       => 'date',
        'ricovero_al'        => 'date',
        'postumi'            => 'boolean',
        'medico_legale'      => 'boolean',
        'totale_spese'       => 'decimal:2',
        'importo_offerta'    => 'decimal:2',
        'importo_concordato' => 'decimal:2',
    ];

    public function sinistro(): BelongsTo
    {
        return $this->belongsTo(Sinistro::class);
    }
}