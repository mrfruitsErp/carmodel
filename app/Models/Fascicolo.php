<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Customer;

class Fascicolo extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'fascicoli';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'operatore_id',
        'tipo_pratica',
        'stato',
        'titolo',
        'note',
        'pratica_type',
        'pratica_id',
        'data_inizio',
        'data_fine',
        'riferimento_veicolo',
        'completato_il',
        'notifica_operatore_il',
    ];

    protected $casts = [
        'data_inizio'             => 'date',
        'data_fine'               => 'date',
        'completato_il'           => 'datetime',
        'notifica_operatore_il'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check()) {
                $query->where('fascicoli.tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    // ──────────────────────────────────────────
    // Relazioni
    // ──────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Customer::class);
    }

    public function operatore()
    {
        return $this->belongsTo(User::class, 'operatore_id');
    }

    public function token()
    {
        return $this->hasMany(FascicoloToken::class);
    }

    public function tokenAttivo()
    {
        return $this->hasOne(FascicoloToken::class)->where('attivo', true)->latest();
    }

    public function documenti()
    {
        return $this->hasMany(FascicoloDocumento::class)->orderBy('ordine');
    }

    public function pratica()
    {
        return $this->morphTo();
    }

    // ──────────────────────────────────────────
    // Media Library
    // ──────────────────────────────────────────
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('allegati_generali')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }

    // ──────────────────────────────────────────
    // Label
    // ──────────────────────────────────────────
    public static function tipiPratica(): array
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

    public static function stati(): array
    {
        return [
            'bozza'         => ['label' => 'Bozza',          'color' => 'secondary'],
            'link_inviato'  => ['label' => 'Link Inviato',   'color' => 'info'],
            'gdpr_accettato'=> ['label' => 'GDPR Accettato', 'color' => 'primary'],
            'in_compilazione'=>['label' => 'In Compilazione','color' => 'warning'],
            'completato'    => ['label' => 'Completato',     'color' => 'success'],
            'verificato'    => ['label' => 'Verificato',     'color' => 'success'],
            'archiviato'    => ['label' => 'Archiviato',     'color' => 'dark'],
        ];
    }

    public function getStatoLabelAttribute(): string
    {
        return self::stati()[$this->stato]['label'] ?? $this->stato;
    }

    public function getStatoColorAttribute(): string
    {
        return self::stati()[$this->stato]['color'] ?? 'secondary';
    }

    public function getTipoPraticaLabelAttribute(): string
    {
        return self::tipiPratica()[$this->tipo_pratica] ?? $this->tipo_pratica;
    }

    // ──────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────
    public function isCompletato(): bool
    {
        return in_array($this->stato, ['completato', 'verificato', 'archiviato']);
    }

    public function getProgressoAttribute(): int
    {
        $totale     = $this->documenti()->count();
        $completati = $this->documenti()->whereIn('stato', ['caricato','firmato','verificato'])->count();
        return $totale > 0 ? (int) round($completati / $totale * 100) : 0;
    }

    // Genera lista documenti dal catalogo per tipo pratica + tipo soggetto
    public function popolaDocumentiDaCatalogo(): void
    {
        $tipoSoggetto = $this->cliente->tipo_soggetto_effettivo ?? 'privato';

        $catalogo = DocumentoCatalogo::perSezione($this->tipo_pratica)
            ->perSoggetto($tipoSoggetto)
            ->orderBy('ordine')
            ->get();

        foreach ($catalogo as $doc) {
            // Non duplica se già presente
            $esiste = $this->documenti()->where('catalogo_id', $doc->id)->exists();
            if (!$esiste) {
                $this->documenti()->create([
                    'tenant_id'      => $this->tenant_id,
                    'catalogo_id'    => $doc->id,
                    'nome'           => $doc->nome,
                    'obbligatorio'   => $doc->obbligatorio_default,
                    'richiede_firma' => $doc->richiede_firma,
                    'richiede_upload'=> $doc->richiede_upload,
                    'modalita_firma' => $doc->modalita_firma,
                    'ordine'         => $doc->ordine,
                ]);
            }
        }
    }
}