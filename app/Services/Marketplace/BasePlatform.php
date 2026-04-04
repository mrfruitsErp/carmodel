<?php

namespace App\Services\Marketplace;

use App\Models\MarketplaceCredential;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceSyncLog;
use App\Models\SaleVehicle;
use App\Services\Marketplace\Contracts\PlatformConnector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BasePlatform implements PlatformConnector
{
    protected int $tenantId;
    protected ?MarketplaceCredential $credential;
    protected array $config;

    public function __construct(int $tenantId)
    {
        $this->tenantId   = $tenantId;
        $this->credential = MarketplaceCredential::forTenant($tenantId)
            ->where('platform', $this->platformKey())
            ->first();
        $this->config = $this->credential?->settings ?? [];
    }

    protected function credentials(): array
    {
        return $this->credential?->credentials ?? [];
    }

    protected function credential(string $key, mixed $default = null): mixed
    {
        return data_get($this->credentials(), $key, $default);
    }

    protected function isConfigured(): bool
    {
        return $this->credential !== null
            && $this->credential->enabled
            && !empty($this->credentials());
    }

    protected function notConfiguredError(): array
    {
        return [
            'success' => false,
            'message' => "Piattaforma {$this->platformName()} non configurata o disabilitata.",
            'raw'     => [],
        ];
    }

    protected function apiRequest(
        string $method,
        string $url,
        array $data = [],
        array $headers = [],
        string $action = 'publish',
        ?int $listingId = null
    ): array {
        $startTime  = microtime(true);
        $result     = 'failed';
        $response   = null;
        $httpStatus = null;
        $error      = null;

        try {
            $http = Http::withHeaders($headers)->timeout(30);

            $response = match(strtoupper($method)) {
                'GET'    => $http->get($url, $data),
                'POST'   => $http->post($url, $data),
                'PUT'    => $http->put($url, $data),
                'PATCH'  => $http->patch($url, $data),
                'DELETE' => $http->delete($url, $data),
                default  => throw new \InvalidArgumentException("HTTP method {$method} not supported"),
            };

            $httpStatus = $response->status();
            $result     = $response->successful() ? 'success' : 'failed';

            if (!$response->successful()) {
                $error = "HTTP {$httpStatus}: " . $response->body();
            }

        } catch (\Throwable $e) {
            $error = $e->getMessage();
            Log::error("[Marketplace:{$this->platformKey()}] {$action} error: {$error}", [
                'tenant_id' => $this->tenantId,
                'url'       => $url,
            ]);
        }

        $duration = (int) ((microtime(true) - $startTime) * 1000);

        MarketplaceSyncLog::create([
            'tenant_id'              => $this->tenantId,
            'marketplace_listing_id' => $listingId,
            'platform'               => $this->platformKey(),
            'action'                 => $action,
            'result'                 => $result,
            'request_payload'        => json_encode(['url' => $url, 'method' => $method, 'data' => $this->maskSensitive($data)]),
            'response_payload'       => $response ? $response->body() : null,
            'error_message'          => $error,
            'http_status'            => $httpStatus,
            'duration_ms'            => $duration,
        ]);

        return [
            'success'  => $result === 'success',
            'status'   => $httpStatus,
            'body'     => $response?->json() ?? [],
            'raw_body' => $response?->body() ?? '',
            'error'    => $error,
        ];
    }

    private function maskSensitive(array $data): array
    {
        foreach (['client_secret','password','token','access_token','api_key'] as $key) {
            if (isset($data[$key])) $data[$key] = '***';
        }
        return $data;
    }

    protected function getPhotoUrls(SaleVehicle $vehicle, int $max = 20): array
    {
        return $vehicle->getMedia('sale_photos')
            ->take($max)
            ->map(fn($m) => $m->getUrl())
            ->values()
            ->toArray();
    }

    protected function mapFuelType(string $fuel, string $platform = 'generic'): string
    {
        $map = [
            'autoscout24' => [
                'benzina'=>'B','diesel'=>'D','gpl'=>'L','metano'=>'C',
                'elettrico'=>'E','ibrido_benzina'=>'H','ibrido_diesel'=>'H','altro'=>'O',
            ],
            'ebay_motors' => [
                'benzina'=>'Gasoline','diesel'=>'Diesel','gpl'=>'LPG','metano'=>'CNG',
                'elettrico'=>'Electric','ibrido_benzina'=>'Hybrid','ibrido_diesel'=>'Hybrid','altro'=>'Other',
            ],
            'generic' => [
                'benzina'=>'gasoline','diesel'=>'diesel','gpl'=>'lpg','metano'=>'cng',
                'elettrico'=>'electric','ibrido_benzina'=>'hybrid','ibrido_diesel'=>'hybrid','altro'=>'other',
            ],
        ];
        return $map[$platform][$fuel] ?? $map['generic'][$fuel] ?? $fuel;
    }

    protected function mapTransmission(string $trans, string $platform = 'generic'): string
    {
        $map = [
            'autoscout24' => ['manuale'=>'M','automatico'=>'A','semiautomatico'=>'S'],
            'generic'     => ['manuale'=>'manual','automatico'=>'automatic','semiautomatico'=>'semi-automatic'],
        ];
        return $map[$platform][$trans] ?? $map['generic'][$trans] ?? $trans;
    }

    public function pause(MarketplaceListing $listing): array
    {
        return $this->delete($listing);
    }

    public function resume(MarketplaceListing $listing): array
    {
        return $this->publish($listing->saleVehicle, $listing);
    }

    public function refreshTokenIfNeeded(): bool
    {
        return false;
    }

    public function requiredFields(): array
    {
        return ['brand','model','year','mileage','fuel_type','asking_price'];
    }

    public function validate(SaleVehicle $vehicle): array
    {
        $errors = [];

        foreach ($this->requiredFields() as $field) {
            if (empty($vehicle->$field)) {
                $errors[] = "Campo obbligatorio mancante: {$field}";
            }
        }

        if ($vehicle->getMedia('sale_photos')->isEmpty()) {
            $errors[] = 'Almeno una foto è obbligatoria';
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}