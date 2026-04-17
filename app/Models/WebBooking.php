<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebBooking extends Model
{
    protected $fillable = [
        'tenant_id', 'fleet_vehicle_id', 'type',
        'name', 'email', 'phone',
        'date_start', 'date_end', 'message',
        'status', 'admin_notes', 'confirmed_at',
    ];

    protected $casts = [
        'date_start'   => 'date',
        'date_end'     => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo     { return $this->belongsTo(Tenant::class); }
    public function fleetVehicle(): BelongsTo { return $this->belongsTo(FleetVehicle::class); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeNuove($q)           { return $q->where('status', 'nuova'); }

    public function getDaysAttribute(): int
    {
        if ($this->date_start && $this->date_end) {
            return $this->date_start->diffInDays($this->date_end) ?: 1;
        }
        return 1;
    }
}