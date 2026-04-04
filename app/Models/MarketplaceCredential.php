<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class MarketplaceCredential extends Model
{
    protected $fillable = [
        'tenant_id', 'platform', 'enabled', 'credentials',
        'token_expires_at', 'settings', 'last_verified_at', 'verified',
    ];

    protected $casts = [
        'settings'         => 'array',
        'enabled'          => 'boolean',
        'verified'         => 'boolean',
        'token_expires_at' => 'datetime',
        'last_verified_at' => 'datetime',
    ];

    public function setCredentialsAttribute(?string $value): void
    {
        $this->attributes['credentials'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getCredentialsAttribute(?string $value): ?array
    {
        if (!$value) return null;
        try {
            return json_decode(Crypt::decryptString($value), true);
        } catch (\Exception) {
            return null;
        }
    }

    public function setCredentialsArray(array $data): void
    {
        $this->credentials = json_encode($data);
        $this->save();
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeEnabled($q)         { return $q->where('enabled', true); }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}