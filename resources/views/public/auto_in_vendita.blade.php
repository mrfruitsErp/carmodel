<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Auto Usate in Vendita - AleCar S.r.l. Torino</title>
  <meta name="description" content="AleCar S.r.l. - Auto usate selezionate a Torino. Qualita garantita, prezzi trasparenti, IVA esposta. Via Ignazio Collino 29, Torino.">
  <meta name="robots" content="index, follow">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f0f2f5;color:#1a1a2e;line-height:1.5}

    /* COOKIE BANNER */
    #cookie-banner{position:fixed;bottom:0;left:0;right:0;background:#1a1a2e;color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;z-index:9999;font-size:13px;flex-wrap:wrap}
    #cookie-banner a{color:#ff6b00;text-decoration:underline}
    #cookie-banner button{background:#ff6b00;color:#fff;border:none;border-radius:6px;padding:8px 20px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap}
    #cookie-banner button.decline{background:transparent;border:1px solid #555;color:#aaa;margin-right:8px}

    /* HEADER */
    .header{background:#fff;border-bottom:3px solid #ff6b00;box-shadow:0 2px 8px rgba(0,0,0,.08);position:sticky;top:0;z-index:100}
    .header-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;height:70px}
    .header-contact{display:flex;align-items:center;gap:20px;font-size:13px;color:#555}
    .header-contact a{color:#1a1a2e;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:5px}
    .header-contact a:hover{color:#ff6b00}

    /* HERO */
    .hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:48px 20px;text-align:center}
    .hero-stats{display:flex;justify-content:center;gap:40px;margin-bottom:32px;flex-wrap:wrap}
    .stat{text-align:center}
    .stat-num{font-size:32px;font-weight:800;color:#ff6b00}
    .stat-label{font-size:12px;color:#aab8c2;text-transform:uppercase;letter-spacing:.08em}

    /* SEARCH */
    .search-wrap{max-width:900px;margin:0 auto}
    .search-box{background:#fff;border-radius:14px;padding:16px 20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;box-shadow:0 8px 32px rgba(0,0,0,.2)}
    .search-box input,.search-box select{border:1.5px solid #e0e0e0;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;background:#f8f9fa;transition:.2s}
    .search-box input:focus,.search-box select:focus{border-color:#ff6b00;background:#fff}
    .search-box input{flex:1;min-width:200px}
    .search-box select{min-width:150px}
    .btn-search{background:#ff6b00;color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap;transition:.2s}
    .btn-search:hover{background:#e05e00}

    /* CONTAINER */
    .container{max-width:1200px;margin:0 auto;padding:32px 20px}

    /* VANTAGGI */
    .vantaggi{background:#fff;border-radius:14px;padding:20px 24px;margin-bottom:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px}
    .vantaggio{display:flex;align-items:center;gap:12px}
    .vantaggio-icon{width:40px;height:40px;background:#fff3e0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .vantaggio-text{font-size:13px;color:#555;font-weight:500}

    /* RISULTATI */
    .results-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .results-count{font-size:15px;color:#555}
    .results-count strong{color:#1a1a2e}

    /* GRIGLIA */
    .cars-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px}

    /* CARD */
    .car-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.07);transition:transform .2s,box-shadow .2s}
    .car-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.12)}
    .car-card a{text-decoration:none;color:inherit;display:block}
    .car-photo{position:relative;height:200px;overflow:hidden;background:#f0f2f5}
    .car-photo img{width:100%;height:100%;object-fit:contain;background:#f8f8f8;transition:transform .3s}
    .car-card:hover .car-photo img{transform:scale(1.04)}
    .no-img{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#ccc;gap:8px}
    .no-img span{font-size:12px}
    .badge-anno{position:absolute;top:12px;left:12px;background:rgba(255,107,0,.92);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px}
    .badge-tratt{position:absolute;top:12px;right:12px;background:rgba(255,255,255,.92);color:#ff6b00;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;border:1px solid rgba(255,107,0,.3)}
    .photo-count{position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;padding:3px 8px;border-radius:8px}
    .car-info{padding:16px}
    .car-title{font-size:17px;font-weight:700;color:#1a1a2e;margin-bottom:2px}
    .car-version{font-size:12px;color:#888;margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .car-specs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
    .spec-tag{background:#f0f2f5;color:#555;font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500}
    .car-price-row{display:flex;align-items:flex-end;justify-content:space-between}
    .car-price{font-size:22px;font-weight:800;color:#ff6b00}
    .btn-detail{background:#1a1a2e;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block;transition:.2s}
    .btn-detail:hover{background:#ff6b00}

    /* AZIENDA */
    .azienda-bar{background:#fff;border-top:3px solid #ff6b00;padding:24px 0;margin-top:40px}
    .azienda-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:grid;grid-template-columns:auto 1fr 1fr;gap:32px;align-items:start}
    .azienda-logo img{height:60px;width:auto}
    .azienda-info h3{font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:8px}
    .azienda-info p{font-size:13px;color:#555;line-height:1.8}
    .azienda-info a{color:#ff6b00;text-decoration:none}
    .azienda-contatti h3{font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:8px}
    .contact-item{display:flex;align-items:center;gap:8px;font-size:13px;color:#555;margin-bottom:6px}
    .contact-item a{color:#1a1a2e;text-decoration:none;font-weight:500}
    .contact-item a:hover{color:#ff6b00}

    /* FOOTER */
    .footer{background:#1a1a2e;color:#aab8c2;padding:24px 20px;font-size:12px}
    .footer-inner{max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:10px}
    .footer-legal{display:flex;gap:16px;flex-wrap:wrap}
    .footer-legal a{color:#ff6b00;text-decoration:none}
    .footer-legal a:hover{text-decoration:underline}
    .footer-copy{color:#666;font-size:11px}

    /* MODAL PRIVACY */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:10000;align-items:center;justify-content:center}
    .modal-overlay.active{display:flex}
    .modal{background:#fff;border-radius:14px;max-width:600px;width:90%;max-height:80vh;overflow-y:auto;padding:28px}
    .modal h2{font-size:18px;font-weight:700;margin-bottom:16px;color:#1a1a2e}
    .modal p{font-size:13px;color:#555;line-height:1.8;margin-bottom:12px}
    .modal h3{font-size:14px;font-weight:700;color:#1a1a2e;margin:16px 0 8px}
    .modal-close{float:right;background:#f0f0f0;border:none;border-radius:6px;padding:6px 14px;cursor:pointer;font-size:13px;font-weight:600}

    @media(max-width:768px){
      .header-contact{display:none}
      .azienda-inner{grid-template-columns:1fr}
      .hero-stats{gap:20px}
      .search-box{flex-direction:column}
      .search-box input,.search-box select,.btn-search{width:100%}
    }
  </style>
</head>
<body>

{{-- COOKIE BANNER --}}
<div id="cookie-banner" style="display:none">
  <span>Utilizziamo cookie tecnici per il corretto funzionamento del sito. <a href="#" onclick="showPrivacy();return false">Privacy Policy</a></span>
  <div>
    <button class="decline" onclick="closeCookies()">Solo necessari</button>
    <button onclick="acceptCookies()">Accetta tutto</button>
  </div>
</div>

{{-- HEADER --}}
<header class="header">
  <div class="header-inner">
    <a href="{{ url('/auto-in-vendita') }}">
      <img src="{{ asset('images/logo_alecar.jpg') }}" alt="AleCar S.r.l." style="height:50px;width:auto;object-fit:contain">
    </a>
    <div class="header-contact">
      <a href="tel:+393278072650">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
        +39 327 807 2650
      </a>
      <a href="mailto:alecarto7@gmail.com">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        alecarto7@gmail.com
      </a>
      <span style="color:#aaa">|</span>
      <span style="font-size:12px;color:#888">Via Ignazio Collino 29, Torino</span>
      <a href="{{ url('/') }}" style="background:#f0f2f5;padding:6px 14px;border-radius:6px;font-size:12px">Area gestionale</a>
    </div>
  </div>
</header>

{{-- HERO --}}
<section class="hero">
  <div class="hero-stats">
    <div class="stat">
      <div class="stat-num">{{ $vehicles->total() }}</div>
      <div class="stat-label">Disponibili</div>
    </div>
    <div class="stat">
      <div class="stat-num">100%</div>
      <div class="stat-label">Garantiti</div>
    </div>
    <div class="stat">
      <div class="stat-num">24h</div>
      <div class="stat-label">Risposta</div>
    </div>
  </div>
  <div class="search-wrap">
    <form method="GET" action="{{ url('auto-in-vendita') }}" class="search-box">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cerca marca, modello...">
      <select name="fuel">
        <option value="">Tutti i carburanti</option>
        @foreach(['benzina','diesel','ibrido','elettrico','gpl','metano'] as $fuel)
        <option value="{{ $fuel }}" {{ request('fuel')===$fuel?'selected':'' }}>{{ ucfirst($fuel) }}</option>
        @endforeach
      </select>
      <select name="price_max">
        <option value="">Qualsiasi prezzo</option>
        @foreach([10000=>'Fino a 10.000 euro',20000=>'Fino a 20.000 euro',30000=>'Fino a 30.000 euro',50000=>'Fino a 50.000 euro'] as $val=>$label)
        <option value="{{ $val }}" {{ request('price_max')==$val?'selected':'' }}>{{ $label }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn-search">Cerca</button>
      @if(request()->hasAny(['search','fuel','price_max']))
        <a href="{{ url('auto-in-vendita') }}" style="font-size:13px;color:#888;white-space:nowrap;text-decoration:none">Reset</a>
      @endif
    </form>
  </div>
</section>

<div class="container">

  {{-- VANTAGGI --}}
  <div class="vantaggi">
    <div class="vantaggio">
      <div class="vantaggio-icon"><svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg></div>
      <span class="vantaggio-text">Veicoli controllati e garantiti</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon"><svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
      <span class="vantaggio-text">Prezzi trasparenti, IVA esposta</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon"><svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg></div>
      <span class="vantaggio-text">Risposta garantita in 24 ore</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon"><svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      <span class="vantaggio-text">Consegna a domicilio disponibile</span>
    </div>
  </div>

  {{-- RISULTATI --}}
  <div class="results-bar">
    <span class="results-count"><strong>{{ $vehicles->total() }}</strong> veicoli trovati</span>
  </div>

  @if($vehicles->isEmpty())
  <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:14px">
    <svg width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:16px"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
    <h2 style="font-size:20px;color:#555;margin-bottom:8px">Nessun veicolo trovato</h2>
    <p style="color:#888;font-size:14px">Prova a modificare i filtri di ricerca</p>
  </div>
  @else
  <div class="cars-grid">
    @foreach($vehicles as $vehicle)
    @php
      $photoUrl = $vehicle->getFirstMediaUrl('sale_photos', 'thumb');
      $photoCount = $vehicle->getMedia('sale_photos')->count();
      $publicUrl = url('auto-in-vendita/'.$vehicle->id.'-'.Str::slug($vehicle->brand.'-'.$vehicle->model));
    @endphp
    <div class="car-card">
      <a href="{{ $publicUrl }}">
        <div class="car-photo">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" loading="lazy">
          @else
            <div class="no-img">
              <svg width="56" height="56" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
              <span>Nessuna foto</span>
            </div>
          @endif
          <span class="badge-anno">{{ $vehicle->year }}</span>
          @if($vehicle->price_negotiable)<span class="badge-tratt">Trattabile</span>@endif
          @if($photoCount > 0)<div class="photo-count">{{ $photoCount }} foto</div>@endif
        </div>
        <div class="car-info">
          <div class="car-title">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
          <div class="car-version">{{ $vehicle->version ?? '' }}</div>
          <div class="car-specs">
            <span class="spec-tag">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
            <span class="spec-tag">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
            @if($vehicle->transmission)<span class="spec-tag">{{ ucfirst($vehicle->transmission) }}</span>@endif
            @if($vehicle->power_hp)<span class="spec-tag">{{ $vehicle->power_hp }} CV</span>@endif
            @if($vehicle->color)<span class="spec-tag">{{ $vehicle->color }}</span>@endif
          </div>
          <div class="car-price-row">
            <div>
              <div class="car-price">{{ number_format($vehicle->asking_price,0,',','.') }} euro</div>
              @if($vehicle->vat_deductible)<div style="font-size:11px;color:#2e7d32;font-weight:600">IVA detraibile</div>@endif
            </div>
            <span class="btn-detail">Scopri di piu</span>
          </div>
        </div>
      </a>
    </div>
    @endforeach
  </div>
  <div style="margin-top:24px">{{ $vehicles->links() }}</div>
  @endif

</div>

{{-- SEZIONE AZIENDA --}}
<div class="azienda-bar">
  <div class="azienda-inner">
    <div class="azienda-logo">
      <img src="{{ asset('images/logo_alecar.jpg') }}" alt="AleCar S.r.l." style="height:60px;width:auto">
    </div>
    <div class="azienda-info">
      <h3>AleCar S.r.l.</h3>
      <p>
        Via Ignazio Collino 29 &mdash; 10100 Torino (TO)<br>
        P.IVA: 11352180019 &mdash; C.F.: 11352180019<br>
        Cod. Univoco SDI: M5UXCR1<br>
        Iscritta alla CCIAA di Torino<br>
        <a href="mailto:alecar@legalmail.it">PEC: alecar@legalmail.it</a>
      </p>
    </div>
    <div class="azienda-contatti">
      <h3>Contatti</h3>
      <div class="contact-item">
        <svg width="15" height="15" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
        <a href="tel:+393278072650">+39 327 807 2650</a>
      </div>
      <div class="contact-item">
        <svg width="15" height="15" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <a href="mailto:alecarto7@gmail.com">alecarto7@gmail.com</a>
      </div>
      <div class="contact-item">
        <svg width="15" height="15" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <span>Via Ignazio Collino 29, Torino</span>
      </div>
    </div>
  </div>
</div>

{{-- FOOTER --}}
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-legal">
      <a href="#" onclick="showPrivacy();return false">Informativa Privacy (GDPR)</a>
      <a href="#" onclick="showCookiePolicy();return false">Cookie Policy</a>
      <a href="#" onclick="showCondizioni();return false">Condizioni di Vendita</a>
      <a href="{{ url('/') }}">Area Gestionale</a>
    </div>
    <p class="footer-copy">
      &copy; {{ date('Y') }} AleCar S.r.l. &mdash; Via Ignazio Collino 29, 10100 Torino (TO) &mdash; P.IVA 11352180019 &mdash; Cod. SDI: M5UXCR1<br>
      I prezzi indicati sono IVA inclusa salvo diversa indicazione. AleCar S.r.l. si riserva il diritto di modificare prezzi e disponibilita senza preavviso.
    </p>
  </div>
</footer>

{{-- MODAL PRIVACY --}}
<div id="modal-privacy" class="modal-overlay">
  <div class="modal">
    <button class="modal-close" onclick="document.getElementById('modal-privacy').classList.remove('active')">Chiudi</button>
    <h2>Informativa sul trattamento dei dati personali</h2>
    <p>Ai sensi degli articoli 13 e 14 del Regolamento (UE) 2016/679 (GDPR), AleCar S.r.l. informa gli utenti del presente sito circa il trattamento dei dati personali.</p>
    <h3>Titolare del trattamento</h3>
    <p>AleCar S.r.l. &mdash; Via Ignazio Collino 29, Torino (TO) &mdash; P.IVA 11352180019<br>Email: alecarto7@gmail.com &mdash; PEC: alecar@legalmail.it</p>
    <h3>Dati raccolti</h3>
    <p>Il sito raccoglie i dati inseriti nei form di contatto (nome, email, telefono, messaggio) al solo scopo di rispondere alle richieste degli utenti.</p>
    <h3>Finalita del trattamento</h3>
    <p>I dati sono trattati per: rispondere a richieste di informazioni sui veicoli, gestire eventuali trattative commerciali, adempiere obblighi di legge.</p>
    <h3>Conservazione</h3>
    <p>I dati sono conservati per il tempo strettamente necessario al soddisfacimento delle finalita per le quali sono stati raccolti, e comunque non oltre 24 mesi.</p>
    <h3>Diritti dell'interessato</h3>
    <p>L'utente puo esercitare i diritti di accesso, rettifica, cancellazione, opposizione, portabilita scrivendo a alecarto7@gmail.com.</p>
    <h3>Cookie</h3>
    <p>Il sito utilizza esclusivamente cookie tecnici necessari al funzionamento. Non vengono utilizzati cookie di profilazione o marketing.</p>
  </div>
</div>

{{-- MODAL CONDIZIONI --}}
<div id="modal-condizioni" class="modal-overlay">
  <div class="modal">
    <button class="modal-close" onclick="document.getElementById('modal-condizioni').classList.remove('active')">Chiudi</button>
    <h2>Condizioni Generali di Vendita</h2>
    <h3>Prezzi</h3>
    <p>Tutti i prezzi indicati sono IVA inclusa salvo diversa indicazione esplicita. AleCar S.r.l. si riserva il diritto di modificare i prezzi senza preavviso. Il prezzo vincolante e quello concordato al momento della firma del contratto di vendita.</p>
    <h3>Disponibilita</h3>
    <p>I veicoli presenti sul sito sono soggetti a disponibilita. AleCar S.r.l. non e responsabile per vendite avvenute nel periodo intercorrente tra la visualizzazione online e la conferma dell'acquisto.</p>
    <h3>Garanzia</h3>
    <p>I veicoli usati sono venduti con garanzia contrattuale secondo le condizioni pattuite. Si applica la normativa vigente in materia di garanzia sui beni di consumo usati (D.Lgs. 206/2005).</p>
    <h3>Foro competente</h3>
    <p>Per qualsiasi controversia e competente il Foro di Torino.</p>
    <h3>Dati aziendali</h3>
    <p>AleCar S.r.l. &mdash; Via Ignazio Collino 29, Torino &mdash; P.IVA 11352180019 &mdash; Cod. SDI M5UXCR1 &mdash; PEC: alecar@legalmail.it</p>
  </div>
</div>

<script>
// Cookie banner
if(!localStorage.getItem('cookies_accepted')){
  document.getElementById('cookie-banner').style.display='flex';
}
function acceptCookies(){
  localStorage.setItem('cookies_accepted','all');
  document.getElementById('cookie-banner').style.display='none';
}
function closeCookies(){
  localStorage.setItem('cookies_accepted','minimal');
  document.getElementById('cookie-banner').style.display='none';
}
function showPrivacy(){document.getElementById('modal-privacy').classList.add('active');}
function showCondizioni(){document.getElementById('modal-condizioni').classList.add('active');}
function showCookiePolicy(){document.getElementById('modal-privacy').classList.add('active');}
// Chiudi modal cliccando fuori
document.querySelectorAll('.modal-overlay').forEach(function(el){
  el.addEventListener('click',function(e){if(e.target===el)el.classList.remove('active');});
});
</script>

</body>
</html>