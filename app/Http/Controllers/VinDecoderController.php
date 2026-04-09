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

        try {
            // NHTSA API - gratuita, no key required
            $response = Http::timeout(10)->get(
                "https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{$vin}?format=json"
            );

            if (!$response->ok()) {
                return response()->json(['error' => 'Errore API NHTSA'], 500);
            }

            $data = $response->json();
            $results = collect($data['Results'] ?? []);

            $get = fn($var) => $results->firstWhere('Variable', $var)['Value'] ?? null;
            $clean = fn($v) => ($v === 'Not Applicable' || $v === '0' || empty($v)) ? null : $v;

            // Mappa carburante
            $fuelMap = [
                'Gasoline' => 'benzina',
                'Diesel'   => 'diesel',
                'Electric' => 'elettrico',
                'Hybrid'   => 'ibrido',
                'CNG'      => 'metano',
                'LPG'      => 'gpl',
                'Flex Fuel' => 'benzina',
            ];
            $fuelRaw = $get('Fuel Type - Primary');
            $fuel = null;
            foreach ($fuelMap as $k => $v) {
                if (str_contains((string)$fuelRaw, $k)) { $fuel = $v; break; }
            }

            // Mappa trasmissione
            $transRaw = $get('Transmission Style');
            $trans = null;
            if (str_contains((string)$transRaw, 'Automatic')) $trans = 'automatico';
            elseif (str_contains((string)$transRaw, 'Manual')) $trans = 'manuale';

            // Mappa carrozzeria
            $bodyMap = [
                'Sedan'       => 'berlina',
                'Hatchback'   => 'hatchback',
                'SUV'         => 'suv',
                'Pickup'      => 'pickup',
                'Wagon'       => 'station_wagon',
                'Coupe'       => 'coupe',
                'Convertible' => 'cabrio',
                'Van'         => 'van',
                'Minivan'     => 'van',
            ];
            $bodyRaw = $get('Body Class');
            $body = null;
            foreach ($bodyMap as $k => $v) {
                if (str_contains((string)$bodyRaw, $k)) { $body = $v; break; }
            }

            $kwRaw = $clean($get('Engine Power (kW)'));
            $kw = $kwRaw ? (float)$kwRaw : null;
            $hp = $kw ? (int)round($kw * 1.36) : null;

            $ccRaw = $clean($get('Displacement (CC)'));
            $cc = $ccRaw ? (int)$ccRaw : null;

            return response()->json([
                'success' => true,
                'data' => [
                    'brand'        => $clean($get('Make')),
                    'model'        => $clean($get('Model')),
                    'year'         => $clean($get('Model Year')),
                    'fuel_type'    => $fuel,
                    'transmission' => $trans,
                    'body_type'    => $body,
                    'doors'        => $clean($get('Doors')),
                    'seats'        => $clean($get('Seating Rows')),
                    'engine_cc'    => $cc,
                    'power_kw'     => $kw ? (int)$kw : null,
                    'power_hp'     => $hp,
                    'manufacturer' => $clean($get('Manufacturer Name')),
                    'country'      => $clean($get('Plant Country')),
                    'raw_model'    => $clean($get('Model')),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Errore connessione: '.$e->getMessage()], 500);
        }
    }
}