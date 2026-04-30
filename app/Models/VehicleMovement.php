<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMovement extends Model
{
    use SoftDeletes;

    protected $table = 'vehicle_movements';

    protected $fillable = [
        'tenant_id',
        'vehicle_type',
        'fleet_vehicle_id',
        'sale_vehicle_id',
        'vehicle_id',
        'tipo',
        'data_inizio',
        'data_fine',
        'luogo_partenza',
        'indirizzo_partenza',
        'luogo_arrivo',
        'indirizzo_arrivo',
        'cliente_id',
        'operatore_id',
        'autista_id',
        'stato',
        'rental_id',
        'work_order_id',
        'claim_id',
        'fascicolo_id',
        'titolo',
        'note',
        'km_partenza',
        'km_arrivo',
        'created_by',
    ];

    protected $casts = [
        'data_inizio' => 'datetime',
        'data_fine'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth()->check()) {
                $query->where('vehicle_movements.tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    // ── Relazioni ─────────────────────────────
    public function fleetVehicle(): BelongsTo  { return $this->belongsTo(FleetVehicle::class); }
    public function saleVehicle(): BelongsTo   { return $this->belongsTo(SaleVehicle::class); }
    public function vehicle(): BelongsTo       { return $this->belongsTo(Vehicle::class); }
    public function cliente(): BelongsTo       { return $this->belongsTo(Customer::class, 'cliente_id'); }
    public function operatore(): BelongsTo     { return $this->belongsTo(User::class, 'operatore_id'); }
    public function autista(): BelongsTo       { return $this->belongsTo(User::class, 'autista_id'); }
    public function createdBy(): BelongsTo     { return $this->belongsTo(User::class, 'created_by'); }
    public function rental(): BelongsTo        { return $this->belongsTo(Rental::class); }
    public function workOrder(): BelongsTo     { return $this->belongsTo(WorkOrder::class); }
    public function claim(): BelongsTo         { return $this->belongsTo(Claim::class); }
    public function fascicolo(): BelongsTo     { return $this->belongsTo(Fascicolo::class); }

    // ── Label helpers ──────────────────────────
    public static function tipi(): array
    {
        return [
            'ritiro_cliente'   => ['label' => 'Ritiro Cliente',    'icon' => '🚗', 'color' => 'info'],
            'consegna_cliente' => ['label' => 'Consegna Cliente',  'icon' => '✅', 'color' => 'success'],
            'trasferimento'    => ['label' => 'Trasferimento',     'icon' => '🔄', 'color' => 'primary'],
            'revisione'        => ['label' => 'Revisione',         'icon' => '🔍', 'color' => 'warning'],
            'collaudo'         => ['label' => 'Collaudo',          'icon' => '🏁', 'color' => 'secondary'],
            'perizia'          => ['label' => 'Perizia',           'icon' => '📋', 'color' => 'dark'],
            'noleggio'         => ['label' => 'Noleggio',          'icon' => '🔑', 'color' => 'info'],
            'sostitutiva'      => ['label' => 'Auto Sostitutiva',  'icon' => '🔄', 'color' => 'warning'],
            'manutenzione'     => ['label' => 'Manutenzione',      'icon' => '🔧', 'color' => 'danger'],
            'altro'            => ['label' => 'Altro',             'icon' => '📁', 'color' => 'secondary'],
        ];
    }

    public static function stati(): array
    {
        return [
            'programmato' => ['label' => 'Programmato', 'color' => 'info'],
            'in_corso'    => ['label' => 'In Corso',    'color' => 'warning'],
            'completato'  => ['label' => 'Completato',  'color' => 'success'],
            'annullato'   => ['label' => 'Annullato',   'color' => 'secondary'],
        ];
    }

    public function getTipoLabelAttribute(): string
    {
        return self::tipi()[$this->tipo]['label'] ?? $this->tipo;
    }

    public function getTipoIconAttribute(): string
    {
        return self::tipi()[$this->tipo]['icon'] ?? '📁';
    }

    public function getTipoColorAttribute(): string
    {
        return self::tipi()[$this->tipo]['color'] ?? 'secondary';
    }

    public function getStatoLabelAttribute(): string
    {
        return self::stati()[$this->stato]['label'] ?? $this->stato;
    }

    public function getStatoColorAttribute(): string
    {
        return self::stati()[$this->stato]['color'] ?? 'secondary';
    }

    public function getVeicoloLabelAttribute(): string
    {
        if ($this->fleetVehicle) return $this->fleetVehicle->full_name;
        if ($this->saleVehicle)  return "{$this->saleVehicle->brand} {$this->saleVehicle->model} ({$this->saleVehicle->plate})";
        if ($this->vehicle)      return $this->vehicle->full_name;
        return '—';
    }

    // ── Scopes ────────────────────────────────
    public function scopeOggi($q)
    {
        return $q->whereDate('data_inizio', today());
    }

    public function scopeSettimana($q)
    {
        return $q->whereBetween('data_inizio', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeInRitardo($q)
    {
        return $q->where('stato', 'in_corso')->where('data_fine', '<', now());
    }

    public function scopeProssimi($q, int $ore = 24)
    {
        return $q->where('stato', 'programmato')
                 ->whereBetween('data_inizio', [now(), now()->addHours($ore)]);
    }
}
