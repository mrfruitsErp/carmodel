<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model {
    protected $fillable = ['tenant_id','code','name','description','category','brand','unit','stock_quantity','min_stock','purchase_price','sale_price','supplier','location','active'];
    protected $casts = ['stock_quantity'=>'decimal:2','purchase_price'=>'decimal:2','sale_price'=>'decimal:2'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function movements(): HasMany { return $this->hasMany(SparePartMovement::class); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public function scopeLowStock($q) { return $q->whereRaw('stock_quantity <= min_stock'); }
    public function isLowStock(): bool { return $this->stock_quantity <= $this->min_stock; }
}
