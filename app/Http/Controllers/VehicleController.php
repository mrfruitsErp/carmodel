<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vehicle, Customer};

class VehicleController extends Controller {

    public function index(Request $request) {

        $q = Vehicle::query()
            ->with(['customer','claims','workOrders']);

        if ($request->search) $q->search($request->search);
        if ($request->status) $q->where('status', $request->status);

        $veicoli = $q->orderBy('plate')->paginate(20);

        return view('veicoli.index', compact('veicoli'));
    }

    public function create() {

        $clienti = Customer::orderBy('last_name')->get();

        return view('veicoli.create', compact('clienti'));
    }

    public function store(Request $request) {

        $v = $request->validate([
            'customer_id'=>'required',
            'plate'=>'required|string|max:20',
            'vin'=>'nullable|string|max:17',
            'brand'=>'nullable|string',
            'model'=>'nullable|string',
            'year'=>'nullable|integer',
            'color'=>'nullable|string',
            'fuel_type'=>'nullable|string',
            'km_current'=>'nullable|integer',
            'insurance_company'=>'nullable|string',
            'insurance_policy'=>'nullable|string',
            'insurance_expiry'=>'nullable|date',
            'revision_expiry'=>'nullable|date',
            'notes'=>'nullable|string'
        ]);

        $vehicle = Vehicle::create($v);

        return redirect()->route('veicoli.show', ['veicoli' => $vehicle->id])
            ->with('success', 'Veicolo creato.');
    }

    // 🔥 FIX QUI
    public function show(Vehicle $veicoli) {

        $veicoli->load(['customer','claims','workOrders']);

        return view('veicoli.show', [
            'vehicle' => $veicoli
        ]);
    }

    // 🔥 FIX QUI
    public function edit(Vehicle $veicoli) {

        $clienti = Customer::all();

        return view('veicoli.create', [
            'vehicle' => $veicoli,
            'clienti' => $clienti
        ]);
    }

    // 🔥 FIX QUI
    public function update(Request $request, Vehicle $veicoli) {

        $veicoli->update($request->except(['tenant_id']));

        return redirect()->route('veicoli.show', ['veicoli' => $veicoli->id])
            ->with('success', 'Veicolo aggiornato.');
    }

    // 🔥 FIX QUI
    public function destroy(Vehicle $veicoli) {

        $veicoli->delete();

        return redirect()->route('veicoli.index')
            ->with('success','Veicolo eliminato.');
    }

    // 🔥 FIX QUI
    public function uploadFoto(Request $request, Vehicle $veicoli) {

        $request->validate([
            'foto'=>'required|file|image|max:8192',
            'phase'=>'required|string'
        ]);

        $veicoli->addMedia($request->file('foto'))
            ->toMediaCollection($request->phase.'_photos');

        return back()->with('success','Foto caricata.');
    }
}
