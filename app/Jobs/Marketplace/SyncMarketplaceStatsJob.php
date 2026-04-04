<?php

namespace App\Jobs\Marketplace;

use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncMarketplaceStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly int $tenantId) {}

    public function handle(MarketplaceManager $manager): void
    {
        $updated = $manager->syncStats($this->tenantId);
        Log::info("[SyncStats] Tenant {$this->tenantId}: aggiornati {$updated} listing");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[SyncStats] Tenant {$this->tenantId} failed: {$e->getMessage()}");
    }
}