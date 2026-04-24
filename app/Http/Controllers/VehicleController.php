<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Vehicle, Customer, VehicleDocument};

class VehicleController extends Controller {

    public function index(Request $request) {
        $q = Vehicle::query()->with(['customer','claims','workOrders']);
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
            'customer_id'      => 'required',
            'plate'            => 'required|string|max:20',
            'vin'              => 'nullable|string|max:17',
            'brand'            => 'nullable|string',
            'model'            => 'nullable|string',
            'year'             => 'nullable|integer',
            'color'            => 'nullable|string',
            'fuel_type'        => 'nullable|string',
            'km_current'       => 'nullable|integer',
            'insurance_company'=> 'nullable|string',
            'insurance_policy' => 'nullable|string',
            'insurance_expiry' => 'nullable|date',
            'revision_expiry'  => 'nullable|date',
            'notes'            => 'nullable|string'
        ]);
        $vehicle = Vehicle::create($v);
        return redirect()->route('veicoli.show', ['veicoli' => $vehicle->id])->with('success', 'Veicolo creato.');
    }

    public function show(Vehicle $veicoli) {
        $veicoli->load(['customer','claims','workOrders','vehicleDocuments.media']);
        return view('veicoli.show', ['vehicle' => $veicoli]);
    }

    public function edit(Vehicle $veicoli) {
        $clienti = Customer::all();
        return view('veicoli.create', ['vehicle' => $veicoli, 'clienti' => $clienti]);
    }

    public function update(Request $request, Vehicle $veicoli) {
        $veicoli->update($request->except(['tenant_id']));
        return redirect()->route('veicoli.show', ['veicoli' => $veicoli->id])->with('success', 'Veicolo aggiornato.');
    }

    public function destroy(Vehicle $veicoli) {
        $veicoli->delete();
        return redirect()->route('veicoli.index')->with('success', 'Veicolo eliminato.');
    }

    public function uploadFoto(Request $request, Vehicle $veicoli) {
        $request->validate(['foto' => 'required|file|image|max:8192', 'phase' => 'required|string']);
        $veicoli->addMedia($request->file('foto'))->toMediaCollection($request->phase.'_photos');
        return back()->with('success', 'Foto caricata.');
    }

    public function uploadDocumento(Request $request, Vehicle $veicoli) {
        $request->validate([
            'file'           => 'required|file|max:20480|mimes:pdf,jpg,jpeg,png,doc,docx',
            'tipo'           => 'required|in:'.implode(',', array_keys(VehicleDocument::tipi())),
            'nome'           => 'nullable|string|max:255',
            'data_emissione' => 'nullable|date',
            'data_scadenza'  => 'nullable|date',
            'note'           => 'nullable|string',
        ]);

        // Se tipo singleFile (libretto, polizza, revisione, bollo) → disattiva i precedenti
        $tipiSingle = ['libretto','polizza','revisione','bollo'];
        if (in_array($request->tipo, $tipiSingle)) {
            $veicoli->vehicleDocuments()
                ->where('tipo', $request->tipo)
                ->where('attivo', true)
                ->update(['attivo' => false]);
        }

        $doc = VehicleDocument::create([
            'tenant_id'      => auth()->user()->tenant_id,
            'vehicle_id'     => $veicoli->id,
            'uploaded_by'    => auth()->id(),
            'tipo'           => $request->tipo,
            'nome'           => $request->nome ?? VehicleDocument::tipi()[$request->tipo],
            'data_emissione' => $request->data_emissione,
            'data_scadenza'  => $request->data_scadenza,
            'note'           => $request->note,
            'attivo'         => true,
        ]);

        $doc->addMedia($request->file('file'))->toMediaCollection('file');

        // Aggiorna scadenze sul veicolo
        if ($request->tipo === 'polizza' && $request->data_scadenza) {
            $veicoli->update(['insurance_expiry' => $request->data_scadenza]);
        }
        if ($request->tipo === 'revisione' && $request->data_scadenza) {
            $veicoli->update(['revision_expiry' => $request->data_scadenza]);
        }

        return back()->with('success', 'Documento caricato.');
    }

    public function deleteDocumento(Request $request, Vehicle $veicoli, int $docId) {
        $doc = VehicleDocument::where('vehicle_id', $veicoli->id)->findOrFail($docId);
        $doc->clearMediaCollection('file');
        $doc->delete();
        return back()->with('success', 'Documento eliminato.');
    }

    public function scanLibretto(Request $request, Vehicle $veicoli) {
        // Implementazione AI scan — da completare con API Claude
        return back()->with('warning', 'Funzione scan in sviluppo.');
    }
}