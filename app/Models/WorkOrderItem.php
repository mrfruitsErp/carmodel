<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderItem extends Model {
    public $timestamps = false;
    protected $fillable = ['work_order_id','item_type','description','quantity','unit_price','discount_percent','total_price','sort_order'];
    protected $casts = ['quantity'=>'decimal:2','unit_price'=>'decimal:2','discount_percent'=>'decimal:2','total_price'=>'decimal:2'];
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function calculateTotal(): float {
        return round($this->quantity * $this->unit_price * (1 - $this->discount_percent / 100), 2);
    }
}
