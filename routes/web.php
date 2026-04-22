<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, CustomerController, VehicleController,
    ClaimController, PersonalInjuryController, ExpertController,
    WorkOrderController, QuoteController, FleetVehicleController,
    RentalController, DocumentController, MailController,
    SparePartController, WincarImportController, VinDecoderController,
    UserController,
    FascicoloController,
    SettingController,
    DocumentoCatalogoController
};
use App\Http\Controllers\Portale\PortaleClienteController;

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
    Route::get('sinistri/export', [ClaimController::class, 'export'])->name('sinistri.export');
    Route::resource('sinistri', ClaimController::class);
    Route::post('sinistri/{claim}/stato', [ClaimController::class, 'updateStato'])->name('sinistri.stato');
    Route::post('sinistri/{claim}/mail', [ClaimController::class, 'sendMail'])->name('sinistri.mail');
    Route::post('sinistri/{claim}/documento', [ClaimController::class, 'uploadDoc'])->name('sinistri.documento');

    // Lesioni & Esperti
    Route::resource('lesioni', PersonalInjuryController::class);
    Route::resource('esperti', ExpertController::class)->names('periti');
    Route::resource('liquidatori', ExpertController::class);
    Route::resource('medici', ExpertController::class);

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
    Route::get('/profile', function() { return redirect()->route('dashboard'); })->name('profile.edit');

    // VIN Decoder API
    Route::post('/api/vin/decode', [VinDecoderController::class, 'decode'])->name('vin.decode');

    // Utenti & Accessi
    Route::get('utenti/accessi', [UserController::class, 'accessLog'])->name('users.access_log');
    Route::get('utenti/create', [UserController::class, 'create'])->name('users.create');
    Route::get('utenti', [UserController::class, 'index'])->name('users.index');
    Route::post('utenti', [UserController::class, 'store'])->name('users.store');
    Route::get('utenti/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('utenti/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('utenti/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('utenti/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');

    // ──────────────────────────────────────────
    // FASCICOLI
    // ──────────────────────────────────────────
    Route::resource('fascicoli', FascicoloController::class);
    Route::post('fascicoli/{fascicolo}/genera-link',       [FascicoloController::class, 'generaLink'])->name('fascicoli.genera-link');
    Route::post('fascicoli/{fascicolo}/disattiva-link',    [FascicoloController::class, 'disattivaLink'])->name('fascicoli.disattiva-link');
    Route::post('fascicoli/{fascicolo}/popola-documenti',  [FascicoloController::class, 'popolaDocumenti'])->name('fascicoli.popola-documenti');
    Route::post('fascicoli/{fascicolo}/completa',          [FascicoloController::class, 'segnaCompletato'])->name('fascicoli.completa');
    Route::post('fascicoli/{fascicolo}/documenti',                              [FascicoloController::class, 'aggiungiDocumento'])->name('fascicoli.documenti.store');
    Route::patch('fascicoli/{fascicolo}/documenti/{documento}',                 [FascicoloController::class, 'aggiornaDocumento'])->name('fascicoli.documenti.update');
    Route::delete('fascicoli/{fascicolo}/documenti/{documento}',                [FascicoloController::class, 'rimuoviDocumento'])->name('fascicoli.documenti.destroy');
    Route::delete('fascicoli/{fascicolo}/media/{media}',                        [FascicoloController::class, 'destroyMedia'])->name('fascicoli.media.destroy');

    // ──────────────────────────────────────────
    // SETTINGS
    // ──────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                   [SettingController::class, 'index'])->name('index');
        Route::get('/{gruppo}',           [SettingController::class, 'gruppo'])->name('gruppo');
        Route::post('/{gruppo}',          [SettingController::class, 'salva'])->name('salva');
        Route::post('/permessi/aggiorna', [SettingController::class, 'aggiornaPermessi'])->name('permessi.aggiorna');
    });

    // ──────────────────────────────────────────
    // CATALOGO DOCUMENTI
    // ──────────────────────────────────────────
    Route::resource('documenti-catalogo', DocumentoCatalogoController::class);

    // Marketplace
    require __DIR__.'/marketplace.php';
});

// ──────────────────────────────────────────
// PORTALE CLIENTE — pubblico con token
// ──────────────────────────────────────────
Route::prefix('portale')->name('portale.')->group(function () {
    Route::get('/{token}',                           [PortaleClienteController::class, 'accesso'])->name('accesso');
    Route::post('/{token}/verifica',                 [PortaleClienteController::class, 'verificaIdentita'])->name('verifica');
    Route::get('/{token}/otp',                       [PortaleClienteController::class, 'otpForm'])->name('otp');
    Route::post('/{token}/otp',                      [PortaleClienteController::class, 'verificaOtp'])->name('otp.verifica');
    Route::post('/{token}/otp/reinvia',              [PortaleClienteController::class, 'reinviaOtp'])->name('otp.reinvia');
    Route::get('/{token}/privacy',                   [PortaleClienteController::class, 'privacy'])->name('privacy');
    Route::post('/{token}/privacy',                  [PortaleClienteController::class, 'accettaPrivacy'])->name('privacy.accetta');
    Route::get('/{token}/documenti',                 [PortaleClienteController::class, 'documenti'])->name('documenti');
    Route::post('/{token}/documenti/{doc}/upload',   [PortaleClienteController::class, 'uploadDocumento'])->name('documenti.upload');
    Route::post('/{token}/documenti/{doc}/firma',    [PortaleClienteController::class, 'firmaDocumento'])->name('documenti.firma');
    Route::post('/{token}/documenti/{doc}/otp-firma',[PortaleClienteController::class, 'verificaFirmaOtp'])->name('documenti.firma.otp');
    Route::post('/{token}/completa',                 [PortaleClienteController::class, 'completaFascicolo'])->name('completa');
});

// Pagine pubbliche (senza auth)
require __DIR__.'/public.php';