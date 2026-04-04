<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Scopes\TenantScope;

class Vehicle extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'tenant_id','customer_id','plate','vin','brand','model','version',
        'year','color','fuel_type','km_current',
        'insurance_company','insurance_policy','insurance_expiry','revision_expiry',
        'status','notes'
    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'revision_expiry' => 'date',
    ];

    /* =========================
       🔥 GLOBAL TENANT LOGIC
    ========================= */
    protected static function booted()
    {
        // ✅ filtro automatico su tutte le query
        static::addGlobalScope(new TenantScope);

        // ✅ assegna tenant automaticamente in creazione
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /* =========================
       MEDIA
    ========================= */
    public function registerMediaCollections(): void {
        $this->addMediaCollection('before_photos')->useDisk('public');
        $this->addMediaCollection('after_photos')->useDisk('public');
        $this->addMediaCollection('damage_photos')->useDisk('public');
        $this->addMediaCollection('documents')->useDisk('public');
    }

    /* =========================
       RELAZIONI
    ========================= */
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class); }

    /* =========================
       ACCESSOR
    ========================= */
    public function getFullNameAttribute(): string {
        return trim("{$this->brand} {$this->model} {$this->year}");
    }

    /* =========================
       SCOPES (ancora utili)
    ========================= */
    public function scopeInShop($q) { return $q->where('status', 'in_officina'); }

    public function scopeSearch($q, string $term) {
        return $q->where(fn($s) => $s
            ->where('plate', 'like', "%{$term}%")
            ->orWhere('vin', 'like', "%{$term}%")
            ->orWhere('brand', 'like', "%{$term}%")
            ->orWhere('model', 'like', "%{$term}%")
        );
    }

    /* =========================
       LOGICA
    ========================= */
    public function isRevisionExpiringSoon(): bool {
        return $this->revision_expiry && $this->revision_expiry->diffInDays(now()) <= 30;
    }
}