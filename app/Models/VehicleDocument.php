<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehicleDocument extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'tenant_id','vehicle_id','uploaded_by','tipo',
        'nome','data_emissione','data_scadenza','note','attivo'
    ];

    protected $casts = [
        'data_emissione' => 'date',
        'data_scadenza'  => 'date',
        'attivo'         => 'boolean',
    ];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('file')->useDisk('public')->singleFile();
    }

    public function isScaduto(): bool {
        return $this->data_scadenza && $this->data_scadenza->isPast();
    }

    public function isInScadenza(int $giorni = 30): bool {
        return $this->data_scadenza &&
            !$this->data_scadenza->isPast() &&
            $this->data_scadenza->diffInDays(now()) <= $giorni;
    }

    public static function tipi(): array {
        return [
            'libretto'   => 'Libretto circolazione',
            'polizza'    => 'Polizza assicurativa',
            'revisione'  => 'Revisione',
            'bollo'      => 'Bollo auto',
            'cid'        => 'CID / Constatazione amichevole',
            'perizia'    => 'Perizia',
            'atto_vendita' => 'Atto di vendita',
            'altro'      => 'Altro documento',
        ];
    }
}