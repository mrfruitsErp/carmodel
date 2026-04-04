<?php

namespace App\Services\Marketplace\Platforms;

use App\Models\MarketplaceListing;
use App\Models\SaleVehicle;
use App\Services\Marketplace\BasePlatform;

class SubitoItPlatform extends BasePlatform
{
    public function platformKey(): string  { return 'subito_it'; }
    public function platformName(): string { return 'Subito.it'; }

    public function testConnection(): array
    {
        $creds = $this->credentials();
        $ok    = !empty($creds['email']) && !empty($creds['password']);

        return [
            'ok'      => $ok,
            'message' => $ok
                ? 'Credenziali Subito.it configurate (automazione browser)'
                : 'Email o password mancanti',
        ];
    }

    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $validation = $this->validate($vehicle);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => implode(', ', $validation['errors']), 'raw' => []];
        }

        \App\Jobs\Marketplace\PublishSubitoJob::dispatch($listing->id, $this->tenantId);

        return [
            'success'      => true,
            'external_id'  => null,
            'external_url' => null,
            'message'      => 'Pubblicazione Subito.it accodata — completata entro pochi minuti',
            'raw'          => ['queued' => true],
        ];
    }

    public function update(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        \App\Jobs\Marketplace\UpdateSubitoJob::dispatch($listing->id, $this->tenantId);
        return ['success' => true, 'message' => 'Aggiornamento Subito.it accodato', 'raw' => []];
    }

    public function updatePrice(MarketplaceListing $listing, float $newPrice): array
    {
        return $this->update($listing->saleVehicle, $listing);
    }

    public function delete(MarketplaceListing $listing): array
    {
        if (!$listing->external_id) return ['success' => true, 'message' => 'OK'];
        \App\Jobs\Marketplace\DeleteSubitoJob::dispatch($listing->id, $this->tenantId);
        return ['success' => true, 'message' => 'Eliminazione Subito.it accodata'];
    }

    public function fetchStats(MarketplaceListing $listing): array
    {
        return ['views' => 0, 'contacts' => 0, 'favorites' => 0];
    }

    public function fetchLeads(MarketplaceListing $listing): array
    {
        return [];
    }

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        return [
            'titolo'      => $vehicle->computed_title,
            'descrizione' => $vehicle->description ?? $vehicle->computed_title,
            'prezzo'      => (float) ($listing->listed_price ?? $vehicle->asking_price),
            'trattabile'  => (bool) $vehicle->price_negotiable,
            'marca'       => $vehicle->brand,
            'modello'     => $vehicle->model,
            'anno'        => (int) $vehicle->year,
            'km'          => (int) $vehicle->mileage,
            'carburante'  => $vehicle->fuel_type,
            'cambio'      => $vehicle->transmission,
            'colore'      => $vehicle->color,
            'cilindrata'  => $vehicle->engine_cc,
            'potenza_cv'  => $vehicle->power_hp,
            'porte'       => (int) $vehicle->doors,
            'foto'        => $this->getPhotoUrls($vehicle),
        ];
    }
}