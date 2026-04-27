<?php

namespace App\Http\Controllers;

use App\Models\FleetVehicle;
use App\Models\SaleVehicle;
use App\Models\Tenant;
use App\Models\WebBooking;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    private function tid(): int
    {
        return Tenant::first()?->id ?? 1;
    }

    public function home()
    {
        $tid = $this->tid();

        $autoInEvidenza = SaleVehicle::forTenant($tid)
            ->where('status', 'attivo')
            ->with('media')
            ->latest()
            ->take(3)
            ->get();

        $veicoliNoleggio = FleetVehicle::forTenant($tid)
            ->where('web_visible', true)
            ->take(3)
            ->get();

        $totaleAuto = SaleVehicle::forTenant($tid)->where('status', 'attivo')->count();

        return view('public.home', compact('autoInEvidenza', 'veicoliNoleggio', 'totaleAuto'));
    }

    public function noleggio()
    {
        $veicoli = FleetVehicle::forTenant($this->tid())
            ->where('web_visible', true)
            ->orderBy('category')
            ->get();

        return view('public.noleggio', compact('veicoli'));
    }

    public function noleggioShow(int $id)
    {
        $veicolo = FleetVehicle::forTenant($this->tid())
            ->where('web_visible', true)
            ->findOrFail($id);

        $dateOccupate = WebBooking::where('fleet_vehicle_id', $id)
            ->whereIn('status', ['nuova', 'confermata'])
            ->whereNotNull('date_start')
            ->whereNotNull('date_end')
            ->get(['date_start', 'date_end'])
            ->map(fn($b) => [
                'start' => $b->date_start->format('Y-m-d'),
                'end'   => $b->date_end->format('Y-m-d'),
            ]);

        return view('public.noleggio_dettaglio', compact('veicolo', 'dateOccupate'));
    }

    public function noleggioBooking(Request $request, int $id)
    {
        $veicolo = FleetVehicle::forTenant($this->tid())
            ->where('web_visible', true)
            ->where('booking_enabled', true)
            ->findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email',
            'phone'       => 'nullable|string|max:30',
            'date_start'  => 'required|date|after_or_equal:today',
            'date_end'    => 'required|date|after:date_start',
            'message'     => 'nullable|string|max:1000',
            'gdpr_consent'=> 'accepted',
        ], [
            'gdpr_consent.accepted' => 'Devi accettare il trattamento dei dati per procedere.',
        ]);

        WebBooking::create([
            'tenant_id'        => $this->tid(),
            'fleet_vehicle_id' => $veicolo->id,
            'type'             => 'noleggio',
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'] ?? null,
            'date_start'       => $data['date_start'],
            'date_end'         => $data['date_end'],
            'message'          => $data['message'] ?? null,
            'status'           => 'nuova',
        ]);

        return back()->with('booking_success', 'Richiesta inviata! Ti contatteremo per confermare la prenotazione.');
    }

    public function chiSiamo()   { return view('public.chi_siamo'); }
    public function servizi()    { return view('public.servizi'); }
    public function contatti()   { return view('public.contatti'); }

    public function contattiSend(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|min:2|max:100',
            'email'       => 'required|email:rfc,dns|max:150',
            'phone'       => 'nullable|string|max:30|regex:/^[\d\s\+\(\)\-\.\/]+$/',
            'subject'     => 'nullable|string|max:150',
            'message'     => 'required|string|min:10|max:2000',
            'gdpr_consent'=> 'accepted',
        ], [
            'gdpr_consent.accepted' => 'Devi accettare il trattamento dei dati per procedere.',
            'phone.regex'           => 'Il numero di telefono contiene caratteri non validi.',
            'email.dns'             => 'Email non valida o dominio inesistente.',
        ]);

        // Analisi anti-spam
        $spam = \App\Services\SpamFilter::analyze($data, $request);

        // Se è palesemente spam (honeypot pieno o submit troppo veloce), rispondi 200
        // ma NON salvare nulla (così il bot pensa di aver inviato e si ferma).
        if ($spam->isSpam && in_array($spam->reason, ['honeypot', 'submit_too_fast'])) {
            return back()->with('contact_success', 'Messaggio inviato! Ti risponderemo entro 24 ore.');
        }

        // Altri casi spam: salva il record ma marcato come spam (per audit + training)
        WebBooking::create([
            'tenant_id'   => $this->tid(),
            'type'        => 'contatto',
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'message'     => ($data['subject'] ?? '' ? "[{$data['subject']}] " : '') . $data['message'],
            'status'      => 'nuova',
            'is_spam'     => $spam->isSpam,
            'spam_reason' => $spam->reason,
            'ip_address'  => $request->ip(),
            'user_agent'  => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->with('contact_success', 'Messaggio inviato! Ti risponderemo entro 24 ore.');
    }

    // ── Pagine legali ─────────────────────────────────────
    public function privacy()        { return view('public.legal.privacy'); }
    public function cookiePolicy()   { return view('public.legal.cookie_policy'); }
    public function terminiVendita() { return view('public.legal.termini_vendita'); }
    public function terminiNoleggio(){ return view('public.legal.termini_noleggio'); }
}
