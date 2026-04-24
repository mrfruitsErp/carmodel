<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VinDecoderController extends Controller
{
    public function decode(Request $request)
    {
        $vin = strtoupper(trim($request->vin));

        if (strlen($vin) !== 17) {
            return response()->json(['error' => 'VIN deve essere di 17 caratteri'], 422);
        }

        // Se la chiave Claude è configurata → usa Claude AI
        if (env('ANTHROPIC_API_KEY')) {
            $result = $this->decodeWithClaude($vin);
            if ($result['success']) {
                return response()->json($result);
            }
        }

        // VIN americani → NHTSA
        $americanPrefixes = ['1', '2', '3', '4', '5', 'K'];
        if (in_array($vin[0], $americanPrefixes)) {
            $result = $this->decodeWithNHTSA($vin);
            if ($result['success']) {
                return response()->json($result);
            }
        }

        // Fallback gratuito: decoder manuale da struttura VIN
        return response()->json($this->decodeManual($vin));
    }

    // ── DECODER MANUALE GRATUITO ─────────────────────────────────────────────
    // Legge WMI (prime 3 cifre) + posizioni chiave del VIN

    private function decodeManual(string $vin): array
    {
        $wmi   = substr($vin, 0, 3); // World Manufacturer Identifier
        $vds   = substr($vin, 3, 6); // Vehicle Descriptor Section
        $vis   = substr($vin, 9, 8); // Vehicle Identifier Section
        $year  = $this->decodeYear($vin[9]);

        // Database WMI principali per il mercato italiano/europeo
        $wmiMap = [
            // FIAT / STELLANTIS
            'ZFA' => ['brand' => 'Fiat',       'country' => 'IT'],
            'ZFF' => ['brand' => 'Ferrari',     'country' => 'IT'],
            'ZAR' => ['brand' => 'Alfa Romeo',  'country' => 'IT'],
            'ZLA' => ['brand' => 'Lancia',      'country' => 'IT'],
            'ZCG' => ['brand' => 'Maserati',    'country' => 'IT'],
            'ZHW' => ['brand' => 'Lamborghini', 'country' => 'IT'],
            'ZDB' => ['brand' => 'Ducati',      'country' => 'IT'],
            'VSS' => ['brand' => 'SEAT',        'country' => 'ES'],
            'VNK' => ['brand' => 'Toyota',      'country' => 'ES'],
            'VSK' => ['brand' => 'Nissan',      'country' => 'ES'],

            // VOLKSWAGEN GROUP
            'WVW' => ['brand' => 'Volkswagen',  'country' => 'DE'],
            'WV1' => ['brand' => 'Volkswagen',  'country' => 'DE'],
            'WV2' => ['brand' => 'Volkswagen',  'country' => 'DE'],
            'WAU' => ['brand' => 'Audi',        'country' => 'DE'],
            'WUA' => ['brand' => 'Audi',        'country' => 'DE'],
            'WA1' => ['brand' => 'Audi',        'country' => 'DE'],
            'WKK' => ['brand' => 'Skoda',       'country' => 'CZ'],
            'TMB' => ['brand' => 'Skoda',       'country' => 'CZ'],
            'VSX' => ['brand' => 'Porsche',     'country' => 'DE'],
            'WP0' => ['brand' => 'Porsche',     'country' => 'DE'],
            'WP1' => ['brand' => 'Porsche',     'country' => 'DE'],

            // BMW GROUP
            'WBA' => ['brand' => 'BMW',         'country' => 'DE'],
            'WBS' => ['brand' => 'BMW M',       'country' => 'DE'],
            'WBX' => ['brand' => 'BMW',         'country' => 'DE'],
            'WBY' => ['brand' => 'BMW i',       'country' => 'DE'],
            'WMW' => ['brand' => 'Mini',        'country' => 'DE'],

            // MERCEDES
            'WDB' => ['brand' => 'Mercedes-Benz', 'country' => 'DE'],
            'WDD' => ['brand' => 'Mercedes-Benz', 'country' => 'DE'],
            'WDC' => ['brand' => 'Mercedes-Benz', 'country' => 'DE'],
            'W1K' => ['brand' => 'Mercedes-Benz', 'country' => 'DE'],

            // FORD
            'WF0' => ['brand' => 'Ford',        'country' => 'DE'],
            'WFO' => ['brand' => 'Ford',        'country' => 'DE'],
            '1FA' => ['brand' => 'Ford',        'country' => 'US'],
            '1FB' => ['brand' => 'Ford',        'country' => 'US'],
            '1FC' => ['brand' => 'Ford',        'country' => 'US'],
            '1FT' => ['brand' => 'Ford',        'country' => 'US'],

            // OPEL / VAUXHALL
            'W0L' => ['brand' => 'Opel',        'country' => 'DE'],
            'W0V' => ['brand' => 'Opel',        'country' => 'DE'],

            // RENAULT GROUP
            'VF1' => ['brand' => 'Renault',     'country' => 'FR'],
            'VF2' => ['brand' => 'Renault',     'country' => 'FR'],
            'VF3' => ['brand' => 'Peugeot',     'country' => 'FR'],
            'VF6' => ['brand' => 'Renault',     'country' => 'FR'],
            'VF7' => ['brand' => 'Citroën',     'country' => 'FR'],
            'VF8' => ['brand' => 'Matra',       'country' => 'FR'],
            'VNE' => ['brand' => 'Nissan',      'country' => 'FR'],

            // VOLVO / GEELY
            'YV1' => ['brand' => 'Volvo',       'country' => 'SE'],
            'YV4' => ['brand' => 'Volvo',       'country' => 'SE'],

            // TOYOTA / LEXUS
            'JTD' => ['brand' => 'Toyota',      'country' => 'JP'],
            'JTM' => ['brand' => 'Toyota',      'country' => 'JP'],
            'JTE' => ['brand' => 'Toyota',      'country' => 'JP'],
            'JTH' => ['brand' => 'Lexus',       'country' => 'JP'],

            // HONDA
            'JHM' => ['brand' => 'Honda',       'country' => 'JP'],
            'JH4' => ['brand' => 'Acura',       'country' => 'JP'],

            // NISSAN
            'JN1' => ['brand' => 'Nissan',      'country' => 'JP'],
            'JN3' => ['brand' => 'Nissan',      'country' => 'JP'],
            'JN6' => ['brand' => 'Nissan',      'country' => 'JP'],

            // MAZDA
            'JM1' => ['brand' => 'Mazda',       'country' => 'JP'],
            'JMZ' => ['brand' => 'Mazda',       'country' => 'JP'],

            // MITSUBISHI
            'JA3' => ['brand' => 'Mitsubishi',  'country' => 'JP'],
            'JA4' => ['brand' => 'Mitsubishi',  'country' => 'JP'],

            // SUBARU
            'JF1' => ['brand' => 'Subaru',      'country' => 'JP'],
            'JF2' => ['brand' => 'Subaru',      'country' => 'JP'],

            // KIA / HYUNDAI
            'KNA' => ['brand' => 'Kia',         'country' => 'KR'],
            'KNB' => ['brand' => 'Kia',         'country' => 'KR'],
            'KMH' => ['brand' => 'Hyundai',     'country' => 'KR'],
            'KMF' => ['brand' => 'Hyundai',     'country' => 'KR'],

            // TESLA
            '5YJ' => ['brand' => 'Tesla',       'country' => 'US'],
            '7SA' => ['brand' => 'Tesla',       'country' => 'US'],
            'LRW' => ['brand' => 'Tesla',       'country' => 'CN'],

            // JEEP / CHRYSLER
            '1C4' => ['brand' => 'Jeep',        'country' => 'US'],
            '1J4' => ['brand' => 'Jeep',        'country' => 'US'],
            '2C4' => ['brand' => 'Chrysler',    'country' => 'US'],
            '2C3' => ['brand' => 'Dodge',       'country' => 'US'],

            // LAND ROVER / JAGUAR
            'SAL' => ['brand' => 'Land Rover',  'country' => 'GB'],
            'SAJ' => ['brand' => 'Jaguar',      'country' => 'GB'],

            // RANGE ROVER
            'SAS' => ['brand' => 'Range Rover', 'country' => 'GB'],
        ];

        // Cerca prima WMI completo (3 caratteri), poi 2 caratteri
        $wmiData = $wmiMap[$wmi] ?? $wmiMap[substr($wmi, 0, 2).'0'] ?? null;
        $brand   = $wmiData['brand'] ?? null;

        // Anno dal VIN (posizione 10)
        $modelYear = $this->decodeYear($vin[9]);

        // Modello base da WMI + logica euristica
        $model = $this->guessModel($brand, $vds, $vin);

        // Se non riconosciuto
        if (!$brand) {
            return [
                'success' => false,
                'error'   => 'WMI non riconosciuto ('.substr($vin,0,3).'). Inserire i dati manualmente o configurare Claude AI per decodifica completa.',
            ];
        }

        return [
            'success' => true,
            'source'  => 'manual',
            'data'    => [
                'brand'        => $brand,
                'model'        => $model,
                'version'      => null,
                'year'         => $modelYear,
                'fuel_type'    => null,
                'transmission' => null,
                'body_type'    => null,
                'doors'        => null,
                'seats'        => null,
                'engine_cc'    => null,
                'power_kw'     => null,
                'power_hp'     => null,
                'color'        => null,
                'features'     => [],
                'description'  => null,
                'confidence'   => 'bassa',
                'notes'        => 'Dati parziali da WMI. Configurare ANTHROPIC_API_KEY per decodifica completa con optional e descrizione.',
            ],
        ];
    }

    private function decodeYear(string $char): ?int
    {
        $yearMap = [
            'A'=>1980,'B'=>1981,'C'=>1982,'D'=>1983,'E'=>1984,'F'=>1985,'G'=>1986,'H'=>1987,
            'J'=>1988,'K'=>1989,'L'=>1990,'M'=>1991,'N'=>1992,'P'=>1993,'R'=>1994,'S'=>1995,
            'T'=>1996,'V'=>1997,'W'=>1998,'X'=>1999,'Y'=>2000,
            '1'=>2001,'2'=>2002,'3'=>2003,'4'=>2004,'5'=>2005,'6'=>2006,'7'=>2007,'8'=>2008,'9'=>2009,
            'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,
            'J'=>2018,'K'=>2019,'L'=>2020,'M'=>2021,'N'=>2022,'P'=>2023,'R'=>2024,'S'=>2025,'T'=>2026,
        ];
        // Gestione ambiguità A=1980 o A=2010 → preferisci quello più recente
        $recent = [
            'A'=>2010,'B'=>2011,'C'=>2012,'D'=>2013,'E'=>2014,'F'=>2015,'G'=>2016,'H'=>2017,
            'J'=>2018,'K'=>2019,'L'=>2020,'M'=>2021,'N'=>2022,'P'=>2023,'R'=>2024,'S'=>2025,'T'=>2026,
            '1'=>2001,'2'=>2002,'3'=>2003,'4'=>2004,'5'=>2005,'6'=>2006,'7'=>2007,'8'=>2008,'9'=>2009,
        ];
        return $recent[$char] ?? null;
    }

    private function guessModel(string $brand, string $vds, string $vin): ?string
    {
        // Modelli comuni per marca basati su VDS (posizioni 4-9)
        $models = [
            'Fiat'          => ['500', 'Panda', 'Punto', 'Tipo', 'Bravo', '500X', '500L'],
            'Ford'          => ['Focus', 'Fiesta', 'Kuga', 'EcoSport', 'Mondeo', 'Galaxy', 'S-Max', 'Puma'],
            'BMW'           => ['Serie 1', 'Serie 2', 'Serie 3', 'Serie 4', 'Serie 5', 'X1', 'X3', 'X5'],
            'Mercedes-Benz' => ['Classe A', 'Classe B', 'Classe C', 'Classe E', 'GLA', 'GLC', 'GLE'],
            'Volkswagen'    => ['Golf', 'Polo', 'Passat', 'Tiguan', 'T-Roc', 'ID.3', 'ID.4'],
            'Audi'          => ['A1', 'A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7'],
            'Renault'       => ['Clio', 'Megane', 'Captur', 'Kadjar', 'Scenic'],
            'Peugeot'       => ['208', '308', '3008', '508', '2008'],
            'Citroën'       => ['C3', 'C4', 'C5', 'Berlingo', 'Spacetourer'],
            'Opel'          => ['Corsa', 'Astra', 'Mokka', 'Insignia', 'Crossland', 'Grandland'],
            'Toyota'        => ['Yaris', 'Corolla', 'RAV4', 'C-HR', 'Prius', 'Auris'],
            'Alfa Romeo'    => ['Giulia', 'Stelvio', 'Giulietta', 'MiTo', 'Tonale'],
            'Kia'           => ['Picanto', 'Rio', 'Ceed', 'Sportage', 'Sorento', 'Stonic'],
            'Hyundai'       => ['i10', 'i20', 'i30', 'Tucson', 'Santa Fe', 'Kona'],
            'Volvo'         => ['V40', 'V60', 'V90', 'XC40', 'XC60', 'XC90'],
            'Tesla'         => ['Model 3', 'Model Y', 'Model S', 'Model X'],
            'Mini'          => ['One', 'Cooper', 'Clubman', 'Countryman', 'Paceman'],
            'Nissan'        => ['Micra', 'Juke', 'Qashqai', 'X-Trail', 'Leaf'],
            'Mazda'         => ['2', '3', '6', 'CX-3', 'CX-5', 'MX-5'],
        ];

        // Ritorna null se non abbiamo abbastanza dati per indovinare il modello
        return null;
    }

    // ── CLAUDE AI DECODER ─────────────────────────────────────────────────────

    private function decodeWithClaude(string $vin): array
    {
        try {
            $prompt = <<<PROMPT
Sei un esperto decodificatore VIN automobilistico con conoscenza completa dei database europei e mondiali.

Analizza questo VIN: {$vin}

Restituisci SOLO un oggetto JSON valido (nessun testo prima o dopo) con questa struttura esatta:

{
  "brand": "marca in italiano/inglese (es: Ford, Fiat, BMW)",
  "model": "modello (es: Focus, 500, Serie 3)",
  "version": "allestimento/versione (es: 1.6 TDCi Titanium, 1.2 Pop Star)",
  "year": 2013,
  "fuel_type": "uno tra: benzina, diesel, ibrido, elettrico, gpl, metano",
  "transmission": "uno tra: manuale, automatico, semi_automatico",
  "body_type": "uno tra: berlina, hatchback, station_wagon, suv, crossover, coupe, cabrio, van, pickup, monovolume",
  "doors": 5,
  "seats": 5,
  "engine_cc": 1596,
  "power_kw": 85,
  "power_hp": 115,
  "color": null,
  "features": ["abs", "esp", "aria_condizionata", "bluetooth"],
  "description": "Descrizione commerciale in italiano di 2-3 frasi per l'annuncio, tono professionale da concessionario",
  "confidence": "alta/media/bassa",
  "notes": "eventuali note sul VIN o dati incerti"
}

Per i features usa SOLO valori da questa lista:
aria_condizionata, clima_automatico, clima_bizona, sedili_riscaldati, sedili_ventilati, sedili_elettrici, sedili_memoria, volante_riscaldato, tetto_apribile, tetto_panoramico, cruise_control, cruise_control_adattivo, abs, esp, airbag_frontali, airbag_laterali, airbag_tendina, sensori_parcheggio_ant, sensori_parcheggio_post, telecamera_posteriore, telecamera_360, lane_assist, blind_spot, frenata_autonoma, riconoscimento_segnali, radio, bluetooth, apple_carplay, android_auto, navigatore, schermo_touch, hifi, usb, wireless_charging, head_up_display, cerchi_lega, vernice_metallizzata, tetto_nero, barre_portapacchi, gancio_traino, specchi_elettrici, specchi_ripiegabili, luci_led, luci_matrix, fari_fendinebbia, start_stop, recupero_energia, paddleshift, launch_control, sospensioni_adattive, 4x4, differenziale_sportivo, freni_sportivi

Se non riesci a decodificare il VIN, restituisci:
{"error": "VIN non riconosciuto o dati insufficienti", "success": false}
PROMPT;

            $response = Http::withHeaders([
                'Content-Type'      => 'application/json',
                'x-api-key'         => env('ANTHROPIC_API_KEY'),
                'anthropic-version' => '2023-06-01',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1000,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->ok()) {
                return ['success' => false, 'error' => 'Errore API Claude: '.$response->status()];
            }

            $body    = $response->json();
            $content = $body['content'][0]['text'] ?? '';

            $content = preg_replace('/```json\s*/i', '', $content);
            $content = preg_replace('/```\s*/i', '', $content);
            $content = trim($content);

            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $content = $matches[0];
            }

            $data = json_decode($content, true);

            if (!$data || isset($data['error'])) {
                return ['success' => false, 'error' => $data['error'] ?? 'Risposta non valida da Claude'];
            }

            return [
                'success' => true,
                'source'  => 'claude',
                'data'    => [
                    'brand'        => $data['brand'] ?? null,
                    'model'        => $data['model'] ?? null,
                    'version'      => $data['version'] ?? null,
                    'year'         => $data['year'] ?? null,
                    'fuel_type'    => $data['fuel_type'] ?? null,
                    'transmission' => $data['transmission'] ?? null,
                    'body_type'    => $data['body_type'] ?? null,
                    'doors'        => $data['doors'] ?? null,
                    'seats'        => $data['seats'] ?? null,
                    'engine_cc'    => $data['engine_cc'] ?? null,
                    'power_kw'     => $data['power_kw'] ?? null,
                    'power_hp'     => $data['power_hp'] ?? null,
                    'color'        => $data['color'] ?? null,
                    'features'     => $data['features'] ?? [],
                    'description'  => $data['description'] ?? null,
                    'confidence'   => $data['confidence'] ?? 'media',
                    'notes'        => $data['notes'] ?? null,
                ],
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Errore Claude: '.$e->getMessage()];
        }
    }

    // ── NHTSA DECODER (VIN americani) ─────────────────────────────────────────

    private function decodeWithNHTSA(string $vin): array
    {
        try {
            $response = Http::timeout(10)->get(
                "https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{$vin}?format=json"
            );

            if (!$response->ok()) {
                return ['success' => false, 'error' => 'Errore NHTSA'];
            }

            $results = collect($response->json()['Results'] ?? []);
            $get     = fn($var) => $results->firstWhere('Variable', $var)['Value'] ?? null;
            $clean   = fn($v) => ($v === 'Not Applicable' || $v === '0' || empty($v)) ? null : $v;

            $brand = $clean($get('Make'));
            $model = $clean($get('Model'));

            if (!$brand && !$model) {
                return ['success' => false, 'error' => 'NHTSA dati insufficienti'];
            }

            $fuelMap  = ['Gasoline'=>'benzina','Diesel'=>'diesel','Electric'=>'elettrico','Hybrid'=>'ibrido','CNG'=>'metano','LPG'=>'gpl'];
            $fuelRaw  = $get('Fuel Type - Primary');
            $fuel     = null;
            foreach ($fuelMap as $k => $v) {
                if (str_contains((string)$fuelRaw, $k)) { $fuel = $v; break; }
            }

            $transRaw = $get('Transmission Style');
            $trans    = null;
            if (str_contains((string)$transRaw, 'Automatic')) $trans = 'automatico';
            elseif (str_contains((string)$transRaw, 'Manual')) $trans = 'manuale';

            $bodyMap  = ['Sedan'=>'berlina','Hatchback'=>'hatchback','SUV'=>'suv','Pickup'=>'pickup','Wagon'=>'station_wagon','Coupe'=>'coupe','Convertible'=>'cabrio','Van'=>'van','Minivan'=>'van'];
            $bodyRaw  = $get('Body Class');
            $body     = null;
            foreach ($bodyMap as $k => $v) {
                if (str_contains((string)$bodyRaw, $k)) { $body = $v; break; }
            }

            $kw = (float)($clean($get('Engine Power (kW)')) ?? 0);
            $cc = (int)($clean($get('Displacement (CC)')) ?? 0);

            return [
                'success' => true,
                'source'  => 'nhtsa',
                'data'    => [
                    'brand'        => $brand,
                    'model'        => $model,
                    'version'      => $clean($get('Trim')),
                    'year'         => $clean($get('Model Year')),
                    'fuel_type'    => $fuel,
                    'transmission' => $trans,
                    'body_type'    => $body,
                    'doors'        => $clean($get('Doors')),
                    'seats'        => $clean($get('Number of Seats')),
                    'engine_cc'    => $cc ?: null,
                    'power_kw'     => $kw ? (int)$kw : null,
                    'power_hp'     => $kw ? (int)round($kw * 1.36) : null,
                    'color'        => null,
                    'features'     => [],
                    'description'  => null,
                    'confidence'   => 'alta',
                    'notes'        => null,
                ],
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
