<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FleetVehicle extends Model {
    use SoftDeletes;
    protected $fillable = [
        'tenant_id','plate','vin','brand','model','year','color','fuel_type',
        'category','seats','km_current','km_last_service',
        'revision_expiry','insurance_expiry','insurance_company','insurance_policy',
        'status','daily_rate','purchase_price','purchase_date','notes'
    ];
    protected $casts = ['revision_expiry'=>'date','insurance_expiry'=>'date','purchase_date'=>'date'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function rentals(): HasMany { return $this->hasMany(Rental::class); }
    public function getFullNameAttribute(): string { return "{$this->brand} {$this->model} ({$this->plate})"; }
    public function scopeAvailable($q) { return $q->where('status','disponibile'); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
}
