<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailTemplate extends Model {
    protected $fillable = ['tenant_id','name','trigger_event','subject','body_html','body_text','active'];
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function scopeForTenant($q,$tid) { return $q->where('tenant_id',$tid); }
    public function scopeActive($q) { return $q->where('active',true); }
    public function scopeForEvent($q, string $event) { return $q->where('trigger_event',$event); }
    // Sostituisce variabili nel template: {{cliente}}, {{targa}} ecc.
    public function render(array $variables): string {
        $body = $this->body_html;
        foreach ($variables as $key => $value) {
            $body = str_replace("{{{$key}}}", $value, $body);
        }
        return $body;
    }
}
