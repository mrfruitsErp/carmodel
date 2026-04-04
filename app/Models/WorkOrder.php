<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class WorkOrder extends Model implements HasMedia
{
    use SoftDeletes, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'tenant_id','job_number','customer_id','vehicle_id','claim_id','quote_id',
        'assigned_to','job_type','status','progress_percent','priority',
        'start_date','expected_end_date','actual_end_date','delivery_date',
        'estimated_amount','actual_amount',
        'description','technical_notes','internal_notes','created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'delivery_date' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('before_photos')->useDisk('public');
        $this->addMediaCollection('after_photos')->useDisk('public');
        $this->addMediaCollection('during_photos')->useDisk('public');
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function quote(): BelongsTo { return $this->belongsTo(Quote::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(WorkOrderItem::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeActive($q) { return $q->whereIn('status', ['attesa','in_lavorazione','attesa_ricambi']); }
    public function scopeOverdue($q) {
        return $q->where('expected_end_date', '<', now())->whereNotIn('status', ['completato','consegnato','annullato']);
    }

    public function isOverdue(): bool {
        return $this->expected_end_date && $this->expected_end_date->isPast()
            && !in_array($this->status, ['completato','consegnato','annullato']);
    }

    public function calculateTotal(): float {
        return $this->items()->sum('total_price');
    }

    public static function generateNumber(int $tenantId): string {
        $year = now()->year;
        $last = static::where('tenant_id', $tenantId)->whereYear('created_at', $year)->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->job_number, -3)) + 1) : 1;
        return "LAV-{$year}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
