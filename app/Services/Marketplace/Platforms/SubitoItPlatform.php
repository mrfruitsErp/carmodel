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
        // Subito.it non ha API: verifica solo che le credenziali siano presenti
        $creds = $this->credentials();
        $ok    = !empty($creds['email']) && !empty($creds['password']);

        return [
            'ok'      => $ok,
            'message' => $ok
                ? 'Credenziali Subito.it configurate (integrazione via automazione browser)'
                : 'Email o password Subito.it mancanti',
        ];
    }

    /**
     * Subito.it non ha API ufficiali.
     * La pubblicazione avviene tramite job che usa Playwright/browser automation.
     * Questo metodo crea il job e imposta il listing in stato "publishing".
     */
    public function publish(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        if (!$this->isConfigured()) return $this->notConfiguredError();

        $validation = $this->validate($vehicle);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => implode(', ', $validation['errors']), 'raw' => []];
        }

        // Dispatcha job async per browser automation
        \App\Jobs\Marketplace\PublishSubitoJob::dispatch($listing->id, $this->tenantId);

        return [
            'success'      => true,
            'external_id'  => null, // verrà popolato dal job
            'external_url' => null,
            'message'      => 'Pubblicazione Subito.it accodata — verrà completata entro pochi minuti',
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
        return []; // I messaggi Subito arrivano via email/notifica
    }

    public function buildPayload(SaleVehicle $vehicle, MarketplaceListing $listing): array
    {
        return [
            'titolo'        => $vehicle->computed_title,
            'descrizione'   => $vehicle->description ?? $vehicle->computed_title,
            'prezzo'        => (float) ($listing->listed_price ?? $vehicle->asking_price),
            'trattabile'    => (bool) $vehicle->price_negotiable,
            'marca'         => $vehicle->brand,
            'modello'       => $vehicle->model,
            'anno'          => (int) $vehicle->year,
            'km'            => (int) $vehicle->mileage,
            'carburante'    => $vehicle->fuel_type,
            'cambio'        => $vehicle->transmission,
            'colore'        => $vehicle->color,
            'cilindrata'    => $vehicle->engine_cc,
            'potenza_cv'    => $vehicle->power_hp,
            'porte'         => (int) $vehicle->doors,
            'foto'          => $this->getPhotoUrls($vehicle),
        ];
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// FACEBOOK MARKETPLACE
// ═══════════════════════════════════════════════════════════════════════════
