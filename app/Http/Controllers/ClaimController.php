<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Claim, Customer, Vehicle, InsuranceCompany, Expert};
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = Claim::forTenant($tenantId)
            ->with(['customer','vehicle','insuranceCompany','expert','assignedTo']);
        if ($request->search) $query->search($request->search);
        if ($request->status) $query->where('status', $request->status);
        if ($request->tipo)   $query->where('claim_type', $request->tipo);
        if ($request->filter === 'urgenti') $query->urgent();
        $sinistri = $query->orderBy('cid_expiry')->paginate(20);
        $contatori = [
            'tutti'          => Claim::forTenant($tenantId)->count(),
            'aperto'         => Claim::forTenant($tenantId)->where('status','aperto')->count(),
            'urgenti'        => Claim::forTenant($tenantId)->urgent()->count(),
            'perizia_attesa' => Claim::forTenant($tenantId)->where('status','perizia_attesa')->count(),
            'in_riparazione' => Claim::forTenant($tenantId)->where('status','in_riparazione')->count(),
            'liquidato'      => Claim::forTenant($tenantId)->where('status','liquidato')->count(),
            'chiuso'         => Claim::forTenant($tenantId)->where('status','chiuso')->count(),
            'archiviato'     => Claim::forTenant($tenantId)->where('status','archiviato')->count(),
        ];
        return view('sinistri.index', compact('sinistri', 'contatori'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        return view('sinistri.create', [
            'clienti'     => Customer::forTenant($tenantId)->orderBy('last_name')->get(),
            'compagnie'   => InsuranceCompany::forTenant($tenantId)->orderBy('name')->get(),
            'periti'      => Expert::forTenant($tenantId)->where('type','perito')->orderBy('name')->get(),
            'liquidatori' => Expert::forTenant($tenantId)->where('type','liquidatore')->orderBy('name')->get(),
            'veicoli'     => Vehicle::where('tenant_id', $tenantId)->orderBy('plate')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'vehicle_id'            => 'nullable|exists:vehicles,id',
            'insurance_company_id'  => 'nullable|exists:insurance_companies,id',
            'expert_id'             => 'nullable|exists:experts,id',
            'liquidatore_id'        => 'nullable|exists:experts,id',
            'claim_type'            => 'required|in:rca,kasko,grandine,furto,incendio,altro',
            'event_date'            => 'required|date',
            'event_location'        => 'nullable|string|max:500',
            'event_description'     => 'nullable|string',
            'counterpart_plate'     => 'nullable|string|max:20',
            'counterpart_insurance' => 'nullable|string|max:100',
            'policy_number'         => 'nullable|string|max:50',
            'estimated_amount'      => 'nullable|numeric',
            'cid_signed'            => 'boolean',
            'cid_date'              => 'nullable|date',
            'cid_expiry'            => 'nullable|date',
            'status'                => 'nullable|string',
            'notes'                 => 'nullable|string',
        ]);
        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id']    = $tenantId;
        $validated['claim_number'] = Claim::generateNumber($tenantId);
        $validated['created_by']   = auth()->id();
        $validated['status']       = $validated['status'] ?? 'aperto';
        $claim = Claim::create($validated);
        return redirect()->route('sinistri.show', $claim)
            ->with('success', "Sinistro {$claim->claim_number} creato.");
    }

    public function show(Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistri->load(['customer','vehicle','insuranceCompany','expert','assignedTo','personalInjuries','workOrders']);
        $statusHistory = DB::table('claim_status_histories')
            ->where('claim_id', $sinistri->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $claim = $sinistri;
        return view('sinistri.show', compact('claim', 'statusHistory'));
    }

    public function edit(Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        $tenantId = auth()->user()->tenant_id;
        $claim = $sinistri;
        return view('sinistri.edit', [
            'claim'       => $claim,
            'clienti'     => Customer::forTenant($tenantId)->orderBy('last_name')->get(),
            'compagnie'   => InsuranceCompany::forTenant($tenantId)->orderBy('name')->get(),
            'periti'      => Expert::forTenant($tenantId)->where('type','perito')->orderBy('name')->get(),
            'liquidatori' => Expert::forTenant($tenantId)->where('type','liquidatore')->orderBy('name')->get(),
            'veicoli'     => Vehicle::where('tenant_id', $tenantId)->orderBy('plate')->get(),
        ]);
    }

    public function update(Request $request, Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistri->update($request->except(['tenant_id','claim_number']));
        return redirect()->route('sinistri.show', $sinistri)->with('success', 'Sinistro aggiornato.');
    }

    public function destroy(Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistri->delete();
        return redirect()->route('sinistri.index')->with('success', 'Sinistro eliminato.');
    }

    public function updateStato(Request $request, Claim $claim)
    {
        $sinistri = $claim;
        $oldStatus = $sinistri->status;
        $sinistri->update(['status' => $request->status]);
        if ($oldStatus !== $request->status) {
            DB::table('claim_status_histories')->insert([
                'claim_id'   => $sinistri->id,
                'status'     => $request->status,
                'notes'      => $request->notes,
                'changed_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back()->with('success', 'Stato aggiornato.');
    }

    public function sendMail(Request $request, Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        return back()->with('success', 'Mail inviata.');
    }

    public function uploadDoc(Request $request, Claim $sinistri)
    {
        abort_if($sinistri->tenant_id !== auth()->user()->tenant_id, 403);
        if ($request->hasFile('documento')) {
            $sinistri->addMedia($request->file('documento'))->toMediaCollection('documents');
        }
        return back()->with('success', 'Documento caricato.');
    }

    public function export(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = Claim::forTenant($tenantId)
            ->with(['customer','insuranceCompany','expert']);
        if ($request->status) $query->where('status', $request->status);
        if ($request->search) $query->search($request->search);
        $claims = $query->orderBy('claim_number')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sinistri_'.date('Ymd').'.csv"',
        ];

        $callback = function() use ($claims) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, [
                'N° Sinistro','Cliente','Compagnia','Tipo',
                'Data Sinistro','Stato','Perito',
                'Importo Stimato','Importo Approvato',
                'N° Polizza','Scadenza CID'
            ], ';');
            foreach ($claims as $c) {
                fputcsv($file, [
                    $c->claim_number,
                    $c->customer?->display_name ?? '',
                    $c->insuranceCompany?->name ?? '',
                    strtoupper($c->claim_type),
                    $c->event_date?->format('d/m/Y') ?? '',
                    str_replace('_',' ',ucfirst($c->status)),
                    $c->expert?->name ?? '',
                    number_format($c->estimated_amount ?? 0, 2, ',', '.'),
                    number_format($c->approved_amount ?? 0, 2, ',', '.'),
                    $c->policy_number ?? '',
                    $c->cid_expiry?->format('d/m/Y') ?? '',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}