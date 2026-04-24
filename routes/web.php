<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, CustomerController, VehicleController,
    ClaimController, PersonalInjuryController, ExpertController,
    WorkOrderController, QuoteController, FleetVehicleController,
    RentalController, DocumentController, MailController,
    SparePartController, VinDecoderController, UserController,
    FascicoloController, SettingController, DocumentoCatalogoController,
    InsuranceCompanyController
};
use App\Http\Controllers\Portale\PortaleClienteController;

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CLIENTI
    Route::get('clienti/cestino', [CustomerController::class, 'cestino'])->name('clienti.cestino');
    Route::post('clienti/{id}/ripristina', [CustomerController::class, 'ripristina'])->name('clienti.ripristina');
    Route::resource('clienti', CustomerController::class)->parameters(['clienti' => 'customer']);
    Route::get('clienti/{customer}/storico', [CustomerController::class, 'storico'])->name('clienti.storico');

    // VEICOLI
    Route::resource('veicoli', VehicleController::class);
    Route::post('veicoli/{vehicle}/foto', [VehicleController::class, 'uploadFoto'])->name('veicoli.foto');
    Route::post('veicoli/{vehicle}/documento', [VehicleController::class, 'uploadDocumento'])->name('veicoli.documento');
    Route::delete('veicoli/{vehicle}/documento/{docId}', [VehicleController::class, 'deleteDocumento'])->name('veicoli.documento.delete');
    Route::post('veicoli/{vehicle}/scan-libretto', [VehicleController::class, 'scanLibretto'])->name('veicoli.scan-libretto');

    // SINISTRI
    Route::get('sinistri/export', [ClaimController::class, 'export'])->name('sinistri.export');
    Route::resource('sinistri', ClaimController::class);
    Route::post('sinistri/{claim}/stato', [ClaimController::class, 'updateStato'])->name('sinistri.stato');
    Route::post('sinistri/{claim}/mail', [ClaimController::class, 'sendMail'])->name('sinistri.mail');
    Route::post('sinistri/{claim}/documento', [ClaimController::class, 'uploadDoc'])->name('sinistri.documento');

    // LESIONI
    Route::resource('lesioni', PersonalInjuryController::class);

    // ESPERTI
    Route::resource('esperti', ExpertController::class)->names('periti');
    Route::resource('liquidatori', ExpertController::class);
    Route::resource('medici', ExpertController::class);

    // ASSICURAZIONI
    Route::resource('assicurazioni', InsuranceCompanyController::class);
    Route::get('assicurazioni/{assicurazioni}/periti', [InsuranceCompanyController::class, 'periti'])->name('assicurazioni.periti');

    // LAVORAZIONI
    Route::resource('lavorazioni', WorkOrderController::class);
    Route::post('lavorazioni/{lavorazioni}/stato', [WorkOrderController::class, 'updateStato'])->name('lavorazioni.stato');
    Route::post('lavorazioni/{lavorazioni}/progresso', [WorkOrderController::class, 'updateProgresso'])->name('lavorazioni.progresso');

    // PREVENTIVI
    Route::resource('preventivi', QuoteController::class);
    Route::post('preventivi/{quote}/converti', [QuoteController::class, 'convertToJob'])->name('preventivi.converti');

    // FLOTTA & NOLEGGIO
    Route::resource('flotta', FleetVehicleController::class);
    Route::resource('noleggio', RentalController::class);
    Route::post('noleggio/{noleggio}/chiudi', [RentalController::class, 'chiudi'])->name('noleggio.chiudi');
    Route::resource('sostitutive', RentalController::class);

    // DOCUMENTI
    Route::resource('documenti', DocumentController::class);
    Route::post('documenti/{document}/pagato', [DocumentController::class, 'markPagato'])->name('documenti.pagato');

    // MAIL
    Route::get('mail', [MailController::class, 'index'])->name('mail.index');
    Route::get('mail/template/create', [MailController::class, 'create'])->name('mail.template.create');
    Route::post('mail/template', [MailController::class, 'store'])->name('mail.template.store');

    // RICAMBI
    Route::resource('ricambi', SparePartController::class);

    // UTENTI
    Route::get('utenti/accessi', [UserController::class, 'accessLog'])->name('utenti.access_log');
    Route::resource('utenti', UserController::class)->names('utenti');
    Route::post('utenti/{user}/toggle', [UserController::class, 'toggleActive'])->name('utenti.toggle');

    // FASCICOLI
    Route::resource('fascicoli', FascicoloController::class);
    Route::post('fascicoli/{fascicolo}/genera-link', [FascicoloController::class, 'generaLink'])->name('fascicoli.genera-link');
    Route::post('fascicoli/{fascicolo}/disattiva-link', [FascicoloController::class, 'disattivaLink'])->name('fascicoli.disattiva-link');
    Route::post('fascicoli/{fascicolo}/popola-documenti', [FascicoloController::class, 'popolaDocumenti'])->name('fascicoli.popola-documenti');
    Route::post('fascicoli/{fascicolo}/completa', [FascicoloController::class, 'segnaCompletato'])->name('fascicoli.completa');

    // SETTINGS
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::get('/mail/test', [SettingController::class, 'testMail'])->name('mail.test');
        Route::post('/permessi/aggiorna', [SettingController::class, 'aggiornaPermessi'])->name('permessi.aggiorna');
        Route::get('/{gruppo}', [SettingController::class, 'gruppo'])->name('gruppo');
        Route::post('/{gruppo}', [SettingController::class, 'salva'])->name('salva');
    });

    // CATALOGO DOCUMENTI
    Route::resource('documenti-catalogo', DocumentoCatalogoController::class)
        ->parameters(['documenti-catalogo' => 'documentoCatalogo']);

    require __DIR__.'/marketplace.php';
});

// PORTALE PUBBLICO
Route::prefix('portale')->name('portale.')->group(function () {
    Route::get('/{token}', [PortaleClienteController::class, 'accesso'])->name('accesso');
    Route::post('/{token}/verifica', [PortaleClienteController::class, 'verificaIdentita'])->name('verifica');
    Route::get('/{token}/otp', [PortaleClienteController::class, 'otpForm'])->name('otp');
    Route::post('/{token}/otp', [PortaleClienteController::class, 'verificaOtp'])->name('otp.verifica');
    Route::post('/{token}/otp/reinvia', [PortaleClienteController::class, 'reinviaOtp'])->name('otp.reinvia');
    Route::get('/{token}/privacy', [PortaleClienteController::class, 'privacy'])->name('privacy');
    Route::post('/{token}/privacy', [PortaleClienteController::class, 'accettaPrivacy'])->name('privacy.accetta');
    Route::get('/{token}/documenti', [PortaleClienteController::class, 'documenti'])->name('documenti');
});

require __DIR__.'/public.php';