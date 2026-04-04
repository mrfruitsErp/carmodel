<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceListing extends Model
{
    protected $fillable = [
        'tenant_id', 'sale_vehicle_id', 'platform', 'external_id', 'external_url',
        'status', 'platform_data', 'platform_config',
        'views', 'contacts', 'favorites',
        'published_at', 'expires_at', 'last_synced_at',
        'last_error_at', 'last_error_message', 'listed_price',
    ];

    protected $casts = [
        'platform_data'   => 'array',
        'platform_config' => 'array',
        'published_at'    => 'datetime',
        'expires_at'      => 'datetime',
        'last_synced_at'  => 'datetime',
        'last_error_at'   => 'datetime',
        'listed_price'    => 'decimal:2',
    ];

    public function tenant(): BelongsTo      { return $this->belongsTo(Tenant::class); }
    public function saleVehicle(): BelongsTo { return $this->belongsTo(SaleVehicle::class); }
    public function leads(): HasMany         { return $this->hasMany(MarketplaceLead::class); }

    public function scopeForTenant($q, $tid)    { return $q->where('tenant_id', $tid); }
    public function scopePublished($q)           { return $q->where('status', 'published'); }
    public function scopePlatform($q, string $p) { return $q->where('platform', $p); }

    public function isPublished(): bool { return $this->status === 'published'; }
    public function hasError(): bool    { return $this->status === 'error'; }
    public function isExpired(): bool   { return $this->expires_at && $this->expires_at->isPast(); }

    public function getPlatformLabelAttribute(): string
    {
        return match($this->platform) {
            'autoscout24'          => 'AutoScout24',
            'automobile_it'        => 'Automobile.it',
            'ebay_motors'          => 'eBay Motors',
            'subito_it'            => 'Subito.it',
            'facebook_marketplace' => 'Facebook Marketplace',
            'mobile_de'            => 'mobile.de',
            'olx'                  => 'OLX',
            'autoungle'            => 'AutoUncle',
            'auto1'                => 'AUTO1.com',
            'quattroruote'         => 'Quattroruote',
            'autosupermarket'      => 'AutoSuperMarket',
            'instagram'            => 'Instagram',
            default                => ucfirst($this->platform),
        };
    }

    public function markPublished(string $externalId, string $externalUrl): void
    {
        $this->update([
            'status'             => 'published',
            'external_id'        => $externalId,
            'external_url'       => $externalUrl,
            'published_at'       => now(),
            'last_synced_at'     => now(),
            'last_error_message' => null,
        ]);
    }

    public function markError(string $message): void
    {
        $this->update([
            'status'             => 'error',
            'last_error_at'      => now(),
            'last_error_message' => $message,
        ]);
    }
}