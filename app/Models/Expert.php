<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expert extends Model {
    protected $fillable = [
        'tenant_id','type','name','title','company_name','insurance_company_id',
        'email','pec','phone','phone2','orario_disponibilita','address','fiscal_code','vat_number','rating','notes','active'
    ];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function insuranceCompany(): BelongsTo { return $this->belongsTo(InsuranceCompany::class); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public function scopePeriti($q) { return $q->where('type','perito'); }
    public function scopeAvvocati($q) { return $q->where('type','avvocato'); }
}