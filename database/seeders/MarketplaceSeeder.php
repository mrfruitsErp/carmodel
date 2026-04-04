<?php

namespace Database\Seeders;

use App\Models\MarketplaceCredential;
use App\Models\SaleVehicle;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->warn('Nessun tenant trovato — esegui prima DatabaseSeeder.');
            return;
        }

        // Credenziali demo (disabled)
        $platforms = [
            'autoscout24' => [
                'enabled'    => false,
                'credential' => ['client_id' => 'YOUR_CLIENT_ID', 'client_secret' => 'YOUR_SECRET'],
            ],
            'automobile_it' => [
                'enabled'    => false,
                'credential' => ['api_key' => 'YOUR_API_KEY'],
            ],
            'ebay_motors' => [
                'enabled'    => false,
                'credential' => [
                    'app_id'        => 'YOUR_APP_ID',
                    'cert_id'       => 'YOUR_CERT_ID',
                    'refresh_token' => 'YOUR_REFRESH_TOKEN',
                    'policies'      => [
                        'fulfillment_policy_id' => '',
                        'payment_policy_id'     => '',
                        'return_policy_id'      => '',
                    ],
                ],
            ],
            'subito_it' => [
                'enabled'    => false,
                'credential' => ['email' => 'tua@email.it', 'password' => 'tua_password'],
            ],
            'facebook_marketplace' => [
                'enabled'    => false,
                'credential' => [
                    'page_access_token' => 'YOUR_PAGE_TOKEN',
                    'catalog_id'        => 'YOUR_CATALOG_ID',
                ],
            ],
        ];

        foreach ($platforms as $platform => $data) {
            $cred = MarketplaceCredential::updateOrCreate(
                ['tenant_id' => $tenant->id, 'platform' => $platform],
                ['enabled'   => $data['enabled']]
            );
            $cred->setCredentialsArray($data['credential']);
        }

        // Veicoli demo
        $demoVehicles = [
            [
                'brand'          => 'BMW',
                'model'          => '320d',
                'version'        => 'Sport Line',
                'year'           => 2021,
                'mileage'        => 45000,
                'fuel_type'      => 'diesel',
                'transmission'   => 'automatico',
                'color'          => 'Nero Zaffiro',
                'doors'          => 4,
                'seats'          => 5,
                'power_kw'       => 140,
                'power_hp'       => 190,
                'body_type'      => 'berlina',
                'condition'      => 'ottimo',
                'asking_price'   => 28500,
                'purchase_price' => 24000,
                'description'    => 'BMW 320d in ottime condizioni, unico proprietario, tagliandi BMW. Full optional: navi, telecamera, sensori parcheggio, sedili riscaldati.',
                'plate'          => 'AB123CD',
                'previous_owners'=> 1,
            ],
            [
                'brand'          => 'Volkswagen',
                'model'          => 'Golf',
                'version'        => '8 1.5 eTSI DSG',
                'year'           => 2022,
                'mileage'        => 28000,
                'fuel_type'      => 'ibrido_benzina',
                'transmission'   => 'automatico',
                'color'          => 'Bianco Perlato',
                'doors'          => 5,
                'seats'          => 5,
                'power_kw'       => 110,
                'power_hp'       => 150,
                'body_type'      => 'berlina',
                'condition'      => 'eccellente',
                'asking_price'   => 26900,
                'purchase_price' => 22500,
                'description'    => 'Golf 8 ibrida mild-hybrid, cambio automatico DSG 7 marce. Come nuova, garanzia Casa Madre.',
                'plate'          => 'EF456GH',
                'previous_owners'=> 1,
            ],
            [
                'brand'          => 'Audi',
                'model'          => 'A4',
                'version'        => '2.0 TDI quattro',
                'year'           => 2020,
                'mileage'        => 62000,
                'fuel_type'      => 'diesel',
                'transmission'   => 'automatico',
                'color'          => 'Grigio Monsone',
                'doors'          => 4,
                'seats'          => 5,
                'power_kw'       => 140,
                'power_hp'       => 190,
                'body_type'      => 'berlina',
                'condition'      => 'buono',
                'asking_price'   => 24900,
                'purchase_price' => 20500,
                'description'    => 'Audi A4 quattro 4x4, navigatore, Virtual Cockpit, fari full LED.',
                'plate'          => 'IJ789KL',
                'previous_owners'=> 2,
            ],
        ];

        foreach ($demoVehicles as $vehicleData) {
            SaleVehicle::create(array_merge($vehicleData, [
                'tenant_id' => $tenant->id,
                'status'    => 'bozza',
                'features'  => ['abs', 'airbag', 'climatizzatore', 'cruise_control', 'sensori_parcheggio'],
            ]));
        }

        $this->command->info('MarketplaceSeeder: ' . count($platforms) . ' piattaforme configurate, ' . count($demoVehicles) . ' veicoli demo creati.');
    }
}