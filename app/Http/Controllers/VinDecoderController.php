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

        // Prefissi VIN americani → usa NHTSA
        $americanPrefixes = ['1', '2', '3', '4', '5', 'K'];
        $isAmerican = in_array($vin[0], $americanPrefixes);

        if ($isAmerican) {
            $result = $this->decodeWithNHTSA($vin);
            if ($result['success']) {
                return response()->json($result);
            }
        }

        // VIN europeo o NHTSA fallito → usa Claude AI
        return response()->json($this->decodeWithClaude($vin));
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

            // Pulisci il JSON da eventuali backtick o testo extra
            $content = preg_replace('/```json\s*/i', '', $content);
            $content = preg_replace('/```\s*/i', '', $content);
            $content = trim($content);

            // Estrai solo il JSON
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
            $year  = $clean($get('Model Year'));

            // Se NHTSA non ha dati sufficienti, fallback a Claude
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
                    'year'         => $year,
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