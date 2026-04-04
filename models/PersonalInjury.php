<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalInjury extends Model {
    use SoftDeletes;
    protected $fillable = [
        'tenant_id','injury_number','claim_id','customer_id','lawyer_id','doctor_id',
        'injury_type','injury_description','icd_code','status',
        'medical_visit_date','medical_report_date',
        'estimated_amount','agreed_amount','paid_amount','paid_date','notes'
    ];
    protected $casts = ['medical_visit_date'=>'date','medical_report_date'=>'date','paid_date'=>'date'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function lawyer(): BelongsTo { return $this->belongsTo(Expert::class,'lawyer_id'); }
    public function doctor(): BelongsTo { return $this->belongsTo(Expert::class,'doctor_id'); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public static function generateNumber(int $tenantId): string {
        $year = now()->year;
        $last = static::where('tenant_id',$tenantId)->whereYear('created_at',$year)->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->injury_number,-3))+1) : 1;
        return "LES-{$year}-".str_pad($seq,3,'0',STR_PAD_LEFT);
    }
}
