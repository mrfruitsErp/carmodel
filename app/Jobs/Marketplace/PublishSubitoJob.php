<?php

namespace App\Jobs\Marketplace;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceCredential;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishSubitoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 300;

    public function __construct(
        public readonly int $listingId,
        public readonly int $tenantId
    ) {}

    public function handle(): void
    {
        $listing     = MarketplaceListing::findOrFail($this->listingId);
        $vehicle     = $listing->saleVehicle;
        $credentials = MarketplaceCredential::forTenant($this->tenantId)
            ->where('platform', 'subito_it')
            ->first();

        if (!$credentials) {
            $listing->markError('Credenziali Subito.it non trovate');
            return;
        }

        // In locale simula successo
        if (app()->environment('local', 'testing')) {
            $fakeId  = 'SIM-' . strtoupper(uniqid());
            $fakeUrl = "https://www.subito.it/auto/" . strtolower("{$vehicle->brand}-{$vehicle->model}/{$fakeId}.htm");
            $listing->markPublished($fakeId, $fakeUrl);
            Log::info("[SubitoJob] SIMULAZIONE listing #{$listing->id} pubblicato come {$fakeId}");
            return;
        }

        // TODO: implementa Playwright/Puppeteer microservice per produzione
        $listing->markError('Automazione Subito.it non implementata in produzione. Pubblica manualmente.');
    }
}