<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceLead extends Model
{
    protected $fillable = [
        'tenant_id', 'marketplace_listing_id', 'sale_vehicle_id', 'platform',
        'lead_name', 'lead_email', 'lead_phone', 'lead_message', 'external_lead_id',
        'status', 'assigned_to', 'notes', 'contacted_at', 'appointment_at',
        'customer_id', 'raw_data',
    ];

    protected $casts = [
        'contacted_at'   => 'datetime',
        'appointment_at' => 'datetime',
        'raw_data'       => 'array',
    ];

    public function tenant(): BelongsTo      { return $this->belongsTo(Tenant::class); }
    public function listing(): BelongsTo     { return $this->belongsTo(MarketplaceListing::class, 'marketplace_listing_id'); }
    public function saleVehicle(): BelongsTo { return $this->belongsTo(SaleVehicle::class); }
    public function assignedTo(): BelongsTo  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function customer(): BelongsTo    { return $this->belongsTo(Customer::class); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeNew($q)             { return $q->where('status', 'nuovo'); }
    public function scopeUnassigned($q)      { return $q->whereNull('assigned_to'); }
}