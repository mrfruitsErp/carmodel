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
        return $this->_parseLibrettoToJson($request->file('file'));
    }

    /**
     * Endpoint usato dalla pagina di creazione veicolo:
     * scansiona il libretto SENZA veicolo esistente, restituisce JSON
     * per pre-compilare il form lato client.
     */
    public function scanLibrettoNuovo(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf',
        ]);
        return $this->_parseLibrettoToJson($request->file('file'));
    }

    /**
     * Applica i dati estratti dal libretto a un veicolo esistente.
     * Riceve in POST i campi già confermati dall'utente.
     */
    public function applicaLibretto(Request $request, Vehicle $veicoli) {
        $data = $request->validate([
            'plate'  => 'nullable|string|max:20',
            'vin'    => 'nullable|string|max:17',
            'brand'  => 'nullable|string|max:100',
            'model'  => 'nullable|string|max:100',
            'version'=> 'nullable|string|max:100',
            'year'   => 'nullable|integer|min:1900|max:2099',
            'color'  => 'nullable|string|max:50',
            'fuel_type' => 'nullable|string|max:30',
        ]);

        // Aggiorna solo i campi non vuoti, normalizza targa/vin in maiuscolo
        $update = [];
        foreach ($data as $k => $v) {
            if ($v === null || $v === '') continue;
            if (in_array($k, ['plate','vin'])) $v = strtoupper(trim($v));
            $update[$k] = $v;
        }
        if (!empty($update)) $veicoli->update($update);

        return back()->with('success', 'Dati libretto applicati al veicolo.');
    }

    /**
     * Prompt unico per estrazione dati libretto (usato da tutti i provider).
     */
    private function _libreittoPrompt(): string {
        return <<<EOT
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
EOT;
    }

    /**
     * Rileva il provider dalla forma della chiave API.
     * - sk-ant-* → Anthropic (Claude)
     * - AIza*    → Google (Gemini)
     */
    private function _detectProvider(string $apiKey): string {
        $k = trim($apiKey);
        if (str_starts_with($k, 'sk-ant-')) return 'anthropic';
        if (str_starts_with($k, 'AIza'))    return 'google';
        // fallback su quanto eventualmente salvato in Settings, altrimenti Anthropic
        $saved = strtolower(trim(\App\Models\Setting::get('ai_provider', 'anthropic')));
        return in_array($saved, ['google','gemini']) ? 'google' : 'anthropic';
    }

    /**
     * Restituisce un nome modello valido per il provider rilevato.
     * Se l'utente ha specificato in Settings un modello coerente col provider, lo usa.
     * Altrimenti applica il default del provider.
     */
    private function _resolveModel(string $provider): string {
        $userModel = trim((string) \App\Models\Setting::get('ai_model', ''));

        $defaults = [
            'anthropic' => 'claude-3-5-sonnet-20240620',
            'google'    => 'gemini-2.0-flash',
        ];

        if ($userModel === '') return $defaults[$provider];

        $low = strtolower($userModel);
        if ($provider === 'google') {
            // valido solo se contiene "gemini-" e ha almeno una versione (es. gemini-2.0-flash)
            if (preg_match('/^gemini-\d/', $low)) return $userModel;
            return $defaults['google'];
        }
        if ($provider === 'anthropic') {
            // valido solo se è un nome Claude completo
            if (str_starts_with($low, 'claude-')) return $userModel;
            return $defaults['anthropic'];
        }
        return $defaults[$provider];
    }

    /**
     * Logica condivisa: dispatcher per provider (anthropic / google).
     */
    private function _parseLibrettoToJson($file) {
        try {
            $apiKey = \App\Models\Setting::get('ai_api_key');

            if (empty($apiKey)) {
                throw new \Exception('API key non configurata. Vai in Impostazioni → AI e inseriscila.');
            }

            $provider = $this->_detectProvider($apiKey);
            $model    = $this->_resolveModel($provider);

            $mimeType = $file->getMimeType();
            $base64   = base64_encode(file_get_contents($file->getRealPath()));

            if ($provider === 'google') {
                $data = $this->_callGemini($apiKey, $base64, $mimeType, $model);
            } else {
                $data = $this->_callAnthropic($apiKey, $base64, $mimeType, $model);
            }

            return response()->json([
                'success'  => true,
                'data'     => $data,
                'provider' => $provider,
                'model'    => $model,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la scansione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chiamata Anthropic Messages API (Claude).
     */
    private function _callAnthropic(string $apiKey, string $base64, string $mimeType, string $model = 'claude-3-5-sonnet-20240620'): array {
        if ($mimeType === 'application/pdf') {
            $mediaContent = [
                'type' => 'document',
                'source' => ['type' => 'base64', 'media_type' => 'application/pdf', 'data' => $base64]
            ];
        } else {
            $mediaContent = [
                'type' => 'image',
                'source' => ['type' => 'base64', 'media_type' => $mimeType, 'data' => $base64]
            ];
        }

        $response = Http::timeout(60)->withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => $model,
            'max_tokens' => 1024,
            'messages'   => [[
                'role'    => 'user',
                'content' => [
                    $mediaContent,
                    ['type' => 'text', 'text' => $this->_libreittoPrompt()]
                ]
            ]]
        ]);

        if ($response->failed()) {
            $body = $response->json();
            $msg  = $body['error']['message'] ?? $response->body();
            $type = $body['error']['type']    ?? 'api_error';

            if (str_contains($msg, 'credit balance is too low')) {
                throw new \Exception('Saldo crediti Anthropic esaurito. Ricarica su console.anthropic.com → Plans & Billing.');
            }
            if (str_contains($msg, 'invalid x-api-key') || str_contains($msg, 'authentication_error')) {
                throw new \Exception('API key Anthropic non valida. Controlla in Impostazioni → AI.');
            }
            if ($response->status() === 429) {
                throw new \Exception('Troppe richieste in poco tempo. Riprova fra qualche secondo.');
            }
            throw new \Exception("API Anthropic ({$type}): {$msg}");
        }

        $raw  = $response->json()['content'][0]['text'] ?? '{}';
        $raw  = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($raw));
        return json_decode($raw, true) ?: [];
    }

    /**
     * Chiamata Google Gemini API (gratuita con free tier).
     * Doc: https://ai.google.dev/api/generate-content
     */
    private function _callGemini(string $apiKey, string $base64, string $mimeType, string $model = 'gemini-2.0-flash'): array {
        // Sicurezza: se il nome modello non sembra valido, usa default
        if (!preg_match('/^gemini-\d/', strtolower($model))) {
            $model = 'gemini-2.0-flash';
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

        $response = Http::timeout(60)->withHeaders([
            'content-type' => 'application/json',
        ])->post($url, [
            'contents' => [[
                'parts' => [
                    ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64]],
                    ['text' => $this->_libreittoPrompt()],
                ]
            ]],
            'generationConfig' => [
                'temperature'      => 0.1,
                'maxOutputTokens'  => 1024,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if ($response->failed()) {
            $body = $response->json();
            $msg  = $body['error']['message'] ?? $response->body();
            $code = $body['error']['code']    ?? $response->status();

            if (in_array($code, [400, 401, 403]) && (stripos($msg,'API key')!==false || stripos($msg,'API_KEY')!==false)) {
                throw new \Exception('API key Gemini non valida. Genera una nuova key su aistudio.google.com/apikey e salvala in Impostazioni → AI.');
            }
            if ($code == 429) {
                throw new \Exception('Limite richieste Gemini superato (free tier: 15 req/min). Riprova fra un minuto.');
            }
            throw new \Exception("API Gemini ({$code}): {$msg}");
        }

        $raw = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $raw = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($raw));
        return json_decode($raw, true) ?: [];
    }
} // Chiusura della classe