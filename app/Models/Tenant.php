<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','slug','email','phone','address','city','postal_code',
        'province','vat_number','fiscal_code','logo_path','plan','active','settings'
    ];

    protected $casts = ['settings' => 'array', 'active' => 'boolean'];

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function customers(): HasMany { return $this->hasMany(Customer::class); }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function claims(): HasMany { return $this->hasMany(Claim::class); }
    public function workOrders(): HasMany { return $this->hasMany(WorkOrder::class); }
    public function fleetVehicles(): HasMany { return $this->hasMany(FleetVehicle::class); }
    public function rentals(): HasMany { return $this->hasMany(Rental::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function insuranceCompanies(): HasMany { return $this->hasMany(InsuranceCompany::class); }
    public function experts(): HasMany { return $this->hasMany(Expert::class); }
    public function mailTemplates(): HasMany { return $this->hasMany(MailTemplate::class); }
    public function spareParts(): HasMany { return $this->hasMany(SparePart::class); }
}
