<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Claim, WorkOrder, Rental, Document, Customer, FleetVehicle};

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $today = now();

        // KPI principali
        $kpi = [
            'sinistri_aperti'    => Claim::forTenant($tenantId)->open()->count(),
            'sinistri_urgenti'   => Claim::forTenant($tenantId)->urgent()->count(),
            'lavorazioni_attive' => WorkOrder::forTenant($tenantId)->active()->count(),
            'lavorazioni_ritardo'=> WorkOrder::forTenant($tenantId)->overdue()->count(),
            'fatturato_mese'     => Document::forTenant($tenantId)->where('payment_status','pagata')->whereMonth('payment_date', $today->month)->sum('total'),
            'auto_noleggiate'    => FleetVehicle::forTenant($tenantId)->where('status','noleggiato')->count() + FleetVehicle::forTenant($tenantId)->where('status','sostitutiva')->count(),
            'auto_disponibili'   => FleetVehicle::forTenant($tenantId)->available()->count(),
            'lesioni_aperte'     => \App\Models\PersonalInjury::forTenant($tenantId)->whereNotIn('status',['chiusa','liquidata'])->count(),
        ];

        // Sinistri urgenti (scadenza CID entro 7gg)
        $sinistri_urgenti = Claim::forTenant($tenantId)
            ->with(['customer','vehicle','insuranceCompany'])
            ->open()->urgent()
            ->orderBy('cid_expiry')
            ->limit(5)->get();

        // Lavorazioni attive
        $lavorazioni = WorkOrder::forTenant($tenantId)
            ->with(['customer','vehicle'])
            ->active()
            ->orderByRaw("FIELD(status,'in_lavorazione','attesa','attesa_ricambi')")
            ->orderBy('expected_end_date')
            ->limit(5)->get();

        // Sostitutive in scadenza
        $sostitutive = Rental::forTenant($tenantId)
            ->with(['customer','fleetVehicle'])
            ->active()
            ->orderBy('expected_end_date')
            ->limit(4)->get();

        // Attività recenti
        $attivita = \Spatie\Activitylog\Models\Activity::where('properties->tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->limit(6)->get();

        // Fatturato per tipo lavorazione (ultimi 30gg)
        $fatturato_tipo = WorkOrder::forTenant($tenantId)
            ->whereIn('status',['completato','consegnato'])
            ->where('actual_end_date','>=', $today->subDays(30))
            ->selectRaw('job_type, SUM(actual_amount) as totale')
            ->groupBy('job_type')
            ->pluck('totale','job_type');

        return view('dashboard', compact(
            'kpi','sinistri_urgenti','lavorazioni','sostitutive','attivita','fatturato_tipo'
        ));
    }
}
