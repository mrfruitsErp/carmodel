<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Quote, QuoteItem, Customer, Vehicle, Claim, WorkOrder};

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $q = Quote::forTenant($tid)->with(['customer','vehicle']);
        if ($request->status) $q->where('status', $request->status);
        if ($request->search) {
            $q->where(fn($s) => $s
                ->where('quote_number', 'like', "%{$request->search}%")
                ->orWhereHas('customer', fn($c) => $c
                    ->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('company_name', 'like', "%{$request->search}%")
                )
            );
        }
        $preventivi = $q->orderByDesc('created_at')->paginate(20);
        return view('preventivi.index', compact('preventivi'));
    }

    public function create(Request $request)
    {
        $tid = auth()->user()->tenant_id;
        $clienti  = Customer::forTenant($tid)->orderBy('last_name')->with('vehicles')->get();
        $sinistri = Claim::forTenant($tid)->open()->with('customer')->get();
        $clienteId = $request->get('cliente_id');
        $veicoloId = $request->get('vehicle_id');

        return view('preventivi.create', compact('clienti', 'sinistri', 'clienteId', 'veicoloId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'vehicle_id'        => 'required|exists:vehicles,id',
            'job_type'          => 'required|string',
            'claim_id'          => 'nullable|exists:claims,id',
            'description'       => 'nullable|string',
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
            'vat_percent'       => 'nullable|numeric|min:0|max:100',
            'valid_until'       => 'nullable|date',
            'notes'             => 'nullable|string',
            // righe
            'items'             => 'nullable|array',
            'items.*.item_type' => 'required_with:items|string',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity'  => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price'=> 'required_with:items|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $tid = auth()->user()->tenant_id;

        DB::transaction(function () use ($validated, $tid, &$quote) {
            $quote = Quote::create([
                'tenant_id'        => $tid,
                'quote_number'     => Quote::generateNumber($tid),
                'customer_id'      => $validated['customer_id'],
                'vehicle_id'       => $validated['vehicle_id'],
                'claim_id'         => $validated['claim_id'] ?? null,
                'job_type'         => $validated['job_type'],
                'description'      => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'] ?? 0,
                'vat_percent'      => $validated['vat_percent'] ?? 22,
                'valid_until'      => $validated['valid_until'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'status'           => 'bozza',
                'created_by'       => auth()->id(),
            ]);

            $sort = 0;
            foreach (($validated['items'] ?? []) as $item) {
                $disc  = floatval($item['discount_percent'] ?? 0);
                $total = QuoteItem::calcTotal(floatval($item['quantity']), floatval($item['unit_price']), $disc);
                QuoteItem::create([
                    'quote_id'         => $quote->id,
                    'item_type'        => $item['item_type'],
                    'description'      => $item['description'],
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'],
                    'discount_percent' => $disc,
                    'total_price'      => $total,
                    'sort_order'       => $sort++,
                ]);
            }

            $quote->recalculate();
        });

        return redirect()->route('preventivi.show', $quote)
            ->with('success', "Preventivo {$quote->quote_number} creato.");
    }

    public function show(Quote $preventivo)
    {
        $preventivo->load(['customer','vehicle','items','claim','convertedJob']);
        return view('preventivi.show', compact('preventivo'));
    }

    public function edit(Quote $preventivo)
    {
        $tid      = auth()->user()->tenant_id;
        $clienti  = Customer::forTenant($tid)->orderBy('last_name')->with('vehicles')->get();
        $sinistri = Claim::forTenant($tid)->open()->with('customer')->get();
        $preventivo->load('items');
        return view('preventivi.edit', compact('preventivo', 'clienti', 'sinistri'));
    }

    public function update(Request $request, Quote $preventivo)
    {
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'vehicle_id'        => 'required|exists:vehicles,id',
            'job_type'          => 'required|string',
            'claim_id'          => 'nullable|exists:claims,id',
            'description'       => 'nullable|string',
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
            'vat_percent'       => 'nullable|numeric|min:0|max:100',
            'valid_until'       => 'nullable|date',
            'notes'             => 'nullable|string',
            'status'            => 'nullable|in:bozza,inviato,accettato,rifiutato,scaduto',
            'items'             => 'nullable|array',
            'items.*.item_type' => 'required_with:items|string',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity'  => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price'=> 'required_with:items|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($validated, $preventivo) {
            $preventivo->update([
                'customer_id'      => $validated['customer_id'],
                'vehicle_id'       => $validated['vehicle_id'],
                'claim_id'         => $validated['claim_id'] ?? null,
                'job_type'         => $validated['job_type'],
                'description'      => $validated['description'] ?? null,
                'discount_percent' => $validated['discount_percent'] ?? 0,
                'vat_percent'      => $validated['vat_percent'] ?? 22,
                'valid_until'      => $validated['valid_until'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'status'           => $validated['status'] ?? $preventivo->status,
            ]);

            // Rimuove righe esistenti e reinserisce
            $preventivo->items()->delete();
            $sort = 0;
            foreach (($validated['items'] ?? []) as $item) {
                $disc  = floatval($item['discount_percent'] ?? 0);
                $total = QuoteItem::calcTotal(floatval($item['quantity']), floatval($item['unit_price']), $disc);
                QuoteItem::create([
                    'quote_id'         => $preventivo->id,
                    'item_type'        => $item['item_type'],
                    'description'      => $item['description'],
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'],
                    'discount_percent' => $disc,
                    'total_price'      => $total,
                    'sort_order'       => $sort++,
                ]);
            }

            $preventivo->recalculate();
        });

        return redirect()->route('preventivi.show', $preventivo)
            ->with('success', 'Preventivo aggiornato.');
    }

    public function destroy(Quote $preventivo)
    {
        $preventivo->delete();
        return redirect()->route('preventivi.index')->with('success', 'Preventivo eliminato.');
    }

    public function convertToJob(Quote $preventivo)
    {
        if ($preventivo->converted_to_job_id) {
            return back()->with('error', 'Preventivo già convertito in commessa.');
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
            'priority'         => 'normale',
            'estimated_amount' => $preventivo->total,
            'description'      => $preventivo->description,
            'created_by'       => auth()->id(),
        ]);
        $preventivo->update(['status' => 'accettato', 'converted_to_job_id' => $wo->id]);
        return redirect()->route('lavorazioni.show', $wo)
            ->with('success', "Preventivo convertito in commessa {$wo->job_number}.");
    }

    public function aggiornaStato(Request $request, Quote $preventivo)
    {
        $request->validate(['status' => 'required|in:bozza,inviato,accettato,rifiutato,scaduto']);
        $preventivo->update(['status' => $request->status]);
        return back()->with('success', 'Stato aggiornato.');
    }
}
