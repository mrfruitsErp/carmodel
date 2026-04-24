<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\{WorkOrder, Customer, Vehicle, Claim, User};

class WorkOrderController extends Controller {
    public function index(Request $request) {
        $tid = auth()->user()->tenant_id;
        $q = WorkOrder::forTenant($tid)->with(['customer','vehicle','claim','assignedTo']);
        if ($request->search) $q->where(fn($s)=>$s->where('job_number','like',"%{$request->search}%")->orWhereHas('vehicle',fn($v)=>$v->where('plate','like',"%{$request->search}%")));
        if ($request->filter === 'overdue') $q->overdue();
        elseif ($request->status) $q->where('status', $request->status);
        else $q->whereNotIn('status',['consegnato','annullato']);
        $lavorazioni = $q->orderBy('expected_end_date')->paginate(20);
        return view('lavorazioni.index', compact('lavorazioni'));
    }

    public function create() {
        $tid = auth()->user()->tenant_id;
        return view('lavorazioni.create', [
            'clienti'  => Customer::forTenant($tid)->get(),
            'tecnici'  => User::forTenant($tid)->get(),
            'sinistri' => Claim::forTenant($tid)->open()->get(),
            'veicoli'  => Vehicle::forTenant($tid)->get(),
        ]);
    }

    public function store(Request $request) {
        $v = $request->validate([
            'customer_id'       => 'required',
            'vehicle_id'        => 'required',
            'job_type'          => 'required',
            'priority'          => 'nullable',
            'expected_end_date' => 'nullable|date',
            'estimated_amount'  => 'nullable|numeric',
            'description'       => 'nullable|string',
            'assigned_to'       => 'nullable|exists:users,id',
            'claim_id'          => 'nullable|exists:claims,id',
        ]);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']  = $tid;
        $v['job_number'] = WorkOrder::generateNumber($tid);
        $v['created_by'] = auth()->id();
        $wo = WorkOrder::create($v);
        return redirect()->route('lavorazioni.show', $wo)->with('success', "Commessa {$wo->job_number} creata.");
    }

    public function show(WorkOrder $lavorazioni) {
        $lavorazioni->load(['customer','vehicle','claim','assignedTo','items','documents']);
        return view('lavorazioni.show', ['workOrder' => $lavorazioni]);
    }

    public function edit(WorkOrder $lavorazioni) {
        $tid = auth()->user()->tenant_id;
        return view('lavorazioni.edit', [
            'workOrder' => $lavorazioni,
            'clienti'   => Customer::forTenant($tid)->get(),
            'tecnici'   => User::forTenant($tid)->get(),
            'sinistri'  => Claim::forTenant($tid)->open()->get(),
            'veicoli'   => Vehicle::forTenant($tid)->get(),
        ]);
    }

    public function update(Request $request, WorkOrder $lavorazioni) {
        $lavorazioni->update($request->except(['tenant_id','job_number']));
        return redirect()->route('lavorazioni.show', $lavorazioni)->with('success', 'Aggiornato.');
    }

    public function destroy(WorkOrder $lavorazioni) {
        $lavorazioni->delete();
        return redirect()->route('lavorazioni.index')->with('success', 'Eliminata.');
    }

    public function updateStato(Request $request, WorkOrder $lavorazioni) {
        $lavorazioni->update(['status' => $request->status, 'technical_notes' => $request->technical_notes ?? $lavorazioni->technical_notes]);
        return back()->with('success', 'Stato aggiornato.');
    }

    public function updateProgresso(Request $request, WorkOrder $lavorazioni) {
        $lavorazioni->update(['progress_percent' => $request->progress, 'status' => $request->status]);
        return back()->with('success', 'Avanzamento aggiornato.');
    }
}
