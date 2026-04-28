<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FleetVehicle;

class FleetVehicleController extends Controller
{
    public function index()
    {
        $tid = auth()->user()->tenant_id;
        $flotta = FleetVehicle::forTenant($tid)->orderBy('plate')->paginate(20);
        $disponibili = FleetVehicle::forTenant($tid)->where('status','disponibile')->count();
        $occupate = FleetVehicle::forTenant($tid)->whereIn('status',['noleggiato','sostitutiva'])->count();
        $manutenzione = FleetVehicle::forTenant($tid)->where('status','manutenzione')->count();
        return view('flotta.index', compact('flotta','disponibili','occupate','manutenzione'));
    }

    public function create()
    {
        return view('flotta.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'plate'            => 'required|string|max:20',
            'vin'              => 'nullable|string|max:17',
            'brand'            => 'nullable|string',
            'model'            => 'nullable|string',
            'year'             => 'nullable|integer',
            'color'            => 'nullable|string',
            'fuel_type'        => 'nullable|string',
            'category'         => 'nullable|string',
            'seats'            => 'nullable|integer',
            'km_current'       => 'nullable|integer',
            'daily_rate'       => 'nullable|numeric',
            'revision_expiry'  => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'insurance_company'=> 'nullable|string',
            'insurance_policy' => 'nullable|string',
            'status'           => 'nullable|string',
            'purchase_price'   => 'nullable|numeric',
            'purchase_date'    => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);
        $v['tenant_id'] = auth()->user()->tenant_id;
        $vehicle = FleetVehicle::create($v);
        return redirect()->route('flotta.show', $vehicle)->with('success', 'Veicolo aggiunto alla flotta.');
    }

    public function show(FleetVehicle $flotta)
    {
        $flotta->load(['rentals.customer']);
        return view('flotta.show', ['vehicle' => $flotta]);
    }

    public function edit(FleetVehicle $flotta)
    {
        return view('flotta.create', ['vehicle' => $flotta]);
    }

    public function update(Request $request, FleetVehicle $flotta)
    {
        $flotta->update($request->except(['tenant_id']));
        return redirect()->route('flotta.show', $flotta)->with('success', 'Veicolo aggiornato.');
    }

    public function destroy(FleetVehicle $flotta)
    {
        $flotta->delete();
        return redirect()->route('flotta.index')->with('success', 'Veicolo rimosso dalla flotta.');
    }
}
