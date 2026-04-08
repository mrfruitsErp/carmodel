<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\{Customer, Vehicle, Claim, InsuranceCompany, Expert, WorkOrder, FleetVehicle, Rental, PersonalInjury, SparePart, Quote};
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    private int $tid = 1;

    public function run(): void
    {
        $this->seedInsuranceCompanies();
        $this->seedExperts();
        $this->seedCustomers();
        $this->seedVehicles();
        $this->seedClaims();
        $this->seedWorkOrders();
        $this->seedFlotta();
        $this->seedNoleggi();
        $this->seedLesioni();
        $this->seedRicambi();
        $this->seedQuotes();
        $this->seedDocumenti();
        $this->command->info('Demo data inseriti correttamente!');
    }

    private function seedInsuranceCompanies(): void
    {
        foreach ([
            ['name'=>'Generali SpA','code'=>'GEN','phone'=>'02 4815162','email'=>'sinistri@generali.it'],
            ['name'=>'UnipolSai','code'=>'UNI','phone'=>'051 5077111','email'=>'sinistri@unipolsai.it'],
            ['name'=>'AXA Italia','code'=>'AXA','phone'=>'02 5806811','email'=>'sinistri@axa.it'],
            ['name'=>'Allianz','code'=>'ALZ','phone'=>'800 226622','email'=>'sinistri@allianz.it'],
            ['name'=>'Zurich','code'=>'ZUR','phone'=>'800 551155','email'=>'sinistri@zurich.it'],
        ] as $c) {
            InsuranceCompany::firstOrCreate(
                ['tenant_id'=>$this->tid,'name'=>$c['name']],
                array_merge($c,['tenant_id'=>$this->tid])
            );
        }
        $this->command->line('  Compagnie OK');
    }

    private function seedExperts(): void
    {
        foreach ([
            ['name'=>'Ing. Marco Rossi','type'=>'perito','phone'=>'335 1234567','email'=>'m.rossi@periti.it','company_name'=>'Studio Rossi','title'=>'Ing.'],
            ['name'=>'Dott. Luca Bianchi','type'=>'perito','phone'=>'347 9876543','email'=>'l.bianchi@periti.it','company_name'=>'Perizie Bianchi','title'=>'Dott.'],
            ['name'=>'Avv. Sara Ferrari','type'=>'avvocato','phone'=>'02 45678901','email'=>'s.ferrari@avvocati.it','company_name'=>'Studio Ferrari','title'=>'Avv.'],
            ['name'=>'Avv. Paolo Greco','type'=>'avvocato','phone'=>'011 9876543','email'=>'p.greco@avvocati.it','company_name'=>'Greco Partners','title'=>'Avv.'],
            ['name'=>'Ing. Anna Moretti','type'=>'perito','phone'=>'333 5678901','email'=>'a.moretti@periti.it','company_name'=>'Moretti Perizie','title'=>'Ing.'],
        ] as $e) {
            Expert::firstOrCreate(
                ['tenant_id'=>$this->tid,'email'=>$e['email']],
                array_merge($e,['tenant_id'=>$this->tid,'active'=>true])
            );
        }
        $this->command->line('  Periti/Avvocati OK');
    }

    private function seedCustomers(): void
    {
        foreach ([
            ['first_name'=>'Mario','last_name'=>'Rossi','email'=>'mario.rossi@email.it','phone'=>'335 1234567','city'=>'Torino','address'=>'Via Roma 15','fiscal_code'=>'RSSMRA80A01L219X','type'=>'private'],
            ['first_name'=>'Anna','last_name'=>'Bianchi','email'=>'anna.bianchi@email.it','phone'=>'347 9876543','city'=>'Milano','address'=>'Corso Vittorio 22','fiscal_code'=>'BNCNNA85B41L219Y','type'=>'private'],
            ['first_name'=>'Luigi','last_name'=>'Verdi','email'=>'luigi.verdi@email.it','phone'=>'333 5678901','city'=>'Roma','address'=>'Via Nazionale 88','fiscal_code'=>'VRDLGU75C01F205Z','type'=>'private'],
            ['first_name'=>'Giulia','last_name'=>'Esposito','email'=>'giulia.esposito@email.it','phone'=>'320 4567890','city'=>'Napoli','address'=>'Via Toledo 45','fiscal_code'=>'SPSGLI90D41F839W','type'=>'private'],
            ['first_name'=>'Roberto','last_name'=>'Conti','email'=>'roberto.conti@email.it','phone'=>'348 7654321','city'=>'Bologna','address'=>'Via Indipendenza 33','fiscal_code'=>'CNTRRT70E01H501V','type'=>'private'],
            ['first_name'=>'Francesca','last_name'=>'Russo','email'=>'f.russo@azienda.it','phone'=>'02 1234567','city'=>'Milano','address'=>'Viale Monza 120','fiscal_code'=>'RSSFNC88F41H501U','type'=>'company','company_name'=>'Russo Srl','vat_number'=>'IT12345678901'],
            ['first_name'=>'Giorgio','last_name'=>'Mancini','email'=>'g.mancini@email.it','phone'=>'051 9876543','city'=>'Bologna','address'=>'Via Ugo Bassi 7','fiscal_code'=>'MNCGRG65G01A944T','type'=>'private'],
            ['first_name'=>'Elena','last_name'=>'Ferretti','email'=>'e.ferretti@email.it','phone'=>'011 5678901','city'=>'Torino','address'=>'Corso Francia 88','fiscal_code'=>'FRRELN92H41L219S','type'=>'private'],
        ] as $c) {
            Customer::firstOrCreate(
                ['tenant_id'=>$this->tid,'email'=>$c['email']],
                array_merge($c,['tenant_id'=>$this->tid,'active'=>true,'created_by'=>1])
            );
        }
        $this->command->line('  Clienti OK');
    }

    private function seedVehicles(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        foreach ([
            ['plate'=>'AB123CD','brand'=>'BMW','model'=>'320d','year'=>2021,'fuel_type'=>'diesel','ci'=>0,'color'=>'Nero Zaffiro','km_current'=>45000],
            ['plate'=>'EF456GH','brand'=>'Volkswagen','model'=>'Golf','year'=>2020,'fuel_type'=>'benzina','ci'=>1,'color'=>'Bianco Perla','km_current'=>32000],
            ['plate'=>'IL789MN','brand'=>'Fiat','model'=>'500','year'=>2022,'fuel_type'=>'benzina','ci'=>2,'color'=>'Rosso Passione','km_current'=>18000],
            ['plate'=>'OP012QR','brand'=>'Audi','model'=>'A4','year'=>2021,'fuel_type'=>'diesel','ci'=>3,'color'=>'Grigio Selenio','km_current'=>55000],
            ['plate'=>'ST345UV','brand'=>'Toyota','model'=>'Yaris','year'=>2023,'fuel_type'=>'ibrido','ci'=>4,'color'=>'Blu Nebula','km_current'=>12000],
            ['plate'=>'WX678YZ','brand'=>'Mercedes','model'=>'Classe C','year'=>2022,'fuel_type'=>'diesel','ci'=>5,'color'=>'Argento Iridio','km_current'=>38000],
            ['plate'=>'AA111BB','brand'=>'Renault','model'=>'Clio','year'=>2020,'fuel_type'=>'benzina','ci'=>6,'color'=>'Arancio Valencia','km_current'=>41000],
            ['plate'=>'CC222DD','brand'=>'Peugeot','model'=>'208','year'=>2021,'fuel_type'=>'elettrico','ci'=>7,'color'=>'Verde Olivine','km_current'=>22000],
        ] as $v) {
            $customer = $customers[$v['ci']] ?? $customers->first();
            Vehicle::firstOrCreate(
                ['tenant_id'=>$this->tid,'plate'=>$v['plate']],
                [
                    'tenant_id'=>$this->tid,
                    'customer_id'=>$customer->id,
                    'plate'=>$v['plate'],
                    'brand'=>$v['brand'],
                    'model'=>$v['model'],
                    'year'=>$v['year'],
                    'fuel_type'=>$v['fuel_type'],
                    'color'=>$v['color'],
                    'km_current'=>$v['km_current'],
                    'status'=>'pronto',
                ]
            );
        }
        $this->command->line('  Veicoli OK');
    }

    private function seedClaims(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        $vehicles  = Vehicle::where('tenant_id',$this->tid)->get();
        $companies = InsuranceCompany::where('tenant_id',$this->tid)->get();
        $periti    = Expert::where('tenant_id',$this->tid)->where('type','perito')->get();
        foreach ([
            ['ci'=>0,'vi'=>0,'coi'=>0,'pi'=>0,'type'=>'rca','status'=>'in_riparazione','event'=>'-30 days','expiry'=>'+15 days','plate'=>'QR345ST','desc'=>'Tamponamento autostrada A4','policy'=>'POL-2024-001234','amount'=>2800],
            ['ci'=>1,'vi'=>1,'coi'=>1,'pi'=>1,'type'=>'kasko','status'=>'perizia_attesa','event'=>'-20 days','expiry'=>'+25 days','plate'=>'AB567CD','desc'=>'Grandinata in parcheggio centro commerciale','policy'=>'POL-2024-005678','amount'=>4500],
            ['ci'=>2,'vi'=>2,'coi'=>2,'pi'=>0,'type'=>'rca','status'=>'aperto','event'=>'-10 days','expiry'=>'+45 days','plate'=>'EF789GH','desc'=>'Collisione al semaforo via Torino','policy'=>'POL-2024-009012','amount'=>1200],
            ['ci'=>3,'vi'=>3,'coi'=>3,'pi'=>2,'type'=>'grandine','status'=>'liquidazione_attesa','event'=>'-60 days','expiry'=>'+5 days','plate'=>null,'desc'=>'Danno da grandine parcheggio scoperto','policy'=>'POL-2024-003456','amount'=>3800],
            ['ci'=>4,'vi'=>4,'coi'=>0,'pi'=>1,'type'=>'rca','status'=>'chiuso','event'=>'-90 days','expiry'=>'-10 days','plate'=>'IL012MN','desc'=>'Urto contro guard rail tangenziale','policy'=>'POL-2023-007890','amount'=>6200],
            ['ci'=>5,'vi'=>5,'coi'=>1,'pi'=>2,'type'=>'furto','status'=>'aperto','event'=>'-5 days','expiry'=>'+60 days','plate'=>null,'desc'=>'Furto parziale specchietti e cerchi in lega','policy'=>'POL-2024-011111','amount'=>2200],
        ] as $c) {
            Claim::firstOrCreate(
                ['tenant_id'=>$this->tid,'policy_number'=>$c['policy']],
                [
                    'tenant_id'=>$this->tid,
                    'claim_number'=>Claim::generateNumber($this->tid),
                    'customer_id'=>($customers[$c['ci']] ?? $customers->first())->id,
                    'vehicle_id'=>($vehicles[$c['vi']] ?? $vehicles->first())->id,
                    'insurance_company_id'=>($companies[$c['coi']] ?? $companies->first())->id,
                    'expert_id'=>($periti[$c['pi']] ?? $periti->first())->id,
                    'claim_type'=>$c['type'],
                    'status'=>$c['status'],
                    'event_date'=>Carbon::parse($c['event']),
                    'event_location'=>'Milano, Corso Buenos Aires 42',
                    'event_description'=>$c['desc'],
                    'counterpart_plate'=>$c['plate'],
                    'policy_number'=>$c['policy'],
                    'cid_signed'=>true,
                    'cid_date'=>Carbon::parse($c['event']),
                    'cid_expiry'=>Carbon::parse($c['expiry']),
                    'estimated_amount'=>$c['amount'],
                    'created_by'=>1,
                ]
            );
        }
        $this->command->line('  Sinistri OK');
    }

    private function seedWorkOrders(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        $vehicles  = Vehicle::where('tenant_id',$this->tid)->get();
        $claims    = Claim::where('tenant_id',$this->tid)->get();
        foreach ([
            ['ci'=>0,'vi'=>0,'cli'=>0,'type'=>'carrozzeria','status'=>'in_lavorazione','priority'=>'alta','desc'=>'Riparazione paraurti posteriore e verniciatura colore originale.','amount'=>2800,'end'=>'+5 days','progress'=>60],
            ['ci'=>1,'vi'=>1,'cli'=>1,'type'=>'carrozzeria','status'=>'attesa','priority'=>'normale','desc'=>'Riparazione grandinata: padiglione, cofano, porta ant. sx.','amount'=>4500,'end'=>'+10 days','progress'=>0],
            ['ci'=>2,'vi'=>2,'cli'=>null,'type'=>'meccanica','status'=>'in_lavorazione','priority'=>'normale','desc'=>'Revisione freni anteriori e posteriori. Sostituzione pastiglie e dischi.','amount'=>450,'end'=>'+2 days','progress'=>80],
            ['ci'=>3,'vi'=>3,'cli'=>null,'type'=>'tagliando','status'=>'completato','priority'=>'normale','desc'=>'Tagliando 60.000 km. Olio motore, filtri, candele.','amount'=>380,'end'=>'-3 days','progress'=>100],
            ['ci'=>4,'vi'=>4,'cli'=>null,'type'=>'gomme','status'=>'attesa','priority'=>'urgente','desc'=>'Sostituzione 4 pneumatici estivi 195/65 R15.','amount'=>520,'end'=>'+1 days','progress'=>0],
            ['ci'=>5,'vi'=>5,'cli'=>2,'type'=>'carrozzeria','status'=>'attesa','priority'=>'normale','desc'=>'Riparazione fiancata destra e porta anteriore destra.','amount'=>1900,'end'=>'+15 days','progress'=>0],
            ['ci'=>6,'vi'=>6,'cli'=>null,'type'=>'elettronica','status'=>'completato','priority'=>'alta','desc'=>'Sostituzione alternatore e batteria. Diagnosi centralina motore.','amount'=>750,'end'=>'-7 days','progress'=>100],
            ['ci'=>7,'vi'=>7,'cli'=>null,'type'=>'detailing','status'=>'in_lavorazione','priority'=>'normale','desc'=>'Lucidatura completa carrozzeria, pulizia interni.','amount'=>280,'end'=>'+1 days','progress'=>50],
        ] as $o) {
            WorkOrder::create([
                'tenant_id'=>$this->tid,
                'job_number'=>WorkOrder::generateNumber($this->tid),
                'customer_id'=>($customers[$o['ci']] ?? $customers->first())->id,
                'vehicle_id'=>($vehicles[$o['vi']] ?? $vehicles->first())->id,
                'claim_id'=>$o['cli']!==null ? ($claims[$o['cli']] ?? null)?->id : null,
                'job_type'=>$o['type'],
                'status'=>$o['status'],
                'priority'=>$o['priority'],
                'description'=>$o['desc'],
                'estimated_amount'=>$o['amount'],
                'expected_end_date'=>Carbon::parse($o['end']),
                'progress_percent'=>$o['progress'],
                'created_by'=>1,
            ]);
        }
        $this->command->line('  Lavorazioni OK');
    }

    private function seedFlotta(): void
    {
        foreach ([
            ['plate'=>'FT001AA','brand'=>'Fiat','model'=>'Tipo','year'=>2022,'fuel_type'=>'benzina','color'=>'Bianco','status'=>'disponibile','daily_rate'=>45,'km_current'=>18000],
            ['plate'=>'FT002BB','brand'=>'Volkswagen','model'=>'Polo','year'=>2021,'fuel_type'=>'benzina','color'=>'Grigio','status'=>'disponibile','daily_rate'=>50,'km_current'=>32000],
            ['plate'=>'FT003CC','brand'=>'Peugeot','model'=>'208','year'=>2023,'fuel_type'=>'ibrido','color'=>'Blu','status'=>'noleggiato','daily_rate'=>55,'km_current'=>9000],
            ['plate'=>'FT004DD','brand'=>'Renault','model'=>'Clio','year'=>2022,'fuel_type'=>'benzina','color'=>'Rosso','status'=>'disponibile','daily_rate'=>48,'km_current'=>24000],
            ['plate'=>'FT005EE','brand'=>'Toyota','model'=>'Aygo','year'=>2023,'fuel_type'=>'benzina','color'=>'Nero','status'=>'manutenzione','daily_rate'=>40,'km_current'=>11000],
        ] as $f) {
            FleetVehicle::firstOrCreate(
                ['tenant_id'=>$this->tid,'plate'=>$f['plate']],
                array_merge($f,['tenant_id'=>$this->tid])
            );
        }
        $this->command->line('  Flotta OK');
    }

    private function seedNoleggi(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        $flotta    = FleetVehicle::where('tenant_id',$this->tid)->get();
        $claims    = Claim::where('tenant_id',$this->tid)->get();
        if ($flotta->isEmpty() || $customers->isEmpty()) return;
        foreach ([
            ['ci'=>0,'fi'=>0,'cli'=>0,'type'=>'sostitutiva','start'=>'-10 days','end'=>'+10 days','rate'=>85,'status'=>'pronto','km'=>25000,'fuel'=>100],
            ['ci'=>1,'fi'=>1,'cli'=>null,'type'=>'breve_termine','start'=>'-5 days','end'=>'+3 days','rate'=>55,'status'=>'pronto','km'=>18000,'fuel'=>80],
            ['ci'=>2,'fi'=>2,'cli'=>2,'type'=>'sostitutiva','start'=>'-30 days','end'=>'-5 days','rate'=>45,'status'=>'chiuso','km'=>31000,'fuel'=>100],
            ['ci'=>3,'fi'=>3,'cli'=>null,'type'=>'lungo_termine','start'=>'-60 days','end'=>'+30 days','rate'=>38,'status'=>'pronto','km'=>42000,'fuel'=>90],
        ] as $n) {
            Rental::create([
                'tenant_id'=>$this->tid,
                'rental_number'=>Rental::generateNumber($this->tid),
                'fleet_vehicle_id'=>($flotta[$n['fi']] ?? $flotta->first())->id,
                'customer_id'=>($customers[$n['ci']] ?? $customers->first())->id,
                'claim_id'=>$n['cli']!==null ? ($claims[$n['cli']] ?? null)?->id : null,
                'rental_type'=>$n['type'],
                'start_date'=>Carbon::parse($n['start']),
                'expected_end_date'=>Carbon::parse($n['end']),
                'actual_end_date'=>$n['status']==='chiuso' ? Carbon::parse($n['end']) : null,
                'daily_rate'=>$n['rate'],
                'status'=>$n['status'],
                'km_start'=>$n['km'],
                'km_end'=>$n['status']==='chiuso' ? $n['km']+850 : null,
                'fuel_level_start'=>$n['fuel'],
                'fuel_level_end'=>$n['status']==='chiuso' ? 75 : null,
                'created_by'=>1,
            ]);
        }
        $this->command->line('  Noleggi OK');
    }

    private function seedLesioni(): void
    {
        $claims   = Claim::where('tenant_id',$this->tid)->get();
        $avvocati = Expert::where('tenant_id',$this->tid)->where('type','avvocato')->get();
        if ($claims->isEmpty()) return;
        foreach ([
            ['cli'=>0,'injury'=>'Colpo di frusta cervicale','days'=>30,'amount'=>3500,'status'=>'visita_medica','notes'=>'Fisioterapia 3 volte a settimana.'],
            ['cli'=>2,'injury'=>'Contusione al ginocchio destro','days'=>15,'amount'=>1200,'status'=>'chiusa','notes'=>'Guarigione completa. Certificato allegato.'],
            ['cli'=>0,'injury'=>'Frattura costola destra','days'=>45,'amount'=>5800,'status'=>'perizia_medica','notes'=>'Rx effettuata. In attesa referto specialistico.'],
        ] as $l) {
            PersonalInjury::create([
                'tenant_id'=>$this->tid,
                'injury_number'=>'LES-'.str_pad(rand(1,999),3,'0',STR_PAD_LEFT),
                'claim_id'=>($claims[$l['cli']] ?? $claims->first())->id,
                'customer_id'=>($claims[$l['cli']] ?? $claims->first())->customer_id,
                'injury_type'=>$l['injury'],
                'injury_description'=>$l['injury'],
                'estimated_amount'=>$l['amount'],
                'status'=>$l['status'],
                'lawyer_id'=>$avvocati->first()?->id,
                'notes'=>$l['notes'],
            ]);
        }
        $this->command->line('  Lesioni OK');
    }

    private function seedRicambi(): void
    {
        foreach ([
            ['code'=>'RIC-001','name'=>'Paraurti anteriore BMW Serie 3','category'=>'carrozzeria','brand'=>'BMW','stock_quantity'=>2,'min_stock'=>1,'purchase_price'=>320,'sale_price'=>580,'location'=>'A1-01'],
            ['code'=>'RIC-002','name'=>'Fanale anteriore dx VW Golf','category'=>'elettrico','brand'=>'VW','stock_quantity'=>1,'min_stock'=>1,'purchase_price'=>145,'sale_price'=>280,'location'=>'A1-02'],
            ['code'=>'RIC-003','name'=>'Pastiglie freno anteriore universali','category'=>'meccanica','brand'=>'Brembo','stock_quantity'=>8,'min_stock'=>3,'purchase_price'=>35,'sale_price'=>75,'location'=>'B2-01'],
            ['code'=>'RIC-004','name'=>'Olio motore 5W30 1L','category'=>'consumabili','brand'=>'Castrol','stock_quantity'=>24,'min_stock'=>10,'purchase_price'=>8,'sale_price'=>18,'location'=>'C1-01'],
            ['code'=>'RIC-005','name'=>'Filtro olio universale','category'=>'meccanica','brand'=>'Mann','stock_quantity'=>15,'min_stock'=>5,'purchase_price'=>6,'sale_price'=>14,'location'=>'C1-02'],
            ['code'=>'RIC-006','name'=>'Specchietto retrovisore sx Fiat 500','category'=>'carrozzeria','brand'=>'Fiat','stock_quantity'=>1,'min_stock'=>1,'purchase_price'=>85,'sale_price'=>160,'location'=>'A2-01'],
            ['code'=>'RIC-007','name'=>'Kit cinghia distribuzione','category'=>'meccanica','brand'=>'Gates','stock_quantity'=>3,'min_stock'=>1,'purchase_price'=>95,'sale_price'=>195,'location'=>'B1-03'],
            ['code'=>'RIC-008','name'=>'Pneumatico 195/65 R15 Michelin','category'=>'gomme','brand'=>'Michelin','stock_quantity'=>6,'min_stock'=>4,'purchase_price'=>68,'sale_price'=>125,'location'=>'D1-01'],
            ['code'=>'RIC-009','name'=>'Batteria 70Ah 640A','category'=>'elettrico','brand'=>'Varta','stock_quantity'=>2,'min_stock'=>1,'purchase_price'=>95,'sale_price'=>180,'location'=>'B3-01'],
            ['code'=>'RIC-010','name'=>'Ammortizzatore anteriore dx','category'=>'meccanica','brand'=>'Monroe','stock_quantity'=>1,'min_stock'=>1,'purchase_price'=>75,'sale_price'=>145,'location'=>'B2-04'],
        ] as $r) {
            SparePart::firstOrCreate(
                ['tenant_id'=>$this->tid,'code'=>$r['code']],
                array_merge($r,['tenant_id'=>$this->tid,'unit'=>'pz','active'=>true])
            );
        }
        $this->command->line('  Ricambi OK');
    }

    private function seedQuotes(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        $vehicles  = Vehicle::where('tenant_id',$this->tid)->get();
        foreach ([
            ['ci'=>0,'vi'=>0,'type'=>'carrozzeria','status'=>'inviato','amount'=>2800,'desc'=>'Riparazione paraurti posteriore e verniciatura colore originale'],
            ['ci'=>1,'vi'=>1,'type'=>'carrozzeria','status'=>'accettato','amount'=>4500,'desc'=>'Riparazione completa danni da grandine'],
            ['ci'=>2,'vi'=>2,'type'=>'meccanica','status'=>'bozza','amount'=>450,'desc'=>'Revisione impianto frenante completo'],
            ['ci'=>5,'vi'=>5,'type'=>'carrozzeria','status'=>'inviato','amount'=>1900,'desc'=>'Riparazione fiancata destra con verniciatura'],
            ['ci'=>6,'vi'=>6,'type'=>'altro','status'=>'scaduto','amount'=>750,'desc'=>'Diagnosi centralina e sostituzione alternatore'],
        ] as $q) {
            Quote::create([
                'tenant_id'=>$this->tid,
                'quote_number'=>Quote::generateNumber($this->tid),
                'customer_id'=>($customers[$q['ci']] ?? $customers->first())->id,
                'vehicle_id'=>($vehicles[$q['vi']] ?? $vehicles->first())->id,
                'job_type'=>$q['type'],
                'status'=>$q['status'],
                'subtotal'=>$q['amount'],'vat_percent'=>22,'vat_amount'=>round($q['amount']*0.22,2),'total'=>round($q['amount']*1.22,2),
                'description'=>$q['desc'],
                'valid_until'=>Carbon::now()->addDays(30),
                'created_by'=>1,
            ]);
        }
        $this->command->line('  Preventivi OK');
    }

    private function seedDocumenti(): void
    {
        $customers = Customer::where('tenant_id',$this->tid)->get();
        foreach ([
            ['ci'=>3,'type'=>'fattura','number'=>'FAT-2026-001','amount'=>380,'status'=>'pagato','date'=>'-3 days','due'=>'+27 days','desc'=>'Tagliando 60.000 km - Audi A4'],
            ['ci'=>6,'type'=>'fattura','number'=>'FAT-2026-002','amount'=>750,'status'=>'pagato','date'=>'-7 days','due'=>'+23 days','desc'=>'Sostituzione alternatore - Renault Clio'],
            ['ci'=>0,'type'=>'fattura','number'=>'FAT-2026-003','amount'=>2800,'status'=>'inviato','date'=>'-2 days','due'=>'+28 days','desc'=>'Riparazione carrozzeria - BMW 320d'],
            ['ci'=>2,'type'=>'ddt','number'=>'DDT-2026-001','amount'=>450,'status'=>'bozza','date'=>'today','due'=>'+30 days','desc'=>'Ricambi freni - Fiat 500'],
            ['ci'=>4,'type'=>'fattura','number'=>'FAT-2026-004','amount'=>520,'status'=>'inviato','date'=>'-1 days','due'=>'+29 days','desc'=>'Sostituzione pneumatici - Toyota Yaris'],
        ] as $d) {
            DB::table('documents')->insertOrIgnore([
                'tenant_id'=>$this->tid,
                'customer_id'=>($customers[$d['ci']] ?? $customers->first())->id,
                'document_type'=>$d['type'],
                'document_number'=>$d['number'],
                'subtotal'=>$d['amount'],
                'vat_percent'=>22,
                'vat_amount'=>round($d['amount']*0.22,2),
                'total'=>round($d['amount']*1.22,2),
                'payment_status'=>$d['status'],
                'issue_date'=>Carbon::parse($d['date']),
                'due_date'=>Carbon::parse($d['due']),
                'notes'=>$d['desc'],
                'created_by'=>1,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }
        $this->command->line('  Documenti OK');
    }
}