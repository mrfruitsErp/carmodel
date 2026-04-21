<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Expert, InsuranceCompany};

class ExpertController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Expert::forTenant($tid)->with('insuranceCompany');

        if ($request->tipo) {
            $q->where('type', $request->tipo);
        }

        if ($request->search) {
            $q->where(fn($s) => $s
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('company_name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
            );
        }

        $esperti = $q->orderBy('name')->paginate(25);

        $contatori = [
            'tutti'         => Expert::forTenant($tid)->count(),
            'perito'        => Expert::forTenant($tid)->where('type','perito')->count(),
            'avvocato'      => Expert::forTenant($tid)->where('type','avvocato')->count(),
            'legale'        => Expert::forTenant($tid)->where('type','legale')->count(),
            'liquidatore'   => Expert::forTenant($tid)->where('type','liquidatore')->count(),
            'medico_legale' => Expert::forTenant($tid)->where('type','medico_legale')->count(),
            'consulente'    => Expert::forTenant($tid)->where('type','consulente')->count(),
        ];

        return view('periti.index', compact('esperti', 'contatori'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        return view('periti.create', [
            'compagnie' => InsuranceCompany::forTenant($tid)->orderBy('name')->get(),
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
        return redirect()->route('periti.show', $e)->with('success', 'Contatto aggiunto.');
    }

    public function show(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->load(['insuranceCompany', 'claims']);
        return view('periti.show', ['esperto' => $periti]);
    }

    public function edit(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $tid = auth()->user()->tenant_id;
        return view('periti.create', [
            'esperto'   => $periti,
            'compagnie' => InsuranceCompany::forTenant($tid)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->update($request->except('tenant_id'));
        return redirect()->route('periti.show', $periti)->with('success', 'Aggiornato.');
    }

    public function destroy(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->delete();
        return redirect()->route('periti.index')->with('success', 'Eliminato.');
    }
}