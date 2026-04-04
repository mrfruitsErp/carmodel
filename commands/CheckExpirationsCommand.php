<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Claim, Rental, Tenant};
use App\Jobs\SendClaimNotification;
use App\Jobs\SendRentalExpiryNotification;

class CheckExpirationsCommand extends Command
{
    protected $signature = 'carmodel:check-expirations';
    protected $description = 'Controlla scadenze CID e noleggi e invia notifiche automatiche';

    public function handle(): void
    {
        $this->info('Controllo scadenze...');

        // CID in scadenza entro 48h
        $expiring = Claim::open()
            ->whereBetween('cid_expiry', [now(), now()->addHours(48)])
            ->with(['customer','vehicle','insuranceCompany'])
            ->get();

        foreach ($expiring as $claim) {
            SendClaimNotification::dispatch($claim, 'cid_expiry_48h');
            $this->line("CID: {$claim->claim_number} — {$claim->customer->display_name}");
        }

        // Noleggi in scadenza entro 24h
        $rentals = Rental::active()
            ->expiringSoon(1)
            ->with(['customer','fleetVehicle'])
            ->get();

        foreach ($rentals as $rental) {
            SendRentalExpiryNotification::dispatch($rental);
            $this->line("Noleggio: {$rental->rental_number} — {$rental->customer->display_name}");
        }

        // Noleggi scaduti → aggiorna stato
        Rental::active()->overdue()->update(['status' => 'scaduto']);

        $this->info("Fatto. CID: {$expiring->count()} | Noleggi: {$rentals->count()}");
    }
}
