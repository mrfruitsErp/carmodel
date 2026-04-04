<?php

namespace App\Services\Marketplace\Platforms;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\BasePlatform;

class AutoScout24Platform extends BasePlatform
{
    private const AUTH_URL    = 'https://auth.autoscout24.com/oauth/token';
    private const BASE_URL    = 'https://listing-creation.api.autoscout24.com';
    private const LEADS_URL   = 'https://listing-creation.api.autoscout24.com/leads';

    public function platformKey(): string  { return 'autoscout24'; }
    public function platformName(): string { return 'AutoScout24'; }

    // ─── Auth ─────────────────────────────────────────────────────────────────

    private function getAccessToken(): ?string
    {
        if (!$this->isConfigured()) return null;

        // Usa token in cache se ancora valido
        if ($this->credential->token_expires_at && $this->credential->token_expires_at->gt(now()->addMinutes(5))) {
            return $this->credential('access_token');
        }

        return $this->fetchNewToken();
    }

    private function fetchNewToken(): ?string
    {
        $result = $this->apiRequest('POST', self::AUTH_URL, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->credential('client_id'),
            'client_secret' => $this->credential('client_secret'),
            'scope'         => 'listing:write listing:read leads:read',
        ], ['Content-Type' => 'application/x-www-form-urlencoded'], 'refresh_token');

        if ($result['success'] && isset($result['body']['access_token'])) {
            $token = $result['body']['access_token'];
            $expiresIn = $result['body']['expires_in'] ?? 3600;

            // Salva token aggiornato nelle credenziali
            $creds = $this->credentials();
            $creds['access_token'] = $token;
            $this->credential->setCredentialsArray($creds);
            $this->credential->update(['token_expires_at' => now()->addSeconds($expiresIn)]);

            return $token;
        }

        return null;
    }

    public function refreshTokenIfNeeded(): bool
    {
        if (!$this->credential?->isTokenExpired()) return false;
        return (bool) $this->fetchNewToken();
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    // ─── Test connessione ─────────────────────────────────────────────────────

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'message' => 'Credenziali non configurate'];
        }

        $token = $this->fetchNewToken();

        return [
            'ok'      => (bool) $token,
            'message' => $token ? 'Connessione AutoScout24 OK' : 'Autenticazione fallita — verifica client_id e client_secret',
        ];
    }

    // ─── Publish ──────────────────────────────────────────────────────────────

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $validation = $this->validate($vehicle);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => implode(', ', $validation['errors']), 'raw' => []];
        }

        $payload = $this->buildPayload($vehicle, $listing);

        $result = $this->apiRequest(
            'POST',
            self::BASE_URL . '/vehicles',
            $payload,
            $this->authHeaders(),
            'publish',
            $listing->id
        );

        if ($result['success']) {
            $vehicleId = $result['body']['id'] ?? null;

            // Upload foto
            if ($vehicleId) {
                $this->uploadPhotos($vehicleId, $vehicle, $listing->id);

                // Pubblica (transizione DRAFT → PUBLISHED)
                $this->apiRequest(
                    'PATCH',
                    self::BASE_URL . "/vehicles/{$vehicleId}",
                    ['status' => 'PUBLISHED'],
                    $this->authHeaders(),
                    'publish',
                    $listing->id
                );
            }

            return [
                'success'      => true,
                'external_id'  => $vehicleId,
                'external_url' => $result['body']['listingUrl'] ?? null,
                'message'      => 'Annuncio pubblicato su AutoScout24',
                'raw'          => $result['body'],
            ];
        }

        return [
            'success' => false,
            'message' => $result['error'] ?? 'Errore pubblicazione AutoScout24',
            'raw'     => $result['body'],
        ];
    }

    private function uploadPhotos(string $vehicleId, SaleVehicle $vehicle, int $listingId): void
    {
        foreach ($vehicle->getMedia('sale_photos') as $media) {
            // AutoScout24 vuole multipart/form-data per le foto
            try {
                \Illuminate\Support\Facades\Http::withToken($this->getAccessToken())
                    ->attach('image', file_get_contents($media->getPath()), $media->file_name)
                    ->post(self::BASE_URL . "/vehicles/{$vehicleId}/images");
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("[AutoScout24] Foto upload failed: {$e->getMessage()}");
            }
        }
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return $this->publish($vehicle, $listing);

        $payload = $this->buildPayload($vehicle, $listing);

        $result = $this->apiRequest(
            'PUT',
            self::BASE_URL . "/vehicles/{$listing->external_id}",
            $payload,
            $this->authHeaders(),
            'update',
            $listing->id
        );

        return [
            'success' => $result['success'],
            'message' => $result['success'] ? 'Annuncio aggiornato' : ($result['error'] ?? 'Errore'),
            'raw'     => $result['body'],
        ];
    }

    public function updatePrice(MarketplaceListing $listing, float $newPrice): array
    {
        if (!$listing->external_id) return ['success' => false, 'message' => 'Annuncio non pubblicato'];

        $result = $this->apiRequest(
            'PATCH',
            self::BASE_URL . "/vehicles/{$listing->external_id}",
            ['price' => ['amount' => $newPrice, 'currency' => 'EUR']],
            $this->authHeaders(),
            'update',
            $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Prezzo aggiornato' : $result['error']];
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function delete(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['success' => true, 'message' => 'Nessun annuncio da eliminare'];

        $result = $this->apiRequest(
            'DELETE',
            self::BASE_URL . "/vehicles/{$listing->external_id}",
            [],
            $this->authHeaders(),
            'delete',
            $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Annuncio eliminato' : $result['error']];
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    public function fetchStats(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['views' => 0, 'contacts' => 0, 'favorites' => 0];

        $result = $this->apiRequest(
            'GET',
            self::BASE_URL . "/vehicles/{$listing->external_id}/stats",
            [],
            $this->authHeaders(),
            'sync_stats',
            $listing->id
        );

        return [
            'views'     => $result['body']['views'] ?? 0,
            'contacts'  => $result['body']['contacts'] ?? 0,
            'favorites' => $result['body']['favorites'] ?? 0,
        ];
    }

    // ─── Leads ────────────────────────────────────────────────────────────────

    public function fetchLeads(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return [];

        $result = $this->apiRequest(
            'GET',
            self::LEADS_URL . "?vehicleId={$listing->external_id}",
            [],
            $this->authHeaders(),
            'fetch_leads',
            $listing->id
        );

        if (!$result['success']) return [];

        return collect($result['body']['leads'] ?? [])->map(fn($lead) => [
            'external_id' => $lead['id'] ?? null,
            'name'        => $lead['name'] ?? null,
            'email'       => $lead['email'] ?? null,
            'phone'       => $lead['phone'] ?? null,
            'message'     => $lead['message'] ?? null,
            'received_at' => $lead['createdAt'] ?? now()->toISOString(),
            'raw'         => $lead,
        ])->toArray();
    }

    // ─── Payload ──────────────────────────────────────────────────────────────

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        $payload = [
            'make'         => $vehicle->brand,
            'model'        => $vehicle->model,
            'version'      => $vehicle->version,
            'year'         => (int) $vehicle->year,
            'mileage'      => (int) $vehicle->mileage,
            'fuelType'     => $this->mapFuelType($vehicle->fuel_type, 'autoscout24'),
            'transmission' => $this->mapTransmission($vehicle->transmission, 'autoscout24'),
            'bodyType'     => $this->mapBodyType($vehicle->body_type),
            'doors'        => (int) $vehicle->doors,
            'seats'        => (int) $vehicle->seats,
            'color'        => $vehicle->color,
            'price'        => [
                'amount'    => (float) ($listing->listed_price ?? $vehicle->asking_price),
                'currency'  => 'EUR',
                'negotiable'=> (bool) $vehicle->price_negotiable,
            ],
            'description' => $vehicle->description ?? $vehicle->computed_title,
        ];

        if ($vehicle->power_kw) {
            $payload['power'] = ['value' => $vehicle->power_kw, 'unit' => 'kw'];
        }

        if ($vehicle->first_registration) {
            $payload['firstRegistration'] = $vehicle->first_registration->format('m/Y');
        }

        if ($vehicle->vin) {
            $payload['vin'] = $vehicle->vin;
        }

        return $payload;
    }

    private function mapBodyType(?string $type): string
    {
        return match($type) {
            'berlina'       => 'Saloon',
            'suv'           => 'SUV',
            'station_wagon' => 'Station Wagon',
            'coupé'         => 'Coupe',
            'cabriolet'     => 'Convertible',
            'monovolume'    => 'Minivan',
            'van'           => 'Van',
            'pickup'        => 'Pick-Up',
            default         => 'Other',
        };
    }

    public function requiredFields(): array
    {
        return ['brand', 'model', 'year', 'mileage', 'fuel_type', 'transmission', 'asking_price', 'description'];
    }
}