<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SaleVehicle;
use Carbon\Carbon;

class SaleVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $tid = 1;
        $vehicles = [
            [
                'brand'=>'BMW','model'=>'320d','version'=>'xDrive Sport Line',
                'year'=>2021,'plate'=>'FK823LP','vin'=>'WBA8E9C50HK896345',
                'mileage'=>42000,'fuel_type'=>'diesel','transmission'=>'automatico',
                'color'=>'Nero Zaffiro Metallizzato','color_type'=>'metallizzato',
                'body_type'=>'berlina','doors'=>4,'seats'=>5,
                'engine_cc'=>1995,'power_kw'=>140,'power_hp'=>190,
                'condition'=>'ottimo','previous_owners'=>1,
                'first_registration'=>'2021-03-15',
                'asking_price'=>28900,'min_price'=>27500,'purchase_price'=>22000,
                'price_negotiable'=>true,'vat_deductible'=>false,
                'title'=>'BMW 320d xDrive Sport Line - Unico proprietario - Full Optional',
                'description'=>"Splendida BMW 320d xDrive in allestimento Sport Line, immatricolata marzo 2021 con soli 42.000 km reali. Unico proprietario, sempre tagliandata in BMW. Motore diesel 2.0 da 190cv abbinato al cambio automatico Steptronic 8 rapporti e trazione integrale xDrive. Carrozzeria in ottime condizioni, interni in pelle Dakota nera senza difetti. Full optional inclusi: navigatore Professional, pacchetto luci LED, sedili riscaldati, telecamera posteriore, sensori parcheggio anteriori e posteriori, cruise control adattivo, head-up display, cerchi in lega 18'' stile 792. Unica proprietaria, non fumatore. Disponibile per visione e prova su strada previo appuntamento.",
                'status'=>'attivo',
                'features'=>['clima_bizona','sedili_riscaldati','sedili_elettrici','navigatore','apple_carplay','android_auto','telecamera_posteriore','sensori_parcheggio_ant','sensori_parcheggio_post','cruise_control_adattivo','head_up_display','luci_led','cerchi_lega','start_stop','4x4','bluetooth','schermo_touch'],
            ],
            [
                'brand'=>'Volkswagen','model'=>'Golf','version'=>'8 GTI 2.0 TSI DSG',
                'year'=>2022,'plate'=>'GH456TY','vin'=>'WVWZZZE1ZNP012345',
                'mileage'=>18500,'fuel_type'=>'benzina','transmission'=>'automatico',
                'color'=>'Bianco Candy','color_type'=>'solido',
                'body_type'=>'hatchback','doors'=>5,'seats'=>5,
                'engine_cc'=>1984,'power_kw'=>180,'power_hp'=>245,
                'condition'=>'ottimo','previous_owners'=>1,
                'first_registration'=>'2022-06-01',
                'asking_price'=>34500,'min_price'=>33000,'purchase_price'=>28000,
                'price_negotiable'=>true,'vat_deductible'=>true,
                'title'=>'VW Golf 8 GTI 2.0 TSI 245cv DSG - IVA detraibile - Come nuova',
                'description'=>"Volkswagen Golf 8 GTI in versione 2.0 TSI da 245cv con cambio DSG a 7 rapporti. Immatricolata giugno 2022 con soli 18.500 km, IVA detraibile. Veicolo in condizioni eccellenti, mai incidentata. Dotata di pacchetto IQ.DRIVE con ACC, Lane Assist e Travel Assist. Infotainment Discover Pro 10'' con navigazione online, App Connect wireless (CarPlay/Android Auto), Digital Cockpit Pro. Cerchi in lega Pretoria 18'', freni sportivi, sospensioni sportive progressiva. Sedili sportivi in tessuto/microfibra ArtVelours. Disponibile per test drive.",
                'status'=>'attivo',
                'features'=>['clima_automatico','cruise_control_adattivo','lane_assist','frenata_autonoma','apple_carplay','android_auto','navigatore','schermo_touch','bluetooth','cerchi_lega','sensori_parcheggio_post','telecamera_posteriore','luci_led','start_stop','paddleshift'],
            ],
            [
                'brand'=>'Audi','model'=>'A4','version'=>'Avant 40 TDI quattro S-tronic S-Line',
                'year'=>2021,'plate'=>'LM789QR','vin'=>'WAUZZZ8V7MA012345',
                'mileage'=>55000,'fuel_type'=>'diesel','transmission'=>'automatico',
                'color'=>'Grigio Cronos Metallizzato','color_type'=>'metallizzato',
                'body_type'=>'station_wagon','doors'=>5,'seats'=>5,
                'engine_cc'=>1968,'power_kw'=>150,'power_hp'=>204,
                'condition'=>'buono','previous_owners'=>2,
                'first_registration'=>'2021-01-20',
                'asking_price'=>31900,'min_price'=>30000,'purchase_price'=>24500,
                'price_negotiable'=>true,'vat_deductible'=>false,
                'title'=>'Audi A4 Avant 40 TDI quattro S-Line - S-tronic - 204cv',
                'description'=>"Audi A4 Avant in allestimento S-Line con motore 2.0 TDI da 204cv, cambio S-tronic 7 rapporti e trazione quattro integrale. Anno 2021, 55.000 km certificati, 2 proprietari. Carrozzeria station wagon molto spaziosa, ideale per famiglia o uso professionale. Esterni in stile sportivo S-Line con paraurti specifici, cerchi in lega 18'' S-Line, inserti in alluminio satinato. Navigatore MMI Plus con schermo 10.1'', Virtual Cockpit Plus 12.3'', Bang & Olufsen 3D Sound System. Tetto panoramico apribile. Servizio tagliandi Audi sempre rispettato.",
                'status'=>'attivo',
                'features'=>['clima_bizona','tetto_panoramico','sedili_riscaldati','navigatore','schermo_touch','apple_carplay','android_auto','hifi','telecamera_posteriore','sensori_parcheggio_ant','sensori_parcheggio_post','cruise_control_adattivo','lane_assist','luci_matrix','cerchi_lega','4x4','start_stop','bluetooth'],
            ],
            [
                'brand'=>'Toyota','model'=>'RAV4','version'=>'2.5 Hybrid AWD-i Style',
                'year'=>2023,'plate'=>'NO234ST','vin'=>'JTMRWREV50D123456',
                'mileage'=>12000,'fuel_type'=>'ibrido_benzina','transmission'=>'automatico',
                'color'=>'Bianco Perla','color_type'=>'perlato',
                'body_type'=>'suv','doors'=>5,'seats'=>5,
                'engine_cc'=>2487,'power_kw'=>160,'power_hp'=>218,
                'condition'=>'ottimo','previous_owners'=>1,
                'first_registration'=>'2023-02-10',
                'asking_price'=>42900,'min_price'=>41000,'purchase_price'=>36000,
                'price_negotiable'=>false,'vat_deductible'=>false,
                'title'=>'Toyota RAV4 2.5 Hybrid 218cv AWD-i Style - 12.000km - Come nuova',
                'description'=>"Toyota RAV4 Hybrid in allestimento Style con sistema ibrido 218cv e trazione integrale elettrica AWD-i. Immatricolata febbraio 2023 con soli 12.000 km, ancora in garanzia Toyota fino a febbraio 2026. Consumi reali certificati 5.5L/100km. Colorazione Bianco Perla bi-tono con tetto nero. Interni con rivestimento SofTex (similpelle) bicolore bianco/nero, con cuciture a contrasto. Toyota Safety Sense di seconda generazione: PCS, LDA, LTA, AHB, BSM, RCTA. Sistema multimedia Toyota Touch 2 con schermo da 9'', JBL Sound System 9 altoparlanti.",
                'status'=>'attivo',
                'features'=>['clima_bizona','sedili_riscaldati','sedili_ventilati','navigatore','apple_carplay','android_auto','hifi','telecamera_360','sensori_parcheggio_ant','sensori_parcheggio_post','cruise_control_adattivo','lane_assist','blind_spot','frenata_autonoma','luci_led','cerchi_lega','4x4','start_stop','recupero_energia','wireless_charging'],
            ],
            [
                'brand'=>'Mercedes-Benz','model'=>'Classe C','version'=>'C 220d AMG Line Premium',
                'year'=>2022,'plate'=>'PQ567UV','vin'=>'WDD2050022R123456',
                'mileage'=>31000,'fuel_type'=>'diesel','transmission'=>'automatico',
                'color'=>'Argento Iridio Metallizzato','color_type'=>'metallizzato',
                'body_type'=>'berlina','doors'=>4,'seats'=>5,
                'engine_cc'=>1993,'power_kw'=>147,'power_hp'=>200,
                'condition'=>'ottimo','previous_owners'=>1,
                'first_registration'=>'2022-09-05',
                'asking_price'=>44900,'min_price'=>43000,'purchase_price'=>37500,
                'price_negotiable'=>true,'vat_deductible'=>true,
                'title'=>'Mercedes C 220d AMG Line Premium - IVA esposta - Full Optional',
                'description'=>"Mercedes-Benz Classe C 220d in allestimento AMG Line Premium Plus, immatricolata settembre 2022 con 31.000 km. IVA esposta per acquirenti business. Unico proprietario, uso aziendale, sempre tagliandata Mercedes. Motore OM654 diesel mild-hybrid da 200cv con sistema EQ Boost 20cv. Cambio automatico 9G-TRONIC. Esterno AMG Line con calandra AMG panamericana, cerchi AMG da 19'', pinze freno rosse. Sistema MBUX di ultima generazione con doppio schermo da 11.9'' con Hyperscreen, Burmester Surround Sound 15 altoparlanti, tetto panoramico elettrocromico.",
                'status'=>'attivo',
                'features'=>['clima_bizona','tetto_panoramico','sedili_riscaldati','sedili_ventilati','sedili_elettrici','sedili_memoria','navigatore','schermo_touch','hifi','telecamera_360','sensori_parcheggio_ant','sensori_parcheggio_post','cruise_control_adattivo','lane_assist','blind_spot','frenata_autonoma','head_up_display','luci_matrix','cerchi_lega','start_stop','recupero_energia','wireless_charging','apple_carplay','android_auto'],
            ],
            [
                'brand'=>'Fiat','model'=>'500','version'=>'1.0 Hybrid Dolcevita',
                'year'=>2022,'plate'=>'RS890WX','vin'=>'ZFA3120000P123456',
                'mileage'=>24000,'fuel_type'=>'ibrido_benzina','transmission'=>'manuale',
                'color'=>'Rosa Venezia','color_type'=>'solido',
                'body_type'=>'hatchback','doors'=>3,'seats'=>4,
                'engine_cc'=>999,'power_kw'=>51,'power_hp'=>70,
                'condition'=>'buono','previous_owners'=>1,
                'first_registration'=>'2022-04-20',
                'asking_price'=>14500,'min_price'=>13800,'purchase_price'=>10500,
                'price_negotiable'=>true,'vat_deductible'=>false,
                'title'=>'Fiat 500 1.0 Hybrid Dolcevita - Tetto apribile - Cerchi lega',
                'description'=>"Fiat 500 1.0 Hybrid in allestimento Dolcevita, immatricolata aprile 2022 con 24.000 km reali. Primo proprietario, non fumatrice, sempre tenuta con cura. Sistema mild-hybrid BSG da 70cv con recupero energia in frenata. Tetto apribile elettrico, cerchi in lega da 16'', verniciatura bicolore con tetto in tinta. Radio DAB+ con schermo da 7'' touchscreen, comandi vocali. Perfetta per uso urbano con consumi di circa 5L/100km. Tagliandi sempre eseguiti in Fiat. Prima di comprare fatevi un giro, rimarrete sorpresi!",
                'status'=>'attivo',
                'features'=>['aria_condizionata','tetto_apribile','cerchi_lega','bluetooth','apple_carplay','sensori_parcheggio_post','start_stop','recupero_energia'],
            ],
            [
                'brand'=>'Porsche','model'=>'Cayenne','version'=>'3.0 V6 Tiptronic S Platinum Edition',
                'year'=>2020,'plate'=>'TU123YZ','vin'=>'WP1ZZZ9YZLDA12345',
                'mileage'=>68000,'fuel_type'=>'benzina','transmission'=>'automatico',
                'color'=>'Blu Notte Metallizzato','color_type'=>'metallizzato',
                'body_type'=>'suv','doors'=>5,'seats'=>5,
                'engine_cc'=>2995,'power_kw'=>250,'power_hp'=>340,
                'condition'=>'buono','previous_owners'=>2,
                'first_registration'=>'2020-07-15',
                'asking_price'=>62900,'min_price'=>60000,'purchase_price'=>52000,
                'price_negotiable'=>true,'vat_deductible'=>false,
                'title'=>'Porsche Cayenne 3.0 V6 340cv Tiptronic - Platinum Edition - Full',
                'description'=>"Porsche Cayenne in edizione Platinum con motore V6 3.0 da 340cv e cambio automatico Tiptronic S 8 rapporti. Anno 2020 con 68.000 km certificati, 2 proprietari, libro tagliandi Porsche Center completo. Allestimento Platinum Edition con elementi esterni in Platinum Silver: cornici finestrini, inserti paraurti, soglie porta illuminate. Interni in pelle Bordeaux a due toni con cuciture a contrasto. Cerchi in lega Cayenne Sport da 21''. Sistema PCM 4.0 con navigazione online, Burmester 3D Sound (18 altoparlanti), head-up display, telecamera surround 360. Sospensioni pneumatiche Porsche con abbassamento automatico in autostrada.",
                'status'=>'attivo',
                'features'=>['clima_bizona','tetto_panoramico','sedili_riscaldati','sedili_ventilati','sedili_elettrici','sedili_memoria','navigatore','schermo_touch','hifi','telecamera_360','sensori_parcheggio_ant','sensori_parcheggio_post','cruise_control_adattivo','lane_assist','blind_spot','frenata_autonoma','head_up_display','luci_matrix','cerchi_lega','4x4','start_stop','sospensioni_adattive','wireless_charging','apple_carplay','android_auto','volante_riscaldato'],
            ],
            [
                'brand'=>'Renault','model'=>'Zoe','version'=>'R135 Intens Z.E. 50 - Batteria inclusa',
                'year'=>2021,'plate'=>'VW456AB','vin'=>'VF1AG000162123456',
                'mileage'=>29000,'fuel_type'=>'elettrico','transmission'=>'automatico',
                'color'=>'Grigio Titanio','color_type'=>'metallizzato',
                'body_type'=>'hatchback','doors'=>5,'seats'=>5,
                'engine_cc'=>0,'power_kw'=>100,'power_hp'=>136,
                'condition'=>'ottimo','previous_owners'=>1,
                'first_registration'=>'2021-08-30',
                'asking_price'=>19900,'min_price'=>18500,'purchase_price'=>15000,
                'price_negotiable'=>true,'vat_deductible'=>false,
                'title'=>'Renault Zoe R135 136cv - Batteria inclusa - Autonomia 395km WLTP',
                'description'=>"Renault Zoe in versione R135 con motore elettrico da 136cv e batteria Z.E. 50 da 52 kWh inclusa nella vendita (non in leasing). Autonomia WLTP certificata di 395 km. Immatricolata agosto 2021 con 29.000 km reali, unico proprietario. Ricarica AC fino a 22kW in corrente alternata (ricarica in circa 2h45). Compatibile con ricarica rapida CCS fino a 50kW. Allestimento Intens con schermo da 9.3'' Easy Link, navigatore integrato, Bose Sound System, cerchi in lega diamantati da 16'', sensori di parcheggio posteriori con camera. Incluso cavo Mode 3 e cavo domestico di emergenza.",
                'status'=>'bozza',
                'features'=>['clima_automatico','navigatore','schermo_touch','apple_carplay','android_auto','hifi','telecamera_posteriore','sensori_parcheggio_post','cerchi_lega','recupero_energia','wireless_charging','bluetooth','cruise_control'],
            ],
        ];

        foreach ($vehicles as $v) {
            SaleVehicle::create(array_merge($v, [
                'tenant_id'  => $tid,
                'created_by' => 1,
                'available_from' => now(),
            ]));
        }

        $this->command->info('  '.count($vehicles).' veicoli in vendita creati!');
    }
}