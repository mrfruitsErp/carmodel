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
        $data = $request->validate([
            'name'         => 'required|string|min:2|max:100',
            'email'        => 'required|email:rfc,dns|max:150',
            'phone'        => 'nullable|string|max:30|regex:/^[\d\s\+\(\)\-\.\/]+$/',
            'message'      => 'nullable|string|max:1500',
            'gdpr_consent' => 'accepted',
        ], [
            'gdpr_consent.accepted' => 'Devi accettare il trattamento dei dati per procedere.',
            'phone.regex'           => 'Il numero di telefono contiene caratteri non validi.',
            'email.dns'             => 'Email non valida o dominio inesistente.',
        ]);

        // Anti-spam analisi
        $spam = \App\Services\SpamFilter::analyze($data, $request);

        // Bot palesi: rispondi 200 ma NON salvare
        if ($spam->isSpam && in_array($spam->reason, ['honeypot', 'submit_too_fast'])) {
            return back()->with('success', 'Grazie! Ti contatteremo al più presto.');
        }

        try {
            $listing = $vehicle->listings()->first();

            \App\Models\MarketplaceLead::create([
                'tenant_id'              => $vehicle->tenant_id,
                'marketplace_listing_id' => $listing?->id,
                'sale_vehicle_id'        => $vehicle->id,
                'platform'               => 'manual',
                'lead_name'              => $data['name'],
                'lead_email'             => $data['email'],
                'lead_phone'             => $data['phone'] ?? null,
                'lead_message'           => ($spam->isSpam ? '[SPAM:'.$spam->reason.'] ' : '') . ($data['message'] ?? ''),
                'status'                 => $spam->isSpam ? 'spam' : 'nuovo',
            ]);
        } catch (\Throwable $e) {
            // Fallback su WebBooking
            \Log::warning('MarketplaceLead create fallito, fallback su WebBooking: ' . $e->getMessage());
            \App\Models\WebBooking::create([
                'tenant_id'   => $vehicle->tenant_id,
                'type'        => 'contatto_veicolo',
                'name'        => $data['name'],
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'message'     => "[{$vehicle->brand} {$vehicle->model} #{$vehicle->id}] " . ($data['message'] ?? ''),
                'status'      => 'nuova',
                'is_spam'     => $spam->isSpam,
                'spam_reason' => $spam->reason,
                'ip_address'  => $request->ip(),
                'user_agent'  => substr((string) $request->userAgent(), 0, 500),
            ]);
        }

        return back()->with('success', 'Grazie! Ti contatteremo al più presto.');
    }
}
