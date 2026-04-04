<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{Tenant, User, Customer, Vehicle, InsuranceCompany, Expert, Claim, FleetVehicle, MailTemplate};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // TENANT DEMO
        $tenant = Tenant::create([
            'name'       => 'Officina Demo Srl',
            'slug'       => 'officina-demo',
            'email'      => 'info@officinademo.it',
            'phone'      => '+39 06 1234567',
            'address'    => 'Via Roma 1, 00100 Roma',
            'vat_number' => 'IT 12345678901',
            'plan'       => 'professional',
        ]);

        // UTENTI
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Admin Principale',
            'email'     => 'admin@demo.it',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'phone'     => '+39 333 1234567',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Marco Rossi',
            'email'     => 'marco@demo.it',
            'password'  => Hash::make('password'),
            'role'      => 'mechanic',
        ]);

        // COMPAGNIE ASSICURATIVE
        $generali = InsuranceCompany::create(['tenant_id'=>$tenant->id,'name'=>'Generali SpA','code'=>'GEN','email'=>'sinistri@generali.it','phone'=>'+39 02 12345678']);
        $unipol   = InsuranceCompany::create(['tenant_id'=>$tenant->id,'name'=>'Unipol Assicurazioni','code'=>'UNI','email'=>'sinistri@unipol.it']);
        $axa      = InsuranceCompany::create(['tenant_id'=>$tenant->id,'name'=>'AXA Italia','code'=>'AXA','email'=>'sinistri@axa.it']);

        // PERITI & AVVOCATI
        $perito = Expert::create([
            'tenant_id'=>$tenant->id,'type'=>'perito','name'=>'Ing. Roberto Belli',
            'insurance_company_id'=>$generali->id,'email'=>'r.belli@generali.it',
            'phone'=>'+39 335 456 7890','rating'=>4
        ]);
        Expert::create([
            'tenant_id'=>$tenant->id,'type'=>'avvocato','name'=>'Avv. Mario Conti',
            'company_name'=>'Studio Legale Conti','email'=>'m.conti@studioconti.it',
            'phone'=>'+39 06 123 4567','rating'=>5
        ]);

        // CLIENTI
        $cliente1 = Customer::create([
            'tenant_id'=>$tenant->id,'type'=>'private',
            'first_name'=>'Marco','last_name'=>'Ferrari',
            'fiscal_code'=>'FRRMRC80A01H501Z',
            'email'=>'marco@email.it','phone'=>'+39 329 123 4567',
            'address'=>'Via Roma 12','city'=>'Roma','postal_code'=>'00100','province'=>'RM',
            'created_by'=>$admin->id,
        ]);
        $cliente2 = Customer::create([
            'tenant_id'=>$tenant->id,'type'=>'company',
            'company_name'=>'Verdi Srl','vat_number'=>'IT07890123456',
            'email'=>'admin@verdisrl.it','phone'=>'+39 02 345 6789',
            'city'=>'Milano','province'=>'MI','created_by'=>$admin->id,
        ]);
        $cliente3 = Customer::create([
            'tenant_id'=>$tenant->id,'type'=>'private',
            'first_name'=>'Anna','last_name'=>'Bianchi',
            'email'=>'anna.b@gmail.com','phone'=>'+39 347 987 6543',
            'city'=>'Roma','province'=>'RM','created_by'=>$admin->id,
        ]);

        // VEICOLI
        $v1 = Vehicle::create([
            'tenant_id'=>$tenant->id,'customer_id'=>$cliente1->id,
            'plate'=>'AB123CD','vin'=>'WBA5E7C59ED123456',
            'brand'=>'BMW','model'=>'320d','year'=>2020,'fuel_type'=>'diesel',
            'km_current'=>87400,'insurance_company'=>'Generali','insurance_policy'=>'GEN-2023-4521789',
            'insurance_expiry'=>'2025-12-31','revision_expiry'=>'2026-06-30','status'=>'in_officina',
        ]);
        $v2 = Vehicle::create([
            'tenant_id'=>$tenant->id,'customer_id'=>$cliente2->id,
            'plate'=>'QR345ST','vin'=>'WAUZZZ8K2AA456789',
            'brand'=>'Audi','model'=>'A4','year'=>2018,'fuel_type'=>'diesel',
            'km_current'=>142000,'status'=>'in_officina',
        ]);
        $v3 = Vehicle::create([
            'tenant_id'=>$tenant->id,'customer_id'=>$cliente3->id,
            'plate'=>'MN789PQ','brand'=>'VW','model'=>'Golf','year'=>2021,'fuel_type'=>'benzina',
            'km_current'=>31000,'status'=>'pronto',
        ]);

        // FLOTTA
        FleetVehicle::create(['tenant_id'=>$tenant->id,'plate'=>'IJ789KL','brand'=>'Fiat','model'=>'Panda','year'=>2021,'category'=>'A','km_current'=>38200,'daily_rate'=>0,'status'=>'sostitutiva']);
        FleetVehicle::create(['tenant_id'=>$tenant->id,'plate'=>'ST111UV','brand'=>'Toyota','model'=>'Yaris','year'=>2020,'category'=>'B','km_current'=>52100,'daily_rate'=>45,'status'=>'noleggiato']);
        FleetVehicle::create(['tenant_id'=>$tenant->id,'plate'=>'WX222YZ','brand'=>'Hyundai','model'=>'i20','year'=>2022,'category'=>'B','km_current'=>21400,'daily_rate'=>40,'status'=>'disponibile']);

        // SINISTRI
        $claim1 = Claim::create([
            'tenant_id'=>$tenant->id,
            'claim_number'=>'SIN-2025-041',
            'customer_id'=>$cliente1->id,'vehicle_id'=>$v1->id,
            'insurance_company_id'=>$generali->id,'expert_id'=>$perito->id,
            'claim_type'=>'rca','event_date'=>'2025-03-28',
            'event_location'=>'Via Aurelia 45, Roma',
            'event_description'=>'Tamponamento in retromarcia. Cliente non responsabile.',
            'counterpart_plate'=>'XY987AB','counterpart_insurance'=>'Generali',
            'policy_number'=>'GEN-2023-4521789','cid_signed'=>true,
            'cid_date'=>'2025-03-28','cid_expiry'=>'2025-04-05',
            'status'=>'perizia_attesa','estimated_amount'=>4200.00,
            'survey_date'=>'2025-04-07','assigned_to'=>$admin->id,'created_by'=>$admin->id,
        ]);
        Claim::create([
            'tenant_id'=>$tenant->id,'claim_number'=>'SIN-2025-040',
            'customer_id'=>$cliente2->id,'vehicle_id'=>$v2->id,
            'insurance_company_id'=>$unipol->id,'claim_type'=>'kasko',
            'event_date'=>'2025-03-22','cid_expiry'=>'2025-04-01',
            'status'=>'in_riparazione','estimated_amount'=>8700.00,
            'assigned_to'=>$admin->id,'created_by'=>$admin->id,
        ]);

        // MAIL TEMPLATE
        MailTemplate::create([
            'tenant_id'=>$tenant->id,'name'=>'Apertura sinistro — cliente',
            'trigger_event'=>'claim_opened',
            'subject'=>'Sinistro {{sinistro}} aperto con successo — {{veicolo}}',
            'body_html'=>'<p>Gentile {{cliente}},</p><p>Confermiamo l\'apertura del sinistro <strong>{{sinistro}}</strong> per il veicolo {{veicolo}} ({{targa}}) con compagnia {{compagnia}}.</p><p>Vi terremo aggiornati sullo stato della pratica.</p><p>Cordiali saluti,<br>Il team CarModel ERP</p>',
            'active'=>true,
        ]);
        MailTemplate::create([
            'tenant_id'=>$tenant->id,'name'=>'Scadenza CID — 48h prima',
            'trigger_event'=>'cid_expiry_48h',
            'subject'=>'⚠ Urgente: scadenza CID sinistro {{sinistro}} il {{scadenza_cid}}',
            'body_html'=>'<p>Gentile {{cliente}},</p><p>Il termine per la presentazione del CID relativo al sinistro <strong>{{sinistro}}</strong> scade il <strong>{{scadenza_cid}}</strong>.</p><p>La preghiamo di contattarci al più presto.</p>',
            'active'=>true,
        ]);
        MailTemplate::create([
            'tenant_id'=>$tenant->id,'name'=>'Veicolo pronto',
            'trigger_event'=>'vehicle_ready',
            'subject'=>'Il suo veicolo {{targa}} è pronto per il ritiro',
            'body_html'=>'<p>Gentile {{cliente}},</p><p>Il suo veicolo <strong>{{veicolo}}</strong> ({{targa}}) è pronto per il ritiro.</p><p>Siamo aperti dal lunedì al venerdì 8:00-18:00.</p>',
            'active'=>true,
        ]);
        MailTemplate::create([
            'tenant_id'=>$tenant->id,'name'=>'Scadenza auto sostitutiva',
            'trigger_event'=>'rental_expiry_24h',
            'subject'=>'Restituzione auto sostitutiva — domani',
            'body_html'=>'<p>Gentile {{cliente}},</p><p>Le ricordiamo che domani scade il contratto per l\'auto sostitutiva assegnatale. La preghiamo di provvedere alla restituzione.</p>',
            'active'=>true,
        ]);

        $this->command->info('✅ Seed completato! Login: admin@demo.it / password');
    }
}
