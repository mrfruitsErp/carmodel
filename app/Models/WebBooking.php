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
        'letto_at', 'letto_da_user_id',
        'is_spam', 'spam_reason', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'date_start'   => 'date',
        'date_end'     => 'date',
        'confirmed_at' => 'datetime',
        'letto_at'     => 'datetime',
        'is_spam'      => 'boolean',
    ];

    public function tenant(): BelongsTo     { return $this->belongsTo(Tenant::class); }
    public function fleetVehicle(): BelongsTo { return $this->belongsTo(FleetVehicle::class); }
    public function lettoDa(): BelongsTo    { return $this->belongsTo(\App\Models\User::class, 'letto_da_user_id'); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeNuove($q)           { return $q->where('status', 'nuova'); }
    public function scopeNonLetti($q)        { return $q->whereNull('letto_at'); }
    public function scopeNonSpam($q)         { return $q->where('is_spam', false); }
    public function scopeSoloSpam($q)        { return $q->where('is_spam', true); }

    public function getDaysAttribute(): int
    {
        if ($this->date_start && $this->date_end) {
            return $this->date_start->diffInDays($this->date_end) ?: 1;
        }
        return 1;
    }

    public function isNotLetto(): bool { return $this->letto_at === null; }

    /**
     * Etichetta human-friendly per il tipo di messaggio.
     */
    public function getTipoLabelAttribute(): string
    {
        return match ($this->type) {
            'noleggio'         => 'Richiesta noleggio',
            'contatto'         => 'Contatto generico',
            'contatto_veicolo' => 'Richiesta veicolo',
            default            => ucfirst($this->type),
        };
    }
}