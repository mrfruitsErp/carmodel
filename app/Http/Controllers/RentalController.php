<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Rental, FleetVehicle, Customer, Claim};

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Rental::forTenant($tid)->with(['customer','fleetVehicle','claim']);
        if ($request->tipo)   $q->where('rental_type', $request->tipo);
        if ($request->status) $q->where('status', $request->status);
        $noleggi = $q->orderByDesc('created_at')->paginate(20);
        return view('noleggio.index', compact('noleggi'));
    }

    public function create(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        return view('noleggio.create', [
            'clienti' => Customer::forTenant($tid)->orderBy('last_name')->get(),
            'flotta'  => FleetVehicle::forTenant($tid)->orderBy('plate')->get(),
            'sinistri'=> Claim::forTenant($tid)->open()->with('customer')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'fleet_vehicle_id'   => 'required|exists:fleet_vehicles,id',
            'customer_id'        => 'required|exists:customers,id',
            'claim_id'           => 'nullable|exists:claims,id',
            'rental_type'        => 'required',
            'start_date'         => 'required|date',
            'expected_end_date'  => 'required|date|after_or_equal:start_date',
            'daily_rate'         => 'nullable|numeric',
            'km_start'           => 'nullable|integer',
            'km_included'        => 'nullable|integer',
            'km_extra_price'     => 'nullable|numeric',
            'fuel_level_start'   => 'nullable|integer',
            'damage_notes_start' => 'nullable|string',
            'notes'              => 'nullable|string',
        ]);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']    = $tid;
        $v['rental_number']= Rental::generateNumber($tid);
        $v['status']       = 'attivo';
        $v['created_by']   = auth()->id();
        $rental = Rental::create($v);
        // Aggiorna stato veicolo
        FleetVehicle::find($v['fleet_vehicle_id'])->update(['status' => $v['rental_type'] === 'sostitutiva' ? 'sostitutiva' : 'noleggiato']);
        return redirect()->route('noleggio.show', $rental)->with('success', "Contratto {$rental->rental_number} creato.");
    }

    public function show(Rental $noleggio)
    {
        $noleggio->load(['customer','fleetVehicle','claim','createdBy']);
        return view('noleggio.show', compact('noleggio'));
    }

    public function edit(Rental $noleggio)
    {
        $tid = auth()->user()->tenant_id;
        return view('noleggio.create', [
            'rental'  => $noleggio,
            'clienti' => Customer::forTenant($tid)->get(),
            'flotta'  => FleetVehicle::forTenant($tid)->get(),
            'sinistri'=> Claim::forTenant($tid)->open()->with('customer')->get(),
        ]);
    }

    public function update(Request $request, Rental $noleggio)
    {
        $noleggio->update($request->except(['tenant_id','rental_number']));
        return redirect()->route('noleggio.show', $noleggio)->with('success', 'Contratto aggiornato.');
    }

    public function destroy(Rental $noleggio)
    {
        $noleggio->delete();
        return redirect()->route('noleggio.index')->with('success', 'Contratto eliminato.');
    }

    public function chiudi(Request $request, Rental $noleggio)
    {
        $noleggio->update([
            'status'          => 'chiuso',
            'actual_end_date' => now(),
            'km_end'          => $request->km_end,
            'fuel_level_end'  => $request->fuel_level_end,
            'damage_notes_end'=> $request->damage_notes_end,
        ]);
        $noleggio->fleetVehicle->update(['status' => 'disponibile']);
        return back()->with('success', 'Contratto chiuso. Veicolo ora disponibile.');
    }
}
