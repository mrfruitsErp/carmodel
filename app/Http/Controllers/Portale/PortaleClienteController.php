<?php

namespace App\Http\Controllers\Portale;

use App\Http\Controllers\Controller;
use App\Models\FascicoloToken;
use App\Models\FascicoloDocumento;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PortaleClienteController extends Controller
{
    // ──────────────────────────────────────────
    // Recupera e valida il token — usato da tutti i metodi
    // ──────────────────────────────────────────
    private function getToken(string $token): FascicoloToken
    {
        $t = FascicoloToken::with(['fascicolo.cliente', 'fascicolo.documenti'])
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($t->isValido(), 410, 'Il link non è più valido o è scaduto.');

        return $t;
    }

    // ──────────────────────────────────────────
    // STEP 1 — Accesso tramite link
    // ──────────────────────────────────────────
    public function accesso(string $token)
    {
        $fascicoloToken = $this->getToken($token);

        // Già autenticato in sessione? Vai direttamente ai documenti
        if (session("portale_autenticato_{$token}")) {
            return redirect()->route('portale.documenti', $token);
        }

        $cliente = $fascicoloToken->fascicolo->cliente;
        $tipo    = $cliente->tipo_soggetto_effettivo; // privato, azienda, impresa_individuale

        return view('portale.accesso', compact('fascicoloToken', 'cliente', 'tipo', 'token'));
    }

    // ──────────────────────────────────────────
    // STEP 2 — Verifica identità (CF / P.IVA)
    // ──────────────────────────────────────────
    public function verificaIdentita(Request $request, string $token)
    {
        $fascicoloToken = $this->getToken($token);
        $cliente        = $fascicoloToken->fascicolo->cliente;

        $request->validate([
            'codice' => 'required|string',
        ]);

        $codiceInserito = strtoupper(trim($request->codice));

        $corretto = match($cliente->tipo_soggetto_effettivo) {
            'privato'              => strtoupper($cliente->codice_fiscale_effettivo ?? '') === $codiceInserito,
            'azienda',
            'azienda',
            'impresa_individuale'  => strtoupper($cliente->partita_iva_effettiva ?? '') === $codiceInserito,
            default                => false,
        };

        if (!$corretto) {
            return back()->withErrors(['codice' => 'Codice non corretto. Riprova.'])->withInput();
        }

        // Salva in sessione la verifica identità
        session(["portale_identita_{$token}" => true]);

        // Registra primo utilizzo del token
        if (!$fascicoloToken->used_at) {
            $fascicoloToken->update(['used_at' => now()]);
        }

        // OTP email abilitato?
        $smsProvider = Setting::get('sms_provider', 'self_hosted');
        if ($smsProvider !== 'self_hosted' && $cliente->email) {
            return redirect()->route('portale.otp', $token);
        }

        // Salta OTP → vai a GDPR
        return redirect()->route('portale.privacy', $token);
    }

    // ──────────────────────────────────────────
    // STEP 3 — OTP email (opzionale)
    // ──────────────────────────────────────────
    public function otpForm(string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_identita_{$token}"), 403);

        // Genera e invia OTP
        $otp    = $fascicoloToken->generaOtp();
        $email  = $fascicoloToken->fascicolo->cliente->email;

        // Invia email con OTP
        Mail::raw("Il tuo codice di accesso CarModel è: {$otp}\n\nValido per " . Setting::get('otp_timeout_minuti', 10) . " minuti.", function ($m) use ($email) {
            $m->to($email)->subject('Codice di accesso CarModel');
        });

        return view('portale.otp', compact('fascicoloToken', 'token'));
    }

    public function verificaOtp(Request $request, string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_identita_{$token}"), 403);

        $request->validate(['otp' => 'required|string|size:6']);

        if (!$fascicoloToken->verificaOtp($request->otp)) {
            return back()->withErrors(['otp' => 'Codice non valido o scaduto.']);
        }

        return redirect()->route('portale.privacy', $token);
    }

    public function reinviaOtp(string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_identita_{$token}"), 403);

        $otp   = $fascicoloToken->generaOtp();
        $email = $fascicoloToken->fascicolo->cliente->email;

        Mail::raw("Il tuo nuovo codice CarModel è: {$otp}", function ($m) use ($email) {
            $m->to($email)->subject('Nuovo codice di accesso CarModel');
        });

        return back()->with('success', 'Nuovo codice inviato.');
    }

    // ──────────────────────────────────────────
    // STEP 4 — Privacy GDPR
    // ──────────────────────────────────────────
    public function privacy(string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_identita_{$token}"), 403);

        // Già accettato?
        if ($fascicoloToken->isGdprAccettato()) {
            return redirect()->route('portale.documenti', $token);
        }

        $testoGdpr    = Setting::get('gdpr_testo', '');
        $versioneGdpr = Setting::get('gdpr_versione', '1.0');

        return view('portale.privacy', compact('fascicoloToken', 'token', 'testoGdpr', 'versioneGdpr'));
    }

    public function accettaPrivacy(Request $request, string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_identita_{$token}"), 403);

        $request->validate(['accetto' => 'accepted']);

        $fascicoloToken->accettaGdpr($request->ip());

        // Aggiorna stato fascicolo
        $fascicoloToken->fascicolo->update(['stato' => 'gdpr_accettato']);

        // Segna autenticato in sessione
        session(["portale_autenticato_{$token}" => true]);

        return redirect()->route('portale.documenti', $token);
    }

    // ──────────────────────────────────────────
    // STEP 5 — Dashboard documenti
    // ──────────────────────────────────────────
    public function documenti(string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_autenticato_{$token}"), 403, 'Sessione scaduta. Riaccedi dal link.');

        $fascicolo  = $fascicoloToken->fascicolo->load(['documenti', 'cliente']);
        $documenti  = $fascicolo->documenti()->orderBy('ordine')->get();
        $progresso  = $fascicolo->progresso;

        // Aggiorna stato
        if ($fascicolo->stato === 'gdpr_accettato') {
            $fascicolo->update(['stato' => 'in_compilazione']);
        }

        return view('portale.documenti', compact('fascicoloToken', 'fascicolo', 'documenti', 'progresso', 'token'));
    }

    // ──────────────────────────────────────────
    // Upload documento
    // ──────────────────────────────────────────
    public function uploadDocumento(Request $request, string $token, FascicoloDocumento $doc)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_autenticato_{$token}"), 403);

        $maxMb = (int) Setting::get('upload_max_mb', 10);
        $request->validate([
            'file' => "required|file|mimes:jpg,jpeg,png,pdf|max:" . ($maxMb * 1024),
        ]);

        $doc->addMedia($request->file('file'))->toMediaCollection('file_documento');
        $doc->update(['stato' => 'caricato', 'caricato_il' => now()]);

        return back()->with('success', "Documento '{$doc->nome}' caricato.");
    }

    // ──────────────────────────────────────────
    // Firma documento — richiede OTP email
    // ──────────────────────────────────────────
    public function firmaDocumento(Request $request, string $token, FascicoloDocumento $doc)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_autenticato_{$token}"), 403);

        $otp   = $doc->generaFirmaOtp();
        $email = $fascicoloToken->fascicolo->cliente->email;

        Mail::raw("Codice per firmare '{$doc->nome}': {$otp}\n\nValido 15 minuti.", function ($m) use ($email, $doc) {
            $m->to($email)->subject("Firma documento: {$doc->nome}");
        });

        return back()->with('success', 'Codice di firma inviato alla tua email.');
    }

    public function verificaFirmaOtp(Request $request, string $token, FascicoloDocumento $doc)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_autenticato_{$token}"), 403);

        $request->validate([
            'otp'  => 'required|string|size:6',
            'nome' => 'required|string|max:100',
        ]);

        $firmato = $doc->apponiFirma(
            $request->otp,
            $request->nome,
            $request->ip(),
            $request->userAgent()
        );

        if (!$firmato) {
            return back()->withErrors(['otp' => 'Codice non valido o scaduto.']);
        }

        return back()->with('success', "Documento '{$doc->nome}' firmato con successo.");
    }

    // ──────────────────────────────────────────
    // Cliente completa il fascicolo
    // ──────────────────────────────────────────
    public function completaFascicolo(Request $request, string $token)
    {
        $fascicoloToken = $this->getToken($token);
        abort_unless(session("portale_autenticato_{$token}"), 403);

        $fascicolo = $fascicoloToken->fascicolo;

        // Verifica obbligatori
        $mancanti = $fascicolo->documenti()
            ->where('obbligatorio', true)
            ->whereNotIn('stato', ['caricato','firmato','verificato'])
            ->count();

        if ($mancanti > 0) {
            return back()->withErrors(['completa' => "Mancano {$mancanti} documento/i obbligatorio/i."]);
        }

        $fascicolo->update([
            'stato'        => 'completato',
            'completato_il'=> now(),
            'notifica_operatore_il' => now(),
        ]);

        // Notifica operatore
        if ($fascicolo->operatore) {
            $fascicolo->operatore->notify(new \App\Notifications\FascicoloCompletato($fascicolo));
        }

        return view('portale.completato', compact('fascicolo', 'token'));
    }
}
