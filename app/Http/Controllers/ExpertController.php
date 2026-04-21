<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Expert, InsuranceCompany};

class ExpertController extends Controller
{
    private function isLiquidatori(): bool
    {
        return request()->routeIs('liquidatori.*');
    }

    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Expert::forTenant($tid)->with('insuranceCompany');

        if ($this->isLiquidatori()) {
            $q->where('type', 'liquidatore');
        } else {
            $q->whereIn('type', ['perito','avvocato','medico_legale','consulente','legale']);
        }

        if ($request->tipo)   $q->where('type', $request->tipo);
        if ($request->search) {
            $q->where(fn($s) => $s
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('company_name', 'like', "%{$request->search}%")
            );
        }
        $esperti = $q->orderBy('name')->paginate(20);
        $isLiquidatori = $this->isLiquidatori();
        return view('periti.index', compact('esperti', 'isLiquidatori'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        $isLiquidatori = $this->isLiquidatori();
        return view('periti.create', [
            'compagnie'     => InsuranceCompany::forTenant($tid)->get(),
            'isLiquidatori' => $isLiquidatori,
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'type'                 => 'required',
            'name'                 => 'required',
            'title'                => 'nullable',
            'company_name'         => 'nullable',
            'insurance_company_id' => 'nullable|exists:insurance_companies,id',
            'email'                => 'nullable|email',
            'phone'                => 'nullable',
            'phone2'               => 'nullable',
            'address'              => 'nullable',
            'fiscal_code'          => 'nullable',
            'vat_number'           => 'nullable',
            'rating'               => 'nullable|integer|min:1|max:5',
            'notes'                => 'nullable',
        ]);
        $v['tenant_id'] = auth()->user()->tenant_id;
        $e = Expert::create($v);
        $route = $this->isLiquidatori() ? 'liquidatori.show' : 'periti.show';
        return redirect()->route($route, $e)->with('success', 'Contatto aggiunto.');
    }

    public function show(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->load(['insuranceCompany', 'claims']);
        $isLiquidatori = $this->isLiquidatori();
        return view('periti.show', ['esperto' => $periti, 'isLiquidatori' => $isLiquidatori]);
    }

    public function edit(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $tid = auth()->user()->tenant_id;
        $isLiquidatori = $this->isLiquidatori();
        return view('periti.create', [
            'esperto'       => $periti,
            'compagnie'     => InsuranceCompany::forTenant($tid)->get(),
            'isLiquidatori' => $isLiquidatori,
        ]);
    }

    public function update(Request $request, Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->update($request->except('tenant_id'));
        $route = $this->isLiquidatori() ? 'liquidatori.show' : 'periti.show';
        return redirect()->route($route, $periti)->with('success', 'Aggiornato.');
    }

    public function destroy(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->delete();
        $route = $this->isLiquidatori() ? 'liquidatori.index' : 'periti.index';
        return redirect()->route($route)->with('success', 'Eliminato.');
    }
}