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

    private function isMedici(): bool
    {
        return request()->routeIs('medici.*');
    }

    private function getType(): string
    {
        if ($this->isLiquidatori()) return 'liquidatore';
        if ($this->isMedici()) return 'medico_legale';
        return 'altri';
    }

    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Expert::forTenant($tid)->with('insuranceCompany');

        if ($this->isLiquidatori()) {
            $q->where('type', 'liquidatore');
        } elseif ($this->isMedici()) {
            $q->where('type', 'medico_legale');
        } else {
            $q->whereIn('type', ['perito','avvocato','consulente','legale']);
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
        $isMedici = $this->isMedici();
        return view('periti.index', compact('esperti', 'isLiquidatori', 'isMedici'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        $isLiquidatori = $this->isLiquidatori();
        $isMedici = $this->isMedici();
        return view('periti.create', [
            'compagnie'     => InsuranceCompany::forTenant($tid)->get(),
            'isLiquidatori' => $isLiquidatori,
            'isMedici'      => $isMedici,
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
        if ($this->isLiquidatori()) return redirect()->route('liquidatori.show', $e)->with('success', 'Liquidatore aggiunto.');
        if ($this->isMedici()) return redirect()->route('medici.show', $e)->with('success', 'Medico legale aggiunto.');
        return redirect()->route('periti.show', $e)->with('success', 'Contatto aggiunto.');
    }

    public function show(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $periti->load(['insuranceCompany', 'claims']);
        $isLiquidatori = $this->isLiquidatori();
        $isMedici = $this->isMedici();
        return view('periti.show', ['esperto' => $periti, 'isLiquidatori' => $isLiquidatori, 'isMedici' => $isMedici]);
    }

    public function edit(Expert $periti)
    {
        abort_if($periti->tenant_id !== auth()->user()->tenant_id, 403);
        $tid = auth()->user()->tenant_id;
        $isLiquidatori = $this->isLiquidatori();
        $isMedici = $this->isMedici();
        return view('periti.create', [
            'esperto'       => $periti,
            'compagnie'