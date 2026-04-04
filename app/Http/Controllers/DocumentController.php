<?php
// ═══════════════════════════════════════════════
// DocumentController.php
// ═══════════════════════════════════════════════
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Document, Customer, WorkOrder, Claim, Rental};

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Document::forTenant($tid)->with(['customer']);
        if ($request->tipo)   $q->where('document_type', $request->tipo);
        if ($request->status) $q->where('payment_status', $request->status);
        if ($request->search) $q->where(fn($s) => $s->where('document_number','like',"%{$request->search}%")->orWhereHas('customer',fn($c)=>$c->search($request->search)));

        $totale_da_pagare   = Document::forTenant($tid)->whereIn('payment_status',['da_pagare'])->sum('total');
        $count_da_pagare    = Document::forTenant($tid)->where('payment_status','da_pagare')->count();
        $totale_pagato_mese = Document::forTenant($tid)->where('payment_status','pagata')->whereMonth('payment_date', now()->month)->sum('total');
        $count_scadute      = Document::forTenant($tid)->where('payment_status','scaduta')->count();

        $documenti = $q->orderByDesc('issue_date')->paginate(20);
        return view('documenti.index', compact('documenti','totale_da_pagare','count_da_pagare','totale_pagato_mese','count_scadute'));
    }

    public function create()
    {
        $tid = auth()->user()->tenant_id;
        return view('documenti.create', ['clienti' => Customer::forTenant($tid)->get()]);
    }

    public function store(Request $request)
    {
        $v = $request->validate(['customer_id'=>'required','document_type'=>'required','issue_date'=>'required|date','due_date'=>'nullable|date','subtotal'=>'required|numeric','vat_percent'=>'nullable|numeric','notes'=>'nullable|string']);
        $tid = auth()->user()->tenant_id;
        $v['tenant_id']       = $tid;
        $v['vat_amount']      = round(($v['subtotal'] - ($v['discount_amount'] ?? 0)) * ($v['vat_percent'] ?? 22) / 100, 2);
        $v['total']           = ($v['subtotal'] - ($v['discount_amount'] ?? 0)) + $v['vat_amount'];
        $v['document_number'] = Document::generateNumber($tid, $v['document_type']);
        $v['created_by']      = auth()->id();
        $doc = Document::create($v);
        return redirect()->route('documenti.show', $doc)->with('success', "Documento {$doc->document_number} creato.");
    }

    public function show(Document $document)
    {
        abort_if($document->tenant_id !== auth()->user()->tenant_id, 403);
        $document->load(['customer','workOrder','claim','items']);
        return view('documenti.show', compact('document'));
    }

    public function edit(Document $document) { return $this->show($document); }

    public function update(Request $request, Document $document)
    {
        abort_if($document->tenant_id !== auth()->user()->tenant_id, 403);
        $document->update($request->except(['tenant_id','document_number']));
        return redirect()->route('documenti.show', $document)->with('success', 'Documento aggiornato.');
    }

    public function destroy(Document $document)
    {
        abort_if($document->tenant_id !== auth()->user()->tenant_id, 403);
        $document->delete();
        return redirect()->route('documenti.index')->with('success', 'Documento eliminato.');
    }

    public function markPagato(Request $request, Document $document)
    {
        abort_if($document->tenant_id !== auth()->user()->tenant_id, 403);
        $document->update(['payment_status'=>'pagata','payment_date'=>now(),'payment_method'=>$request->payment_method ?? 'contanti']);
        $document->customer->recalculateTotalValue();
        return back()->with('success', 'Documento segnato come pagato.');
    }
}
