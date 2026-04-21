<?php
namespace App\Observers;
use App\Models\Claim;
use App\Jobs\SendClaimNotification;
class ClaimObserver
{
    public function created(Claim $claim): void
    {
        try {
            SendClaimNotification::dispatch($claim, 'claim_opened');
        } catch (\Exception $e) {}
        if ($claim->vehicle) {
            try {
                $claim->vehicle->update(['status' => 'in_officina']);
            } catch (\Exception $e) {}
        }
    }

    public function updated(Claim $claim): void
    {
        if ($claim->isDirty('status')) {
            try {
                $claim->statusHistory()->create([
                    'status'     => $claim->status,
                    'changed_by' => auth()->id(),
                ]);
            } catch (\Exception $e) {}
            try {
                if ($claim->status === 'perizia_attesa') {
                    SendClaimNotification::dispatch($claim, 'survey_scheduled');
                }
                if ($claim->status === 'liquidato' && $claim->vehicle) {
                    $claim->vehicle->update(['status' => 'pronto']);
                }
            } catch (\Exception $e) {}
        }
    }
}
