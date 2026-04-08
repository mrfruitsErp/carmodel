<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{PersonalInjury, Claim, Expert, Customer};

class PersonalInjuryController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = PersonalInjury::forTenant($tid)->with(['customer','claim','lawyer','doctor']);
        if ($request->status) $q->where('status', $request->status);
        $lesioni = $q->orderByDesc('created_at')->paginate(20);
        return view('lesioni.index', compact('lesioni'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        return view('lesioni.create', [
            'sinistri' => Claim::forTenant($tid)->open()->with('customer')->get(),
            'avvocati' => Expert::forTenant($tid)->where('type','avvocato')->get(),
            'clienti'  => Customer::forTenant($tid)->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'claim_id'           => 'nullable|exists:claims,id',
            'customer_id'        => 'nullable|exists:customers,id',
            'injury_type'        => 'required|string',
            'injury_description' => 'nullable|string',
            'icd_code'           => 'nullable|string|max:20',
            'status'             => 'nullable|string',
            'lawyer_id'          => 'nullable|exists:experts,id',
            'doctor_id'          => 'nullable|exists:experts,id',
            'estimated_amount'   => 'nullable|numeric',
            'agreed_amount'      => 'nullable|numeric',
            'paid_amount'        => 'nullable|numeric',
            'paid_date'          => 'nullable|date',
            'medical_visit_date' => 'nullable|date',
            'medical_report_date'=> 'nullable|date',
            'notes'              => 'nullable|string',
        ]);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']     = $tid;
        $v['injury_number'] = PersonalInjury::generateNumber($tid);
        $v['status']        = $v['status'] ?? 'aperta';
        $li = PersonalInjury::create($v);
        return redirect()->route('lesioni.show', $li)->with('success', "Lesione {$li->injury_number} registrata.");
    }

    public function show(PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->load(['customer','claim','lawyer','doctor']);
        return view('lesioni.show', ['lesione' => $lesioni]);
    }

    public function edit(PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $tid = auth()->user()->tenant_id;
        return view('lesioni.create', [
            'lesione'  => $lesioni,
            'sinistri' => Claim::forTenant($tid)->open()->with('customer')->get(),
            'avvocati' => Expert::forTenant($tid)->where('type','avvocato')->get(),
            'clienti'  => Customer::forTenant($tid)->orderBy('last_name')->get(),
        ]);
    }

    public function update(Request $request, PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->update($request->except(['tenant_id','injury_number']));
        return redirect()->route('lesioni.show', $lesioni)->with('success', 'Lesione aggiornata.');
    }

    public function destroy(PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->delete();
        return redirect()->route('lesioni.index')->with('success', 'Eliminata.');
    }
}