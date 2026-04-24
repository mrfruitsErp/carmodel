<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceLead;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceCredential;
use App\Models\SaleVehicle;
use App\Services\Marketplace\MarketplaceManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function __construct(private MarketplaceManager $manager) {}

    private function tid(): int { return Auth::user()->tenant_id; }

    public function dashboard()
    {
        $stats = $this->manager->dashboardStats($this->tid());

        $recentLeads = MarketplaceLead::forTenant($this->tid())
            ->with('saleVehicle')
            ->latest()
            ->take(10)
            ->get();

        $errorListings = MarketplaceListing::forTenant($this->tid())
            ->where('status', 'error')
            ->with('saleVehicle')
            ->latest()
            ->take(5)
            ->get();

        $allVehicles = SaleVehicle::forTenant($this->tid())
            ->latest()
            ->get();

        return view('marketplace.dashboard', compact('stats', 'recentLeads', 'errorListings', 'allVehicles'));
    }

    public function publish(Request $request, SaleVehicle $saleVehicle)
    {

        $request->validate([
            'platforms'   => 'required|array|min:1',
            'platforms.*' => 'in:' . implode(',', $this->manager->allPlatforms()),
            'price'       => 'nullable|numeric|min:0',
        ]);

        $results = $this->manager->publishVehicle(
            $saleVehicle,
            $request->platforms,
            $request->price
        );

        $success = collect($results)->filter(fn($r) => $r['success'])->count();
        $failed  = collect($results)->filter(fn($r) => !$r['success'])->count();

        $message = "Pubblicazione completata: {$success} successi";
        if ($failed > 0) $message .= ", {$failed} errori";

        return back()->with($failed === 0 ? 'success' : 'warning', $message);
    }

    public function unpublish(MarketplaceListing $listing)
    {

        $result = $this->manager->unpublish($listing);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function updatePrice(Request $request, SaleVehicle $saleVehicle)
    {
        $request->validate(['asking_price' => 'required|numeric|min:0']);

        $saleVehicle->update(['asking_price' => $request->asking_price]);

        return back()->with('success', 'Prezzo aggiornato.');
    }

    public function leads(Request $request)
    {
        $query = MarketplaceLead::forTenant($this->tid())
            ->with(['saleVehicle', 'assignedTo'])
            ->latest();

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($platform = $request->platform) {
            $query->where('platform', $platform);
        }

        $leads = $query->paginate(25)->withQueryString();

        return view('marketplace.leads.index', compact('leads'));
    }

    public function updateLead(Request $request, MarketplaceLead $lead)
    {

        $lead->update($request->validate([
            'status'         => 'required|in:nuovo,contattato,appuntamento,trattativa,vinto,perso',
            'notes'          => 'nullable|string',
            'assigned_to'    => 'nullable|exists:users,id',
            'appointment_at' => 'nullable|date',
        ]));

        return back()->with('success', 'Lead aggiornato.');
    }

    public function settings()
    {
        $credentials = MarketplaceCredential::forTenant($this->tid())
            ->get()
            ->keyBy('platform');
        $platforms = $this->manager->allPlatforms();

        return view('marketplace.settings', compact('credentials', 'platforms'));
    }

    public function saveCredentials(Request $request, string $platform)
    {
        abort_if(!in_array($platform, $this->manager->allPlatforms()), 404);

        $request->validate([
            'enabled'     => 'boolean',
            'credentials' => 'required|array',
        ]);

        $credential = MarketplaceCredential::updateOrCreate(
            ['tenant_id' => $this->tid(), 'platform' => $platform],
            [
                'enabled'  => $request->boolean('enabled'),
                'settings' => $request->settings ?? [],
            ]
        );

        $credential->setCredentialsArray($request->credentials);

        return back()->with('success', "Credenziali {$platform} salvate.");
    }

    public function testConnection(string $platform)
    {
        abort_if(!in_array($platform, $this->manager->allPlatforms()), 404);

        $connector = $this->manager->connector($platform, $this->tid());
        $result    = $connector->testConnection();

        return response()->json($result);
    }

    public function syncStats()
    {
        \App\Jobs\Marketplace\SyncMarketplaceStatsJob::dispatch($this->tid())
            ->onQueue('marketplace');

        return back()->with('success', 'Sincronizzazione statistiche avviata in background.');
    }

    public function syncLeads()
    {
        \App\Jobs\Marketplace\FetchMarketplaceLeadsJob::dispatch($this->tid())
            ->onQueue('marketplace');

        return back()->with('success', 'Recupero lead avviato in background.');
    }
}
