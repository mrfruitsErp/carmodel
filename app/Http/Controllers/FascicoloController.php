<?php

namespace App\Http\Controllers;

use App\Models\Fascicolo;
use App\Models\FascicoloToken;
use App\Models\FascicoloDocumento;
use App\Models\DocumentoCatalogo;
use App\Models\Customer;
use App\Models\User;
use App\Models\FleetVehicle;
use App\Models\SaleVehicle;
use App\Models\Claim;
use App\Models\WorkOrder;
use App\Models\Rental;
use App\Notifications\FascicoloCompletato;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FascicoloController extends Controller
{
    public function index(Request $request)
    {
        $query = Fascicolo::with(['cliente', 'tokenAttivo'])
            ->orderByDesc('created_at');

        if ($request->filled('tipo_pratica')) $query->where('tipo_pratica', $request->tipo_pratica);
        if ($request->filled('stato'))        $query->where('stato', $request->stato);
        if ($request->filled('cliente_id'))   $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('search')) {
            $query->whereHas('cliente', fn($q) =>
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('company_name', 'like', "%{$request->search}%")
                  ->orWhere('ragione_sociale', 'like', "%{$request->search}%")
            );
        }

        $fascicoli   = $query->paginate(20)->withQueryString();
        $clienti     = Customer::orderBy('first_name')->get();
        $tipiPratica = Fascicolo::tipiPratica();
        $stati       = Fascicolo::stati();

        return view('fascicoli.index', compact('fascicoli', 'clienti', 'tipiPratica', 'stati'));
    }

    public function create(Request $request)
    {
        $tid         = auth()->user()->tenant_id;
        $clienti     = Customer::orderBy('first_name')->get();
        $tipiPratica = Fascicolo::tipiPratica();
        $stati       = Fascicolo::stati();
        $clienteId   = $request->get('cliente_id');

        // Veicoli flotta (noleggio)
        $fleetVehicles = FleetVehicle::forTenant($tid)->orderBy('brand')->orderBy('model')->get();

        // Veicoli in vendita
        $saleVehicles = SaleVehicle::forTenant($tid)->where('status', 'attivo')->orderBy('brand')->orderBy('model')->get();

        // Sinistri
        $sinistri = Claim::where('tenant_id', $tid)->orderByDesc('created_at')
            ->select('id','claim_number','event_description','counterpart_plate')->get();

        // Riparazioni/Lavorazioni
        $lavorazioni = WorkOrder::where('tenant_id', $tid)->orderByDesc('created_at')
            ->with('vehicle:id,brand,model,plate')
            ->select('id','job_number','description','vehicle_id')->get();

        // Noleggi attivi
        $noleggi = Rental::where('tenant_id', $tid)->whereIn('status', ['confermato','attivo','confirmed','active'])
            ->with('vehicle:id,brand,model,plate')
            ->orderByDesc('created_at')->get();

        return view('fascicoli.create', compact(
            'clienti', 'tipiPratica', 'stati', 'clienteId',
            'fleetVehicles', 'saleVehicles', 'sinistri', 'lavorazioni', 'noleggi'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'           => 'required|exists:clienti,id',
            'tipo_pratica'         => 'required|in:' . implode(',', array_keys(Fascicolo::tipiPratica())),
            'stato'                => 'required|in:' . implode(',', array_keys(Fascicolo::stati())),
            'titolo'               => 'nullable|string|max:255',
            'note'                 => 'nullable|string',
            'operatore_id'         => 'nullable|exists:users,id',
            'data_inizio'          => 'nullable|date',
            'data_fine'            => 'nullable|date|after_or_equal:data_inizio',
            'riferimento_veicolo'  => 'nullable|string|max:255',
            'fleet_vehicle_id'     => 'nullable|integer',
            'sale_vehicle_id'      => 'nullable|integer',
            'pratica_type'         => 'nullable|string',
            'pratica_id'           => 'nullable|integer',
        ]);

        // Auto-popola riferimento_veicolo dal veicolo selezionato
        if (!empty($validated['fleet_vehicle_id'])) {
            $v = FleetVehicle::find($validated['fleet_vehicle_id']);
            if ($v && empty($validated['riferimento_veicolo'])) {
                $validated['riferimento_veicolo'] = "{$v->brand} {$v->model} - {$v->plate}";
            }
        } elseif (!empty($validated['sale_vehicle_id'])) {
            $v = SaleVehicle::find($validated['sale_vehicle_id']);
            if ($v && empty($validated['riferimento_veicolo'])) {
                $validated['riferimento_veicolo'] = "{$v->brand} {$v->model} - {$v->plate}";
            }
        }

        $fascicolo = Fascicolo::create(array_merge($validated, [
            'tenant_id' => auth()->user()->tenant_id,
        ]));

        // Popola automaticamente documenti dal catalogo
        $fascicolo->popolaDocumentiDaCatalogo();

        return redirect()->route('fascicoli.show', $fascicolo)
            ->with('success', 'Fascicolo creato. Documenti caricati dal catalogo.');
    }

    public function show(Fascicolo $fascicolo)
    {
        $fascicolo->load(['cliente', 'documenti.catalogo', 'tokenAttivo', 'operatore']);
        $tokenAttivo = $fascicolo->tokenAttivo;
        $linkPortale = $tokenAttivo
            ? route('portale.accesso', $tokenAttivo->token)
            : null;

        return view('fascicoli.show', compact('fascicolo', 'tokenAttivo', 'linkPortale'));
    }

    public function edit(Fascicolo $fascicolo)
    {
        $clienti     = Customer::orderBy('first_name')->get();
        $tipiPratica = Fascicolo::tipiPratica();
        $stati       = Fascicolo::stati();
        $operatori   = User::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('fascicoli.edit', compact('fascicolo', 'clienti', 'tipiPratica', 'stati', 'operatori'));
    }

    public function update(Request $request, Fascicolo $fascicolo)
    {
        $validated = $request->validate([
            'cliente_id'           => 'required|exists:clienti,id',
            'tipo_pratica'         => 'required|in:' . implode(',', array_keys(Fascicolo::tipiPratica())),
            'stato'                => 'required|in:' . implode(',', array_keys(Fascicolo::stati())),
            'titolo'               => 'nullable|string|max:255',
            'note'                 => 'nullable|string',
            'operatore_id'         => 'nullable|exists:users,id',
            'data_inizio'          => 'nullable|date',
            'data_fine'            => 'nullable|date',
            'riferimento_veicolo'  => 'nullable|string|max:100',
        ]);

        $fascicolo->update($validated);

        return redirect()->route('fascicoli.show', $fascicolo)
            ->with('success', 'Fascicolo aggiornato.');
    }

    public function destroy(Fascicolo $fascicolo)
    {
        $fascicolo->delete();
        return redirect()->route('fascicoli.index')->with('success', 'Fascicolo eliminato.');
    }

    // ──────────────────────────────────────────
    // Genera link portale cliente
    // ──────────────────────────────────────────
    public function generaLink(Request $request, Fascicolo $fascicolo)
    {
        $request->validate([
            'giorni_scadenza' => 'nullable|integer|min:0|max:365',
            'referente_id'    => 'nullable|exists:cliente_referenti,id',
        ]);

        // Disattiva token precedenti
        $fascicolo->token()->update(['attivo' => false]);

        $token = FascicoloToken::genera(
            $fascicolo->id,
            $fascicolo->tenant_id,
            $request->referente_id,
            $request->giorni_scadenza
        );

        $fascicolo->update(['stato' => 'link_inviato']);

        $link = route('portale.accesso', $token->token);

        return back()->with('success', "Link generato: {$link}")->with('link_portale', $link);
    }

    public function disattivaLink(Fascicolo $fascicolo)
    {
        $fascicolo->token()->update(['attivo' => false]);
        return back()->with('success', 'Link disattivato.');
    }

    // ──────────────────────────────────────────
    // Documenti
    // ──────────────────────────────────────────
    public function popolaDocumenti(Fascicolo $fascicolo)
    {
        $fascicolo->popolaDocumentiDaCatalogo();
        return back()->with('success', 'Documenti aggiornati dal catalogo.');
    }

    public function aggiungiDocumento(Request $request, Fascicolo $fascicolo)
    {
        $request->validate([
            'catalogo_id'    => 'nullable|exists:documento_catalogo,id',
            'nome'           => 'required|string|max:255',
            'obbligatorio'   => 'boolean',
            'richiede_firma' => 'boolean',
            'richiede_upload'=> 'boolean',
        ]);

        $fascicolo->documenti()->create(array_merge(
            $request->only(['catalogo_id','nome','obbligatorio','richiede_firma','richiede_upload']),
            ['tenant_id' => $fascicolo->tenant_id]
        ));

        return back()->with('success', 'Documento aggiunto.');
    }

    public function aggiornaDocumento(Request $request, Fascicolo $fascicolo, FascicoloDocumento $documento)
    {
        $request->validate([
            'nome'             => 'required|string|max:255',
            'obbligatorio'     => 'boolean',
            'note_operatore'   => 'nullable|string',
            'stato'            => 'nullable|in:richiesto,caricato,firmato,verificato,rifiutato',
        ]);

        $documento->update($request->only(['nome','obbligatorio','note_operatore','stato']));

        return back()->with('success', 'Documento aggiornato.');
    }

    public function rimuoviDocumento(Fascicolo $fascicolo, FascicoloDocumento $documento)
    {
        $documento->delete();
        return back()->with('success', 'Documento rimosso.');
    }

    public function destroyMedia(Fascicolo $fascicolo, int $mediaId)
    {
        $fascicolo->media()->findOrFail($mediaId)->delete();
        return back()->with('success', 'File eliminato.');
    }

    public function segnaCompletato(Fascicolo $fascicolo)
    {
        $fascicolo->update([
            'stato'        => 'verificato',
            'completato_il'=> now(),
        ]);
        return back()->with('success', 'Fascicolo segnato come verificato.');
    }
}
