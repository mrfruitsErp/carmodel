<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleVehicleController;
use App\Http\Controllers\MarketplaceController;

Route::middleware(['auth'])->prefix('marketplace')->name('marketplace.')->group(function () {

    // â”€â”€ Dashboard â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('/', [MarketplaceController::class, 'dashboard'])->name('dashboard');

    // â”€â”€ Veicoli in vendita â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::resource('vehicles', SaleVehicleController::class, [
        'parameters' => ['vehicles' => 'saleVehicle'],
    ])->names([
        'index'   => 'vehicles.index',
        'create'  => 'vehicles.create',
        'store'   => 'vehicles.store',
        'show'    => 'vehicles.show',
        'edit'    => 'vehicles.edit',
        'update'  => 'vehicles.update',
        'destroy' => 'vehicles.destroy',
    ]);

    // Foto
    Route::post('vehicles/{saleVehicle}/foto',           [SaleVehicleController::class, 'uploadFoto'])->name('vehicles.foto.upload');
    Route::delete('vehicles/{saleVehicle}/foto/{media}', [SaleVehicleController::class, 'deleteFoto'])->name('vehicles.foto.delete');
    Route::post('vehicles/{saleVehicle}/foto/reorder',   [SaleVehicleController::class, 'reorderFoto'])->name('vehicles.foto.reorder');

    // Venduto
    Route::post('vehicles/{saleVehicle}/status',[SaleVehicleController::class,'changeStatus'])->name('vehicles.status');
    Route::post('vehicles/{saleVehicle}/sold', [SaleVehicleController::class, 'markSold'])->name('vehicles.sold');

    // â”€â”€ Pubblicazione â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::post('vehicles/{saleVehicle}/publish',  [MarketplaceController::class, 'publish'])->name('publish');
    Route::delete('listings/{listing}/unpublish',  [MarketplaceController::class, 'unpublish'])->name('unpublish');
    Route::post('vehicles/{saleVehicle}/price',    [MarketplaceController::class, 'updatePrice'])->name('update-price');

    // â”€â”€ Lead â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('leads',              [MarketplaceController::class, 'leads'])->name('leads.index');
    Route::patch('leads/{lead}',     [MarketplaceController::class, 'updateLead'])->name('leads.update');

    // â”€â”€ Impostazioni â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::get('settings',                           [MarketplaceController::class, 'settings'])->name('settings');
    Route::post('settings/{platform}/credentials',   [MarketplaceController::class, 'saveCredentials'])->name('settings.credentials');
    Route::get('settings/{platform}/test',           [MarketplaceController::class, 'testConnection'])->name('settings.test');

    // â”€â”€ Sync manuali â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::post('sync/stats', [MarketplaceController::class, 'syncStats'])->name('sync.stats');
    Route::post('sync/leads', [MarketplaceController::class, 'syncLeads'])->name('sync.leads');
});