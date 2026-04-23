<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    protected $table = 'customers';
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'tenant_id','type','first_name','last_name','fiscal_code','date_of_birth',
        'company_name','vat_number','sdi_code','pec_email',
        'email','phone','phone2','whatsapp',
        'address','city','postal_code','province','country',
        'notes','tags','source','total_value','active','created_by',
        // Nuovi campi aggiunti dalla migration 000003
        'tipo_soggetto','codice_fiscale','partita_iva','pec','codice_sdi','ragione_sociale',
    ];

    protected $casts = [
        'tags'          => 'array',
        'active'        => 'boolean',
        'date_of_birth' => 'date',
        'total_value'   => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // ──────────────────────────────────────────
    // Accessori esistenti
    // ──────────────────────────────────────────
    public function getDisplayNameAttribute(): string {
        return $this->type === 'company'
            ? ($this->company_name ?? $this->ragione_sociale ?? '')
            : trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string {
        $name  = $this->display_name;
        $words = explode(' ', $name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    // ──────────────────────────────────────────
    // Accessori per compatibilità con i nuovi moduli
    // Mappa i campi inglesi esistenti → campi italiani del modulo Fascicoli
    // ──────────────────────────────────────────

    // tipo_soggetto: usa il campo nuovo se presente, altrimenti mappa da 'type'
    public function getTipoSoggettoEffettivoAttribute(): string {
        if ($this->tipo_soggetto) return $this->tipo_soggetto;
        return match($this->type) {
            'company'  => 'azienda',
            'private'  => 'privato',
            default    => 'privato',
        };
    }

    // codice_fiscale: usa campo nuovo oppure fiscal_code esistente
    public function getCodiceFiscaleEffettivoAttribute(): ?string {
        return $this->codice_fiscale ?? $this->fiscal_code;
    }

    // partita_iva: usa campo nuovo oppure vat_number esistente
    public function getPartitaIvaEffettivaAttribute(): ?string {
        return $this->partita_iva ?? $this->vat_number;
    }

    // ──────────────────────────────────────────
    // Relazioni esistenti
    // ──────────────────────────────────────────
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
    public function personalInjuries(): HasMany { return $this->hasMany(PersonalInjury::class); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function rentals(): HasMany { return $this->hasMany(Rental::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    // Nuove relazioni modulo Fascicoli
    public function fascicoli(): HasMany { return $this->hasMany(Fascicolo::class, 'cliente_id'); }
    public function referenti(): HasMany { return $this->hasMany(ClienteReferente::class, 'cliente_id'); }

    // ──────────────────────────────────────────
    // Scopes esistenti
    // ──────────────────────────────────────────
    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopePrivate($q) { return $q->where('type', 'private'); }
    public function scopeCompany($q) { return $q->where('type', 'company'); }
    public function scopeWithOpenClaims($q) {
        return $q->whereHas('claims', fn($c) => $c->whereNotIn('status', ['chiuso','archiviato']));
    }
    public function scopeSearch($q, string $term) {
        return $q->where(fn($sub) => $sub
            ->where('first_name', 'like', "%{$term}%")
            ->orWhere('last_name', 'like', "%{$term}%")
            ->orWhere('company_name', 'like', "%{$term}%")
            ->orWhere('fiscal_code', 'like', "%{$term}%")
            ->orWhere('vat_number', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
        );
    }

    public function recalculateTotalValue(): void {
        $this->update(['total_value' => $this->documents()->where('payment_status', 'pagata')->sum('total')]);
    }
}