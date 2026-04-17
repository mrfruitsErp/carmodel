{{-- servizi.blade.php --}}
@extends('public.layout')
@section('title', 'Servizi - AleCar S.r.l. Torino')
@section('description', 'AleCar S.r.l. offre vendita auto usate, noleggio, perizie e assistenza a Torino.')

@section('content')

<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:60px 0 48px">
  <div class="container">
    <div class="section-label">Cosa facciamo</div>
    <h1 class="section-title">I nostri servizi</h1>
    <p class="section-sub">Vendita, noleggio e assistenza. Tutto quello che ti serve per il tuo veicolo, in un unico posto.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;margin-bottom:60px">
      @foreach([
        [
          '🚗', 'Vendita auto usate',
          'Selezioniamo con cura ogni veicolo del nostro stock. Ogni auto viene verificata meccanicamente, documentata e proposta a prezzi trasparenti con IVA sempre esposta.',
          'Auto in vendita', 'public.vehicles.index'
        ],
        [
          '📅', 'Noleggio veicoli',
          'Noleggio breve e lungo termine per privati e aziende. Flotta sempre aggiornata con veicoli di categoria A, B, C e superiori. Consegna a domicilio disponibile.',
          'Prenota ora', 'public.noleggio'
        ],
        [
          '🔍', 'Perizie e valutazioni',
          'Valutiamo il tuo veicolo usato con perizia professionale. Servizio utile per compravendite tra privati, pratiche assicurative e finanziamenti.',
          'Contattaci', 'public.contatti'
        ],
        [
          '📋', 'Pratiche e documenti',
          'Gestiamo le pratiche di trasferimento proprietà, passaggi di proprietà, radiazioni e tutte le pratiche burocratiche legate all\'acquisto del tuo veicolo.',
          'Richiedi info', 'public.contatti'
        ],
      ] as [$icon,$title,$desc,$cta,$route])
      <div class="card" style="padding:32px">
        <div style="width:52px;height:52px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px">{{ $icon }}</div>
        <div class="orange-line"></div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:12px">{{ $title }}</h3>
        <p style="font-size:14px;color:var(--text2);line-height:1.8;margin-bottom:20px">{{ $desc }}</p>
        <a href="{{ route($route) }}" class="btn btn-ghost btn-sm">{{ $cta }} →</a>
      </div>
      @endforeach
    </div>

    {{-- CTA --}}
    <div style="background:var(--bg2);border:1px solid rgba(255,107,0,.2);border-radius:16px;padding:48px;text-align:center">
      <div style="font-size:32px;margin-bottom:16px">💬</div>
      <h2 style="font-size:24px;font-weight:800;margin-bottom:12px">Hai bisogno di un servizio personalizzato?</h2>
      <p style="color:var(--text2);margin-bottom:28px">Contattaci e troveremo insieme la soluzione migliore per le tue esigenze.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="tel:+393278072650" class="btn btn-primary">📞 Chiamaci ora</a>
        <a href="{{ route('public.contatti') }}" class="btn btn-ghost">Invia un messaggio</a>
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
@media(max-width:768px){
  div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr!important}
}
</style>
@endpush
@endsection