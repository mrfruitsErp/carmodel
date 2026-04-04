<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{PersonalInjury, Claim, Expert};

class PersonalInjuryController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = PersonalInjury::forTenant($tid)->with(['customer', 'claim', 'lawyer', 'doctor']);
        if ($request->status) $q->where('status', $request->status);
        $lesioni = $q->orderByDesc('created_at')->paginate(20);
        return view('lesioni.index', compact('lesioni'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        return view('lesioni.create', [
            'sinistri' => Claim::forTenant($tid)->open()->with('customer')->get(),
            'avvocati' => Expert::forTenant($tid)->avvocati()->get(),
            'medici'   => Expert::forTenant($tid)->where('type', 'medico_legale')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'claim_id'           => 'required|exists:claims,id',
            'customer_id'        => 'required|exists:customers,id',
            'injury_type'        => 'nullable|string',
            'injury_description' => 'nullable|string',
            'lawyer_id'          => 'nullable|exists:experts,id',
            'doctor_id'          => 'nullable|exists:experts,id',
            'estimated_amount'   => 'nullable|numeric',
            'notes'              => 'nullable|string',
        ]);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']     = $tid;
        $v['injury_number'] = PersonalInjury::generateNumber($tid);
        $li = PersonalInjury::create($v);
        return redirect()->route('lesioni.show', $li)->with('success', "Lesione {$li->injury_number} registrata.");
    }

    public function show(PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->load(['customer', 'claim', 'lawyer', 'doctor']);
        return view('lesioni.show', ['lesione' => $lesioni]);
    }

    public function edit(PersonalInjury $lesioni)
    {
        return $this->show($lesioni);
    }

    public function update(Request $request, PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->update($request->except(['tenant_id', 'injury_number']));
        return redirect()->route('lesioni.show', $lesioni)->with('success', 'Lesione aggiornata.');
    }

    public function destroy(PersonalInjury $lesioni)
    {
        abort_if($lesioni->tenant_id !== auth()->user()->tenant_id, 403);
        $lesioni->delete();
        return redirect()->route('lesioni.index')->with('success', 'Eliminata.');
    }
}