<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'password', 'role',
        'phone', 'avatar_path', 'active', 'last_login_at',
        'custom_permissions', 'notes',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'last_login_at'      => 'datetime',
        'active'             => 'boolean',
        'password'           => 'hashed',
        'custom_permissions' => 'array',
    ];

    // Tutti i permessi disponibili nel sistema
    public const ALL_PERMISSIONS = [
        'clienti'      => ['view' => 'Vedere clienti',      'edit' => 'Modificare clienti'],
        'veicoli'      => ['view' => 'Vedere veicoli',      'edit' => 'Modificare veicoli'],
        'sinistri'     => ['view' => 'Vedere sinistri',     'edit' => 'Modificare sinistri'],
        'lesioni'      => ['view' => 'Vedere lesioni',      'edit' => 'Modificare lesioni'],
        'periti'       => ['view' => 'Vedere periti',       'edit' => 'Modificare periti'],
        'lavorazioni'  => ['view' => 'Vedere lavorazioni',  'edit' => 'Modificare lavorazioni'],
        'preventivi'   => ['view' => 'Vedere preventivi',   'edit' => 'Modificare preventivi'],
        'noleggio'     => ['view' => 'Vedere noleggio',     'edit' => 'Modificare noleggio'],
        'fatture'      => ['view' => 'Vedere fatture',      'edit' => 'Modificare fatture'],
        'marketplace'  => ['view' => 'Vedere marketplace',  'edit' => 'Modificare marketplace'],
        'ricambi'      => ['view' => 'Vedere ricambi',      'edit' => 'Modificare ricambi'],
        'utenti'       => ['manage' => 'Gestire utenti'],
        'impostazioni' => ['manage' => 'Gestire impostazioni'],
    ];

    // Permessi default per ruolo
    public const ROLE_DEFAULTS = [
        'admin' => '*', // tutto
        'manager' => [
            'clienti.view','clienti.edit',
            'veicoli.view','veicoli.edit',
            'sinistri.view','sinistri.edit',
            'lesioni.view','lesioni.edit',
            'periti.view','periti.edit',
            'lavorazioni.view','lavorazioni.edit',
            'preventivi.view','preventivi.edit',
            'noleggio.view','noleggio.edit',
            'fatture.view','fatture.edit',
            'marketplace.view','marketplace.edit',
            'ricambi.view','ricambi.edit',
        ],
        'operatore' => [
            'clienti.view','clienti.edit',
            'veicoli.view','veicoli.edit',
            'sinistri.view','sinistri.edit',
            'lesioni.view','lesioni.edit',
            'periti.view',
            'lavorazioni.view','lavorazioni.edit',
            'preventivi.view','preventivi.edit',
            'noleggio.view',
            'ricambi.view',
        ],
        'vendite' => [
            'clienti.view',
            'marketplace.view','marketplace.edit',
        ],
    ];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function accessLogs(): HasMany { return $this->hasMany(UserAccessLog::class); }

    public function scopeForTenant($query, int $tenantId) {
        return $query->where('tenant_id', $tenantId);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManager(): bool { return in_array($this->role, ['admin','manager']); }

    public function canDo(string $permission): bool
    {
        return $this->hasAccess($permission);
    }

    public function hasAccess(string $permission): bool
    {
        if ($this->role === 'admin') return true;

        // Controlla custom_permissions prima
        $custom = $this->custom_permissions ?? [];
        if (isset($custom[$permission])) {
            return (bool) $custom[$permission];
        }

        // Fallback ai default del ruolo
        $defaults = self::ROLE_DEFAULTS[$this->role] ?? [];
        return in_array($permission, $defaults);
    }
}