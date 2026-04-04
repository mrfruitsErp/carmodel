<?php

namespace App\Services\Marketplace\Platforms;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\BasePlatform;

class AutomobileItPlatform extends BasePlatform
{
    private const BASE_URL = 'https://api.automobile.it/v2';

    public function platformKey(): string  { return 'automobile_it'; }
    public function platformName(): string { return 'Automobile.it'; }

    private function authHeaders(): array
    {
        return [
            'X-Api-Key'    => $this->credential('api_key'),
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) return ['ok' => false, 'message' => 'API key non configurata'];

        $result = $this->apiRequest('GET', self::BASE_URL . '/account', [], $this->authHeaders(), 'refresh_token');

        return [
            'ok'      => $result['success'],
            'message' => $result['success'] ? 'Connessione Automobile.it OK' : 'API key non valida',
        ];
    }

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $validation = $this->validate($vehicle);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => implode(', ', $validation['errors']), 'raw' => []];
        }

        $payload = $this->buildPayload($vehicle, $listing);
        $result  = $this->apiRequest('POST', self::BASE_URL . '/listings', $payload, $this->authHeaders(), 'publish', $listing->id);

        if ($result['success']) {
            $id = $result['body']['id'] ?? null;

            // Upload foto come media separato
            if ($id) {
                foreach ($vehicle->getMedia('sale_photos') as $media) {
                    try {
                        \Illuminate\Support\Facades\Http::withHeaders(['X-Api-Key' => $this->credential('api_key')])
                            ->attach('file', file_get_contents($media->getPath()), $media->file_name)
                            ->post(self::BASE_URL . "/listings/{$id}/media");
                    } catch (\Throwable) {}
                }

                // Attiva l'annuncio
                $this->apiRequest('PATCH', self::BASE_URL . "/listings/{$id}", ['status' => 'published'], $this->authHeaders(), 'publish', $listing->id);
            }

            return [
                'success'      => true,
                'external_id'  => (string) $id,
                'external_url' => $result['body']['url'] ?? null,
                'message'      => 'Annuncio pubblicato su Automobile.it',
                'raw'          => $result['body'],
            ];
        }

        return ['success' => false, 'message' => $result['error'] ?? 'Errore', 'raw' => $result['body']];
    }

    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return $this->publish($vehicle, $listing);

        $result = $this->apiRequest(
            'PUT',
            self::BASE_URL . "/listings/{$listing->external_id}",
            $this->buildPayload($vehicle, $listing),
            $this->authHeaders(),
            'update',
            $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Aggiornato' : $result['error'], 'raw' => $result['body']];
    }

    public function updatePrice(MarketplaceListing $listing, float $newPrice): array
    {
        $result = $this->apiRequest(
            'PATCH',
            self::BASE_URL . "/listings/{$listing->external_id}",
            ['price' => $newPrice],
            $this->authHeaders(),
            'update',
            $listing->id
        );
        return ['success' => $result['success'], 'message' => $result['success'] ? 'Prezzo aggiornato' : $result['error']];
    }

    public function delete(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['success' => true, 'message' => 'OK'];

        $result = $this->apiRequest('DELETE', self::BASE_URL . "/listings/{$listing->external_id}", [], $this->authHeaders(), 'delete', $listing->id);
        return ['success' => $result['success'], 'message' => $result['success'] ? 'Eliminato' : $result['error']];
    }

    public function fetchStats(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['views' => 0, 'contacts' => 0, 'favorites' => 0];

        $result = $this->apiRequest('GET', self::BASE_URL . "/listings/{$listing->external_id}/stats", [], $this->authHeaders(), 'sync_stats', $listing->id);

        return [
            'views'     => $result['body']['views'] ?? 0,
            'contacts'  => $result['body']['contacts'] ?? 0,
            'favorites' => $result['body']['saves'] ?? 0,
        ];
    }

    public function fetchLeads(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return [];

        $result = $this->apiRequest('GET', self::BASE_URL . "/leads?listing_id={$listing->external_id}", [], $this->authHeaders(), 'fetch_leads', $listing->id);

        return collect($result['body']['leads'] ?? [])->map(fn($l) => [
            'external_id' => $l['id'] ?? null,
            'name'        => $l['name'] ?? null,
            'email'       => $l['email'] ?? null,
            'phone'       => $l['phone'] ?? null,
            'message'     => $l['message'] ?? null,
            'received_at' => $l['created_at'] ?? now()->toISOString(),
            'raw'         => $l,
        ])->toArray();
    }

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        return [
            'make'         => $vehicle->brand,
            'model'        => $vehicle->model,
            'version'      => $vehicle->version,
            'year'         => (int) $vehicle->year,
            'mileage'      => (int) $vehicle->mileage,
            'fuel'         => $this->mapFuelType($vehicle->fuel_type),
            'transmission' => $this->mapTransmission($vehicle->transmission),
            'color'        => $vehicle->color,
            'doors'        => (int) $vehicle->doors,
            'seats'        => (int) $vehicle->seats,
            'price'        => (float) ($listing->listed_price ?? $vehicle->asking_price),
            'negotiable'   => (bool) $vehicle->price_negotiable,
            'description'  => $vehicle->description ?? $vehicle->computed_title,
            'status'       => 'draft',
        ];
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// EBAY MOTORS
// ═══════════════════════════════════════════════════════════════════════════
