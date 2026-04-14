<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'tenant_id', 'action', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }

    public function scopeForTenant($q, $tid) { return $q->where('tenant_id', $tid); }
}