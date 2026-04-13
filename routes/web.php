<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, CustomerController, VehicleController,
    ClaimController, PersonalInjuryController, ExpertController,
    WorkOrderController, QuoteController, FleetVehicleController,
    RentalController, DocumentController, MailController,
    SparePartController, WincarImportController, VinDecoderController,
    ProfileController
};

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Clienti
    Route::resource('clienti', CustomerController::class);
    Route::get('clienti/{customer}/storico', [CustomerController::class, 'storico'])->name('clienti.storico');

    // Veicoli
    Route::resource('veicoli', VehicleController::class);
    Route::post('veicoli/{vehicle}/foto', [VehicleController::class, 'uploadFoto'])->name('veicoli.foto');

    // Sinistri
    Route::resource('sinistri', ClaimController::class);
    Route::post('sinistri/{claim}/stato', [ClaimController::class, 'updateStato'])->name('sinistri.stato');
    Route::post('sinistri/{claim}/mail', [ClaimController::class, 'sendMail'])->name('sinistri.mail');
    Route::post('sinistri/{claim}/documento', [ClaimController::class, 'uploadDoc'])->name('sinistri.documento');

    // Lesioni & Periti
    Route::resource('lesioni', PersonalInjuryController::class);
    Route::resource('periti', ExpertController::class);

    // Lavorazioni
    Route::resource('lavorazioni', WorkOrderController::class);
    Route::post('lavorazioni/{lavorazioni}/stato', [WorkOrderController::class, 'updateStato'])->name('lavorazioni.stato');
    Route::post('lavorazioni/{lavorazioni}/progresso', [WorkOrderController::class, 'updateProgresso'])->name('lavorazioni.progresso');

    // Preventivi
    Route::resource('preventivi', QuoteController::class);
    Route::post('preventivi/{quote}/converti', [QuoteController::class, 'convertToJob'])->name('preventivi.converti');

    // Flotta & Noleggio
    Route::resource('flotta', FleetVehicleController::class);
    Route::resource('noleggio', RentalController::class);
    Route::post('noleggio/{noleggio}/chiudi', [RentalController::class, 'chiudi'])->name('noleggio.chiudi');
    Route::resource('sostitutive', RentalController::class);

    // Documenti
    Route::resource('documenti', DocumentController::class);
    Route::post('documenti/{document}/pagato', [DocumentController::class, 'markPagato'])->name('documenti.pagato');

    // Mail
    Route::get('mail', [MailController::class, 'index'])->name('mail.index');
    Route::get('mail/template/create', [MailController::class, 'create'])->name('mail.template.create');
    Route::post('mail/template', [MailController::class, 'store'])->name('mail.template.store');

    // Ricambi
    Route::resource('ricambi', SparePartController::class);

    // Import Wincar
    Route::get('import/wincar', [WincarImportController::class, 'index'])->name('import.wincar');
    Route::post('import/wincar', [WincarImportController::class, 'upload'])->name('import.wincar.upload');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // VIN Decoder API (NHTSA)
    Route::post('/api/vin/decode', [VinDecoderController::class, 'decode'])->name('vin.decode');

    // Marketplace
    require __DIR__.'/marketplace.php';
});

// Pagine pubbliche (senza auth)
require __DIR__.'/public.php';