{{-- chi_siamo.blade.php --}}
@extends('public.layout')
@section('title', 'Chi Siamo - AleCar S.r.l. Torino')
@section('description', 'AleCar S.r.l. - La nostra storia, la nostra sede e il nostro team a Torino.')

@section('content')

<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:60px 0 48px">
  <div class="container">
    <div class="section-label">La nostra storia</div>
    <h1 class="section-title">Chi siamo</h1>
    <p class="section-sub">AleCar S.r.l. — nata dalla passione per l'automobile e dalla voglia di offrire un servizio trasparente e di qualità.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:60px">
      <div>
        <div class="orange-line"></div>
        <h2 style="font-size:28px;font-weight:800;margin-bottom:20px">La nostra missione</h2>
        <p style="color:var(--text2);font-size:15px;line-height:1.9;margin-bottom:16px">
          AleCar S.r.l. nasce a Torino con un obiettivo chiaro: rendere l'acquisto e il noleggio di un'auto un'esperienza semplice, trasparente e soddisfacente.
        </p>
        <p style="color:var(--text2);font-size:15px;line-height:1.9;margin-bottom:16px">
          Ogni veicolo del nostro stock viene selezionato con cura, verificato meccanicamente e proposto a prezzi equi con IVA sempre esposta. Nessuna sorpresa, nessun costo nascosto.
        </p>
        <p style="color:var(--text2);font-size:15px;line-height:1.9">
          Offriamo anche un servizio di noleggio breve e lungo termine per privati e aziende, con una flotta sempre aggiornata e veicoli pronti alla consegna.
        </p>
      </div>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:36px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
          @foreach([
            ['🚗','Stock veicoli','Selezione accurata di auto usate garantite'],
            ['📅','Noleggio','Breve e lungo termine per privati e aziende'],
            ['🔍','Trasparenza','Prezzi chiari, IVA sempre esposta'],
            ['📞','Assistenza','Supporto dedicato prima e dopo l\'acquisto'],
          ] as [$icon,$title,$desc])
          <div style="background:var(--bg3);border-radius:10px;padding:20px">
            <div style="font-size:24px;margin-bottom:10px">{{ $icon }}</div>
            <div style="font-size:14px;font-weight:700;margin-bottom:6px">{{ $title }}</div>
            <div style="font-size:12px;color:var(--text3);line-height:1.6">{{ $desc }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="divider"></div>

    {{-- Sede e orari --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:32px">
        <div class="orange-line"></div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:20px">Dove siamo</h3>
        <div style="display:flex;flex-direction:column;gap:12px;font-size:14px">
          <div style="display:flex;gap:12px;align-items:flex-start">
            <span style="color:var(--orange);flex-shrink:0">📍</span>
            <div><div style="font-weight:600">Indirizzo</div><div style="color:var(--text2)">Via Ignazio Collino 29<br>10100 Torino (TO)</div></div>
          </div>
          <div style="display:flex;gap:12px;align-items:center">
            <span style="color:var(--orange)">📞</span>
            <div><div style="font-weight:600">Telefono</div><a href="tel:+393278072650" style="color:var(--text2);text-decoration:none">+39 327 807 2650</a></div>
          </div>
          <div style="display:flex;gap:12px;align-items:center">
            <span style="color:var(--orange)">✉️</span>
            <div><div style="font-weight:600">Email</div><a href="mailto:alecarto7@gmail.com" style="color:var(--text2);text-decoration:none">alecarto7@gmail.com</a></div>
          </div>
          <div style="display:flex;gap:12px;align-items:center">
            <span style="color:var(--orange)">🏛️</span>
            <div><div style="font-weight:600">PEC</div><a href="mailto:alecar@legalmail.it" style="color:var(--text2);text-decoration:none">alecar@legalmail.it</a></div>
          </div>
        </div>
      </div>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:32px">
        <div class="orange-line"></div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:20px">Orari di apertura</h3>
        <div style="display:flex;flex-direction:column;gap:10px">
          @foreach([
            ['Lunedì – Venerdì', '09:00 – 18:00', true],
            ['Sabato', '09:00 – 13:00', true],
            ['Domenica', 'Chiuso', false],
          ] as [$day,$time,$open])
          <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px">
            <span style="color:var(--text2)">{{ $day }}</span>
            <span style="font-weight:600;color:{{ $open ? 'var(--orange)' : 'var(--text3)' }}">{{ $time }}</span>
          </div>
          @endforeach
          <p style="font-size:12px;color:var(--text3);margin-top:8px">Per appuntamenti fuori orario contattaci telefonicamente.</p>
        </div>
      </div>
    </div>

    {{-- Dati aziendali --}}
    <div style="margin-top:40px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px">
      <div style="font-size:12px;color:var(--text3);line-height:2">
        <strong style="color:var(--text2)">AleCar S.r.l.</strong> — Via Ignazio Collino 29, 10100 Torino (TO) —
        P.IVA: 11352180019 — C.F.: 11352180019 — Cod. Univoco SDI: M5UXCR1 —
        Iscritta alla CCIAA di Torino — PEC: alecar@legalmail.it
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
@media(max-width:768px){
  div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>
@endpush
@endsection