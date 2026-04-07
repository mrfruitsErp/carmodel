<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicVehicleController;

Route::prefix('auto-in-vendita')->name('public.vehicles.')->group(function () {
    Route::get('/', [PublicVehicleController::class, 'index'])->name('index');
    Route::get('/{id}-{slug}', [PublicVehicleController::class, 'show'])->name('show');
    Route::post('/{vehicle}/contatto', [PublicVehicleController::class, 'contact'])->name('contact');
});