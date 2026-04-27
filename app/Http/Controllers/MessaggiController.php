<?php

namespace App\Http\Controllers;

use App\Models\WebBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gestione messaggi inbound dal sito pubblico (alecar.it):
 *  - richieste contatto generiche (form contatti)
 *  - richieste informazioni veicolo (dal dettaglio auto)
 *  - richieste noleggio (form noleggio + dettaglio)
 *
 * Tutti i messaggi sono salvati nella tabella `web_bookings`. Da qui un operatore
 * (con permesso clienti.view) li vede, li segna come letti, risponde, e ne traccia
 * lo stato (nuova, confermata, rifiutata, annullata).
 */
class MessaggiController extends Controller
{
    private function tid(): int { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $q = WebBooking::forTenant($this->tid())
            ->with('fleetVehicle:id,brand,model,plate')
            ->latest();

        // ── Default: nascondi messaggi spam (li mostri solo col filtro "spam") ──
        if ($request->view !== 'spam') {
            $q->where('is_spam', false);
        } else {
            $q->where('is_spam', true);
        }

        // Filtri
        if ($f = $request->status) $q->where('status', $f);
        if ($f = $request->type)   $q->where('type', $f);
        if ($request->boolean('non_letti')) $q->whereNull('letto_at');
        if ($s = $request->search) {
            $q->where(fn($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")
                ->orWhere('message', 'like', "%{$s}%")
            );
        }

        $messaggi = $q->paginate(25)->withQueryString();

        // Conteggi per i tab in cima (escluso spam, contati separatamente)
        $base = WebBooking::forTenant($this->tid())->where('is_spam', false);
        $stats = [
            'totale'    => (clone $base)->count(),
            'non_letti' => (clone $base)->whereNull('letto_at')->count(),
            'nuove'     => (clone $base)->where('status', 'nuova')->count(),
            'noleggio'  => (clone $base)->where('type', 'noleggio')->count(),
            'contatti'  => (clone $base)->whereIn('type', ['contatto', 'contatto_veicolo'])->count(),
            'spam'      => WebBooking::forTenant($this->tid())->where('is_spam', true)->count(),
        ];

        return view('messaggi.index', compact('messaggi', 'stats'));
    }

    /**
     * Marca/smarca manualmente un messaggio come spam.
     */
    public function toggleSpam(WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);
        $messaggio->update([
            'is_spam'     => !$messaggio->is_spam,
            'spam_reason' => $messaggio->is_spam ? null : 'manual',
        ]);
        return back()->with('success', $messaggio->is_spam
            ? 'Messaggio segnato come spam.'
            : 'Messaggio rimosso da spam.');
    }

    public function show(WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);

        // Marca come letto al primo accesso
        if (!$messaggio->letto_at) {
            $messaggio->update([
                'letto_at'         => now(),
                'letto_da_user_id' => Auth::id(),
            ]);
        }

        $messaggio->load(['fleetVehicle', 'lettoDa']);
        return view('messaggi.show', compact('messaggio'));
    }

    public function markLetto(WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);

        $messaggio->update([
            'letto_at'         => now(),
            'letto_da_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Messaggio segnato come letto.');
    }

    public function markNonLetto(WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);

        $messaggio->update([
            'letto_at'         => null,
            'letto_da_user_id' => null,
        ]);

        return back()->with('success', 'Messaggio segnato come non letto.');
    }

    public function updateStatus(Request $request, WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);

        $request->validate([
            'status'      => 'required|in:nuova,confermata,rifiutata,annullata',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $messaggio->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'confirmed_at'=> $request->status === 'confermata' ? now() : null,
        ]);

        return back()->with('success', 'Stato aggiornato.');
    }

    public function destroy(WebBooking $messaggio)
    {
        abort_if($messaggio->tenant_id !== $this->tid(), 403);
        $messaggio->delete();
        return redirect()->route('messaggi.index')->with('success', 'Messaggio eliminato.');
    }
}
