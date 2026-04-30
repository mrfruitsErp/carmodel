<?php

namespace App\Http\Controllers;

use App\Models\VehicleMovement;
use App\Models\FleetVehicle;
use App\Models\SaleVehicle;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\User;
use App\Models\Rental;
use App\Models\WorkOrder;
use App\Models\Claim;
use Illuminate\Http\Request;

class VehicleMovementController extends Controller
{
    public function index(Request $request)
    {
        $tid   = auth()->user()->tenant_id;
        $query = VehicleMovement::with(['fleetVehicle','saleVehicle','vehicle','cliente','operatore','autista'])
            ->orderBy('data_inizio');

        if ($request->filled('stato'))  $query->where('stato', $request->stato);
        if ($request->filled('tipo'))   $query->where('tipo', $request->tipo);
        if ($request->filled('data'))   $query->whereDate('data_inizio', $request->data);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('titolo', 'like', "%{$s}%")
                  ->orWhere('luogo_partenza', 'like', "%{$s}%")
                  ->orWhere('luogo_arrivo', 'like', "%{$s}%")
                  ->orWhereHas('cliente', fn($q2) =>
                      $q2->where('first_name','like',"%{$s}%")
                         ->orWhere('last_name','like',"%{$s}%")
                         ->orWhere('company_name','like',"%{$s}%")
                  );
            });
        }

        // Stats per la sidebar
        $stats = [
            'oggi'       => VehicleMovement::whereDate('data_inizio', today())->whereNotIn('stato',['annullato'])->count(),
            'in_corso'   => VehicleMovement::where('stato','in_corso')->count(),
            'programmati'=> VehicleMovement::where('stato','programmato')->whereDate('data_inizio','>=',today())->count(),
            'in_ritardo' => VehicleMovement::where('stato','in_corso')->where('data_fine','<',now())->count(),
        ];

        $movimenti   = $query->paginate(25)->withQueryString();
        $tipi        = VehicleMovement::tipi();
        $stati       = VehicleMovement::stati();

        return view('movimenti.index', compact('movimenti','tipi','stati','stats'));
    }

    public function calendario(Request $request)
    {
        $anno  = $request->get('anno', now()->year);
        $mese  = $request->get('mese', now()->month);

        $inizio = \Carbon\Carbon::create($anno, $mese, 1)->startOfMonth();
        $fine   = $inizio->copy()->endOfMonth();

        $movimenti = VehicleMovement::with(['fleetVehicle','saleVehicle','vehicle','cliente','operatore'])
            ->whereBetween('data_inizio', [$inizio, $fine])
            ->whereNotIn('stato', ['annullato'])
            ->orderBy('data_inizio')
            ->get();

        // Raggruppa per giorno
        $perGiorno = $movimenti->groupBy(fn($m) => $m->data_inizio->format('Y-m-d'));

        // Dati per il calendario JSON (FullCalendar)
        $eventi = $movimenti->map(fn($m) => [
            'id'    => $m->id,
            'title' => ($m->tipo_icon . ' ' . ($m->titolo ?: $m->tipo_label)) .
                       ($m->veicolo_label !== '—' ? ' — ' . $m->veicolo_label : ''),
            'start' => $m->data_inizio->toIso8601String(),
            'end'   => $m->data_fine?->toIso8601String(),
            'color' => match($m->tipo_color) {
                'success'   => '#22c55e',
                'warning'   => '#f59e0b',
                'danger'    => '#ef4444',
                'info'      => '#3b82f6',
                'primary'   => '#8b5cf6',
                'dark'      => '#374151',
                default     => '#6b7280',
            },
            'url'   => route('movimenti.show', $m->id),
        ])->values();

        return view('movimenti.calendario', compact('anno','mese','inizio','fine','perGiorno','eventi'));
    }

    public function create(Request $request)
    {
        $tid          = auth()->user()->tenant_id;
        $tipi         = VehicleMovement::tipi();
        $stati        = VehicleMovement::stati();
        $clienti      = Customer::orderBy('first_name')->get();
        $operatori    = User::where('tenant_id', $tid)->orderBy('name')->get();
        $fleetVehicles= FleetVehicle::forTenant($tid)->orderBy('brand')->get();
        $saleVehicles = SaleVehicle::forTenant($tid)->orderBy('brand')->get();
        $customerVehicles = Vehicle::forTenant($tid)->orderBy('brand')->get();

        // Pre-fill da query string (es. dal pulsante "Crea attività" in veicolo)
        $preVehicleType = $request->get('vehicle_type', 'fleet');
        $preVehicleId   = $request->get('vehicle_id');
        $preTipo        = $request->get('tipo');
        $preClienteId   = $request->get('cliente_id');

        return view('movimenti.create', compact(
            'tipi','stati','clienti','operatori',
            'fleetVehicles','saleVehicles','customerVehicles',
            'preVehicleType','preVehicleId','preTipo','preClienteId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_type'      => 'required|in:fleet,sale,customer',
            'fleet_vehicle_id'  => 'nullable|integer',
            'sale_vehicle_id'   => 'nullable|integer',
            'vehicle_id'        => 'nullable|integer',
            'tipo'              => 'required|in:' . implode(',', array_keys(VehicleMovement::tipi())),
            'titolo'            => 'nullable|string|max:255',
            'data_inizio'       => 'required|date',
            'data_fine'         => 'nullable|date',
            'luogo_partenza'    => 'nullable|string|max:255',
            'indirizzo_partenza'=> 'nullable|string|max:255',
            'luogo_arrivo'      => 'nullable|string|max:255',
            'indirizzo_arrivo'  => 'nullable|string|max:255',
            'cliente_id'        => 'nullable|exists:customers,id',
            'operatore_id'      => 'nullable|exists:users,id',
            'autista_id'        => 'nullable|exists:users,id',
            'stato'             => 'required|in:' . implode(',', array_keys(VehicleMovement::stati())),
            'km_partenza'       => 'nullable|integer',
            'km_arrivo'         => 'nullable|integer',
            'note'              => 'nullable|string',
            'rental_id'         => 'nullable|integer',
            'work_order_id'     => 'nullable|integer',
            'claim_id'          => 'nullable|integer',
            'fascicolo_id'      => 'nullable|integer',
        ]);

        $movimento = VehicleMovement::create(array_merge($validated, [
            'tenant_id'  => auth()->user()->tenant_id,
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('movimenti.show', $movimento)
            ->with('success', 'Movimento creato.');
    }

    public function show(VehicleMovement $movimento)
    {
        $movimento->load(['fleetVehicle','saleVehicle','vehicle','cliente','operatore','autista','rental','workOrder','claim','fascicolo']);
        return view('movimenti.show', compact('movimento'));
    }

    public function edit(VehicleMovement $movimento)
    {
        $tid          = auth()->user()->tenant_id;
        $tipi         = VehicleMovement::tipi();
        $stati        = VehicleMovement::stati();
        $clienti      = Customer::orderBy('first_name')->get();
        $operatori    = User::where('tenant_id', $tid)->orderBy('name')->get();
        $fleetVehicles= FleetVehicle::forTenant($tid)->orderBy('brand')->get();
        $saleVehicles = SaleVehicle::forTenant($tid)->orderBy('brand')->get();
        $customerVehicles = Vehicle::forTenant($tid)->orderBy('brand')->get();

        return view('movimenti.edit', compact(
            'movimento','tipi','stati','clienti','operatori',
            'fleetVehicles','saleVehicles','customerVehicles'
        ));
    }

    public function update(Request $request, VehicleMovement $movimento)
    {
        $validated = $request->validate([
            'vehicle_type'      => 'required|in:fleet,sale,customer',
            'fleet_vehicle_id'  => 'nullable|integer',
            'sale_vehicle_id'   => 'nullable|integer',
            'vehicle_id'        => 'nullable|integer',
            'tipo'              => 'required|in:' . implode(',', array_keys(VehicleMovement::tipi())),
            'titolo'            => 'nullable|string|max:255',
            'data_inizio'       => 'required|date',
            'data_fine'         => 'nullable|date',
            'luogo_partenza'    => 'nullable|string|max:255',
            'indirizzo_partenza'=> 'nullable|string|max:255',
            'luogo_arrivo'      => 'nullable|string|max:255',
            'indirizzo_arrivo'  => 'nullable|string|max:255',
            'cliente_id'        => 'nullable|exists:customers,id',
            'operatore_id'      => 'nullable|exists:users,id',
            'autista_id'        => 'nullable|exists:users,id',
            'stato'             => 'required|in:' . implode(',', array_keys(VehicleMovement::stati())),
            'km_partenza'       => 'nullable|integer',
            'km_arrivo'         => 'nullable|integer',
            'note'              => 'nullable|string',
            'rental_id'         => 'nullable|integer',
            'work_order_id'     => 'nullable|integer',
            'claim_id'          => 'nullable|integer',
            'fascicolo_id'      => 'nullable|integer',
        ]);

        $movimento->update($validated);

        return redirect()->route('movimenti.show', $movimento)
            ->with('success', 'Movimento aggiornato.');
    }

    public function aggiornaStato(Request $request, VehicleMovement $movimento)
    {
        $request->validate(['stato' => 'required|in:programmato,in_corso,completato,annullato']);
        $data = ['stato' => $request->stato];
        if ($request->stato === 'completato' && $request->filled('km_arrivo')) {
            $data['km_arrivo'] = $request->km_arrivo;
        }
        $movimento->update($data);
        return back()->with('success', 'Stato aggiornato.');
    }

    public function destroy(VehicleMovement $movimento)
    {
        $movimento->delete();
        return redirect()->route('movimenti.index')->with('success', 'Movimento eliminato.');
    }

    // API JSON per FullCalendar
    public function apiEventi(Request $request)
    {
        $start = $request->get('start') ? \Carbon\Carbon::parse($request->start) : now()->startOfMonth();
        $end   = $request->get('end')   ? \Carbon\Carbon::parse($request->end)   : now()->endOfMonth();

        $movimenti = VehicleMovement::with(['fleetVehicle','saleVehicle','vehicle','cliente'])
            ->whereBetween('data_inizio', [$start, $end])
            ->whereNotIn('stato', ['annullato'])
            ->get();

        return response()->json($movimenti->map(fn($m) => [
            'id'              => $m->id,
            'title'           => ($m->titolo ?: $m->tipo_label) . ($m->veicolo_label !== '—' ? ' — ' . $m->veicolo_label : ''),
            'start'           => $m->data_inizio->toIso8601String(),
            'end'             => $m->data_fine?->toIso8601String(),
            'backgroundColor' => match($m->tipo_color) {
                'success' => '#22c55e', 'warning' => '#f59e0b',
                'danger'  => '#ef4444', 'info'    => '#3b82f6',
                'primary' => '#8b5cf6', 'dark'    => '#374151',
                default   => '#6b7280',
            },
            'borderColor'     => 'transparent',
            'textColor'       => '#fff',
            'extendedProps'   => [
                'stato'          => $m->stato_label,
                'luogo_partenza' => $m->luogo_partenza,
                'luogo_arrivo'   => $m->luogo_arrivo,
                'cliente'        => $m->cliente?->display_name ?? '—',
                'url'            => route('movimenti.show', $m->id),
            ],
        ])->values());
    }
}
