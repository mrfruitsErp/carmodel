<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Quote, Customer, Vehicle, Claim, WorkOrder};

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Quote::forTenant($tid)->with(['customer','vehicle']);
        if ($request->status) $q->where('status', $request->status);
        $preventivi = $q->orderByDesc('created_at')->paginate(20);
        return view('preventivi.index', compact('preventivi'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        return view('preventivi.create', [
            'clienti'  => Customer::forTenant($tid)->orderBy('last_name')->get(),
            'sinistri' => Claim::forTenant($tid)->open()->with('customer')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $v = $request->validate(['customer_id'=>'required','vehicle_id'=>'required','job_type'=>'required','description'=>'nullable|string','discount_percent'=>'nullable|numeric','vat_percent'=>'nullable|numeric','valid_until'=>'nullable|date','notes'=>'nullable|string','claim_id'=>'nullable|exists:claims,id']);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']    = $tid;
        $v['quote_number'] = Quote::generateNumber($tid);
        $v['status']       = 'bozza';
        $v['created_by']   = auth()->id();
        $quote = Quote::create($v);
        return redirect()->route('preventivi.show', $quote)->with('success', "Preventivo {$quote->quote_number} creato.");
    }

    public function show(Quote $preventivo)
    {
        abort_if($preventivo->tenant_id !== auth()->user()->tenant_id, 403);
        $preventivo->load(['customer','vehicle','items','claim']);
        return view('preventivi.show', compact('preventivo'));
    }

    public function edit(Quote $preventivo)
    {
        abort_if($preventivo->tenant_id !== auth()->user()->tenant_id, 403);
        return view('preventivi.show', compact('preventivo'));
    }

    public function update(Request $request, Quote $preventivo)
    {
        abort_if($preventivo->tenant_id !== auth()->user()->tenant_id, 403);
        $preventivo->update($request->except(['tenant_id','quote_number']));
        $preventivo->recalculate();
        return redirect()->route('preventivi.show', $preventivo)->with('success', 'Preventivo aggiornato.');
    }

    public function destroy(Quote $preventivo)
    {
        abort_if($preventivo->tenant_id !== auth()->user()->tenant_id, 403);
        $preventivo->delete();
        return redirect()->route('preventivi.index')->with('success', 'Preventivo eliminato.');
    }

    public function convertToJob(Quote $preventivo)
    {
        abort_if($preventivo->tenant_id !== auth()->user()->tenant_id, 403);
        if ($preventivo->converted_to_job_id) {
            return back()->with('error', 'Preventivo già convertito in lavorazione.');
        }
        $tid = auth()->user()->tenant_id;
        $wo = WorkOrder::create([
            'tenant_id'        => $tid,
            'job_number'       => WorkOrder::generateNumber($tid),
            'customer_id'      => $preventivo->customer_id,
            'vehicle_id'       => $preventivo->vehicle_id,
            'claim_id'         => $preventivo->claim_id,
            'quote_id'         => $preventivo->id,
            'job_type'         => $preventivo->job_type,
            'status'           => 'attesa',
            'estimated_amount' => $preventivo->total,
            'description'      => $preventivo->description,
            'created_by'       => auth()->id(),
        ]);
        $preventivo->update(['status' => 'accettato', 'converted_to_job_id' => $wo->id]);
        return redirect()->route('lavorazioni.show', $wo)->with('success', "Preventivo convertito in commessa {$wo->job_number}.");
    }
}
