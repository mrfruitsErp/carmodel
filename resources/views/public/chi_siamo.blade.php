{{-- chi_siamo.blade.php --}}
@extends('public.layout')
@php
  use App\Models\Setting;
  $pageTitle = Setting::get('page_chi_siamo_title') ?: Setting::get('seo_site_title','Chi Siamo - AleCar S.r.l.');
  $pageDesc  = Setting::get('page_chi_siamo_description') ?: Setting::get('seo_site_description','');
  $h1        = Setting::get('page_chi_siamo_h1') ?: Setting::get('chi_siamo_h1','Chi siamo');
  $h2        = Setting::get('page_chi_siamo_h2') ?: Setting::get('chi_siamo_h2','La nostra storia');
  $testo     = Setting::get('chi_siamo_testo','AleCar nasce a Torino con la missione di rendere l\'acquisto e il noleggio di veicoli usati un\'esperienza trasparente, semplice e affidabile.');
  $missione  = Setting::get('chi_siamo_missione','La nostra missione è offrire veicoli selezionati di qualità con prezzi chiari e assistenza dedicata.');
  $visione   = Setting::get('chi_siamo_visione','Crediamo che ogni cliente meriti un\'esperienza d\'acquisto serena, senza sorprese.');
  $foto      = Setting::get('chi_siamo_foto','');
  $anno      = Setting::get('azienda_anno','2018');
@endphp
@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')

{{-- HERO --}}
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:60px 0 48px">
  <div class="container">
    <div class="section-label">{{ $h2 }}</div>
    <h1 class="section-title">{{ $h1 }}</h1>
    <p class="section-sub">{{ Setting::get('azienda_descrizione','AleCar S.r.l. — veicoli usati garantiti, prezzi trasparenti e IVA esposta.') }}</p>
  </div>
</section>

{{-- INTRO + STORIA --}}
<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:60px">
      <div>
        <div class="orange-line"></div>
        <h2 style="font-size:28px;font-weight:800;margin-bottom:20px">Torino, {{ $anno }}. Una passione che diventa impresa.</h2>
        <p style="color:var(--text2);font-size:15px;line-height:1.9;margin-bottom:16px">{{ $testo }}</p>
        @if($missione)<p style="color:var(--text2);font-size:15px;line-height:1.9;margin-bottom:16px">{{ $missione }}</p>@endif
        @if($visione)<p style="color:var(--text2);font-size:15px;line-height:1.9">{{ $visione }}</p>@endif
      </div>
      @if($foto)
      <div>
        <img src="{{ $foto }}" alt="Chi siamo AleCar" style="width:100%;border-radius:16px;border:1px solid var(--border);object-fit:cover;max-height:400px">
      </div>
      @else
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:36px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
          @foreach([
            ['🔧','Officina & Carrozzeria','Riparazioni, revisioni e verniciatura certificata'],
            ['🚗','Vendita Auto','Nuove, km0 e usate selezionate multimarca'],
            ['🔑','Noleggio','Breve e lungo termine, vetture sostitutive sempre disponibili'],
            ['📋','Pratiche & Sinistri','Passaggi di proprietà, immatricolazioni e gestione sinistri completa'],
          ] as [$icon,$title,$desc])
          <div style="background:var(--bg3);border-radius:10px;padding:20px">
            <div style="font-size:24px;margin-bottom:10px">{{ $icon }}</div>
            <div style="font-size:14px;font-weight:700;margin-bottom:6px">{{ $title }}</div>
            <div style="font-size:12px;color:var(--text3);line-height:1.6">{{ $desc }}</div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    <div class="divider"></div>

    {{-- TUTTI I SERVIZI --}}
    <div style="margin:60px 0 0">
      <div class="orange-line"></div>
      <h2 style="font-size:26px;font-weight:800;margin-bottom:8px">Tutto quello che riguarda la tua auto. In un unico posto.</h2>
      <p style="color:var(--text2);font-size:15px;line-height:1.8;margin-bottom:40px;max-width:700px">Siamo l'alternativa moderna alla concessionaria tradizionale. Non vendiamo solo auto: ti seguiamo in ogni momento della vita del tuo veicolo.</p>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:60px">

        {{-- Officina --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">🔧</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Officina & Carrozzeria</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Riparazioni meccaniche, revisioni, carrozzeria e verniciatura professionale. Tecnici certificati, ricambi originali e tempi certi. La tua auto torna come nuova.</p>
        </div>

        {{-- Vendita --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">🚗</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Vendita Auto Nuove e Usate</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Ampia selezione multimarca di vetture nuove, km0 e usate garantite. Valutazione dell'usato, permuta e finanziamenti personalizzati. IVA sempre esposta, nessuna sorpresa.</p>
        </div>

        {{-- Noleggio --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">🔑</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Noleggio Breve e Lungo Termine</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Soluzioni flessibili per privati e aziende. Auto sostitutive durante le riparazioni, noleggio giornaliero o mensile, fleet aziendale gestita chiavi in mano.</p>
        </div>

        {{-- Sinistri --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">🛡️</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Gestione Sinistri</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Ti affianchiamo in ogni fase: dalla perizia al rapporto con la compagnia assicurativa, fino alla riparazione completa e alla vettura sostitutiva. Ci pensiamo noi a tutto.</p>
        </div>

        {{-- Pratiche --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">📋</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Pratiche Auto</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Passaggi di proprietà, immatricolazioni, radiazioni, visure e pratiche assicurative. Zero code, zero stress: ci occupiamo noi di tutta la burocrazia.</p>
        </div>

        {{-- Pneumatici --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:28px">
          <div style="font-size:30px;margin-bottom:14px">🔩</div>
          <h3 style="font-size:16px;font-weight:800;margin-bottom:10px">Tagliandi, Gomme & Revisioni</h3>
          <p style="font-size:13px;color:var(--text2);line-height:1.8">Cambi gomme stagionali, tagliandi programmati e revisioni ministeriali. Prezzi trasparenti, appuntamenti rapidi, nessuna sorpresa in fattura.</p>
        </div>

      </div>
    </div>

    <div class="divider"></div>

    {{-- PERCHE' SCEGLIERCI --}}
    <div style="margin:60px 0">
      <div class="orange-line"></div>
      <h2 style="font-size:26px;font-weight:800;margin-bottom:8px">Perché scegliere AleCar</h2>
      <p style="color:var(--text2);font-size:15px;line-height:1.8;margin-bottom:36px;max-width:700px">Perché non devi più girare da un'officina all'altra, da un'agenzia di pratiche all'altra, da un noleggiatore all'altro. Da AleCar trovi tutto, gestito da persone competenti che si prendono cura di te.</p>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
        @foreach([
          ['✅','Oltre 10 anni di esperienza','Nel settore automotive torinese, con centinaia di clienti soddisfatti.'],
          ['✅','Servizio a 360°','Vendita, riparazione, noleggio, pratiche: un solo interlocutore per tutto.'],
          ['✅','Assistenza personalizzata','Per privati e aziende, con soluzioni su misura per ogni esigenza.'],
          ['✅','Trasparenza totale','Preventivi chiari, tempi certi, nessun costo nascosto.'],
          ['✅','Vetture sostitutive','Sempre disponibili durante le riparazioni, senza attese.'],
          ['✅','Team certificato','Aggiornato sulle ultime tecnologie, pronto a seguirti in ogni fase.'],
        ] as [$icon,$title,$desc])
        <div style="display:flex;gap:14px;align-items:flex-start;background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:20px">
          <span style="color:var(--orange);font-size:18px;flex-shrink:0">{{ $icon }}</span>
          <div>
            <div style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $title }}</div>
            <div style="font-size:12px;color:var(--text3);line-height:1.6">{{ $desc }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="divider"></div>

    {{-- SEDE + ORARI --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:60px">
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

    {{-- CTA --}}
    <div style="margin-top:48px;background:linear-gradient(135deg,var(--bg2) 0%,var(--bg3) 100%);border:1px solid var(--orange);border-radius:16px;padding:40px;text-align:center">
      <h3 style="font-size:22px;font-weight:800;margin-bottom:12px">Hai bisogno di un'auto, un preventivo o un consiglio?</h3>
      <p style="color:var(--text2);font-size:15px;margin-bottom:28px">Contattaci subito — rispondiamo entro poche ore. Nessun centralino, parli direttamente con noi.</p>
      <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
        <a href="tel:+393278072650" style="background:var(--orange);color:#000;font-weight:700;padding:14px 28px;border-radius:8px;text-decoration:none;font-size:14px">📞 Chiamaci ora</a>
        <a href="mailto:alecarto7@gmail.com" style="background:var(--bg3);border:1px solid var(--border);color:var(--text1);font-weight:600;padding:14px 28px;border-radius:8px;text-decoration:none;font-size:14px">✉️ Scrivici una email</a>
        <a href="{{ route('public.vehicles.index') }}" style="background:var(--bg3);border:1px solid var(--border);color:var(--text1);font-weight:600;padding:14px 28px;border-radius:8px;text-decoration:none;font-size:14px">🚗 Vedi le auto in vendita</a>
      </div>
    </div>

    {{-- DATI AZIENDALI --}}
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
  div[style*="grid-template-columns:1fr 1fr"],
  div[style*="grid-template-columns:repeat(3,1fr)"]{
    grid-template-columns:1fr!important
  }
}
</style>
@endpush
@endsection