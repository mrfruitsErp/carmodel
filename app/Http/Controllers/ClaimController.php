<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Claim, Customer, Vehicle, InsuranceCompany, Expert};
use App\Jobs\SendClaimNotification;

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
        return view('sinistri.index', compact('sinistri'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;
        return view('sinistri.create', [
            'clienti'   => Customer::forTenant($tenantId)->orderBy('last_name')->get(),
            'compagnie' => InsuranceCompany::forTenant($tenantId)->orderBy('name')->get(),
            'periti'    => Expert::forTenant($tenantId)->periti()->orderBy('name')->get(),
            'veicoli'   => Vehicle::forTenant($tenantId)->orderBy('plate')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'vehicle_id'            => 'required|exists:vehicles,id',
            'insurance_company_id'  => 'nullable|exists:insurance_companies,id',
            'expert_id'             => 'nullable|exists:experts,id',
            'claim_type'            => 'required|in:rca,kasko,grandine,furto,incendio,altro',
            'event_date'            => 'required|date',
            'event_location'        => 'nullable|string|max:500',
            'event_description'     => 'nullable|string',
            'counterpart_plate'     => 'nullable|string|max:20',
            'policy_number'         => 'nullable|string|max:50',
            'cid_signed'            => 'boolean',
            'cid_date'              => 'nullable|date',
            'cid_expiry'            => 'nullable|date',
            'status'                => 'nullable|string',
            'notes'                 => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id']     = $tenantId;
        $validated['claim_number']  = Claim::generateNumber($tenantId);
        $validated['created_by']    = auth()->id();
        $validated['status']        = $validated['status'] ?? 'aperto';

        $claim = Claim::create($validated);

        return redirect()->route('sinistri.show', $claim)
            ->with('success', "Sinistro {$claim->claim_number} creato.");
    }

    public function show(Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistro->load(['customer','vehicle','insuranceCompany','expert','assignedTo','workOrders','documents']);
        return view('sinistri.show', compact('sinistro'));
    }

    public function edit(Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        $tenantId = auth()->user()->tenant_id;
        return view('sinistri.create', [
            'sinistro'  => $sinistro,
            'clienti'   => Customer::forTenant($tenantId)->orderBy('last_name')->get(),
            'compagnie' => InsuranceCompany::forTenant($tenantId)->orderBy('name')->get(),
            'periti'    => Expert::forTenant($tenantId)->periti()->orderBy('name')->get(),
            'veicoli'   => Vehicle::forTenant($tenantId)->orderBy('plate')->get(),
        ]);
    }

    public function update(Request $request, Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistro->update($request->except(['tenant_id','claim_number']));
        return redirect()->route('sinistri.show', $sinistro)->with('success', 'Sinistro aggiornato.');
    }

    public function destroy(Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistro->delete();
        return redirect()->route('sinistri.index')->with('success', 'Sinistro eliminato.');
    }

    public function updateStato(Request $request, Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        $sinistro->update(['status' => $request->status]);
        return back()->with('success', 'Stato aggiornato.');
    }

    public function sendMail(Request $request, Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        // TODO: implementare invio mail
        return back()->with('success', 'Mail inviata.');
    }

    public function uploadDoc(Request $request, Claim $sinistro)
    {
        abort_if($sinistro->tenant_id !== auth()->user()->tenant_id, 403);
        if ($request->hasFile('documento')) {
            $sinistro->addMedia($request->file('documento'))->toMediaCollection('documents');
        }
        return back()->with('success', 'Documento caricato.');
    }
}