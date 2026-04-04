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
            'estimated_amount'      => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $validated['tenant_id']    = $tenantId;
        $validated['claim_number'] = Claim::generateNumber($tenantId);
        $validated['created_by']   = auth()->id();

        $claim = Claim::create($validated);

        // Job: invia mail automatica apertura sinistro
        SendClaimNotification::dispatch($claim, 'claim_opened');

        return redirect()->route('sinistri.show', $claim)->with('success', "Sinistro {$claim->claim_number} aperto con successo.");
    }

    public function show(Claim $claim)
    {
        $this->authorizeTenant($claim);
        $claim->load(['customer','vehicle','insuranceCompany','expert','statusHistory.changedBy','personalInjuries','workOrders','rentals']);
        return view('sinistri.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $this->authorizeTenant($claim);
        $tenantId = auth()->user()->tenant_id;
        return view('sinistri.edit', [
            'claim'     => $claim,
            'compagnie' => InsuranceCompany::forTenant($tenantId)->get(),
            'periti'    => Expert::forTenant($tenantId)->periti()->get(),
        ]);
    }

    public function update(Request $request, Claim $claim)
    {
        $this->authorizeTenant($claim);
        $validated = $request->validate([
            'status'            => 'required',
            'estimated_amount'  => 'nullable|numeric',
            'approved_amount'   => 'nullable|numeric',
            'survey_date'       => 'nullable|date',
            'cid_expiry'        => 'nullable|date',
            'notes'             => 'nullable|string',
            'internal_notes'    => 'nullable|string',
        ]);
        $claim->update($validated);
        return redirect()->route('sinistri.show', $claim)->with('success', 'Sinistro aggiornato.');
    }

    public function updateStato(Request $request, Claim $claim)
    {
        $this->authorizeTenant($claim);
        $request->validate(['status' => 'required|string', 'notes' => 'nullable|string']);
        $claim->updateStatus($request->status, $request->notes);
        return back()->with('success', 'Stato aggiornato.');
    }

    public function sendMail(Request $request, Claim $claim)
    {
        $this->authorizeTenant($claim);
        $request->validate(['trigger_event' => 'required|string']);
        SendClaimNotification::dispatch($claim, $request->trigger_event);
        return back()->with('success', 'Mail in coda di invio.');
    }

    public function uploadDoc(Request $request, Claim $claim)
    {
        $this->authorizeTenant($claim);
        $request->validate(['documento' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png']);
        $claim->addMedia($request->file('documento'))->toMediaCollection('claim_documents');
        return back()->with('success', 'Documento allegato.');
    }

    private function authorizeTenant(Claim $claim): void
    {
        abort_if($claim->tenant_id !== auth()->user()->tenant_id, 403);
    }
}
