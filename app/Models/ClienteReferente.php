<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customer;

class ClienteReferente extends Model
{
    use SoftDeletes;

    protected $table = 'cliente_referenti';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'nome',
        'cognome',
        'email',
        'telefono',
        'ruolo',
        'is_principale',
        'can_upload',
        'sezioni_visibili',
        'token_accesso',
        'codice_identificativo',
        'autorizzato_da',
        'autorizzato_il',
        'attivo',
    ];

    protected $casts = [
        'sezioni_visibili' => 'array',
        'is_principale'    => 'boolean',
        'can_upload'       => 'boolean',
        'attivo'           => 'boolean',
        'autorizzato_il'   => 'datetime',
    ];

    protected $hidden = ['token_accesso', 'codice_identificativo'];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check()) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Customer::class);
    }

    public function autorizzatoDa()
    {
        return $this->belongsTo(ClienteReferente::class, 'autorizzato_da');
    }

    public function tokens()
    {
        return $this->hasMany(FascicoloToken::class, 'referente_id');
    }

    public function getNomeCompletoAttribute(): string
    {
        return trim("{$this->nome} {$this->cognome}");
    }
}