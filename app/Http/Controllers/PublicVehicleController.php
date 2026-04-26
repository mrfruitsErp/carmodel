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
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'phone'        => 'nullable|string|max:30',
            'message'      => 'nullable|string|max:1500',
            'gdpr_consent' => 'accepted',
        ], [
            'gdpr_consent.accepted' => 'Devi accettare il trattamento dei dati per procedere.',
        ]);

        try {
            $listing = $vehicle->listings()->first();

            MarketplaceLead::create([
                'tenant_id'              => $vehicle->tenant_id,
                'marketplace_listing_id' => $listing?->id, // null se non c'è listing — la colonna deve essere nullable
                'sale_vehicle_id'        => $vehicle->id,
                'platform'               => 'manual',
                'lead_name'              => $request->name,
                'lead_email'             => $request->email,
                'lead_phone'             => $request->phone,
                'lead_message'           => $request->message,
                'status'                 => 'nuovo',
            ]);
        } catch (\Throwable $e) {
            // Fallback: se MarketplaceLead fallisce (es. listing_id NOT NULL),
            // salviamo nel WebBooking (struttura più semplice, sempre disponibile).
            \Log::warning('MarketplaceLead create fallito, fallback su WebBooking: ' . $e->getMessage());
            \App\Models\WebBooking::create([
                'tenant_id' => $vehicle->tenant_id,
                'type'      => 'contatto_veicolo',
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'message'   => "[{$vehicle->brand} {$vehicle->model} #{$vehicle->id}] " . ($request->message ?? ''),
                'status'    => 'nuova',
            ]);
        }

        return back()->with('success', 'Grazie! Ti contatteremo al più presto.');
    }
}
