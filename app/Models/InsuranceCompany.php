<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsuranceCompany extends Model {
    protected $fillable = ['tenant_id','name','code','email','phone','fax','address','portal_url','notes','active'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
    public function experts(): HasMany { return $this->hasMany(Expert::class); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
}
