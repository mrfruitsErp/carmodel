<?php

namespace App\Jobs\Marketplace;

use App\Models\MarketplaceListing;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishToAutoScout24Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 5;
    public int $timeout = 60;
    public int $backoff = 30;

    public function __construct(
        public readonly int $listingId,
        public readonly int $tenantId
    ) {}

    public function handle(MarketplaceManager $manager): void
    {
        $listing   = MarketplaceListing::findOrFail($this->listingId);
        $vehicle   = $listing->saleVehicle;
        $connector = $manager->connector('autoscout24', $this->tenantId);
        $connector->refreshTokenIfNeeded();
        $result = $connector->publish($vehicle, $listing);

        if ($result['success']) {
            $listing->markPublished($result['external_id'], $result['external_url']);
        } else {
            $listing->markError($result['message']);
            $this->fail(new \RuntimeException($result['message']));
        }
    }
}