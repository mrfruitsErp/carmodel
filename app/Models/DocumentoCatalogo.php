<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoCatalogo extends Model
{
    use SoftDeletes;

    protected $table = 'documento_catalogo';

    protected $fillable = [
        'tenant_id',
        'nome',
        'descrizione',
        'tipo_soggetto',
        'sezioni_collegate',
        'richiede_firma',
        'richiede_upload',
        'modalita_firma',
        'template_testo',
        'obbligatorio_default',
        'ordine',
        'attivo',
    ];

    protected $casts = [
        'sezioni_collegate'    => 'array',
        'richiede_firma'       => 'boolean',
        'richiede_upload'      => 'boolean',
        'obbligatorio_default' => 'boolean',
        'attivo'               => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check()) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    // ──────────────────────────────────────────
    // Sezioni disponibili — espandibili
    // ──────────────────────────────────────────
    public static function sezioniDisponibili(): array
    {
        return [
            'noleggio'          => '🚗 Noleggio',
            'sinistro'          => '⚠️ Sinistro',
            'riparazione'       => '🔧 Riparazione',
            'perizia'           => '📋 Perizia',
            'auto_sostitutiva'  => '🔄 Auto Sostitutiva',
            'lesioni_personali' => '🏥 Lesioni Personali',
            'vendita_auto'      => '💰 Vendita Auto',
            'altro'             => '📁 Altro',
        ];
    }

    // Documenti per sezione specifica
    public function scopePerSezione($query, string $sezione)
    {
        return $query->whereJsonContains('sezioni_collegate', $sezione)->where('attivo', true);
    }

    // Documenti per tipo soggetto
    public function scopePerSoggetto($query, string $tipo)
    {
        return $query->where(function ($q) use ($tipo) {
            $q->where('tipo_soggetto', $tipo)->orWhere('tipo_soggetto', 'entrambi');
        });
    }

    public function fascicoloDocumenti()
    {
        return $this->hasMany(FascicoloDocumento::class, 'catalogo_id');
    }
}