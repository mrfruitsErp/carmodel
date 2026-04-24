<?php

namespace App\Http\Controllers;

use App\Models\DocumentoCatalogo;
use Illuminate\Http\Request;

class DocumentoCatalogoController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            // Usa isAdmin() che esiste in User.php
            if (!$user->isAdmin()) {
                $ok = \DB::table('setting_permissions')
                    ->where('tenant_id', $user->tenant_id)
                    ->where('user_id', $user->id)
                    ->where('gruppo', 'documenti')
                    ->where('can_edit', true)
                    ->exists();
                abort_unless($ok, 403, 'Accesso non autorizzato.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = DocumentoCatalogo::orderBy('ordine');

        if ($request->filled('sezione')) {
            $query->whereJsonContains('sezioni_collegate', $request->sezione);
        }
        if ($request->filled('tipo_soggetto')) {
            if ($request->tipo_soggetto !== 'entrambi') {
                $query->where(function ($q) use ($request) {
                    $q->where('tipo_soggetto', $request->tipo_soggetto)
                      ->orWhere('tipo_soggetto', 'entrambi');
                });
            }
        }

        $documenti = $query->paginate(30)->withQueryString();
        return view('documenti-catalogo.index', compact('documenti'));
    }

    public function create()
    {
        return view('documenti-catalogo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'                => 'required|string|max:255',
            'descrizione'         => 'nullable|string',
            'tipo_soggetto'       => 'required|in:privato,azienda,entrambi',
            'sezioni_collegate'   => 'required|array|min:1',
            'sezioni_collegate.*' => 'in:' . implode(',', array_keys(DocumentoCatalogo::sezioniDisponibili())),
            'richiede_firma'      => 'nullable|boolean',
            'richiede_upload'     => 'nullable|boolean',
            'modalita_firma'      => 'required|in:self_hosted,provider_esterno,entrambi',
            'template_testo'      => 'nullable|string',
            'obbligatorio_default'=> 'nullable|boolean',
            'ordine'              => 'nullable|integer|min:0',
            'attivo'              => 'nullable|boolean',
        ]);

        // Checkbox non spuntate non arrivano nel request — normalizziamo
        $validated['richiede_firma']       = $request->boolean('richiede_firma');
        $validated['richiede_upload']      = $request->boolean('richiede_upload');
        $validated['obbligatorio_default'] = $request->boolean('obbligatorio_default');
        $validated['attivo']               = $request->boolean('attivo', true);
        $validated['ordine']               = $request->input('ordine', 0);

        DocumentoCatalogo::create(array_merge($validated, [
            'tenant_id' => auth()->user()->tenant_id,
        ]));

        return redirect()->route('documenti-catalogo.index')
            ->with('success', 'Documento aggiunto al catalogo.');
    }

    // Laravel resource route genera: documenti-catalogo/{documento_catalogo}
    // Il parametro si chiama $documentoCatalogo (camelCase del nome snake)
    public function show(DocumentoCatalogo $documentoCatalogo)
    {
        return redirect()->route('documenti-catalogo.edit', $documentoCatalogo);
    }

    public function edit(DocumentoCatalogo $documentoCatalogo)
    {
        $documento = $documentoCatalogo;
        return view('documenti-catalogo.edit', compact('documento'));
    }

    public function update(Request $request, DocumentoCatalogo $documentoCatalogo)
    {
        $validated = $request->validate([
            'nome'                => 'required|string|max:255',
            'descrizione'         => 'nullable|string',
            'tipo_soggetto'       => 'required|in:privato,azienda,entrambi',
            'sezioni_collegate'   => 'required|array|min:1',
            'sezioni_collegate.*' => 'in:' . implode(',', array_keys(DocumentoCatalogo::sezioniDisponibili())),
            'richiede_firma'      => 'nullable|boolean',
            'richiede_upload'     => 'nullable|boolean',
            'modalita_firma'      => 'required|in:self_hosted,provider_esterno,entrambi',
            'template_testo'      => 'nullable|string',
            'obbligatorio_default'=> 'nullable|boolean',
            'ordine'              => 'nullable|integer|min:0',
            'attivo'              => 'nullable|boolean',
        ]);

        $validated['richiede_firma']       = $request->boolean('richiede_firma');
        $validated['richiede_upload']      = $request->boolean('richiede_upload');
        $validated['obbligatorio_default'] = $request->boolean('obbligatorio_default');
        $validated['attivo']               = $request->boolean('attivo');
        $validated['ordine']               = $request->input('ordine', 0);

        $documentoCatalogo->update($validated);

        return redirect()->route('documenti-catalogo.index')
            ->with('success', 'Documento aggiornato.');
    }

    public function destroy(DocumentoCatalogo $documentoCatalogo)
    {
        $documentoCatalogo->delete();
        return back()->with('success', 'Documento rimosso dal catalogo.');
    }
}
