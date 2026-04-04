<?php

namespace App\Jobs\Marketplace;

use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchMarketplaceLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(public readonly int $tenantId) {}

    public function handle(MarketplaceManager $manager): void
    {
        $imported = $manager->fetchAllLeads($this->tenantId);
        Log::info("[FetchLeads] Tenant {$this->tenantId}: importati {$imported} lead");
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[FetchLeads] Tenant {$this->tenantId} failed: {$e->getMessage()}");
    }
}