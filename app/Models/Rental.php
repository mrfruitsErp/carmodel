<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id','rental_number','fleet_vehicle_id','customer_id','claim_id',
        'rental_type','start_date','expected_end_date','actual_end_date',
        'km_start','km_end','km_included','km_extra_price',
        'daily_rate','total_days','subtotal','extra_charges','discount','vat_percent','total',
        'status','fuel_level_start','fuel_level_end',
        'damage_notes_start','damage_notes_end','notes','created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function fleetVehicle(): BelongsTo { return $this->belongsTo(FleetVehicle::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeActive($q) { return $q->where('status', 'attivo'); }
    public function scopeExpiringSoon($q, int $days = 3) {
        return $q->where('expected_end_date', '<=', now()->addDays($days))->where('status', 'attivo');
    }
    public function scopeOverdue($q) {
        return $q->where('expected_end_date', '<', now())->where('status', 'attivo');
    }

    public function isOverdue(): bool {
        return $this->status === 'attivo' && $this->expected_end_date->isPast();
    }

    public static function generateNumber(int $tenantId): string {
        $year = now()->year;
        $last = static::where('tenant_id', $tenantId)->whereYear('created_at', $year)->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->rental_number, -3)) + 1) : 1;
        return "NOL-{$year}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
