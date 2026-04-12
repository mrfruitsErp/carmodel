<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SaleVehicle extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'tenant_id', 'plate', 'vin', 'brand', 'model', 'version',
        'year', 'mileage', 'fuel_type', 'transmission', 'color', 'color_type',
        'doors', 'seats', 'engine_cc', 'power_kw', 'power_hp', 'body_type',
        'condition', 'previous_owners', 'first_registration', 'features',
        'asking_price', 'min_price', 'price_negotiable', 'vat_deductible',
        'purchase_price', 'badge_label', 'title', 'description', 'internal_notes',
        'status', 'available_from', 'sold_date', 'sold_price',
        'sold_to_customer_id', 'vehicle_id', 'created_by',
    ];

    protected $casts = [
        'features'           => 'array',
        'first_registration' => 'date',
        'available_from'     => 'date',
        'sold_date'          => 'date',
        'price_negotiable'   => 'boolean',
        'vat_deductible'     => 'boolean',
        'asking_price'       => 'decimal:2',
        'min_price'          => 'decimal:2',
        'purchase_price'     => 'decimal:2',
        'sold_price'         => 'decimal:2',
    ];

    public const BADGE_PRESETS = [
        'Trattabile',
        'Occasione del mese',
        'Introvabile',
        'Pronta consegna',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('sale_photos')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('sale_documents')
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)->height(300)->sharpen(10)
            ->performOnCollections('sale_photos');

        $this->addMediaConversion('watermarked')
            ->width(1200)->height(900)
            ->performOnCollections('sale_photos');
    }

    // Relazioni
    public function tenant(): BelongsTo         { return $this->belongsTo(Tenant::class); }
    public function vehicle(): BelongsTo        { return $this->belongsTo(Vehicle::class); }
    public function soldToCustomer(): BelongsTo { return $this->belongsTo(Customer::class, 'sold_to_customer_id'); }
    public function createdBy(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }
    public function listings(): HasMany         { return $this->hasMany(MarketplaceListing::class); }
    public function leads(): HasMany            { return $this->hasMany(MarketplaceLead::class); }

    // Scopes
    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeActive($q)          { return $q->where('status', 'attivo'); }
    public function scopeDraft($q)           { return $q->where('status', 'bozza'); }
    public function scopeSold($q)            { return $q->where('status', 'venduto'); }

    public function scopeSearch($q, string $term)
    {
        return $q->where(fn($s) => $s
            ->where('brand',    'like', "%{$term}%")
            ->orWhere('model',  'like', "%{$term}%")
            ->orWhere('plate',  'like', "%{$term}%")
            ->orWhere('vin',    'like', "%{$term}%")
            ->orWhere('version','like', "%{$term}%")
        );
    }

    // Accessori
    public function getFullNameAttribute(): string
    {
        return trim("{$this->brand} {$this->model} {$this->version} ({$this->year})");
    }

    public function getMarginAttribute(): ?float
    {
        if ($this->purchase_price && $this->asking_price) {
            return round($this->asking_price - $this->purchase_price, 2);
        }
        return null;
    }

    public function getMarginPercentAttribute(): ?float
    {
        if ($this->purchase_price && $this->purchase_price > 0 && $this->asking_price) {
            return round((($this->asking_price - $this->purchase_price) / $this->purchase_price) * 100, 1);
        }
        return null;
    }

    public function getPrimaryPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('sale_photos', 'thumb') ?: null;
    }

    public function getActiveListingsCountAttribute(): int
    {
        return $this->listings()->where('status', 'published')->count();
    }

    public function isPublishedOn(string $platform): bool
    {
        return $this->listings()
            ->where('platform', $platform)
            ->where('status', 'published')
            ->exists();
    }

    public function getListingFor(string $platform): ?MarketplaceListing
    {
        return $this->listings()->where('platform', $platform)->first();
    }

    public function totalViews(): int    { return $this->listings()->sum('views'); }
    public function totalContacts(): int { return $this->leads()->count(); }

    public function markAsSold(float $price, ?int $customerId = null): void
    {
        $this->update([
            'status'              => 'venduto',
            'sold_date'           => now(),
            'sold_price'          => $price,
            'sold_to_customer_id' => $customerId,
        ]);
        $this->listings()->where('status', 'published')->each(
            fn($l) => app(\App\Services\Marketplace\MarketplaceManager::class)->unpublish($l)
        );
    }

    public function getComputedTitleAttribute(): string
    {
        if ($this->title) return $this->title;
        $parts = [$this->brand, $this->model, $this->version, $this->year,
                  '–', number_format($this->mileage, 0, ',', '.') . ' km'];
        return implode(' ', array_filter($parts));
    }
}