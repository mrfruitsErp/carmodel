<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Claim extends Model implements HasMedia
{
    use SoftDeletes, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'tenant_id','claim_number','customer_id','vehicle_id',
        'insurance_company_id','expert_id','claim_type',
        'event_date','event_location','event_description',
        'counterpart_plate','counterpart_insurance','counterpart_policy',
        'policy_number','policy_expiry',
        'cid_signed','cid_date','cid_expiry',
        'status','estimated_amount','approved_amount','paid_amount','paid_date',
        'survey_date','survey_notes','notes','internal_notes',
        'assigned_to','created_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'policy_expiry' => 'date',
        'cid_date' => 'date',
        'cid_expiry' => 'date',
        'survey_date' => 'date',
        'paid_date' => 'date',
        'cid_signed' => 'boolean',
        'estimated_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('claim_documents')->useDisk('public');
        $this->addMediaCollection('vehicle_photos')->useDisk('public');
    }

    // Relazioni
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function insuranceCompany(): BelongsTo { return $this->belongsTo(InsuranceCompany::class); }
    public function expert(): BelongsTo { return $this->belongsTo(Expert::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
   
    public function personalInjuries(): HasMany { return $this->hasMany(PersonalInjury::class); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class); }
    public function rentals(): HasMany { return $this->hasMany(Rental::class); }

    // Scopes
    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
    public function scopeOpen($q) { return $q->whereNotIn('status', ['chiuso','archiviato']); }
    public function scopeUrgent($q) {
        return $q->where('cid_expiry', '<=', now()->addDays(7))
                 ->whereNotIn('status', ['chiuso','archiviato','liquidato']);
    }
    public function scopeSearch($q, string $term) {
        return $q->where(fn($s) => $s
            ->where('claim_number', 'like', "%{$term}%")
            ->orWhereHas('customer', fn($c) => $c->search($term))
            ->orWhereHas('vehicle', fn($v) => $v->search($term))
        );
    }

    // Helper
    public function isCidExpiringSoon(): bool {
        return $this->cid_expiry && $this->cid_expiry->diffInDays(now()) <= 7 && !in_array($this->status, ['chiuso','archiviato']);
    }

    public function isOverdue(): bool {
        return $this->cid_expiry && $this->cid_expiry->isPast() && !in_array($this->status, ['chiuso','archiviato']);
    }

    public function updateStatus(string $newStatus, ?string $notes = null, ?int $userId = null): void {
        $this->update(['status' => $newStatus]);
        $this->statusHistory()->create([
            'status' => $newStatus,
            'notes' => $notes,
            'changed_by' => $userId ?? auth()->id(),
        ]);
    }

    // Numerazione automatica
    public static function generateNumber(int $tenantId): string {
        $year = now()->year;
        $last = static::where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->claim_number, -3)) + 1) : 1;
        return "SIN-{$year}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
