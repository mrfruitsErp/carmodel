<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);

        try {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));

            // Prepara il contenuto immagine per Claude
            if ($mimeType === 'application/pdf') {
                $mediaContent = [
                    'type' => 'document',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => 'application/pdf',
                        'data'       => $base64,
                    ]
                ];
            } else {
                $mediaContent = [
                    'type' => 'image',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => $mimeType,
                        'data'       => $base64,
                    ]
                ];
            }

            $response = Http::withHeaders([
                'x-api-key'         => \App\Models\Setting::get('ai_api_key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => \App\Models\Setting::get('ai_model', 'claude-3-5-sonnet-20240620'),
                'max_tokens' => 1024,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            $mediaContent,
                            [
                                'type' => 'text',
                                'text' => <<<EOT
Sei un assistente per carrozzerie italiane. Analizza questo documento (libretto di circolazione italiano) ed estrai i seguenti dati in formato JSON puro senza markdown:
{
  "targa": "",
  "vin": "",
  "marca": "",
  "modello": "",
  "versione": "",
  "anno_immatricolazione": "",
  "colore": "",
  "alimentazione": "",
  "intestatario_nome": "",
  "intestatario_cf": "",
  "intestatario_indirizzo": ""
}
Se un campo non è leggibile o non presente, lascia stringa vuota. Rispondi SOLO con il JSON, nessun altro testo.
EOT
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception("Errore API Anthropic: " . $response->body());
            }

            $data = json_decode($response->json()['content'][0]['text'], true);

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la scansione: ' . $e->getMessage()
            ], 500);
        }
    }
} // Chiusura della classe