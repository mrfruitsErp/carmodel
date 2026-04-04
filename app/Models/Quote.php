<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model {
    use SoftDeletes;
    protected $fillable = [
        'tenant_id','quote_number','customer_id','vehicle_id','claim_id','status','job_type',
        'description','subtotal','discount_percent','discount_amount','vat_percent','vat_amount','total',
        'valid_until','notes','converted_to_job_id','created_by'
    ];
    protected $casts = ['valid_until'=>'date','total'=>'decimal:2','subtotal'=>'decimal:2'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function items(): HasMany { return $this->hasMany(QuoteItem::class); }
    public function convertedJob(): BelongsTo { return $this->belongsTo(WorkOrder::class,'converted_to_job_id'); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public function recalculate(): void {
        $sub = $this->items()->sum('total_price');
        $disc = round($sub * $this->discount_percent / 100, 2);
        $taxable = $sub - $disc;
        $vat = round($taxable * $this->vat_percent / 100, 2);
        $this->update(['subtotal'=>$sub,'discount_amount'=>$disc,'vat_amount'=>$vat,'total'=>$taxable+$vat]);
    }
    public static function generateNumber(int $tenantId): string {
        $year = now()->year;
        $last = static::where('tenant_id',$tenantId)->whereYear('created_at',$year)->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->quote_number,-3))+1) : 1;
        return "PRV-{$year}-".str_pad($seq,3,'0',STR_PAD_LEFT);
    }
}
