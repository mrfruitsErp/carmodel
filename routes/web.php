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

/**
 * Tutte le route ERP (login + dashboard + admin) sono raggruppate
 * in una closure che viene applicata a:
 *  - PRODUZIONE → Route::domain('erp.alecar.it')   (gestionale dedicato)
 *                  + IP server 142.93.99.245       (debug diretto)
 *  - LOCALE     → nessun filtro dominio            (carmodel.local serve tutto)
 */
$erpRoutes = function () {
    Route::middleware(['auth'])->group(function () {

        // Dashboard: accessibile a chiunque sia loggato
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ─── CLIENTI (view obbligatorio per accedere, edit per POST/PUT/DELETE) ───
        Route::middleware('cando:clienti.view')->group(function () {
            Route::get('clienti', [CustomerController::class, 'index'])->name('clienti.index');
            Route::get('clienti/cestino', [CustomerController::class, 'cestino'])->name('clienti.cestino');
            Route::get('clienti/{customer}', [CustomerController::class, 'show'])->name('clienti.show');
            Route::get('clienti/{customer}/storico', [CustomerController::class, 'storico'])->name('clienti.storico');
        });
        Route::middleware('cando:clienti.edit')->group(function () {
            Route::get('clienti/create', [CustomerController::class, 'create'])->name('clienti.create');
            Route::post('clienti', [CustomerController::class, 'store'])->name('clienti.store');
            Route::get('clienti/{customer}/edit', [CustomerController::class, 'edit'])->name('clienti.edit');
            Route::put('clienti/{customer}', [CustomerController::class, 'update'])->name('clienti.update');
            Route::patch('clienti/{customer}', [CustomerController::class, 'update']);
            Route::delete('clienti/{customer}', [CustomerController::class, 'destroy'])->name('clienti.destroy');
            Route::post('clienti/{id}/ripristina', [CustomerController::class, 'ripristina'])->name('clienti.ripristina');
        });

        // ─── VEICOLI ───
        Route::middleware('cando:veicoli.view')->group(function () {
            Route::get('veicoli', [VehicleController::class, 'index'])->name('veicoli.index');
            Route::get('veicoli/{veicoli}', [VehicleController::class, 'show'])->name('veicoli.show');
        });
        Route::middleware('cando:veicoli.edit')->group(function () {
            Route::get('veicoli/create', [VehicleController::class, 'create'])->name('veicoli.create');
            Route::post('veicoli', [VehicleController::class, 'store'])->name('veicoli.store');
            Route::get('veicoli/{veicoli}/edit', [VehicleController::class, 'edit'])->name('veicoli.edit');
            Route::put('veicoli/{veicoli}', [VehicleController::class, 'update'])->name('veicoli.update');
            Route::patch('veicoli/{veicoli}', [VehicleController::class, 'update']);
            Route::delete('veicoli/{veicoli}', [VehicleController::class, 'destroy'])->name('veicoli.destroy');
            Route::post('veicoli/scan-libretto-nuovo', [VehicleController::class, 'scanLibrettoNuovo'])->name('veicoli.scan-libretto-nuovo');
            Route::post('veicoli/{vehicle}/foto', [VehicleController::class, 'uploadFoto'])->name('veicoli.foto');
            Route::post('veicoli/{vehicle}/documento', [VehicleController::class, 'uploadDocumento'])->name('veicoli.documento');
            Route::delete('veicoli/{vehicle}/documento/{docId}', [VehicleController::class, 'deleteDocumento'])->name('veicoli.documento.delete');
            Route::post('veicoli/{vehicle}/scan-libretto', [VehicleController::class, 'scanLibretto'])->name('veicoli.scan-libretto');
            Route::post('veicoli/{vehicle}/applica-libretto', [VehicleController::class, 'applicaLibretto'])->name('veicoli.applica-libretto');
        });

        // ─── SINISTRI ───
        Route::middleware('cando:sinistri.view')->group(function () {
            Route::get('sinistri/export', [ClaimController::class, 'export'])->name('sinistri.export');
            Route::get('sinistri', [ClaimController::class, 'index'])->name('sinistri.index');
            Route::get('sinistri/{sinistri}', [ClaimController::class, 'show'])->name('sinistri.show');
        });
        Route::middleware('cando:sinistri.edit')->group(function () {
            Route::get('sinistri/create', [ClaimController::class, 'create'])->name('sinistri.create');
            Route::post('sinistri', [ClaimController::class, 'store'])->name('sinistri.store');
            Route::get('sinistri/{sinistri}/edit', [ClaimController::class, 'edit'])->name('sinistri.edit');
            Route::put('sinistri/{sinistri}', [ClaimController::class, 'update'])->name('sinistri.update');
            Route::patch('sinistri/{sinistri}', [ClaimController::class, 'update']);
            Route::delete('sinistri/{sinistri}', [ClaimController::class, 'destroy'])->name('sinistri.destroy');
            Route::post('sinistri/{claim}/stato', [ClaimController::class, 'updateStato'])->name('sinistri.stato');
            Route::post('sinistri/{claim}/mail', [ClaimController::class, 'sendMail'])->name('sinistri.mail');
            Route::post('sinistri/{claim}/documento', [ClaimController::class, 'uploadDoc'])->name('sinistri.documento');
        });

        // ─── LESIONI ───
        Route::middleware('cando:lesioni.view')->group(function () {
            Route::get('lesioni', [PersonalInjuryController::class, 'index'])->name('lesioni.index');
            Route::get('lesioni/{lesioni}', [PersonalInjuryController::class, 'show'])->name('lesioni.show');
        });
        Route::middleware('cando:lesioni.edit')->group(function () {
            Route::get('lesioni/create', [PersonalInjuryController::class, 'create'])->name('lesioni.create');
            Route::post('lesioni', [PersonalInjuryController::class, 'store'])->name('lesioni.store');
            Route::get('lesioni/{lesioni}/edit', [PersonalInjuryController::class, 'edit'])->name('lesioni.edit');
            Route::put('lesioni/{lesioni}', [PersonalInjuryController::class, 'update'])->name('lesioni.update');
            Route::patch('lesioni/{lesioni}', [PersonalInjuryController::class, 'update']);
            Route::delete('lesioni/{lesioni}', [PersonalInjuryController::class, 'destroy'])->name('lesioni.destroy');
        });

        // ─── ESPERTI / PERITI / LIQUIDATORI / MEDICI ───
        Route::middleware('cando:periti.view')->group(function () {
            Route::resource('esperti', ExpertController::class)->only(['index','show'])->names('periti');
            Route::resource('liquidatori', ExpertController::class)->only(['index','show']);
            Route::resource('medici', ExpertController::class)->only(['index','show']);
        });
        Route::middleware('cando:periti.edit')->group(function () {
            Route::resource('esperti', ExpertController::class)->except(['index','show'])->names('periti');
            Route::resource('liquidatori', ExpertController::class)->except(['index','show']);
            Route::resource('medici', ExpertController::class)->except(['index','show']);
        });

        // ─── ASSICURAZIONI (parte del modulo periti) ───
        Route::middleware('cando:periti.view')->group(function () {
            Route::get('assicurazioni', [InsuranceCompanyController::class, 'index'])->name('assicurazioni.index');
            Route::get('assicurazioni/{assicurazioni}', [InsuranceCompanyController::class, 'show'])->name('assicurazioni.show');
            Route::get('assicurazioni/{assicurazioni}/periti', [InsuranceCompanyController::class, 'periti'])->name('assicurazioni.periti');
        });
        Route::middleware('cando:periti.edit')->group(function () {
            Route::resource('assicurazioni', InsuranceCompanyController::class)->except(['index','show']);
        });

        // ─── LAVORAZIONI ───
        Route::middleware('cando:lavorazioni.view')->group(function () {
            Route::get('lavorazioni', [WorkOrderController::class, 'index'])->name('lavorazioni.index');
            Route::get('lavorazioni/{lavorazioni}', [WorkOrderController::class, 'show'])->name('lavorazioni.show');
        });
        Route::middleware('cando:lavorazioni.edit')->group(function () {
            Route::resource('lavorazioni', WorkOrderController::class)->except(['index','show']);
            Route::post('lavorazioni/{lavorazioni}/stato', [WorkOrderController::class, 'updateStato'])->name('lavorazioni.stato');
            Route::post('lavorazioni/{lavorazioni}/progresso', [WorkOrderController::class, 'updateProgresso'])->name('lavorazioni.progresso');
        });

        // ─── PREVENTIVI ───
        Route::middleware('cando:preventivi.view')->group(function () {
            Route::get('preventivi', [QuoteController::class, 'index'])->name('preventivi.index');
            Route::get('preventivi/{preventivi}', [QuoteController::class, 'show'])->name('preventivi.show');
        });
        Route::middleware('cando:preventivi.edit')->group(function () {
            Route::resource('preventivi', QuoteController::class)->except(['index','show']);
            Route::post('preventivi/{quote}/converti', [QuoteController::class, 'convertToJob'])->name('preventivi.converti');
        });

        // ─── FLOTTA & NOLEGGIO ───
        Route::middleware('cando:noleggio.view')->group(function () {
            Route::get('flotta', [FleetVehicleController::class, 'index'])->name('flotta.index');
            Route::get('flotta/{flotta}', [FleetVehicleController::class, 'show'])->name('flotta.show');
            Route::get('noleggio', [RentalController::class, 'index'])->name('noleggio.index');
            Route::get('noleggio/{noleggio}', [RentalController::class, 'show'])->name('noleggio.show');
            Route::get('sostitutive', [RentalController::class, 'index'])->name('sostitutive.index');
            Route::get('sostitutive/{sostitutive}', [RentalController::class, 'show'])->name('sostitutive.show');
        });
        Route::middleware('cando:noleggio.edit')->group(function () {
            Route::resource('flotta', FleetVehicleController::class)->except(['index','show']);
            Route::resource('noleggio', RentalController::class)->except(['index','show']);
            Route::post('noleggio/{noleggio}/chiudi', [RentalController::class, 'chiudi'])->name('noleggio.chiudi');
            Route::resource('sostitutive', RentalController::class)->except(['index','show']);
        });

        // ─── DOCUMENTI / FATTURE ───
        Route::middleware('cando:fatture.view')->group(function () {
            Route::get('documenti', [DocumentController::class, 'index'])->name('documenti.index');
            Route::get('documenti/{documenti}', [DocumentController::class, 'show'])->name('documenti.show');
        });
        Route::middleware('cando:fatture.edit')->group(function () {
            Route::resource('documenti', DocumentController::class)->except(['index','show']);
            Route::post('documenti/{document}/pagato', [DocumentController::class, 'markPagato'])->name('documenti.pagato');
        });

        // ─── MAIL (visibile a tutti gli utenti loggati) ───
        Route::get('mail', [MailController::class, 'index'])->name('mail.index');
        Route::get('mail/template/create', [MailController::class, 'create'])->name('mail.template.create');
        Route::post('mail/template', [MailController::class, 'store'])->name('mail.template.store');

        // ─── RICAMBI ───
        Route::middleware('cando:ricambi.view')->group(function () {
            Route::get('ricambi', [SparePartController::class, 'index'])->name('ricambi.index');
            Route::get('ricambi/{ricambi}', [SparePartController::class, 'show'])->name('ricambi.show');
        });
        Route::middleware('cando:ricambi.edit')->group(function () {
            Route::resource('ricambi', SparePartController::class)->except(['index','show']);
        });

        // ─── UTENTI (solo admin/manager) ───
        Route::middleware('cando:utenti.manage')->group(function () {
            Route::get('utenti/accessi', [UserController::class, 'accessLog'])->name('utenti.access_log');
            Route::resource('utenti', UserController::class)->names('utenti');
            Route::post('utenti/{user}/toggle', [UserController::class, 'toggleActive'])->name('utenti.toggle');
        });

        // ─── FASCICOLI (parte del modulo clienti) ───
        Route::middleware('cando:clienti.view')->group(function () {
            Route::get('fascicoli', [FascicoloController::class, 'index'])->name('fascicoli.index');
            Route::get('fascicoli/{fascicolo}', [FascicoloController::class, 'show'])->name('fascicoli.show');
        });
        Route::middleware('cando:clienti.edit')->group(function () {
            Route::resource('fascicoli', FascicoloController::class)->except(['index','show']);
            Route::post('fascicoli/{fascicolo}/genera-link', [FascicoloController::class, 'generaLink'])->name('fascicoli.genera-link');
            Route::post('fascicoli/{fascicolo}/disattiva-link', [FascicoloController::class, 'disattivaLink'])->name('fascicoli.disattiva-link');
            Route::post('fascicoli/{fascicolo}/popola-documenti', [FascicoloController::class, 'popolaDocumenti'])->name('fascicoli.popola-documenti');
            Route::post('fascicoli/{fascicolo}/completa', [FascicoloController::class, 'segnaCompletato'])->name('fascicoli.completa');
        });

        // ─── SETTINGS (solo chi può gestire impostazioni) ───
        Route::middleware('cando:impostazioni.manage')->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::get('/mail/test', [SettingController::class, 'testMail'])->name('mail.test');
            Route::post('/permessi/aggiorna', [SettingController::class, 'aggiornaPermessi'])->name('permessi.aggiorna');
            Route::get('/{gruppo}', [SettingController::class, 'gruppo'])->name('gruppo');
            Route::post('/{gruppo}', [SettingController::class, 'salva'])->name('salva');
        });

        // ─── CATALOGO DOCUMENTI (parte di gestione documenti) ───
        Route::middleware('cando:fatture.view')->group(function () {
            Route::resource('documenti-catalogo', DocumentoCatalogoController::class)
                ->parameters(['documenti-catalogo' => 'documentoCatalogo']);
        });

        // ─── MARKETPLACE (incluso da marketplace.php, accessibile a chi ha marketplace.view) ───
        Route::middleware('cando:marketplace.view')->group(function () {
            require __DIR__.'/marketplace.php';
        });
    });
};

/*
 * IMPORTANTE: le route del sito pubblico (`routes/public.php`) vanno caricate
 * PRIMA di quelle ERP, così su alecar.it il routing matcha public.* prima dei
 * path collidenti (/noleggio, /, /contatti...) registrati anche dall'ERP.
 *
 * Le route ERP hanno middleware "restrict" che ne limita l'esposizione ai soli
 * domini gestionali (app.alecar.it, erp.alecar.it, IP server).
 */
require __DIR__.'/public.php';

if (app()->environment('production')) {
    // PROD: ERP + login SOLO sui domini gestionali
    Route::middleware('restrict:app.alecar.it,erp.alecar.it,142.93.99.245')->group(function () use ($erpRoutes) {
        require __DIR__.'/auth.php';   // login, register, password reset
        $erpRoutes();
    });
} else {
    // DEV: nessun filtro dominio (carmodel.local / localhost servono tutto).
    require __DIR__.'/auth.php';
    $erpRoutes();
}

// PORTALE PUBBLICO (per fascicoli cliente, accessibile via token da QUALSIASI dominio)
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
