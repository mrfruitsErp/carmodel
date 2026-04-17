@extends('public.layout')
@section('title', 'AleCar S.r.l. - Vendita Auto e Noleggio Torino')
@section('description', 'AleCar S.r.l. Torino - Auto usate garantite e noleggio veicoli. Qualità, trasparenza e assistenza dedicata.')

@section('content')

{{-- HERO --}}
<section style="min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;background:var(--bg)">
  {{-- Carbon texture overlay --}}
  <div style="position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.015) 0,rgba(255,255,255,.015) 1px,transparent 0,transparent 50%);background-size:8px 8px;pointer-events:none"></div>
  {{-- Orange glow --}}
  <div style="position:absolute;top:20%;right:10%;width:500px;height:500px;background:radial-gradient(circle,rgba(255,107,0,.12) 0,transparent 70%);pointer-events:none"></div>

  <div class="container" style="position:relative;z-index:1;padding-top:40px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center">
      <div>
        <div style="display:inline-flex;align-items:center;gap:8px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.3);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:600;color:var(--orange);letter-spacing:.06em;margin-bottom:24px">
          <span style="width:6px;height:6px;border-radius:50%;background:var(--orange);animation:pulse 2s infinite"></span>
          TORINO — DAL 2018
        </div>
        <h1 style="font-size:clamp(36px,5vw,64px);font-weight:900;line-height:1.1;margin-bottom:20px">
          Auto <span style="color:var(--orange)">selezionate</span><br>e noleggio<br>su misura
        </h1>
        <p style="font-size:16px;color:var(--text2);line-height:1.8;margin-bottom:32px;max-width:440px">
          AleCar S.r.l. — veicoli usati garantiti, prezzi trasparenti e IVA esposta. Noleggio breve e lungo termine con flotta sempre aggiornata.
        </p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <a href="{{ route('public.vehicles.index') }}" class="btn btn-primary" style="font-size:15px;padding:13px 28px">
            Vedi auto in vendita
          </a>
          <a href="{{ route('public.noleggio') }}" class="btn btn-ghost" style="font-size:15px;padding:13px 28px">
            Noleggio veicoli
          </a>
        </div>
        {{-- Stats --}}
        <div style="display:flex;gap:32px;margin-top:48px;padding-top:32px;border-top:1px solid var(--border)">
          <div>
            <div style="font-size:32px;font-weight:800;color:var(--orange)">{{ $totaleAuto }}</div>
            <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:.08em">Auto disponibili</div>
          </div>
          <div>
            <div style="font-size:32px;font-weight:800;color:var(--orange)">100%</div>
            <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:.08em">Garantite</div>
          </div>
          <div>
            <div style="font-size:32px;font-weight:800;color:var(--orange)">24h</div>
            <div style="font-size:12px;color:var(--text3);text-transform:uppercase;letter-spacing:.08em">Risposta</div>
          </div>
        </div>
      </div>
      {{-- Right side decorative --}}
      <div style="position:relative;display:flex;align-items:center;justify-content:center">
        <div style="width:100%;max-width:480px;aspect-ratio:4/3;background:var(--bg2);border:1px solid var(--border);border-radius:16px;overflow:hidden;position:relative">
          <div style="position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.02) 0,rgba(255,255,255,.02) 1px,transparent 0,transparent 50%);background-size:6px 6px"></div>
          <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px">
            <svg width="80" height="80" fill="none" stroke="var(--orange)" stroke-width="1" viewBox="0 0 24 24" opacity=".3"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
            <span style="font-size:12px;color:var(--text3)">AleCar — Torino</span>
          </div>
          <div style="position:absolute;bottom:16px;left:16px;right:16px;background:rgba(0,0,0,.7);border-radius:8px;padding:12px 16px;backdrop-filter:blur(8px)">
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px">SEDE</div>
            <div style="font-size:13px;color:var(--text);font-weight:600">Via Ignazio Collino 29, Torino</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- VANTAGGI --}}
<section class="section-sm" style="background:var(--bg2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container">
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
      @foreach([
        ['🔍','Veicoli controllati','Ogni auto viene verificata e certificata prima della vendita'],
        ['💰','Prezzi trasparenti','IVA sempre esposta, nessun costo nascosto'],
        ['📞','Risposta in 24h','Rispondiamo a tutte le richieste entro un giorno lavorativo'],
        ['🚗','Consegna a domicilio','Consegniamo il veicolo direttamente da te'],
      ] as [$icon,$title,$desc])
      <div style="display:flex;gap:16px;align-items:flex-start">
        <div style="width:44px;height:44px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">{{ $icon }}</div>
        <div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px">{{ $title }}</div>
          <div style="font-size:12px;color:var(--text3);line-height:1.6">{{ $desc }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- AUTO IN EVIDENZA --}}
@if($autoInEvidenza->count())
<section class="section">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px">
      <div>
        <div class="section-label">Stock selezionato</div>
        <h2 class="section-title">Auto in vendita</h2>
        <p class="section-sub">Veicoli usati selezionati, controllati e garantiti. IVA sempre esposta.</p>
      </div>
      <a href="{{ route('public.vehicles.index') }}" class="btn btn-ghost btn-sm">Vedi tutto →</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
      @foreach($autoInEvidenza as $auto)
      @php $photoUrl = $auto->getFirstMediaUrl('sale_photos','thumb'); @endphp
      <a href="{{ url('auto-in-vendita/'.$auto->id.'-'.Str::slug($auto->brand.'-'.$auto->model)) }}" class="card" style="text-decoration:none;color:inherit;display:block">
        <div style="height:200px;overflow:hidden;position:relative;background:var(--bg3)">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $auto->brand }} {{ $auto->model }}" style="width:100%;height:100%;object-fit:cover;transition:.3s">
          @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text3)">
              <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
            </div>
          @endif
          @if($auto->badge_label)
            <span style="position:absolute;top:10px;left:10px;background:rgba(255,107,0,.92);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px">{{ $auto->badge_label }}</span>
          @endif
        </div>
        <div style="padding:16px">
          <div style="font-size:15px;font-weight:700;margin-bottom:4px">{{ $auto->brand }} {{ $auto->model }}</div>
          <div style="font-size:12px;color:var(--text3);margin-bottom:10px">{{ $auto->version }}</div>
          <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
            <span style="font-size:11px;background:var(--bg3);color:var(--text3);padding:2px 8px;border-radius:4px">{{ number_format($auto->mileage,0,',','.') }} km</span>
            <span style="font-size:11px;background:var(--bg3);color:var(--text3);padding:2px 8px;border-radius:4px">{{ ucfirst(str_replace('_',' ',$auto->fuel_type)) }}</span>
            <span style="font-size:11px;background:var(--bg3);color:var(--text3);padding:2px 8px;border-radius:4px">{{ $auto->year }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center">
            @if($auto->display_price)
              <div style="font-size:20px;font-weight:800;color:var(--orange)">{{ $auto->display_price }}</div>
            @else
              <div style="font-size:14px;color:var(--text3)">Prezzo su richiesta</div>
            @endif
            <span style="font-size:12px;color:var(--orange);font-weight:600">Scopri →</span>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px">
      <a href="{{ route('public.vehicles.index') }}" class="btn btn-primary">Vedi tutte le auto disponibili</a>
    </div>
  </div>
</section>
@endif

{{-- NOLEGGIO --}}
@if($veicoliNoleggio->count())
<section class="section" style="background:var(--bg2);border-top:1px solid var(--border)">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px">
      <div>
        <div class="section-label">Flotta disponibile</div>
        <h2 class="section-title">Noleggio veicoli</h2>
        <p class="section-sub">Noleggio breve e lungo termine. Veicoli sempre controllati e pronti alla consegna.</p>
      </div>
      <a href="{{ route('public.noleggio') }}" class="btn btn-ghost btn-sm">Vedi flotta →</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
      @foreach($veicoliNoleggio as $v)
      <a href="{{ route('public.noleggio.show', $v->id) }}" class="card" style="text-decoration:none;color:inherit;display:block;padding:24px">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
          <div style="width:50px;height:50px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">🚗</div>
          <div>
            <div style="font-size:15px;font-weight:700">{{ $v->brand }} {{ $v->model }}</div>
            <div style="font-size:12px;color:var(--text3)">Categoria {{ $v->category }} — {{ $v->seats }} posti</div>
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          @php $rate = $v->daily_rate_public ?: $v->daily_rate; @endphp
          @if($rate > 0)
            <div><span style="font-size:20px;font-weight:800;color:var(--orange)">€{{ number_format($rate,0,',','.') }}</span><span style="font-size:12px;color:var(--text3)">/giorno</span></div>
          @else
            <div style="font-size:13px;color:var(--text3)">Prezzo su richiesta</div>
          @endif
          <span style="font-size:12px;color:var(--orange);font-weight:600">Prenota →</span>
        </div>
      </a>
      @endforeach
    </div>
    <div style="text-align:center;margin-top:32px">
      <a href="{{ route('public.noleggio') }}" class="btn btn-primary">Vedi tutta la flotta</a>
    </div>
  </div>
</section>
@endif

{{-- CTA CONTATTI --}}
<section class="section">
  <div class="container">
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:60px;text-align:center;position:relative;overflow:hidden">
      <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(255,107,0,.06) 0,transparent 70%);pointer-events:none"></div>
      <div class="section-label" style="text-align:center">Siamo qui per te</div>
      <h2 class="section-title" style="text-align:center;margin:0 auto 16px">Hai domande? Scrivici</h2>
      <p style="color:var(--text2);margin-bottom:32px;max-width:460px;margin-left:auto;margin-right:auto">Il nostro team risponde entro 24 ore. Chiamaci o inviaci un messaggio.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="tel:+393278072650" class="btn btn-primary">📞 +39 327 807 2650</a>
        <a href="{{ route('public.contatti') }}" class="btn btn-ghost">Invia un messaggio</a>
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}
@media(max-width:900px){
  section > .container > div:first-child[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
  div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:repeat(2,1fr)!important}
  div[style*="grid-template-columns:repeat(3,1fr)"]{grid-template-columns:1fr!important}
}
</style>
@endpush
@endsection