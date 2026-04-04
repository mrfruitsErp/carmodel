<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = Customer::forTenant($tenantId)->with(['vehicles','claims']);

        if ($request->search) $query->search($request->search);
        if ($request->type)   $query->where('type', $request->type);
        if ($request->filter === 'sinistro_aperto') $query->withOpenClaims();

        $clienti = $query->orderBy('last_name')->orderBy('company_name')->paginate(20);
        return view('clienti.index', compact('clienti'));
    }

    public function create() { return view('clienti.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'         => 'required|in:private,company',
            'first_name'   => 'nullable|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'fiscal_code'  => 'nullable|string|max:20',
            'vat_number'   => 'nullable|string|max:30',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:30',
            'whatsapp'     => 'nullable|string|max:30',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:100',
            'postal_code'  => 'nullable|string|max:10',
            'province'     => 'nullable|string|max:5',
            'notes'        => 'nullable|string',
            'source'       => 'nullable|string',
        ]);
        $validated['tenant_id']  = auth()->user()->tenant_id;
        $validated['created_by'] = auth()->id();
        $customer = Customer::create($validated);
        return redirect()->route('clienti.show', $customer)->with('success', 'Cliente creato.');
    }

    public function show(Customer $customer)
    {
        abort_if($customer->tenant_id !== auth()->user()->tenant_id, 403);
        $customer->load(['vehicles.claims','claims.insuranceCompany','claims.expert','personalInjuries','workOrders','rentals','documents']);
        return view('clienti.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        abort_if($customer->tenant_id !== auth()->user()->tenant_id, 403);
        return view('clienti.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if($customer->tenant_id !== auth()->user()->tenant_id, 403);
        $customer->update($request->except(['tenant_id','created_by']));
        return redirect()->route('clienti.show', $customer)->with('success', 'Cliente aggiornato.');
    }

    public function storico(Customer $customer)
    {
        abort_if($customer->tenant_id !== auth()->user()->tenant_id, 403);
        return view('clienti.storico', compact('customer'));
    }
}
