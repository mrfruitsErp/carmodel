<?php

namespace App\Services\Marketplace\Platforms;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\BasePlatform;

class FacebookMarketplacePlatform extends BasePlatform
{
    private const GRAPH_URL = 'https://graph.facebook.com/v19.0';

    public function platformKey(): string  { return 'facebook_marketplace'; }
    public function platformName(): string { return 'Facebook Marketplace'; }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->credential('page_access_token')];
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) return ['ok' => false, 'message' => 'Page Access Token non configurato'];

        $result = $this->apiRequest(
            'GET',
            self::GRAPH_URL . '/me',
            ['access_token' => $this->credential('page_access_token')],
            [], 'refresh_token'
        );

        return [
            'ok'      => $result['success'],
            'message' => $result['success']
                ? 'Connessione Facebook OK — ' . ($result['body']['name'] ?? 'Page')
                : 'Token non valido',
        ];
    }

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $catalogId = $this->credential('catalog_id');
        if (!$catalogId) {
            return ['success' => false, 'message' => 'Catalog ID Facebook non configurato', 'raw' => []];
        }

        $payload = array_merge(
            $this->buildPayload($vehicle, $listing),
            ['access_token' => $this->credential('page_access_token')]
        );

        $result = $this->apiRequest(
            'POST',
            self::GRAPH_URL . "/{$catalogId}/vehicles",
            $payload, [], 'publish', $listing->id
        );

        if ($result['success'] && isset($result['body']['id'])) {
            return [
                'success'      => true,
                'external_id'  => $result['body']['id'],
                'external_url' => "https://www.facebook.com/marketplace/item/{$result['body']['id']}",
                'message'      => 'Annuncio pubblicato su Facebook Marketplace',
                'raw'          => $result['body'],
            ];
        }

        return ['success' => false, 'message' => $result['error'] ?? 'Errore Facebook', 'raw' => $result['body']];
    }

    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return $this->publish($vehicle, $listing);

        $payload              = $this->buildPayload($vehicle, $listing);
        $payload['access_token'] = $this->credential('page_access_token');

        $result = $this->apiRequest(
            'POST',
            self::GRAPH_URL . "/{$listing->external_id}",
            $payload, [], 'update', $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Aggiornato' : $result['error'], 'raw' => $result['body']];
    }

    public function updatePrice(MarketplaceListing $listing, float $newPrice): array
    {
        $result = $this->apiRequest(
            'POST',
            self::GRAPH_URL . "/{$listing->external_id}",
            [
                'price'        => (int) ($newPrice * 100),
                'currency'     => 'EUR',
                'access_token' => $this->credential('page_access_token'),
            ],
            [], 'update', $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Prezzo aggiornato' : $result['error']];
    }

    public function delete(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['success' => true, 'message' => 'OK'];

        $result = $this->apiRequest(
            'DELETE',
            self::GRAPH_URL . "/{$listing->external_id}",
            ['access_token' => $this->credential('page_access_token')],
            [], 'delete', $listing->id
        );

        return ['success' => $result['success'], 'message' => $result['success'] ? 'Eliminato' : $result['error']];
    }

    public function fetchStats(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['views' => 0, 'contacts' => 0, 'favorites' => 0];

        $result = $this->apiRequest(
            'GET',
            self::GRAPH_URL . "/{$listing->external_id}/insights",
            [
                'metric'       => 'post_impressions,post_engaged_users',
                'access_token' => $this->credential('page_access_token'),
            ],
            [], 'sync_stats', $listing->id
        );

        $data    = collect($result['body']['data'] ?? []);
        $views   = $data->firstWhere('name', 'post_impressions')['values'][0]['value'] ?? 0;
        $engaged = $data->firstWhere('name', 'post_engaged_users')['values'][0]['value'] ?? 0;

        return ['views' => (int) $views, 'contacts' => (int) $engaged, 'favorites' => 0];
    }

    public function fetchLeads(MarketplaceListing $listing): array
    {
        return [];
    }

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        $photos = $this->getPhotoUrls($vehicle);

        return [
            'make'             => $vehicle->brand,
            'model'            => $vehicle->model,
            'year'             => (int) $vehicle->year,
            'mileage'          => ['value' => (int) $vehicle->mileage, 'unit' => 'KM'],
            'fuel_type'        => strtoupper($this->mapFuelType($vehicle->fuel_type)),
            'transmission'     => strtoupper($this->mapTransmission($vehicle->transmission)),
            'exterior_color'   => $vehicle->color ?? 'OTHER',
            'body_style'       => strtoupper($vehicle->body_type ?? 'SEDAN'),
            'price'            => (int) (($listing->listed_price ?? $vehicle->asking_price) * 100),
            'currency'         => 'EUR',
            'description'      => $vehicle->description ?? $vehicle->computed_title,
            'title'            => $vehicle->computed_title,
            'url'              => url('/'),
            'image'            => array_map(fn($url) => ['url' => $url], $photos),
            'availability'     => 'AVAILABLE',
            'condition'        =