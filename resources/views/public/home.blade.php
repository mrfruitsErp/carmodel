@extends('public.layout')
@php
  use App\Models\Setting;
  $pageTitle = Setting::get('page_home_title') ?: Setting::get('seo_site_title','AleCar S.r.l. - Vendita Auto e Noleggio Torino');
  $pageDesc  = Setting::get('page_home_description') ?: Setting::get('seo_site_description','AleCar S.r.l. Torino - Auto usate garantite e noleggio veicoli.');
@endphp
@section('title', $pageTitle)
@section('description', $pageDesc)

@section('content')
@php
  $heroBadge     = Setting::get('hero_badge','TORINO — DAL 2018');
  $heroTitolo    = Setting::get('hero_titolo','Auto <span style="color:var(--orange)">selezionate</span><br>e noleggio<br>su misura');
  $heroSotto     = Setting::get('hero_sottotitolo','AleCar S.r.l. — veicoli usati garantiti, prezzi trasparenti e IVA esposta.');
  $heroCta1      = Setting::get('hero_cta1_testo','Vedi auto in vendita');
  $heroCta2      = Setting::get('hero_cta2_testo','Noleggio veicoli');
  $heroImg       = Setting::get('hero_immagine','');
  $indirizzo     = Setting::get('azienda_indirizzo','Via Ignazio Collino 29, Torino');
  $h1Home        = Setting::get('page_home_h1','');
  $ctaLabel      = Setting::get('home_cta_label','Siamo qui per te');
  $ctaTitolo     = Setting::get('home_cta_titolo','Hai domande? Scrivici');
  $ctaTesto      = Setting::get('home_cta_testo','Il nostro team risponde entro 24 ore. Chiamaci o inviaci un messaggio.');
  $ctaBtn1       = Setting::get('home_cta_btn1', Setting::get('azienda_telefono','+39 327 807 2650'));
  $ctaBtn2       = Setting::get('home_cta_btn2','Invia un messaggio');
@endphp

{{-- HERO --}}
<section class="hero-section"@if($heroImg) style="background-image:linear-gradient(to bottom, rgba(10,10,10,.7),rgba(10,10,10,.95)),url('{{ $heroImg }}');background-size:cover;background-position:center"@endif>
  <div class="hero-overlay"></div>
  <div class="hero-glow"></div>

  <div class="container hero-container">
    <div class="hero-grid">
      <div>
        <div class="hero-badge">
          <span class="hero-badge-dot"></span>
          {{ $heroBadge }}
        </div>
        <h1 class="hero-title">
          @if($h1Home){!! $h1Home !!}@else{!! $heroTitolo !!}@endif
        </h1>
        <p class="hero-subtitle">{{ $heroSotto }}</p>
        <div class="hero-cta">
          <a href="{{ route('public.vehicles.index') }}" class="btn btn-primary hero-btn">{{ $heroCta1 }}</a>
          <a href="{{ route('public.noleggio') }}" class="btn btn-ghost hero-btn">{{ $heroCta2 }}</a>
        </div>
        {{-- Stats --}}
        <div class="hero-stats">
          <div class="hero-stat">
            <div class="hero-stat-num">{{ $totaleAuto }}</div>
            <div class="hero-stat-lbl">Auto disponibili</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num">100%</div>
            <div class="hero-stat-lbl">Garantite</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-num">24h</div>
            <div class="hero-stat-lbl">Risposta</div>
          </div>
        </div>
      </div>
      {{-- Right side decorativa (nascosta su mobile) --}}
      <div class="hero-side">
        <div class="hero-side-card">
          <div class="hero-side-pattern"></div>
          <div class="hero-side-icon">
            <svg width="80" height="80" fill="none" stroke="var(--orange)" stroke-width="1" viewBox="0 0 24 24" opacity=".3"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
            <span style="font-size:12px;color:var(--text3)">AleCar — Torino</span>
          </div>
          <div class="hero-side-info">
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px">SEDE</div>
            <div style="font-size:13px;color:var(--text);font-weight:600">{{ $indirizzo }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- VANTAGGI --}}
@php
  $vantaggi = [];
  for($i=1;$i<=4;$i++){
    $vantaggi[] = [
      Setting::get("vantaggio_{$i}_icon",['🔍','💰','📞','🚗'][$i-1]),
      Setting::get("vantaggio_{$i}_titolo",['Veicoli controllati','Prezzi trasparenti','Risposta in 24h','Consegna a domicilio'][$i-1]),
      Setting::get("vantaggio_{$i}_desc",['Ogni auto viene verificata e certificata prima della vendita','IVA sempre esposta, nessun costo nascosto','Rispondiamo a tutte le richieste entro un giorno lavorativo','Consegniamo il veicolo direttamente da te'][$i-1]),
    ];
  }
@endphp
<section class="vantaggi-section">
  <div class="container">
    <div class="vantaggi-grid">
      @foreach($vantaggi as [$icon,$title,$desc])
      <div class="vantaggio">
        <div class="vantaggio-icon">{{ $icon }}</div>
        <div>
          <div class="vantaggio-title">{{ $title }}</div>
          <div class="vantaggio-desc">{{ $desc }}</div>
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
    <div class="section-header">
      <div>
        <div class="section-label">Stock selezionato</div>
        <h2 class="section-title">Auto in vendita</h2>
        <p class="section-sub">Veicoli usati selezionati, controllati e garantiti. IVA sempre esposta.</p>
      </div>
      <a href="{{ route('public.vehicles.index') }}" class="btn btn-ghost btn-sm hide-on-mobile">Vedi tutto →</a>
    </div>
    <div class="cards-grid">
      @foreach($autoInEvidenza as $auto)
      @php $photoUrl = $auto->getFirstMediaUrl('sale_photos','thumb'); @endphp
      <a href="{{ url('auto-in-vendita/'.$auto->id.'-'.Str::slug($auto->brand.'-'.$auto->model)) }}" class="card auto-card">
        <div class="auto-card-img">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $auto->brand }} {{ $auto->model }}">
          @else
            <div class="auto-card-noimg">
              <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
            </div>
          @endif
          @if($auto->badge_label)
            <span class="auto-card-badge">{{ $auto->badge_label }}</span>
          @endif
        </div>
        <div class="auto-card-body">
          <div class="auto-card-title">{{ $auto->brand }} {{ $auto->model }}</div>
          <div class="auto-card-version">{{ $auto->version }}</div>
          <div class="auto-card-tags">
            <span>{{ number_format($auto->mileage,0,',','.') }} km</span>
            <span>{{ ucfirst(str_replace('_',' ',$auto->fuel_type)) }}</span>
            <span>{{ $auto->year }}</span>
          </div>
          <div class="auto-card-footer">
            @if($auto->display_price)
              <div class="auto-card-price">{{ $auto->display_price }}</div>
            @else
              <div class="auto-card-price-na">Prezzo su richiesta</div>
            @endif
            <span class="auto-card-cta">Scopri →</span>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    <div class="section-cta-center">
      <a href="{{ route('public.vehicles.index') }}" class="btn btn-primary">Vedi tutte le auto disponibili</a>
    </div>
  </div>
</section>
@endif

{{-- NOLEGGIO --}}
@if($veicoliNoleggio->count())
<section class="section section-alt">
  <div class="container">
    <div class="section-header">
      <div>
        <div class="section-label">Flotta disponibile</div>
        <h2 class="section-title">Noleggio veicoli</h2>
        <p class="section-sub">Noleggio breve e lungo termine. Veicoli sempre controllati e pronti alla consegna.</p>
      </div>
      <a href="{{ route('public.noleggio') }}" class="btn btn-ghost btn-sm hide-on-mobile">Vedi flotta →</a>
    </div>
    <div class="cards-grid">
      @foreach($veicoliNoleggio as $v)
      <a href="{{ route('public.noleggio.show', $v->id) }}" class="card noleggio-card">
        <div class="noleggio-card-head">
          <div class="noleggio-card-icon">🚗</div>
          <div>
            <div class="noleggio-card-title">{{ $v->brand }} {{ $v->model }}</div>
            <div class="noleggio-card-meta">Categoria {{ $v->category }} — {{ $v->seats }} posti</div>
          </div>
        </div>
        <div class="noleggio-card-footer">
          @php $rate = $v->daily_rate_public ?: $v->daily_rate; @endphp
          @if($rate > 0)
            <div><span class="noleggio-card-price">€{{ number_format($rate,0,',','.') }}</span><span class="noleggio-card-price-unit">/giorno</span></div>
          @else
            <div class="auto-card-price-na">Prezzo su richiesta</div>
          @endif
          <span class="auto-card-cta">Prenota →</span>
        </div>
      </a>
      @endforeach
    </div>
    <div class="section-cta-center">
      <a href="{{ route('public.noleggio') }}" class="btn btn-primary">Vedi tutta la flotta</a>
    </div>
  </div>
</section>
@endif

{{-- CTA CONTATTI --}}
<section class="section">
  <div class="container">
    <div class="cta-box">
      <div class="cta-glow"></div>
      <div class="section-label" style="text-align:center">{{ $ctaLabel }}</div>
      <h2 class="section-title cta-title">{{ $ctaTitolo }}</h2>
      <p class="cta-text">{{ $ctaTesto }}</p>
      <div class="cta-actions">
        <a href="tel:{{ preg_replace('/[^+\d]/','',$ctaBtn1) }}" class="btn btn-primary">📞 {{ $ctaBtn1 }}</a>
        <a href="{{ route('public.contatti') }}" class="btn btn-ghost">{{ $ctaBtn2 }}</a>
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}

/* HERO */
.hero-section{min-height:90vh;display:flex;align-items:center;position:relative;overflow:hidden;background:var(--bg);padding:80px 0 60px}
.hero-overlay{position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.015) 0,rgba(255,255,255,.015) 1px,transparent 0,transparent 50%);background-size:8px 8px;pointer-events:none}
.hero-glow{position:absolute;top:20%;right:10%;width:min(500px,80vw);height:min(500px,80vw);background:radial-gradient(circle,rgba(255,107,0,.12) 0,transparent 70%);pointer-events:none}
.hero-container{position:relative;z-index:1}
.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(24px,5vw,60px);align-items:center}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.3);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:600;color:var(--orange);letter-spacing:.06em;margin-bottom:20px}
.hero-badge-dot{width:6px;height:6px;border-radius:50%;background:var(--orange);animation:pulse 2s infinite}
.hero-title{font-size:clamp(32px,6vw,64px);font-weight:900;line-height:1.1;margin-bottom:18px}
.hero-subtitle{font-size:clamp(14px,2vw,16px);color:var(--text2);line-height:1.7;margin-bottom:28px;max-width:480px}
.hero-cta{display:flex;gap:12px;flex-wrap:wrap}
.hero-btn{font-size:clamp(13px,1.6vw,15px);padding:13px 24px}
.hero-stats{display:flex;gap:clamp(16px,4vw,32px);margin-top:36px;padding-top:28px;border-top:1px solid var(--border);flex-wrap:wrap}
.hero-stat-num{font-size:clamp(22px,4vw,32px);font-weight:800;color:var(--orange);line-height:1}
.hero-stat-lbl{font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;margin-top:4px}
.hero-side{display:flex;align-items:center;justify-content:center;position:relative}
.hero-side-card{width:100%;max-width:480px;aspect-ratio:4/3;background:var(--bg2);border:1px solid var(--border);border-radius:16px;overflow:hidden;position:relative}
.hero-side-pattern{position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.02) 0,rgba(255,255,255,.02) 1px,transparent 0,transparent 50%);background-size:6px 6px}
.hero-side-icon{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px}
.hero-side-info{position:absolute;bottom:16px;left:16px;right:16px;background:rgba(0,0,0,.7);border-radius:8px;padding:12px 16px;backdrop-filter:blur(8px)}

/* VANTAGGI — auto-fit responsive */
.vantaggi-section{padding:48px 0;background:var(--bg2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.vantaggi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}
.vantaggio{display:flex;gap:16px;align-items:flex-start}
.vantaggio-icon{width:44px;height:44px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.vantaggio-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px}
.vantaggio-desc{font-size:12px;color:var(--text3);line-height:1.6}

/* SECTION HEADER */
.section-header{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;gap:16px;flex-wrap:wrap}
.section-cta-center{text-align:center;margin-top:32px}
.section-alt{background:var(--bg2);border-top:1px solid var(--border)}

/* CARDS GRID — auto-fit, sempre responsive */
.cards-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px}

/* AUTO CARD */
.auto-card{text-decoration:none;color:inherit;display:block}
.auto-card-img{height:200px;overflow:hidden;position:relative;background:var(--bg3)}
.auto-card-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.auto-card-noimg{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text3)}
.auto-card-badge{position:absolute;top:10px;left:10px;background:rgba(255,107,0,.92);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.auto-card-body{padding:16px}
.auto-card-title{font-size:15px;font-weight:700;margin-bottom:4px}
.auto-card-version{font-size:12px;color:var(--text3);margin-bottom:10px;min-height:18px}
.auto-card-tags{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
.auto-card-tags span{font-size:11px;background:var(--bg3);color:var(--text3);padding:2px 8px;border-radius:4px}
.auto-card-footer{display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap}
.auto-card-price{font-size:20px;font-weight:800;color:var(--orange)}
.auto-card-price-na{font-size:13px;color:var(--text3)}
.auto-card-cta{font-size:12px;color:var(--orange);font-weight:600}

/* NOLEGGIO CARD */
.noleggio-card{text-decoration:none;color:inherit;display:block;padding:20px}
.noleggio-card-head{display:flex;align-items:center;gap:14px;margin-bottom:14px}
.noleggio-card-icon{width:50px;height:50px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.noleggio-card-title{font-size:15px;font-weight:700}
.noleggio-card-meta{font-size:12px;color:var(--text3)}
.noleggio-card-footer{display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap}
.noleggio-card-price{font-size:20px;font-weight:800;color:var(--orange)}
.noleggio-card-price-unit{font-size:12px;color:var(--text3)}

/* CTA BOX */
.cta-box{background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:clamp(28px,6vw,60px);text-align:center;position:relative;overflow:hidden}
.cta-glow{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(255,107,0,.06) 0,transparent 70%);pointer-events:none}
.cta-title{text-align:center;margin:0 auto 16px;position:relative}
.cta-text{color:var(--text2);margin-bottom:28px;max-width:460px;margin-left:auto;margin-right:auto;position:relative;font-size:clamp(13px,1.6vw,15px)}
.cta-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative}

/* HIDE ON MOBILE */
.hide-on-mobile{display:inline-flex}

/* ─── BREAKPOINTS ─── */

/* Tablet ≤ 900px */
@media(max-width:900px){
  .hero-section{min-height:auto;padding:48px 0 40px}
  .hero-grid{grid-template-columns:1fr;gap:32px}
  .hero-side{display:none}
  .vantaggi-grid{grid-template-columns:repeat(2,1fr)}
}

/* Mobile ≤ 600px */
@media(max-width:600px){
  .hero-section{padding:32px 0}
  .hero-stats{gap:20px;justify-content:space-between;width:100%}
  .hero-stat{flex:1;min-width:0}
  .hero-cta{width:100%}
  .hero-cta .btn{flex:1;text-align:center;justify-content:center;min-width:140px}
  .vantaggi-grid{grid-template-columns:1fr}
  .vantaggi-section{padding:32px 0}
  .section-header{margin-bottom:24px}
  .hide-on-mobile{display:none!important}
  .cards-grid{grid-template-columns:1fr;gap:16px}
  .auto-card-img{height:180px}
  .cta-actions{flex-direction:column;align-items:stretch}
  .cta-actions .btn{justify-content:center}
}

/* Mobile stretto ≤ 380px */
@media(max-width:380px){
  .hero-stat-num{font-size:20px}
  .hero-cta .btn{font-size:13px;padding:11px 16px}
}
</style>
@endpush
@endsection
