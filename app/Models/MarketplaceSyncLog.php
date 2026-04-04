<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceSyncLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id', 'marketplace_listing_id', 'platform', 'action',
        'result', 'request_payload', 'response_payload',
        'error_message', 'http_status', 'duration_ms', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo  { return $this->belongsTo(Tenant::class); }
    public function listing(): BelongsTo { return $this->belongsTo(MarketplaceListing::class, 'marketplace_listing_id'); }
}