<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use App\Models\Claim;
use App\Models\WorkOrder;
use App\Observers\ClaimObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer sinistri — aggiorna stato veicolo e invia mail automatiche
        Claim::observe(ClaimObserver::class);

        // Paginazione senza stile Tailwind (usa HTML semplice)
        Paginator::useBootstrapFive();
    }
}