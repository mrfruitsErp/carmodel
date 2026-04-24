<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SparePart;

class SparePartController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = SparePart::forTenant($tid);
        if ($request->search) {
            $q->where(fn($s) => $s->where('name', 'like', "%{$request->search}%")->orWhere('code', 'like', "%{$request->search}%"));
        }
        if ($request->filter === 'sotto_scorta') $q->lowStock();

        $ricambi      = $q->orderBy('name')->paginate(20);
        $totale       = SparePart::forTenant($tid)->count();
        $sotto_scorta = SparePart::forTenant($tid)->lowStock()->count();
        $valore       = SparePart::forTenant($tid)->selectRaw('SUM(stock_quantity * purchase_price) as tot')->value('tot') ?? 0;

        return view('ricambi.index', compact('ricambi', 'totale', 'sotto_scorta', 'valore'));
    }

    public function create()
    {
        return view('ricambi.create');
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'           => 'required',
            'code'           => 'nullable',
            'category'       => 'nullable',
            'brand'          => 'nullable',
            'unit'           => 'nullable',
            'stock_quantity' => 'nullable|numeric',
            'min_stock'      => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'sale_price'     => 'nullable|numeric',
            'supplier'       => 'nullable',
            'location'       => 'nullable',
        ]);
        $v['tenant_id'] = auth()->user()->tenant_id;
        $r = SparePart::create($v);
        return redirect()->route('ricambi.show', $r)->with('success', 'Ricambio aggiunto.');
    }

    public function show(SparePart $ricambi)
    {
        return view('ricambi.show', ['ricambio' => $ricambi]);
    }

    public function edit(SparePart $ricambi)
    {
        return $this->show($ricambi);
    }

    public function update(Request $request, SparePart $ricambi)
    {
        $ricambi->update($request->except('tenant_id'));
        return redirect()->route('ricambi.show', $ricambi)->with('success', 'Aggiornato.');
    }

    public function destroy(SparePart $ricambi)
    {
        $ricambi->delete();
        return redirect()->route('ricambi.index')->with('success', 'Eliminato.');
    }

    public function movimento(Request $request, SparePart $ricambi)
    {
        $request->validate(['movement_type' => 'required', 'quantity' => 'required|numeric']);
        $delta = $request->movement_type === 'carico' ? $request->quantity : -$request->quantity;
        $ricambi->movements()->create([
            'tenant_id'     => $ricambi->tenant_id,
            'movement_type' => $request->movement_type,
            'quantity'      => $request->quantity,
            'unit_price'    => $request->unit_price,
            'notes'         => $request->notes,
            'created_by'    => auth()->id(),
        ]);
        $ricambi->increment('stock_quantity', $delta);
        return back()->with('success', 'Movimento registrato.');
    }
}
