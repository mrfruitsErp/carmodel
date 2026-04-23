<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsuranceCompany;
use App\Models\Expert;

class InsuranceCompanyController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $q = InsuranceCompany::where('tenant_id', $this->tid());
        if ($request->search) {
            $q->where(fn($s) => $s
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
            );
        }
        $compagnie = $q->orderBy('name')->paginate(25);
        $totale = InsuranceCompany::where('tenant_id', $this->tid())->count();
        return view('assicurazioni.index', compact('compagnie', 'totale'));
    }

    public function create()
    {
        return view('assicurazioni.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50',
            'codice_fiscale'   => 'nullable|string|max:20',
            'piva'             => 'nullable|string|max:30',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:30',
            'fax'              => 'nullable|string|max:30',
            'pec'              => 'nullable|email',
            'codice_sdi'       => 'nullable|string|max:10',
            'address'          => 'nullable|string',
            'portal_url'       => 'nullable|url',
            'referente'        => 'nullable|string|max:100',
            'referente_email'  => 'nullable|email',
            'referente_phone'  => 'nullable|string|max:30',
            'notes'            => 'nullable|string',
            'active'           => 'boolean',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['active'] = $request->boolean('active', true);
        $company = InsuranceCompany::create($data);
        return redirect()->route('assicurazioni.show', $company)->with('success', 'Compagnia aggiunta.');
    }

    public function show(InsuranceCompany $assicurazioni)
    {
        abort_if($assicurazioni->tenant_id !== $this->tid(), 403);
        $periti = Expert::where('tenant_id', $this->tid())
            ->where('insurance_company_id', $assicurazioni->id)
            ->orderBy('type')->orderBy('name')->get();
        $sinistri = $assicurazioni->claims()->with('customer','vehicle')
            ->orderByDesc('created_at')->limit(10)->get();
        return view('assicurazioni.show', compact('assicurazioni', 'periti', 'sinistri'));
    }

    public function edit(InsuranceCompany $assicurazioni)
    {
        abort_if($assicurazioni->tenant_id !== $this->tid(), 403);
        return view('assicurazioni.create', ['company' => $assicurazioni]);
    }

    public function update(Request $request, InsuranceCompany $assicurazioni)
    {
        abort_if($assicurazioni->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50',
            'codice_fiscale'   => 'nullable|string|max:20',
            'piva'             => 'nullable|string|max:30',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:30',
            'fax'              => 'nullable|string|max:30',
            'pec'              => 'nullable|email',
            'codice_sdi'       => 'nullable|string|max:10',
            'address'          => 'nullable|string',
            'portal_url'       => 'nullable|url',
            'referente'        => 'nullable|string|max:100',
            'referente_email'  => 'nullable|email',
            'referente_phone'  => 'nullable|string|max:30',
            'notes'            => 'nullable|string',
            'active'           => 'boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $assicurazioni->update($data);
        return redirect()->route('assicurazioni.show', $assicurazioni)->with('success', 'Compagnia aggiornata.');
    }

    public function destroy(InsuranceCompany $assicurazioni)
    {
        abort_if($assicurazioni->tenant_id !== $this->tid(), 403);
        $assicurazioni->delete();
        return redirect()->route('assicurazioni.index')->with('success', 'Compagnia eliminata.');
    }

    // API per AJAX nel form sinistro
    public function periti(InsuranceCompany $assicurazioni)
    {
        abort_if($assicurazioni->tenant_id !== $this->tid(), 403);
        $periti = Expert::where('tenant_id', $this->tid())
            ->where('insurance_company_id', $assicurazioni->id)
            ->orderBy('name')->get(['id','name','type']);
        return response()->json($periti);
    }
}