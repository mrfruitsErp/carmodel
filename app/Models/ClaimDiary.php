<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDiary extends Model
{
    protected $table = 'claim_diary';

    protected $fillable = [
        'tenant_id','claim_id','user_id',
        'data_evento','tipo','oggetto','testo','importo',
    ];

    protected $casts = [
        'data_evento' => 'date',
        'importo'     => 'decimal:2',
    ];

    public function claim(): BelongsTo { return $this->belongsTo(Claim::class); }
    public function user(): BelongsTo  { return $this->belongsTo(User::class); }

    public static function tipiLabel(): array
    {
        return [
            'nota'          => '📝 Nota',
            'chiamata'      => '📞 Chiamata',
            'mail_inviata'  => '✉️ Mail inviata',
            'mail_ricevuta' => '📩 Mail ricevuta',
            'pec_inviata'   => '📮 PEC inviata',
            'pec_ricevuta'  => '📬 PEC ricevuta',
            'incontro'      => '🤝 Incontro',
            'sollecito'     => '⚠️ Sollecito',
            'pagamento'     => '💰 Pagamento',
            'altro'         => '🔹 Altro',
        ];
    }

    public function getTipoIconAttribute(): string
    {
        return match($this->tipo) {
            'nota'          => '📝',
            'chiamata'      => '📞',
            'mail_inviata'  => '✉️',
            'mail_ricevuta' => '📩',
            'pec_inviata'   => '📮',
            'pec_ricevuta'  => '📬',
            'incontro'      => '🤝',
            'sollecito'     => '⚠️',
            'pagamento'     => '💰',
            default         => '🔹',
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'sollecito'     => 'red',
            'pagamento'     => 'green',
            'mail_inviata','mail_ricevuta','pec_inviata','pec_ricevuta' => 'blue',
            'chiamata'      => 'amber',
            default         => 'gray',
        ];
    }
}
