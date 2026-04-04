<?php
namespace App\Observers;

use App\Models\Claim;
use App\Jobs\SendClaimNotification;

class ClaimObserver
{
    public function created(Claim $claim): void
    {
        // Invia mail automatica apertura sinistro
        SendClaimNotification::dispatch($claim, 'claim_opened');
        // Aggiorna stato veicolo
        $claim->vehicle->update(['status' => 'in_officina']);
    }

    public function updated(Claim $claim): void
    {
        if ($claim->isDirty('status')) {
            // Log cambio stato
            $claim->statusHistory()->create([
                'status' => $claim->status,
                'changed_by' => auth()->id(),
            ]);
            // Trigger mail se stato specifico
            if ($claim->status === 'perizia_attesa') {
                SendClaimNotification::dispatch($claim, 'survey_scheduled');
            }
            if ($claim->status === 'liquidato') {
                $claim->vehicle->update(['status' => 'pronto']);
            }
        }
    }
}
