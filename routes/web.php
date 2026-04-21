<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, CustomerController, VehicleController,
    ClaimController, PersonalInjuryController, ExpertController,
    WorkOrderController, QuoteController, FleetVehicleController,
    RentalController, DocumentController, MailController,
    SparePartController, WincarImportController, VinDecoderController,
    UserController
};

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Clienti
    Route::middleware('permission:clienti.view')->group(function () {
        Route::resource('clienti', CustomerController::class);
        Route::get('clienti/{customer}/storico', [CustomerController::class, 'storico'])->name('clienti.storico');
    });

    // Veicoli
    Route::middleware('permission:veicoli.view')->group(function () {
        Route::resource('veicoli', VehicleController::class);
        Route::post('veicoli/{vehicle}/foto', [VehicleController::class, 'uploadFoto'])->name('veicoli.foto');
    });

    // Sinistri
    Route::middleware('permission:sinistri.view')->group(function () {
        Route::resource('sinistri', ClaimController::class);
        Route::post('sinistri/{claim}/stato', [ClaimController::class, 'updateStato'])->name('sinistri.stato');
        Route::post('sinistri/{claim}/mail', [ClaimController::class, 'sendMail'])->name('sinistri.mail');
        Route::post('sinistri/{claim}/documento', [ClaimController::class, 'uploadDoc'])->name('sinistri.documento');
    });

    // Lesioni & Periti
    Route::middleware('permission:lesioni.view')->group(function () {
        Route::resource('lesioni', PersonalInjuryController::class);
    });
    Route::middleware('permission:periti.view')->group(function () {
        Route::resource('periti', ExpertController::class);
    Route::resource('liquidatori', ExpertController::class);
    Route::resource('medici', ExpertController::class);
    });

    // Lavorazioni
    Route::middleware('permission:lavorazioni.view')->group(function () {
        Route::resource('lavorazioni', WorkOrderController::class);
        Route::post('lavorazioni/{lavorazioni}/stato', [WorkOrderController::class, 'updateStato'])->name('lavorazioni.stato');
        Route::post('lavorazioni/{lavorazioni}/progresso', [WorkOrderController::class, 'updateProgresso'])->name('lavorazioni.progresso');
    });

    // Preventivi
    Route::middleware('permission:preventivi.view')->group(function () {
        Route::resource('preventivi', QuoteController::class);
        Route::post('preventivi/{quote}/converti', [QuoteController::class, 'convertToJob'])->name('preventivi.converti');
    });

    // Flotta & Noleggio
    Route::middleware('permission:noleggio.view')->group(function () {
        Route::resource('flotta', FleetVehicleController::class);
        Route::resource('noleggio', RentalController::class);
        Route::post('noleggio/{noleggio}/chiudi', [RentalController::class, 'chiudi'])->name('noleggio.chiudi');
        Route::resource('sostitutive', RentalController::class);
    });

    // Documenti
    Route::middleware('permission:fatture.view')->group(function () {
        Route::resource('documenti', DocumentController::class);
        Route::post('documenti/{document}/pagato', [DocumentController::class, 'markPagato'])->name('documenti.pagato');
    });

    // Mail
    Route::get('mail', [MailController::class, 'index'])->name('mail.index');
    Route::get('mail/template/create', [MailController::class, 'create'])->name('mail.template.create');
    Route::post('mail/template', [MailController::class, 'store'])->name('mail.template.store');

    // Ricambi
    Route::middleware('permission:ricambi.view')->group(function () {
        Route::resource('ricambi', SparePartController::class);
    });

    // Import Wincar
    Route::get('import/wincar', [WincarImportController::class, 'index'])->name('import.wincar');
    Route::post('import/wincar', [WincarImportController::class, 'upload'])->name('import.wincar.upload');

    // Profile
    Route::get('/profile', function() { return redirect()->route('dashboard'); })->name('profile.edit');

    // VIN Decoder API
    Route::post('/api/vin/decode', [VinDecoderController::class, 'decode'])->name('vin.decode');

    // Utenti & Accessi
    Route::middleware('permission:utenti.manage')->group(function () {
        Route::get('utenti/accessi', [UserController::class, 'accessLog'])->name('users.access_log');
        Route::get('utenti/create', [UserController::class, 'create'])->name('users.create');
        Route::get('utenti', [UserController::class, 'index'])->name('users.index');
        Route::post('utenti', [UserController::class, 'store'])->name('users.store');
        Route::get('utenti/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('utenti/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('utenti/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('utenti/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    });

    // Marketplace
    require __DIR__.'/marketplace.php';
});

// Pagine pubbliche (senza auth)
require __DIR__.'/public.php';

