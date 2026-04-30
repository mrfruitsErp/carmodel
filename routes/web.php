<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController, CustomerController, VehicleController,
    ClaimController, PersonalInjuryController, ExpertController,
    WorkOrderController, QuoteController, FleetVehicleController,
    RentalController, DocumentController, MailController,
    SparePartController, VinDecoderController, UserController,
    FascicoloController, SettingController, DocumentoCatalogoController,
    InsuranceCompanyController, MessaggiController, VehicleMovementController
};
use App\Http\Controllers\Portale\PortaleClienteController;
use App\Http\Controllers\DeployController;

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
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /*
         * IMPORTANTE: per ogni sezione, le route /create + /edit DEVONO essere
         * registrate PRIMA delle /{id} per evitare che Laravel matchi "create"
         * o "cestino" come ID. Stessa cosa per i path statici aggiuntivi.
         */

        // ─── CLIENTI ───
        Route::middleware('cando:clienti.edit')->group(function () {
            Route::get('clienti/create', [CustomerController::class, 'create'])->name('clienti.create');
            Route::post('clienti', [CustomerController::class, 'store'])->name('clienti.store');
            Route::get('clienti/{customer}/edit', [CustomerController::class, 'edit'])->name('clienti.edit');
            Route::put('clienti/{customer}', [CustomerController::class, 'update'])->name('clienti.update');
            Route::patch('clienti/{customer}', [CustomerController::class, 'update']);
            Route::delete('clienti/{customer}', [CustomerController::class, 'destroy'])->name('clienti.destroy');
            Route::post('clienti/{id}/ripristina', [CustomerController::class, 'ripristina'])->name('clienti.ripristina');
        });
        Route::middleware('cando:clienti.view')->group(function () {
            Route::get('clienti', [CustomerController::class, 'index'])->name('clienti.index');
            Route::get('clienti/cestino', [CustomerController::class, 'cestino'])->name('clienti.cestino');
            Route::get('clienti/{customer}/storico', [CustomerController::class, 'storico'])->name('clienti.storico');
            Route::get('clienti/{customer}', [CustomerController::class, 'show'])->name('clienti.show');
        });

        // ─── VEICOLI ───
        Route::middleware('cando:veicoli.edit')->group(function () {
            Route::post('veicoli/scan-libretto-nuovo', [VehicleController::class, 'scanLibrettoNuovo'])->name('veicoli.scan-libretto-nuovo');
            Route::get('veicoli/create', [VehicleController::class, 'create'])->name('veicoli.create');
            Route::post('veicoli', [VehicleController::class, 'store'])->name('veicoli.store');
            Route::get('veicoli/{veicoli}/edit', [VehicleController::class, 'edit'])->name('veicoli.edit');
            Route::put('veicoli/{veicoli}', [VehicleController::class, 'update'])->name('veicoli.update');
            Route::patch('veicoli/{veicoli}', [VehicleController::class, 'update']);
            Route::delete('veicoli/{veicoli}', [VehicleController::class, 'destroy'])->name('veicoli.destroy');
            Route::post('veicoli/{vehicle}/foto', [VehicleController::class, 'uploadFoto'])->name('veicoli.foto');
            Route::post('veicoli/{vehicle}/documento', [VehicleController::class, 'uploadDocumento'])->name('veicoli.documento');
            Route::delete('veicoli/{vehicle}/documento/{docId}', [VehicleController::class, 'deleteDocumento'])->name('veicoli.documento.delete');
            Route::post('veicoli/{vehicle}/scan-libretto', [VehicleController::class, 'scanLibretto'])->name('veicoli.scan-libretto');
            Route::post('veicoli/{vehicle}/applica-libretto', [VehicleController::class, 'applicaLibretto'])->name('veicoli.applica-libretto');
        });
        Route::middleware('cando:veicoli.view')->group(function () {
            Route::get('veicoli', [VehicleController::class, 'index'])->name('veicoli.index');
            Route::get('veicoli/{veicoli}', [VehicleController::class, 'show'])->name('veicoli.show');
        });

        // ─── SINISTRI ───
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
            Route::post('sinistri/{sinistri}/diario', [ClaimController::class, 'diaryStore'])->name('sinistri.diario.store');
            Route::delete('sinistri/{sinistri}/diario/{diary}', [ClaimController::class, 'diaryDestroy'])->name('sinistri.diario.destroy');
        });
        Route::middleware('cando:sinistri.view')->group(function () {
            Route::get('sinistri/export', [ClaimController::class, 'export'])->name('sinistri.export');
            Route::get('sinistri', [ClaimController::class, 'index'])->name('sinistri.index');
            Route::get('sinistri/{sinistri}', [ClaimController::class, 'show'])->name('sinistri.show');
            Route::get('sinistri/{sinistri}/stampa', [ClaimController::class, 'stampa'])->name('sinistri.stampa');
        });

        // ─── LESIONI ───
        Route::middleware('cando:lesioni.edit')->group(function () {
            Route::get('lesioni/create', [PersonalInjuryController::class, 'create'])->name('lesioni.create');
            Route::post('lesioni', [PersonalInjuryController::class, 'store'])->name('lesioni.store');
            Route::get('lesioni/{lesioni}/edit', [PersonalInjuryController::class, 'edit'])->name('lesioni.edit');
            Route::put('lesioni/{lesioni}', [PersonalInjuryController::class, 'update'])->name('lesioni.update');
            Route::patch('lesioni/{lesioni}', [PersonalInjuryController::class, 'update']);
            Route::delete('lesioni/{lesioni}', [PersonalInjuryController::class, 'destroy'])->name('lesioni.destroy');
        });
        Route::middleware('cando:lesioni.view')->group(function () {
            Route::get('lesioni', [PersonalInjuryController::class, 'index'])->name('lesioni.index');
            Route::get('lesioni/{lesioni}', [PersonalInjuryController::class, 'show'])->name('lesioni.show');
        });

        // ─── ESPERTI / LIQUIDATORI / MEDICI ───
        Route::middleware('cando:periti.edit')->group(function () {
            Route::resource('esperti', ExpertController::class)->except(['index','show'])->names('periti')->parameters(['esperti' => 'esperto']);
            Route::resource('liquidatori', ExpertController::class)->except(['index','show'])->parameters(['liquidatori' => 'liquidatore']);
            Route::resource('medici', ExpertController::class)->except(['index','show'])->parameters(['medici' => 'medico']);
        });
        Route::middleware('cando:periti.view')->group(function () {
            Route::resource('esperti', ExpertController::class)->only(['index','show'])->names('periti')->parameters(['esperti' => 'esperto']);
            Route::resource('liquidatori', ExpertController::class)->only(['index','show'])->parameters(['liquidatori' => 'liquidatore']);
            Route::resource('medici', ExpertController::class)->only(['index','show'])->parameters(['medici' => 'medico']);
        });

        // ─── ASSICURAZIONI (parte del modulo periti) ───
        Route::middleware('cando:periti.edit')->group(function () {
            Route::resource('assicurazioni', InsuranceCompanyController::class)->except(['index','show'])->parameters(['assicurazioni' => 'assicurazioni']);
        });
        Route::middleware('cando:periti.view')->group(function () {
            Route::get('assicurazioni', [InsuranceCompanyController::class, 'index'])->name('assicurazioni.index');
            Route::get('assicurazioni/{assicurazioni}/periti', [InsuranceCompanyController::class, 'periti'])->name('assicurazioni.periti');
            Route::get('assicurazioni/{assicurazioni}', [InsuranceCompanyController::class, 'show'])->name('assicurazioni.show');
        });

        // ─── LAVORAZIONI ───
        Route::middleware('cando:lavorazioni.edit')->group(function () {
            Route::resource('lavorazioni', WorkOrderController::class)->except(['index','show'])->parameters(['lavorazioni' => 'lavorazioni']);
            Route::post('lavorazioni/{lavorazioni}/stato', [WorkOrderController::class, 'updateStato'])->name('lavorazioni.stato');
            Route::post('lavorazioni/{lavorazioni}/progresso', [WorkOrderController::class, 'updateProgresso'])->name('lavorazioni.progresso');
        });
        Route::middleware('cando:lavorazioni.view')->group(function () {
            Route::get('lavorazioni', [WorkOrderController::class, 'index'])->name('lavorazioni.index');
            Route::get('lavorazioni/{lavorazioni}', [WorkOrderController::class, 'show'])->name('lavorazioni.show');
        });

        // ─── PREVENTIVI ───
        Route::middleware('cando:preventivi.edit')->group(function () {
            Route::resource('preventivi', QuoteController::class)->except(['index','show'])->parameters(['preventivi' => 'preventivo']);
            Route::post('preventivi/{quote}/converti', [QuoteController::class, 'convertToJob'])->name('preventivi.converti');
            Route::post('preventivi/{quote}/stato', [QuoteController::class, 'aggiornaStato'])->name('preventivi.stato');
        });
        Route::middleware('cando:preventivi.view')->group(function () {
            Route::get('preventivi', [QuoteController::class, 'index'])->name('preventivi.index');
            Route::get('preventivi/{preventivi}', [QuoteController::class, 'show'])->name('preventivi.show');
        });

        // ─── FLOTTA / NOLEGGIO / SOSTITUTIVE ───
        Route::middleware('cando:noleggio.edit')->group(function () {
            Route::resource('flotta', FleetVehicleController::class)->except(['index','show'])->parameters(['flotta' => 'flotta']);
            Route::resource('noleggio', RentalController::class)->except(['index','show'])->parameters(['noleggio' => 'noleggio']);
            Route::post('noleggio/{noleggio}/chiudi', [RentalController::class, 'chiudi'])->name('noleggio.chiudi');
            Route::resource('sostitutive', RentalController::class)->except(['index','show'])->parameters(['sostitutive' => 'sostitutive']);
        });
        Route::middleware('cando:noleggio.view')->group(function () {
            Route::get('flotta', [FleetVehicleController::class, 'index'])->name('flotta.index');
            Route::get('flotta/{flotta}', [FleetVehicleController::class, 'show'])->name('flotta.show');
            Route::get('noleggio', [RentalController::class, 'index'])->name('noleggio.index');
            Route::get('noleggio/{noleggio}', [RentalController::class, 'show'])->name('noleggio.show');
            Route::get('sostitutive', [RentalController::class, 'index'])->name('sostitutive.index');
            Route::get('sostitutive/{sostitutive}', [RentalController::class, 'show'])->name('sostitutive.show');
        });

        // ─── DOCUMENTI / FATTURE ───
        Route::middleware('cando:fatture.edit')->group(function () {
            Route::resource('documenti', DocumentController::class)->except(['index','show'])->parameters(['documenti' => 'documento']);
            Route::post('documenti/{document}/pagato', [DocumentController::class, 'markPagato'])->name('documenti.pagato');
        });
        Route::middleware('cando:fatture.view')->group(function () {
            Route::get('documenti', [DocumentController::class, 'index'])->name('documenti.index');
            Route::get('documenti/{documenti}', [DocumentController::class, 'show'])->name('documenti.show');
        });

        // ─── MAIL (visibile a tutti gli utenti loggati) ───
        Route::get('mail', [MailController::class, 'index'])->name('mail.index');
        Route::get('mail/template/create', [MailController::class, 'create'])->name('mail.template.create');
        Route::post('mail/template', [MailController::class, 'store'])->name('mail.template.store');

        // ─── MESSAGGI dal sito pubblico (contatti, richieste noleggio/veicoli) ───
        Route::middleware('cando:clienti.view')->prefix('messaggi')->name('messaggi.')->group(function () {
            Route::get('/', [MessaggiController::class, 'index'])->name('index');
            Route::get('/{messaggio}', [MessaggiController::class, 'show'])->name('show');
            Route::post('/{messaggio}/letto', [MessaggiController::class, 'markLetto'])->name('letto');
            Route::post('/{messaggio}/non-letto', [MessaggiController::class, 'markNonLetto'])->name('non-letto');
            Route::post('/{messaggio}/spam', [MessaggiController::class, 'toggleSpam'])->name('spam');
            Route::post('/{messaggio}/stato', [MessaggiController::class, 'updateStatus'])->name('stato');
            Route::delete('/{messaggio}', [MessaggiController::class, 'destroy'])->name('destroy');
        });

        // ─── RICAMBI ───
        Route::middleware('cando:ricambi.view')->group(function () {
            Route::get('ricambi', [SparePartController::class, 'index'])->name('ricambi.index');
            Route::get('ricambi/{ricambi}', [SparePartController::class, 'show'])->name('ricambi.show');
        });
        Route::middleware('cando:ricambi.edit')->group(function () {
            Route::resource('ricambi', SparePartController::class)->except(['index','show'])->parameters(['ricambi' => 'ricambio']);
        });

        // ─── UTENTI (solo admin/manager) ───
        Route::middleware('cando:utenti.manage')->group(function () {
            Route::get('utenti/accessi', [UserController::class, 'accessLog'])->name('utenti.access_log');
            Route::resource('utenti', UserController::class)->names('utenti');
            Route::post('utenti/{user}/toggle', [UserController::class, 'toggleActive'])->name('utenti.toggle');
        });

        // ─── MOVIMENTI VEICOLI ───
        Route::get('movimenti/calendario', [VehicleMovementController::class, 'calendario'])->name('movimenti.calendario');
        Route::get('movimenti/api-eventi', [VehicleMovementController::class, 'apiEventi'])->name('movimenti.api-eventi');
        Route::get('movimenti/create', [VehicleMovementController::class, 'create'])->name('movimenti.create');
        Route::post('movimenti', [VehicleMovementController::class, 'store'])->name('movimenti.store');
        Route::get('movimenti/{movimento}/edit', [VehicleMovementController::class, 'edit'])->name('movimenti.edit');
        Route::put('movimenti/{movimento}', [VehicleMovementController::class, 'update'])->name('movimenti.update');
        Route::patch('movimenti/{movimento}/stato', [VehicleMovementController::class, 'aggiornaStato'])->name('movimenti.stato');
        Route::delete('movimenti/{movimento}', [VehicleMovementController::class, 'destroy'])->name('movimenti.destroy');
        Route::get('movimenti/{movimento}', [VehicleMovementController::class, 'show'])->name('movimenti.show');
        Route::get('movimenti', [VehicleMovementController::class, 'index'])->name('movimenti.index');

        // ─── FASCICOLI (parte del modulo clienti) ───
        // IMPORTANTE: le route statiche (/create, /edit) devono venire PRIMA di /{fascicolo}
        Route::middleware('cando:clienti.edit')->group(function () {
            Route::get('fascicoli/create', [FascicoloController::class, 'create'])->name('fascicoli.create');
            Route::post('fascicoli', [FascicoloController::class, 'store'])->name('fascicoli.store');
            Route::get('fascicoli/{fascicolo}/edit', [FascicoloController::class, 'edit'])->name('fascicoli.edit');
            Route::put('fascicoli/{fascicolo}', [FascicoloController::class, 'update'])->name('fascicoli.update');
            Route::patch('fascicoli/{fascicolo}', [FascicoloController::class, 'update']);
            Route::delete('fascicoli/{fascicolo}', [FascicoloController::class, 'destroy'])->name('fascicoli.destroy');
            Route::post('fascicoli/{fascicolo}/genera-link', [FascicoloController::class, 'generaLink'])->name('fascicoli.genera-link');
            Route::post('fascicoli/{fascicolo}/disattiva-link', [FascicoloController::class, 'disattivaLink'])->name('fascicoli.disattiva-link');
            Route::post('fascicoli/{fascicolo}/popola-documenti', [FascicoloController::class, 'popolaDocumenti'])->name('fascicoli.popola-documenti');
            Route::post('fascicoli/{fascicolo}/completa', [FascicoloController::class, 'segnaCompletato'])->name('fascicoli.completa');
            // Documenti del fascicolo
            Route::post('fascicoli/{fascicolo}/documenti', [FascicoloController::class, 'aggiungiDocumento'])->name('fascicoli.documenti.store');
            Route::put('fascicoli/{fascicolo}/documenti/{documento}', [FascicoloController::class, 'aggiornaDocumento'])->name('fascicoli.documenti.update');
            Route::patch('fascicoli/{fascicolo}/documenti/{documento}', [FascicoloController::class, 'aggiornaDocumento']);
            Route::delete('fascicoli/{fascicolo}/documenti/{documento}', [FascicoloController::class, 'rimuoviDocumento'])->name('fascicoli.documenti.destroy');
            Route::delete('fascicoli/{fascicolo}/media/{mediaId}', [FascicoloController::class, 'destroyMedia'])->name('fascicoli.media.destroy');
        });
        Route::middleware('cando:clienti.view')->group(function () {
            Route::get('fascicoli', [FascicoloController::class, 'index'])->name('fascicoli.index');
            Route::get('fascicoli/{fascicolo}', [FascicoloController::class, 'show'])->name('fascicoli.show');
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

// ─── PORTALE PUBBLICO ───────────────────────────────────────────────────────
// Registrate PRIMA delle route ERP così non vengono oscurate dal Route::domain().
// Accessibile da app.alecar.it/portale/... senza autenticazione.
$portaleRoutes = function () {
    Route::prefix('portale')->name('portale.')->group(function () {
        Route::get('/{token}', [PortaleClienteController::class, 'accesso'])->name('accesso');
        Route::post('/{token}/verifica', [PortaleClienteController::class, 'verificaIdentita'])->name('verifica');
        Route::get('/{token}/otp', [PortaleClienteController::class, 'otpForm'])->name('otp');
        Route::post('/{token}/otp', [PortaleClienteController::class, 'verificaOtp'])->name('otp.verifica');
        Route::post('/{token}/otp/reinvia', [PortaleClienteController::class, 'reinviaOtp'])->name('otp.reinvia');
        Route::get('/{token}/privacy', [PortaleClienteController::class, 'privacy'])->name('privacy');
        Route::post('/{token}/privacy', [PortaleClienteController::class, 'accettaPrivacy'])->name('privacy.accetta');
        Route::get('/{token}/documenti', [PortaleClienteController::class, 'documenti'])->name('documenti');
        Route::post('/{token}/documenti/{doc}/upload', [PortaleClienteController::class, 'uploadDocumento'])->name('documenti.upload');
        Route::post('/{token}/documenti/{doc}/firma', [PortaleClienteController::class, 'firmaDocumento'])->name('documenti.firma');
        Route::post('/{token}/documenti/{doc}/firma/otp', [PortaleClienteController::class, 'verificaFirmaOtp'])->name('documenti.firma.otp');
        Route::post('/{token}/completa', [PortaleClienteController::class, 'completaFascicolo'])->name('completa');
    });
};

if (app()->environment('production')) {
    // PROD: ERP + login registrati SOLO sul dominio gestionale tramite Route::domain().
    // Così su alecar.it queste route non esistono proprio e non interferiscono
    // con la homepage pubblica (niente redirect a /login da auth middleware).
    Route::domain('app.alecar.it')->group(function () use ($erpRoutes, $portaleRoutes) {
        $portaleRoutes(); // Portale PRIMA dell'ERP (nessun middleware auth)
        require __DIR__.'/auth.php';
        $erpRoutes();
    });
} else {
    // DEV: nessun filtro dominio (carmodel.local / localhost servono tutto).
    $portaleRoutes();
    require __DIR__.'/auth.php';
    $erpRoutes();
}

// DEPLOY WEBHOOK (protetto da token segreto in .env → DEPLOY_SECRET)
Route::get('/deploy-hook', [DeployController::class, 'run'])->name('deploy.hook');
Route::post('/deploy-patch', [DeployController::class, 'patch'])->name('deploy.patch');
