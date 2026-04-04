<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id','name','email','password','role',
        'phone','avatar_path','active','last_login_at'
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }

    public function getFullNameAttribute(): string { return $this->name; }

    // Scope per tenant corrente
    public function scopeForTenant($query, int $tenantId) {
        return $query->where('tenant_id', $tenantId);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManager(): bool { return in_array($this->role, ['admin','manager']); }
}
