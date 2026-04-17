<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicVehicleController;
use App\Http\Controllers\PublicSiteController;

Route::prefix('sito')->group(function () {

    // ── Homepage ──────────────────────────────────────────
    Route::get('/', [PublicSiteController::class, 'home'])->name('public.home');

    // ── Auto in vendita ───────────────────────────────────
    Route::get('/auto-in-vendita', [PublicVehicleController::class, 'index'])->name('public.vehicles.index');
    Route::get('/auto-in-vendita/{id}-{slug?}', [PublicVehicleController::class, 'show'])->name('public.vehicles.show');
    Route::post('/auto-in-vendita/{vehicle}/contatto', [PublicVehicleController::class, 'contact'])->name('public.vehicles.contact');

    // ── Noleggio ──────────────────────────────────────────
    Route::get('/noleggio', [PublicSiteController::class, 'noleggio'])->name('public.noleggio');
    Route::get('/noleggio/{id}', [PublicSiteController::class, 'noleggioShow'])->name('public.noleggio.show');
    Route::post('/noleggio/{id}/prenota', [PublicSiteController::class, 'noleggioBooking'])->name('public.noleggio.booking');

    // ── Pagine aziendali ──────────────────────────────────
    Route::get('/chi-siamo', [PublicSiteController::class, 'chiSiamo'])->name('public.chi_siamo');
    Route::get('/servizi', [PublicSiteController::class, 'servizi'])->name('public.servizi');
    Route::get('/contatti', [PublicSiteController::class, 'contatti'])->name('public.contatti');
    Route::post('/contatti', [PublicSiteController::class, 'contattiSend'])->name('public.contatti.send');

    // ── Pagine legali ─────────────────────────────────────
    Route::get('/privacy-policy', [PublicSiteController::class, 'privacy'])->name('public.privacy');
    Route::get('/cookie-policy', [PublicSiteController::class, 'cookiePolicy'])->name('public.cookie_policy');
    Route::get('/termini-vendita', [PublicSiteController::class, 'terminiVendita'])->name('public.termini_vendita');
    Route::get('/termini-noleggio', [PublicSiteController::class, 'terminiNoleggio'])->name('public.termini_noleggio');

});