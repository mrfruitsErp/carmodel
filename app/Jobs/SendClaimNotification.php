<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Models\{Claim, MailTemplate, MailLog};
use Illuminate\Support\Facades\Mail;
use App\Mail\ClaimNotificationMail;

class SendClaimNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Claim $claim,
        public string $triggerEvent
    ) {}

    public function handle(): void
    {
        $template = MailTemplate::forTenant($this->claim->tenant_id)
            ->active()
            ->forEvent($this->triggerEvent)
            ->first();

        if (!$template) return;

        $customer = $this->claim->customer;
        if (!$customer->email) return;

        $variables = [
            'cliente'        => $customer->display_name,
            'sinistro'       => $this->claim->claim_number,
            'targa'          => $this->claim->vehicle->plate,
            'veicolo'        => $this->claim->vehicle->full_name,
            'compagnia'      => $this->claim->insuranceCompany?->name ?? '',
            'data_sinistro'  => $this->claim->event_date->format('d/m/Y'),
            'scadenza_cid'   => $this->claim->cid_expiry?->format('d/m/Y') ?? '',
            'data_perizia'   => $this->claim->survey_date?->format('d/m/Y') ?? '',
            'perito'         => $this->claim->expert?->name ?? '',
        ];

        $body = $template->render($variables);
        $subject = $template->render(['subject' => $template->subject]);

        try {
            Mail::to($customer->email, $customer->display_name)
                ->send(new ClaimNotificationMail($subject, $body));

            MailLog::create([
                'tenant_id'    => $this->claim->tenant_id,
                'template_id'  => $template->id,
                'to_email'     => $customer->email,
                'to_name'      => $customer->display_name,
                'subject'      => $subject,
                'customer_id'  => $customer->id,
                'claim_id'     => $this->claim->id,
                'status'       => 'sent',
                'sent_at'      => now(),
                'is_automatic' => true,
            ]);
        } catch (\Exception $e) {
            MailLog::create([
                'tenant_id'    => $this->claim->tenant_id,
                'template_id'  => $template->id,
                'to_email'     => $customer->email,
                'subject'      => $subject,
                'customer_id'  => $customer->id,
                'claim_id'     => $this->claim->id,
                'status'       => 'failed',
                'error_message' => $e->getMessage(),
                'is_automatic' => true,
            ]);
        }
    }
}
