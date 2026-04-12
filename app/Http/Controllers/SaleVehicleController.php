<?php

namespace App\Http\Controllers;

use App\Models\SaleVehicle;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleVehicleController extends Controller
{
    public function __construct(private MarketplaceManager $manager) {}

    private function tid(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = SaleVehicle::forTenant($this->tid())
            ->withCount(['listings' => fn($q) => $q->where('status', 'published')])
            ->with(['listings'])
            ->latest();

        if ($s = $request->search) $query->search($s);
        if ($status = $request->status) $query->where('status', $status);

        $vehicles = $query->paginate(20)->withQueryString();
        $stats    = $this->manager->dashboardStats($this->tid());

        return view('marketplace.vehicles.index', compact('vehicles', 'stats'));
    }

    public function create()
    {
        return view('marketplace.vehicles.form', [
            'vehicle'   => new SaleVehicle(),
            'platforms' => $this->manager->allPlatforms(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand'              => 'required|string|max:100',
            'model'              => 'required|string|max:100',
            'version'            => 'nullable|string|max:150',
            'year'               => 'required|integer|min:1980|max:2027',
            'mileage'            => 'required|integer|min:0',
            'fuel_type'          => 'required|string',
            'transmission'       => 'required|string',
            'color'              => 'nullable|string|max:80',
            'color_type'         => 'nullable|string|max:30',
            'doors'              => 'nullable|integer|between:2,6',
            'seats'              => 'nullable|integer|between:2,9',
            'engine_cc'          => 'nullable|integer',
            'power_kw'           => 'nullable|integer',
            'power_hp'           => 'nullable|integer',
            'body_type'          => 'nullable|string',
            'condition'          => 'required|string',
            'previous_owners'    => 'nullable|integer|min:0',
            'first_registration' => 'nullable|date',
            'features'           => 'nullable|array',
            'asking_price'       => 'required|numeric|min:0',
            'min_price'          => 'nullable|numeric|min:0',
            'price_negotiable'   => 'boolean',
            'vat_deductible'     => 'boolean',
            'purchase_price'     => 'nullable|numeric|min:0',
            'badge_label'        => 'nullable|string|max:40',
            'title'              => 'nullable|string|max:200',
            'description'        => 'nullable|string',
            'internal_notes'     => 'nullable|string',
            'plate'              => 'nullable|string|max:20',
            'vin'                => 'nullable|string|max:17',
            'status'             => 'nullable|string',
        ]);

        $data['tenant_id']        = $this->tid();
        $data['created_by']       = Auth::id();
        $data['price_negotiable'] = $request->boolean('price_negotiable');
        $data['vat_deductible']   = $request->boolean('vat_deductible');

        // Stato: usa quello del form oppure forza bozza se non specificato
        if (empty($data['status'])) {
            $data['status'] = $request->input('action') === 'attivo' ? 'attivo' : 'bozza';
        }

        $vehicle = SaleVehicle::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $vehicle->addMedia($photo)->toMediaCollection('sale_photos');
            }
        }

        return redirect()
            ->route('marketplace.vehicles.show', $vehicle)
            ->with('success', 'Veicolo creato con successo.');
    }

    public function show(SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $saleVehicle->load(['listings', 'leads' => fn($q) => $q->latest()->take(10)]);
        $validations      = $this->manager->validateForPlatforms($saleVehicle, $this->manager->allPlatforms());
        $enabledPlatforms = $this->manager->enabledPlatforms($this->tid());
        return view('marketplace.vehicles.show', compact('saleVehicle', 'validations', 'enabledPlatforms'));
    }

    public function edit(SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        return view('marketplace.vehicles.form', [
            'vehicle'   => $saleVehicle,
            'platforms' => $this->manager->allPlatforms(),
        ]);
    }

    public function update(Request $request, SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);

        $data = $request->validate([
            'brand'              => 'required|string|max:100',
            'model'              => 'required|string|max:100',
            'version'            => 'nullable|string|max:150',
            'year'               => 'required|integer',
            'mileage'            => 'required|integer|min:0',
            'fuel_type'          => 'required|string',
            'transmission'       => 'required|string',
            'color'              => 'nullable|string|max:80',
            'color_type'         => 'nullable|string|max:30',
            'doors'              => 'nullable|integer',
            'seats'              => 'nullable|integer',
            'engine_cc'          => 'nullable|integer',
            'power_kw'           => 'nullable|integer',
            'power_hp'           => 'nullable|integer',
            'body_type'          => 'nullable|string',
            'condition'          => 'required|string',
            'previous_owners'    => 'nullable|integer|min:0',
            'first_registration' => 'nullable|date',
            'features'           => 'nullable|array',
            'asking_price'       => 'required|numeric|min:0',
            'min_price'          => 'nullable|numeric|min:0',
            'price_negotiable'   => 'boolean',
            'vat_deductible'     => 'boolean',
            'purchase_price'     => 'nullable|numeric|min:0',
            'badge_label'        => 'nullable|string|max:40',
            'title'              => 'nullable|string|max:200',
            'description'        => 'nullable|string',
            'internal_notes'     => 'nullable|string',
            'plate'              => 'nullable|string|max:20',
            'vin'                => 'nullable|string|max:17',
            'status'             => 'nullable|string',
        ]);

        $data['price_negotiable'] = $request->boolean('price_negotiable');
        $data['vat_deductible']   = $request->boolean('vat_deductible');

        // Gestione tasto: "Salva bozza" o "Pubblica annuncio"
        if ($request->input('action') === 'attivo') {
            $data['status'] = 'attivo';
        } elseif ($request->input('action') === 'bozza') {
            $data['status'] = 'bozza';
        }

        $saleVehicle->update($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $saleVehicle->addMedia($photo)->toMediaCollection('sale_photos');
            }
        }

        if ($saleVehicle->status === 'attivo') {
            $this->manager->updateVehicle($saleVehicle);
        }

        return redirect()
            ->route('marketplace.vehicles.show', $saleVehicle)
            ->with('success', 'Veicolo aggiornato.');
    }

    public function destroy(SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $this->manager->unpublishAll($saleVehicle);
        $saleVehicle->delete();
        return redirect()
            ->route('marketplace.vehicles.index')
            ->with('success', 'Veicolo rimosso.');
    }

    public function uploadFoto(Request $request, SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $request->validate(['photo' => 'required|image|max:10240']);
        $media = $saleVehicle->addMediaFromRequest('photo')->toMediaCollection('sale_photos');
        return response()->json([
            'id'        => $media->id,
            'url'       => $media->getUrl(),
            'thumb_url' => $media->getUrl('thumb'),
        ]);
    }

    public function deleteFoto(SaleVehicle $saleVehicle, int $mediaId)
    {
        $this->authorizeVehicle($saleVehicle);
        $saleVehicle->deleteMedia($mediaId);
        return response()->json(['ok' => true]);
    }

    public function reorderFoto(Request $request, SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $request->validate(['order' => 'required|array']);
        $saleVehicle->media()
            ->whereIn('id', $request->order)
            ->each(fn($m) => $m->update(['order_column' => array_search($m->id, $request->order)]));
        return response()->json(['ok' => true]);
    }

    public function markSold(Request $request, SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $request->validate(['sold_price' => 'required|numeric|min:0']);
        $saleVehicle->markAsSold($request->sold_price, $request->customer_id);
        return redirect()
            ->route('marketplace.vehicles.show', $saleVehicle)
            ->with('success', 'Veicolo marcato come venduto.');
    }

    public function changeStatus(Request $request, SaleVehicle $saleVehicle)
    {
        $this->authorizeVehicle($saleVehicle);
        $status = $request->status ?? ($saleVehicle->status === 'attivo' ? 'sospeso' : 'attivo');
        $saleVehicle->update(['status' => $status]);
        return redirect()->route('marketplace.vehicles.show', $saleVehicle)
            ->with('success', 'Stato aggiornato: ' . ucfirst($status));
    }

    private function authorizeVehicle(SaleVehicle $vehicle): void
    {
        abort_if($vehicle->tenant_id !== $this->tid(), 403);
    }
}