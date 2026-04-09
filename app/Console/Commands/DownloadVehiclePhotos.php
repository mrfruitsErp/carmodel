<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SaleVehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadVehiclePhotos extends Command
{
    protected $signature = 'vehicles:download-photos {--tenant=1} {--force}';
    protected $description = 'Scarica foto reali per i veicoli in vendita da Unsplash';

    // Mappa ricerche Unsplash per marca/modello
    private array $photoMap = [
        'BMW 320d'         => ['bmw 3 series sedan black','bmw 320d interior','bmw 3 series rear'],
        'Volkswagen Golf'  => ['volkswagen golf white','vw golf interior','volkswagen golf hatchback'],
        'Audi A4'          => ['audi a4 avant grey','audi a4 interior','audi a4 rear'],
        'Toyota RAV4'      => ['toyota rav4 hybrid white','toyota rav4 interior','toyota rav4 suv'],
        'Mercedes-Benz Classe C' => ['mercedes c class silver','mercedes interior luxury','mercedes c class rear'],
        'Fiat 500'         => ['fiat 500 pink','fiat 500 interior','fiat 500 city'],
        'Porsche Cayenne'  => ['porsche cayenne blue','porsche cayenne interior','porsche suv'],
        'Renault Zoe'      => ['renault zoe electric grey','electric car interior','renault zoe charging'],
    ];

    // Foto fallback per tipo carrozzeria
    private array $bodyFallback = [
        'berlina'        => ['sedan car','sedan interior','sedan rear view'],
        'hatchback'      => ['hatchback car','compact car interior','hatchback rear'],
        'suv'            => ['suv car','suv interior luxury','suv exterior'],
        'station_wagon'  => ['station wagon car','estate car interior','wagon rear'],
        'coupe'          => ['coupe car sport','coupe interior','sport car rear'],
        'cabrio'         => ['convertible car','cabrio interior','open top car'],
        'van'            => ['van car','minivan interior','commercial van'],
        default           => ['luxury car','car interior modern','car exterior'],
    ];

    public function handle(): int
    {
        $tid = (int)$this->option('tenant');
        $force = $this->option('force');

        $vehicles = SaleVehicle::forTenant($tid)->get();
        $this->info("Trovati {$vehicles->count()} veicoli.");

        $accessKey = config('services.unsplash.access_key', 'client-id');
        $useUnsplash = $accessKey !== 'client-id';

        foreach ($vehicles as $vehicle) {
            $existing = $vehicle->getMedia('sale_photos')->count();
            if ($existing > 0 && !$force) {
                $this->line("  Skip {$vehicle->brand} {$vehicle->model} - ha gia {$existing} foto");
                continue;
            }

            $this->line("  Scarico foto per: {$vehicle->brand} {$vehicle->model} ({$vehicle->year})");

            $key = "{$vehicle->brand} {$vehicle->model}";
            $queries = $this->photoMap[$key]
                ?? $this->bodyFallback[$vehicle->body_type]
                ?? $this->bodyFallback['default'];

            $downloaded = 0;
            foreach ($queries as $i => $query) {
                try {
                    if ($useUnsplash) {
                        $url = $this->getUnsplashUrl($query, $accessKey);
                    } else {
                        // Usa Lorem Picsum con seed basato sul veicolo
                        $seed = abs(crc32($vehicle->brand.$vehicle->model.$i));
                        $url = "https://picsum.photos/seed/{$seed}/800/600";
                    }

                    if (!$url) continue;

                    $response = Http::timeout(15)->get($url);
                    if (!$response->ok()) continue;

                    $ext = 'jpg';
                    $filename = strtolower(str_replace(' ', '_', "{$vehicle->brand}_{$vehicle->model}_{$i}.{$ext}"));
                    $tmpPath = sys_get_temp_dir().'//'.$filename;
                    file_put_contents($tmpPath, $response->body());

                    $vehicle->addMedia($tmpPath)
                        ->usingFileName($filename)
                        ->usingName("{$vehicle->brand} {$vehicle->model} - foto ".($i+1))
                        ->toMediaCollection('sale_photos');

                    $downloaded++;
                    $this->line("    Foto ".($i+1)." scaricata");
                    sleep(1); // rate limiting

                } catch (\Exception $e) {
                    $this->warn("    Errore foto ".($i+1).": ".$e->getMessage());
                }
            }

            $this->info("  {$vehicle->brand} {$vehicle->model}: {$downloaded} foto aggiunte");
        }

        $this->info('Download completato!');
        return 0;
    }

    private function getUnsplashUrl(string $query, string $accessKey): ?string
    {
        $response = Http::get('https://api.unsplash.com/search/photos', [
            'query'       => $query,
            'per_page'    => 1,
            'orientation' => 'landscape',
            'client_id'   => $accessKey,
        ]);

        if (!$response->ok()) return null;
        $results = $response->json('results');
        if (empty($results)) return null;

        return $results[0]['urls']['regular'] ?? null;
    }
}