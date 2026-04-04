<?php

namespace App\Services\Marketplace\Platforms;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\BasePlatform;

class EbayMotorsPlatform extends BasePlatform
{
    private const AUTH_URL      = 'https://api.ebay.com/identity/v1/oauth2/token';
    private const INVENTORY_URL = 'https://api.ebay.com/sell/inventory/v1';
    private const OFFER_URL     = 'https://api.ebay.com/sell/inventory/v1/offer';
    private const MARKETPLACE   = 'EBAY_IT';
    private const CATEGORY_ID   = '6002'; // Cars & Trucks IT

    public function platformKey(): string  { return 'ebay_motors'; }
    public function platformName(): string { return 'eBay Motors'; }

    private function getToken(): ?string
    {
        if ($this->credential?->token_expires_at?->gt(now()->addMinutes(5))) {
            return $this->credential('access_token');
        }
        return $this->refreshAccessToken();
    }

    private function refreshAccessToken(): ?string
    {
        $appId   = $this->credential('app_id');
        $certId  = $this->credential('cert_id');
        $refresh = $this->credential('refresh_token');

        if (!$appId || !$certId || !$refresh) return null;

        $result = $this->apiRequest('POST', self::AUTH_URL, [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh,
            'scope'         => 'https://api.ebay.com/oauth/api_scope/sell.inventory',
        ], [
            'Authorization' => 'Basic ' . base64_encode("{$appId}:{$certId}"),
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ], 'refresh_token');

        if ($result['success'] && isset($result['body']['access_token'])) {
            $token = $result['body']['access_token'];
            $creds = $this->credentials();
            $creds['access_token'] = $token;
            $this->credential->setCredentialsArray($creds);
            $this->credential->update(['token_expires_at' => now()->addSeconds($result['body']['expires_in'] ?? 7200)]);
            return $token;
        }

        return null;
    }

    public function refreshTokenIfNeeded(): bool
    {
        if (!$this->credential?->isTokenExpired()) return false;
        return (bool) $this->refreshAccessToken();
    }

    private function authHeaders(): array
    {
        return [
            'Authorization'                => 'Bearer ' . $this->getToken(),
            'Content-Type'                 => 'application/json',
            'X-EBAY-C-MARKETPLACE-ID'      => self::MARKETPLACE,
        ];
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) return ['ok' => false, 'message' => 'Credenziali non configurate'];

        $token = $this->getToken();
        return [
            'ok'      => (bool) $token,
            'message' => $token ? 'Connessione eBay OK' : 'Token non valido',
        ];
    }

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $sku = $this->buildSku($vehicle);

        // Step 1: Crea/aggiorna inventory item
        $inventoryResult = $this->apiRequest(
            'PUT',
            self::INVENTORY_URL . "/inventory_item/{$sku}",
            $this->buildPayload($vehicle, $listing),
            $this->authHeaders(),
            'publish',
            $listing->id
        );

        if (!$inventoryResult['success'] && $inventoryResult['status'] !== 204) {
            return ['success' => false, 'message' => $inventoryResult['error'] ?? 'Errore inventory', 'raw' => []];
        }

        // Step 2: Crea offer
        $offerResult = $this->createOffer($vehicle, $listing, $sku);

        if (!$offerResult['success']) {
            return $offerResult;
        }

        $offerId = $offerResult['offer_id'];

        // Step 3: Pubblica offer
        $publishResult = $this->apiRequest(
            'POST',
            self::OFFER_URL . "/{$offerId}/publish",
            [],
            $this->authHeaders(),
            'publish',
            $listing->id
        );

        if ($publishResult['success'] || $publishResult['status'] === 200) {
            return [
                'success'      => true,
                'external_id'  => $publishResult['body']['listingId'] ?? $offerId,
                'external_url' => $publishResult['body']['listingUrl'] ?? null,
                'message'      => 'Annuncio pubblicato su eBay Motors',
                'raw'          => $publishResult['body'],
            ];
        }

        return ['success' => false, 'message' => $publishResult['error'] ?? 'Errore pubblicazione', 'raw' => $publishResult['body']];
    }

    private function createOffer(SaleVehicle $vehicle, MarketplaceListing $listing, string $sku): array
    {
        $policies = $this->credential('policies') ?? [];

        $offerPayload = [
            'sku'               => $sku,
            'marketplaceId'     => self::MARKETPLACE,
            'format'            => 'FIXED_PRICE',
            'availableQuantity' => 1,
            'categoryId'        => self::CATEGORY_ID,
            'listingPolicies'   => [
                'fulfillmentPolicyId' => $policies['fulfillment_policy_id'] ?? '',
                'paymentPolicyId'     => $policies['payment_policy_id'] ?? '',
                'returnPolicyId'      => $policies['return_policy_id'] ?? '',
            ],
            'pricingSummary' => [
                'price' => [
                    'value'    => number_format((float) ($listing->listed_price ?? $vehicle->asking_price), 2, '.', ''),
                    'currency' => 'EUR',
                ],
            ],
        ];

        $result = $this->apiRequest('POST', self::OFFER_URL, $offerPayload, $this->authHeaders(), 'publish', $listing->id);

        return [
            'success'  => $result['success'],
            'offer_id' => $result['body']['offerId'] ?? null,
            'message'  => $result['error'] ?? '',
            'raw'      => $result['body'],
        ];
    }

    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        // eBay: aggiorna inventory item (PUT è idempotente)
        $sku = $this->buildSku($vehicle);

        $result = $this->apiRequest(
            'PUT',
            self::INVENTORY_URL . "/inventory_item/{$sku}",
            $this->buildPayload($vehicle, $listing),
            $this->authHeaders(),
            'update',
            $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Aggiornato' : $result['error'], 'raw' => $result['body']];
    }

    public function updatePrice(MarketplaceListing $listing, float $newPrice): array
    {
        if (!$listing->external_id) return ['success' => false, 'message' => 'Offerta non trovata'];

        $result = $this->apiRequest(
            'PATCH',
            self::OFFER_URL . "/{$listing->external_id}",
            ['pricingSummary' => ['price' => ['value' => number_format($newPrice, 2, '.', ''), 'currency' => 'EUR']]],
            $this->authHeaders(),
            'update',
            $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Prezzo aggiornato' : $result['error']];
    }

    public function delete(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['success' => true, 'message' => 'OK'];

        $result = $this->apiRequest(
            'DELETE',
            self::OFFER_URL . "/{$listing->external_id}",
            [],
            $this->authHeaders(),
            'delete',
            $listing->id
        );
        return ['success' => $result['success'], 'message' => $result['success'] ? 'Eliminato' : $result['error']];
    }

    public function fetchStats(MarketplaceListing $listing): array
    {
        // eBay non ha endpoint stats dedicato per listing auto, ritorna dati mock
        return ['views' => 0, 'contacts' => 0, 'favorites' => 0];
    }

    public function fetchLeads(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return [];

        $result = $this->apiRequest(
            'GET',
            'https://apiz.ebay.com/post-order/v2/inquiry?item_id=' . $listing->external_id,
            [],
            array_merge($this->authHeaders(), ['X-EBAY-C-MARKETPLACE-ID' => self::MARKETPLACE]),
            'fetch_leads',
            $listing->id
        );

        return collect($result['body']['inquiries'] ?? [])->map(fn($i) => [
            'external_id' => $i['inquiryId'] ?? null,
            'name'        => $i['buyerUsername'] ?? null,
            'email'       => null,
            'phone'       => null,
            'message'     => $i['buyerMessage'] ?? null,
            'received_at' => $i['creationDate'] ?? now()->toISOString(),
            'raw'         => $i,
        ])->toArray();
    }

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        $photos = $this->getPhotoUrls($vehicle, 12);

        return [
            'product' => [
                'title'       => $vehicle->computed_title,
                'description' => $vehicle->description ?? $vehicle->computed_title,
                'aspects'     => [
                    'Make'           => [$vehicle->brand],
                    'Model'          => [$vehicle->model],
                    'Year'           => [(string) $vehicle->year],
                    'Mileage'        => [$vehicle->mileage . ' km'],
                    'Fuel Type'      => [$this->mapFuelType($vehicle->fuel_type, 'ebay_motors')],
                    'Transmission'   => [$this->mapTransmission($vehicle->transmission)],
                    'Body Type'      => [$vehicle->body_type ?? 'Other'],
                    'Number of Doors'=> [(string) $vehicle->doors],
                    'Colour'         => [$vehicle->color ?? 'Not specified'],
                ],
                'imageUrls' => $photos,
            ],
            'condition'    => 'USED_EXCELLENT',
            'availability' => ['shipToLocationAvailability' => ['quantity' => 1]],
        ];
    }

    private function buildSku(SaleVehicle $vehicle): string
    {
        return strtoupper(implode('-', array_filter([
            $vehicle->brand,
            $vehicle->model,
            $vehicle->year,
            $vehicle->plate ?? $vehicle->id,
            'IT',
        ])));
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// SUBITO.IT (browser automation — nessuna API ufficiale)
// ═══════════════════════════════════════════════════════════════════════════
