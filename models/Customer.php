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
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'tenant_id','type','first_name','last_name','fiscal_code','date_of_birth',
        'company_name','vat_number','sdi_code','pec_email',
        'email','phone','phone2','whatsapp',
        'address','city','postal_code','province','country',
        'notes','tags','source','total_value','active','created_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'active' => 'boolean',
        'date_of_birth' => 'date',
        'total_value' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    // Accessori
    public function getDisplayNameAttribute(): string {
        return $this->type === 'company'
            ? $this->company_name
            : trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string {
        $name = $this->display_name;
        $words = explode(' ', $name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    // Relazioni
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
    public function personalInjuries(): HasMany { return $this->hasMany(PersonalInjury::class); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function rentals(): HasMany { return $this->hasMany(Rental::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    // Scopes
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

    // Aggiorna valore totale
    public function recalculateTotalValue(): void {
        $this->update(['total_value' => $this->documents()->where('payment_status', 'pagata')->sum('total')]);
    }
}
