<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quote_id','item_type','description','quantity','unit_price','discount_percent','total_price','sort_order',
    ];

    protected $casts = [
        'quantity'         => 'decimal:2',
        'unit_price'       => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_price'      => 'decimal:2',
    ];

    public function quote(): BelongsTo { return $this->belongsTo(Quote::class); }

    public static function calcTotal(float $qty, float $unitPrice, float $discountPercent = 0): float
    {
        $gross = $qty * $unitPrice;
        return round($gross - ($gross * $discountPercent / 100), 2);
    }
}
