<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicVehicleController;
use App\Http\Controllers\PublicSiteController;

/**
 * Route del SITO PUBBLICO (vetrina AleCar).
 *
 * Strategia:
 *  - In PRODUZIONE: le route stanno alla root del dominio alecar.it / www.alecar.it
 *    Es. https://alecar.it/auto-in-vendita
 *  - In SVILUPPO/altro: prefix "/sito" per non collidere con le route ERP
 *    Es. http://carmodel.local/sito/auto-in-vendita
 *
 * I nomi delle route sono sempre `public.*` (es. route('public.vehicles.index')).
 */
$siteRoutes = function () {

    // ── Homepage ──────────────────────────────────────────
    Route::get('/', [PublicSiteController::class, 'home'])->name('home');

    // ── Auto in vendita ───────────────────────────────────
    Route::get('/auto-in-vendita', [PublicVehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/auto-in-vendita/{id}-{slug?}', [PublicVehicleController::class, 'show'])->name('vehicles.show');
    Route::post('/auto-in-vendita/{vehicle}/contatto', [PublicVehicleController::class, 'contact'])->name('vehicles.contact');

    // ── Noleggio ──────────────────────────────────────────
    Route::get('/noleggio', [PublicSiteController::class, 'noleggio'])->name('noleggio');
    Route::get('/noleggio/{id}', [PublicSiteController::class, 'noleggioShow'])->name('noleggio.show');
    Route::post('/noleggio/{id}/prenota', [PublicSiteController::class, 'noleggioBooking'])->name('noleggio.booking');

    // ── Pagine aziendali ──────────────────────────────────
    Route::get('/chi-siamo', [PublicSiteController::class, 'chiSiamo'])->name('chi_siamo');
    Route::get('/servizi', [PublicSiteController::class, 'servizi'])->name('servizi');
    Route::get('/contatti', [PublicSiteController::class, 'contatti'])->name('contatti');
    Route::post('/contatti', [PublicSiteController::class, 'contattiSend'])->name('contatti.send');

    // ── Pagine legali ─────────────────────────────────────
    Route::get('/privacy-policy', [PublicSiteController::class, 'privacy'])->name('privacy');
    Route::get('/cookie-policy', [PublicSiteController::class, 'cookiePolicy'])->name('cookie_policy');
    Route::get('/termini-vendita', [PublicSiteController::class, 'terminiVendita'])->name('termini_vendita');
    Route::get('/termini-noleggio', [PublicSiteController::class, 'terminiNoleggio'])->name('termini_noleggio');
};

if (app()->environment('production')) {
    // PROD: alla root del dominio alecar.it (URL puliti)
    Route::domain('www.alecar.it')->name('public.')->group($siteRoutes);
    Route::domain('alecar.it')->name('public.')->group($siteRoutes);
} else {
    // DEV/locale: prefix "/sito" per non collidere con la dashboard ERP
    Route::prefix('sito')->name('public.')->group($siteRoutes);
}
