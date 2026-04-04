<?php

namespace App\Services\Marketplace\Contracts;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;

interface PlatformConnector
{
    public function platformKey(): string;
    public function platformName(): string;

    public function testConnection(): array;

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array;
    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array;
    public function updatePrice(MarketplaceListing $listing, float $newPrice): array;
    public function pause(MarketplaceListing $listing): array;
    public function resume(MarketplaceListing $listing): array;
    public function delete(MarketplaceListing $listing): array;
    public function fetchStats(MarketplaceListing $listing): array;
    public function fetchLeads(MarketplaceListing $listing): array;
    public function refreshTokenIfNeeded(): bool;
    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array;
    public function requiredFields(): array;
    public function validate(SaleVehicle $vehicle): array;
}