<?php

namespace App\Services\Marketplace;

use App\Models\MarketplaceCredential;
use App\Models\MarketplaceLead;
use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\Contracts\PlatformConnector;
use App\Services\Marketplace\Platforms\AutoScout24Platform;
use App\Services\Marketplace\Platforms\AutomobileItPlatform;
use App\Services\Marketplace\Platforms\EbayMotorsPlatform;
use App\Services\Marketplace\Platforms\SubitoItPlatform;
use App\Services\Marketplace\Platforms\FacebookMarketplacePlatform;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketplaceManager
{
    private const PLATFORM_MAP = [
        'autoscout24'          => AutoScout24Platform::class,
        'automobile_it'        => AutomobileItPlatform::class,
        'ebay_motors'          => EbayMotorsPlatform::class,
        'subito_it'            => SubitoItPlatform::class,
        'facebook_marketplace' => FacebookMarketplacePlatform::class,
    ];

    public function connector(string $platform, int $tenantId): PlatformConnector
    {
        $class = self::PLATFORM_MAP[$platform] ?? null;

        if (!$class) {
            throw new \InvalidArgumentException("Piattaforma '{$platform}' non supportata.");
        }

        return new $class($tenantId);
    }

    public function allPlatforms(): array
    {
        return array_keys(self::PLATFORM_MAP);
    }

    public function enabledPlatforms(int $tenantId): Collection
    {
        return MarketplaceCredential::forTenant($tenantId)
            ->enabled()
            ->pluck('platform');
    }

    // ── Pubblicazione ─────────────────────────────────────────────────────────

    public function publishVehicle(SaleVehicle $vehicle, ?array $platforms = null, ?float $price = null): array
    {
        $platforms = $platforms ?? $this->enabledPlatforms($vehicle->tenant_id)->toArray();
        $results   = [];

        foreach ($platforms as $platform) {
            $results[$platform] = $this->publishOnPlatform($vehicle, $platform, $price);
        }

        if (collect($results)->contains('success', true) && $vehicle->status === 'bozza') {
            $vehicle->update(['status' => 'attivo']);
        }

        return $results;
    }

    public function publishOnPlatform(SaleVehicle $vehicle, string $platform, ?float $price = null): array
    {
        DB::beginTransaction();

        try {
            $listing = MarketplaceListing::updateOrCreate(
                ['sale_vehicle_id' => $vehicle->id, 'platform' => $platform],
                [
                    'tenant_id'    => $vehicle->tenant_id,
                    'status'       => 'publishing',
                    'listed_price' => $price ?? $vehicle->asking_price,
                ]
            );

            $connector = $this->connector($platform, $vehicle->tenant_id);
            $result    = $connector->publish($vehicle, $listing);

            if ($result['success']) {
                $listing->markPublished(
                    $result['external_id'] ?? '',
                    $result['external_url'] ?? ''
                );
            } else {
                $listing->markError($result['message']);
            }

            DB::commit();

            return [
                'success'      => $result['success'],
                'message'      => $result['message'],
                'external_url' => $result['external_url'] ?? null,
                'listing_id'   => $listing->id,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("[MarketplaceManager] Publish error on {$platform}: {$e->getMessage()}", [
                'vehicle_id' => $vehicle->id,
                'tenant_id'  => $vehicle->tenant_id,
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ── Aggiornamento ─────────────────────────────────────────────────────────

    public function updateVehicle(SaleVehicle $vehicle): array
    {
        $results = [];

        foreach ($vehicle->listings()->published()->get() as $listing) {
            $connector = $this->connector($listing->platform, $vehicle->tenant_id);
            $result    = $connector->update($vehicle, $listing);

            if ($result['success']) {
                $listing->update(['last_synced_at' => now()]);
            } else {
                $listing->markError($result['message']);
            }

            $results[$listing->platform] = $result;
        }

        return $results;
    }

    public function updatePrice(SaleVehicle $vehicle, float $newPrice): array
    {
        $results = [];
        $vehicle->update(['asking_price' => $newPrice]);

        foreach ($vehicle->listings()->published()->get() as $listing) {
            $connector                   = $this->connector($listing->platform, $vehicle->tenant_id);
            $results[$listing->platform] = $connector->updatePrice($listing, $newPrice);
        }

        return $results;
    }

    // ── Rimozione ─────────────────────────────────────────────────────────────

    public function unpublish(MarketplaceListing $listing): array
    {
        $connector = $this->connector($listing->platform, $listing->tenant_id);
        $result    = $connector->delete($listing);

        if ($result['success']) {
            $listing->update(['status' => 'deleted']);
        }

        return $result;
    }

    public function unpublishAll(SaleVehicle $vehicle): array
    {
        $results = [];

        foreach ($vehicle->listings()->whereIn('status', ['published','paused'])->get() as $listing) {
            $results[$listing->platform] = $this->unpublish($listing);
        }

        return $results;
    }

    // ── Sync stats ────────────────────────────────────────────────────────────

    public function syncStats(int $tenantId): int
    {
        $updated = 0;

        MarketplaceListing::forTenant($tenantId)
            ->published()
            ->chunk(50, function ($listings) use (&$updated) {
                foreach ($listings as $listing) {
                    try {
                        $connector = $this->connector($listing->platform, $listing->tenant_id);
                        $stats     = $connector->fetchStats($listing);

                        $listing->update([
                            'views'          => $stats['views'],
                            'contacts'       => $stats['contacts'],
                            'favorites'      => $stats['favorites'],
                            'last_synced_at' => now(),
                        ]);

                        $updated++;
                    } catch (\Throwable $e) {
                        Log::warning("[MarketplaceManager] Stats sync failed #{$listing->id}: {$e->getMessage()}");
                    }
                }
            });

        return $updated;
    }

    // ── Lead ──────────────────────────────────────────────────────────────────

    public function fetchAllLeads(int $tenantId): int
    {
        $imported = 0;

        MarketplaceListing::forTenant($tenantId)
            ->published()
            ->chunk(50, function ($listings) use (&$imported) {
                foreach ($listings as $listing) {
                    $imported += $this->fetchLeadsForListing($listing);
                }
            });

        return $imported;
    }

    public function fetchLeadsForListing(MarketplaceListing $listing): int
    {
        try {
            $connector = $this->connector($listing->platform, $listing->tenant_id);
            $leads     = $connector->fetchLeads($listing);
            $imported  = 0;

            foreach ($leads as $leadData) {
                $exists = MarketplaceLead::where('marketplace_listing_id', $listing->id)
                    ->where('external_lead_id', $leadData['external_id'])
                    ->exists();

                if (!$exists) {
                    MarketplaceLead::create([
                        'tenant_id'              => $listing->tenant_id,
                        'marketplace_listing_id' => $listing->id,
                        'sale_vehicle_id'        => $listing->sale_vehicle_id,
                        'platform'               => $listing->platform,
                        'external_lead_id'       => $leadData['external_id'],
                        'lead_name'              => $leadData['name'],
                        'lead_email'             => $leadData['email'],
                        'lead_phone'             => $leadData['phone'],
                        'lead_message'           => $leadData['message'],
                        'raw_data'               => $leadData['raw'] ?? null,
                        'status'                 => 'nuovo',
                    ]);
                    $imported++;
                }
            }

            return $imported;

        } catch (\Throwable $e) {
            Log::warning("[MarketplaceManager] Lead fetch failed #{$listing->id}: {$e->getMessage()}");
            return 0;
        }
    }

    // ── Validazione ───────────────────────────────────────────────────────────

    public function validateForPlatforms(SaleVehicle $vehicle, array $platforms): array
    {
        $results = [];

        foreach ($platforms as $platform) {
            $connector          = $this->connector($platform, $vehicle->tenant_id);
            $results[$platform] = $connector->validate($vehicle);
        }

        return $results;
    }

    // ── Dashboard stats ───────────────────────────────────────────────────────

    public function dashboardStats(int $tenantId): array
    {
        return [
            'vehicles_active'    => SaleVehicle::forTenant($tenantId)->where('status', 'attivo')->count(),
            'vehicles_draft'     => SaleVehicle::forTenant($tenantId)->where('status', 'bozza')->count(),
            'vehicles_sold'      => SaleVehicle::forTenant($tenantId)->where('status', 'venduto')->count(),
            'listings_published' => MarketplaceListing::forTenant($tenantId)->where('status', 'published')->count(),
            'listings_error'     => MarketplaceListing::forTenant($tenantId)->where('status', 'error')->count(),
            'total_views'        => MarketplaceListing::forTenant($tenantId)->sum('views'),
            'total_contacts'     => MarketplaceListing::forTenant($tenantId)->sum('contacts'),
            'leads_new'          => MarketplaceLead::forTenant($tenantId)->where('status', 'nuovo')->count(),
            'leads_total'        => MarketplaceLead::forTenant($tenantId)->count(),
            'by_platform'        => MarketplaceListing::forTenant($tenantId)
                ->where('status', 'published')
                ->selectRaw('platform, count(*) as cnt, sum(views) as views, sum(contacts) as contacts')
                ->groupBy('platform')
                ->get()
                ->keyBy('platform')
                ->toArray(),
        ];
    }
}