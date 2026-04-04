<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model {
    use SoftDeletes;
    protected $fillable = [
        'tenant_id','document_number','document_type','customer_id',
        'work_order_id','claim_id','rental_id',
        'issue_date','due_date','subtotal','discount_amount','vat_percent','vat_amount','total',
        'payment_status','payment_date','payment_method',
        'sdi_status','sdi_id','pdf_path','xml_path','notes','created_by'
    ];
    protected $casts = ['issue_date'=>'date','due_date'=>'date','payment_date'=>'date','total'=>'decimal:2'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function workOrder(): BelongsTo { return $this->belongsTo(WorkOrder::class); }
    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function rental(): BelongsTo { return $this->belongsTo(Rental::class); }
    public function items(): HasMany { return $this->hasMany(DocumentItem::class); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public function scopeUnpaid($q) { return $q->whereIn('payment_status',['da_pagare','scaduta']); }
    public static function generateNumber(int $tenantId, string $type): string {
        $year = now()->year;
        $prefix = match($type) { 'fattura'=>'FT','ddt'=>'DDT','nota_credito'=>'NC', default=>'DOC' };
        $last = static::where('tenant_id',$tenantId)->where('document_type',$type)->whereYear('created_at',$year)->orderByDesc('id')->first();
        $seq = $last ? (intval(substr($last->document_number,-3))+1) : 1;
        return "{$prefix}-{$year}-".str_pad($seq,3,'0',STR_PAD_LEFT);
    }
}
