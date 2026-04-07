<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceLead;
use App\Models\SaleVehicle;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PublicVehicleController extends Controller
{
    private function getTenantId(): int
    {
        return Tenant::first()?->id ?? 1;
    }

    public function index(Request $request)
    {
        $tenantId = $this->getTenantId();

        $query = SaleVehicle::forTenant($tenantId)
            ->where('status', 'attivo')
            ->with(['media'])
            ->latest();

        if ($s = $request->search) {
            $query->where(fn($q) => $q
                ->where('brand', 'like', "%{$s}%")
                ->orWhere('model', 'like', "%{$s}%")
                ->orWhere('version', 'like', "%{$s}%")
            );
        }

        if ($fuel = $request->fuel) {
            $query->where('fuel_type', $fuel);
        }

        if ($priceMax = $request->price_max) {
            $query->where('asking_price', '<=', $priceMax);
        }

        $vehicles = $query->paginate(12)->withQueryString();

        return view('public.auto_in_vendita', compact('vehicles'));
    }

    public function show(Request $request, int $id, string $slug = '')
    {
        $tenantId = $this->getTenantId();

        $vehicle = SaleVehicle::forTenant($tenantId)
            ->where('id', $id)
            ->with(['media', 'listings'])
            ->firstOrFail();

        return view('public.auto_dettaglio', compact('vehicle'));
    }

    public function contact(Request $request, SaleVehicle $vehicle)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:30',
            'message' => 'nullable|string|max:1000',
        ]);

        $listing = $vehicle->listings()->first();

        MarketplaceLead::create([
            'tenant_id'              => $vehicle->tenant_id,
            'marketplace_listing_id' => $listing?->id ?? $vehicle->listings()->first()?->id,
            'sale_vehicle_id'        => $vehicle->id,
            'platform'               => 'manual',
            'lead_name'              => $request->name,
            'lead_email'             => $request->email,
            'lead_phone'             => $request->phone,
            'lead_message'           => $request->message,
            'status'                 => 'nuovo',
        ]);

        return back()->with('success', 'Grazie! Ti contatteremo al più presto.');
    }
}