<?php

namespace App\Jobs\Marketplace;

use App\Models\Tenant;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MarketplaceDailySync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function handle(MarketplaceManager $manager): void
    {
        $tenants = Tenant::where('active', true)->pluck('id');

        foreach ($tenants as $tenantId) {
            foreach ($manager->enabledPlatforms($tenantId) as $platform) {
                try {
                    $manager->connector($platform, $tenantId)->refreshTokenIfNeeded();
                } catch (\Throwable) {}
            }

            SyncMarketplaceStatsJob::dispatch($tenantId)->onQueue('marketplace');
            FetchMarketplaceLeadsJob::dispatch($tenantId)->onQueue('marketplace');
        }

        Log::info('[MarketplaceDailySync] Avviato sync per ' . $tenants->count() . ' tenant');
    }
}