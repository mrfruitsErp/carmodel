<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'AleCar S.r.l. - Torino') | AleCar</title>
  <meta name="description" content="@yield('description', 'AleCar S.r.l. - Vendita auto usate e noleggio a Torino. Via Ignazio Collino 29.')">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}">
  <style>
    :root {
      --bg:        #0a0a0a;
      --bg2:       #111111;
      --bg3:       #1a1a1a;
      --bg4:       #222222;
      --border:    #2a2a2a;
      --border2:   #333333;
      --text:      #f0f0f0;
      --text2:     #bbbbbb;
      --text3:     #777777;
      --orange:    #ff6b00;
      --orange2:   #e05e00;
      --orange-bg: rgba(255,107,0,.08);
      --mono:      'Courier New', monospace;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--bg);color:var(--text);line-height:1.6}
    ::-webkit-scrollbar{width:6px;height:6px}
    ::-webkit-scrollbar-track{background:var(--bg2)}
    ::-webkit-scrollbar-thumb{background:var(--orange);border-radius:3px}

    /* NAVBAR */
    .navbar{position:fixed;top:0;left:0;right:0;z-index:1000;background:rgba(10,10,10,.95);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);height:64px;display:flex;align-items:center}
    .navbar-inner{max-width:1200px;margin:0 auto;padding:0 24px;display:flex;align-items:center;justify-content:space-between;width:100%}
    .navbar-logo img{height:40px;width:auto}
    .navbar-menu{display:flex;align-items:center;gap:4px}
    .navbar-menu a{color:var(--text2);text-decoration:none;font-size:13px;font-weight:500;padding:7px 14px;border-radius:6px;transition:.15s;letter-spacing:.02em}
    .navbar-menu a:hover,.navbar-menu a.active{color:var(--orange);background:var(--orange-bg)}
    .navbar-cta{background:var(--orange)!important;color:#000!important;font-weight:700!important}
    .navbar-cta:hover{background:var(--orange2)!important}
    .hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:4px}
    .hamburger span{width:22px;height:2px;background:var(--text2);border-radius:2px;transition:.2s}
    .mobile-menu{display:none;position:fixed;top:64px;left:0;right:0;background:var(--bg2);border-bottom:1px solid var(--border);padding:16px 24px;z-index:999}
    .mobile-menu a{display:block;color:var(--text2);text-decoration:none;padding:10px 0;font-size:14px;border-bottom:1px solid var(--border)}
    .mobile-menu.open{display:block}

    /* MAIN */
    main{padding-top:64px;min-height:100vh}

    /* LAYOUT */
    .section{padding:80px 0}
    .section-sm{padding:48px 0}
    .container{max-width:1200px;margin:0 auto;padding:0 24px}
    .section-label{font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--orange);margin-bottom:10px}
    .section-title{font-size:36px;font-weight:800;color:var(--text);line-height:1.2;margin-bottom:16px}
    .section-sub{font-size:16px;color:var(--text2);max-width:560px}

    /* CARD */
    .card{background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden;transition:.2s}
    .card:hover{border-color:var(--orange);transform:translateY(-3px);box-shadow:0 12px 40px rgba(255,107,0,.1)}

    /* BUTTONS */
    .btn{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:.15s;border:none}
    .btn-primary{background:var(--orange);color:#000}
    .btn-primary:hover{background:var(--orange2);color:#000}
    .btn-ghost{background:transparent;color:var(--text2);border:1px solid var(--border2)}
    .btn-ghost:hover{border-color:var(--orange);color:var(--orange)}
    .btn-sm{padding:7px 16px;font-size:13px}

    /* FORM */
    .form-group{margin-bottom:16px}
    .form-label{display:block;font-size:12px;font-weight:600;color:var(--text3);letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px}
    .form-input,.form-select,.form-textarea{width:100%;background:var(--bg3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;transition:.15s;font-family:inherit}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--orange);background:var(--bg4)}
    .form-textarea{resize:vertical;min-height:120px}
    .form-select option{background:var(--bg3)}
    .form-check{display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--text2);cursor:pointer}
    .form-check input[type=checkbox]{width:16px;height:16px;accent-color:var(--orange);flex-shrink:0;margin-top:2px}
    .form-check a{color:var(--orange);text-decoration:none}
    .form-check a:hover{text-decoration:underline}

    /* BADGES */
    .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
    .badge-orange{background:var(--orange-bg);color:var(--orange);border:1px solid rgba(255,107,0,.3)}
    .badge-green{background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2)}

    /* DIVIDER */
    .divider{height:1px;background:linear-gradient(90deg,transparent,var(--border2),transparent);margin:40px 0}
    .orange-line{width:40px;height:3px;background:var(--orange);border-radius:2px;margin-bottom:20px}

    /* ALERTS */
    .alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);color:#4ade80;padding:14px 18px;border-radius:8px;font-size:14px;margin-bottom:20px}
    .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#f87171;padding:14px 18px;border-radius:8px;font-size:14px;margin-bottom:20px}

    /* FOOTER */
    .footer{background:var(--bg2);border-top:1px solid var(--border);padding:48px 0 24px}
    .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px}
    .footer-logo{margin-bottom:16px}
    .footer-logo img{height:44px;width:auto}
    .footer-desc{font-size:13px;color:var(--text3);line-height:1.8}
    .footer-col h4{font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--text3);margin-bottom:14px}
    .footer-col a,.footer-col p{display:block;font-size:13px;color:var(--text3);text-decoration:none;margin-bottom:8px;transition:.15s}
    .footer-col a:hover{color:var(--orange)}
    .footer-bottom{border-top:1px solid var(--border);padding-top:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
    .footer-copy{font-size:11px;color:var(--text3)}
    .footer-legal{display:flex;gap:16px;flex-wrap:wrap}
    .footer-legal a{font-size:11px;color:var(--text3);text-decoration:none;transition:.15s}
    .footer-legal a:hover{color:var(--orange)}

    /* COOKIE BANNER */
    #cookie-banner{position:fixed;bottom:0;left:0;right:0;background:var(--bg2);border-top:2px solid var(--orange);padding:20px 24px;display:none;z-index:9999;box-shadow:0 -8px 32px rgba(0,0,0,.4)}
    .cookie-inner{max-width:1200px;margin:0 auto;display:flex;gap:24px;align-items:flex-start;flex-wrap:wrap}
    .cookie-text{flex:1;min-width:260px}
    .cookie-text h4{font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px}
    .cookie-text p{font-size:12px;color:var(--text2);line-height:1.7}
    .cookie-text a{color:var(--orange);text-decoration:none}
    .cookie-details{margin-top:10px;display:none}
    .cookie-details.open{display:block}
    .cookie-type{display:flex;align-items:center;justify-content:space-between;background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:10px 14px;margin-bottom:8px;font-size:12px}
    .cookie-type-label{color:var(--text2)}
    .cookie-type-badge{font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px}
    .cookie-always{background:rgba(34,197,94,.1);color:#4ade80}
    .cookie-toggle{position:relative;display:inline-block;width:36px;height:20px}
    .cookie-toggle input{display:none}
    .cookie-slider{position:absolute;inset:0;background:var(--border2);border-radius:10px;cursor:pointer;transition:.2s}
    .cookie-toggle input:checked + .cookie-slider{background:var(--orange)}
    .cookie-slider:before{content:'';position:absolute;width:14px;height:14px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s}
    .cookie-toggle input:checked + .cookie-slider:before{transform:translateX(16px)}
    .cookie-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap;flex-shrink:0}
    .cookie-btn{padding:9px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:.15s}
    .cookie-btn-accept{background:var(--orange);color:#000}
    .cookie-btn-accept:hover{background:var(--orange2)}
    .cookie-btn-save{background:var(--bg3);color:var(--text2);border:1px solid var(--border2)}
    .cookie-btn-save:hover{border-color:var(--orange);color:var(--orange)}
    .cookie-btn-details{background:transparent;color:var(--text3);font-size:12px;text-decoration:underline;border:none;cursor:pointer}

    /* MODAL LEGALE */
    .legal-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:10000;align-items:center;justify-content:center;padding:20px}
    .legal-modal.open{display:flex}
    .legal-modal-box{background:var(--bg2);border:1px solid var(--border);border-radius:16px;max-width:700px;width:100%;max-height:85vh;display:flex;flex-direction:column}
    .legal-modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
    .legal-modal-header h3{font-size:17px;font-weight:700}
    .legal-modal-close{background:var(--bg3);border:1px solid var(--border2);color:var(--text2);border-radius:6px;padding:5px 12px;cursor:pointer;font-size:13px;transition:.15s}
    .legal-modal-close:hover{border-color:var(--orange);color:var(--orange)}
    .legal-modal-body{padding:24px;overflow-y:auto;font-size:13px;color:var(--text2);line-height:1.9}
    .legal-modal-body h4{font-size:14px;font-weight:700;color:var(--text);margin:20px 0 8px}
    .legal-modal-body h4:first-child{margin-top:0}
    .legal-modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}

    /* ═══════════════════════════════════════════════════════════════
       RESPONSIVE — globale
       ═══════════════════════════════════════════════════════════════ */
    body{overflow-x:hidden;-webkit-text-size-adjust:100%}
    img{max-width:100%;height:auto}
    main{padding-top:64px;min-height:100vh;width:100%;overflow-x:hidden}

    /* Bottoni: clamp font + padding fluidi */
    .btn{font-size:clamp(13px,1.6vw,14px);padding:clamp(10px,1.4vw,11px) clamp(16px,2.4vw,24px)}

    /* TABLET ≤ 900px */
    @media(max-width:900px){
      .container{padding:0 16px}
      .footer-grid{grid-template-columns:1fr 1fr;gap:24px!important}
      .section-title{font-size:clamp(22px,3.5vw,28px)}

      /* Pattern di grid inline più comuni → riduci colonne */
      div[style*="grid-template-columns:1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]),
      div[style*="grid-template-columns: 1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]){grid-template-columns:1fr!important;gap:24px!important}

      div[style*="grid-template-columns:1fr 1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]),
      div[style*="grid-template-columns:repeat(3,1fr)"]{grid-template-columns:repeat(2,1fr)!important;gap:16px!important}

      div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:repeat(2,1fr)!important;gap:16px!important}

      div[style*="grid-template-columns:repeat(5,1fr)"]{grid-template-columns:repeat(2,1fr)!important;gap:14px!important}

      /* Padding sezioni grandi → ridotti */
      div[style*="padding:60px"]:not([style*="padding:60px 0"]){padding:32px 20px!important}
      div[style*="padding:48px"]:not([style*="padding:48px 0"]){padding:28px 20px!important}
      div[style*="padding:40px"]:not([style*="padding:40px 0"]){padding:24px 16px!important}

      .section{padding:48px 0}
    }

    /* MOBILE ≤ 600px */
    @media(max-width:600px){
      html,body{font-size:14px}
      .container{padding:0 14px}

      .navbar-menu{display:none}
      .hamburger{display:flex}
      .navbar-logo img{height:32px}

      .footer-grid{grid-template-columns:1fr;gap:24px!important;text-align:left}

      /* Tutti i grid inline → 1 colonna */
      div[style*="grid-template-columns:1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]),
      div[style*="grid-template-columns:1fr 1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]),
      div[style*="grid-template-columns:repeat(2,1fr)"],
      div[style*="grid-template-columns:repeat(3,1fr)"],
      div[style*="grid-template-columns:repeat(4,1fr)"],
      div[style*="grid-template-columns:repeat(5,1fr)"]{grid-template-columns:1fr!important;gap:16px!important}

      /* Tipografia */
      h1{font-size:clamp(26px,7vw,34px)!important;line-height:1.15!important}
      h2{font-size:clamp(20px,5.5vw,26px)!important}
      h3{font-size:clamp(17px,4.5vw,20px)!important}
      .section-title{font-size:clamp(20px,5vw,24px)!important}

      /* Padding */
      .section{padding:32px 0}
      section{padding-left:0;padding-right:0}
      div[style*="padding:80px"]{padding:40px 16px!important}
      div[style*="padding:60px"]:not([style*="padding:60px 0"]){padding:28px 16px!important}
      div[style*="padding:48px"]:not([style*="padding:48px 0"]){padding:24px 14px!important}

      /* Numeri grandi (statistiche) ridotti */
      div[style*="font-size:32px"]:not(.section-title):not(h1):not(h2){font-size:24px!important}
      div[style*="font-size:36px"]:not(.section-title):not(h1):not(h2){font-size:26px!important}

      /* Form input — 16px previene zoom iOS al focus */
      .form-input,.form-select,.form-textarea,input,textarea,select{font-size:16px!important}

      /* Cookie banner full width */
      .cookie-inner{flex-direction:column;align-items:stretch}
      .cookie-actions{width:100%;flex-direction:column;gap:8px}
      .cookie-btn{flex:1;text-align:center;width:100%}

      /* Modal legali */
      .legal-modal{padding:12px}
      .legal-modal-box{max-height:90vh}
      .legal-modal-body{padding:16px;font-size:13px}
      .legal-modal-footer{flex-direction:column;gap:8px}
      .legal-modal-footer .btn{width:100%}

      /* WhatsApp FAB più piccolo */
      a[href*="wa.me"]{width:50px!important;height:50px!important;bottom:18px!important;right:18px!important}
      a[href*="wa.me"] svg{width:24px!important;height:24px!important}

      /* CTA con 2 bottoni: in colonna full-width */
      div[style*="display:flex"][style*="justify-content:center"][style*="flex-wrap:wrap"] > .btn,
      div[style*="display:flex"][style*="gap:12px"][style*="flex-wrap:wrap"] > .btn,
      div[style*="display:flex"][style*="gap:16px"][style*="flex-wrap:wrap"] > .btn{flex:1 1 100%;justify-content:center}

      /* Form di ricerca pubblici (auto-in-vendita, noleggio, ecc.):
         su mobile gli input/select con min-width devono andare a piena larghezza */
      form input[type="text"],
      form input[type="email"],
      form input[type="tel"],
      form input[type="number"],
      form input[type="search"],
      form select,
      form textarea{min-width:0!important;width:100%!important}

      form button[type="submit"]{width:100%}

      /* Form a flex con gap → impila in colonna */
      form[style*="display:flex"]{flex-direction:column!important;align-items:stretch!important}
    }

    /* MOBILE STRETTO ≤ 380px */
    @media(max-width:380px){
      .container{padding:0 12px}
      .navbar-inner{padding:0 14px}
      .navbar-logo img{height:28px}
      h1{font-size:24px!important}
    }
  </style>
  @stack('styles')
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
  <div class="navbar-inner">
    @php
      $logoPath = 'images/logo-alecar-compact.png';
      $logoVer  = file_exists(public_path($logoPath)) ? filemtime(public_path($logoPath)) : '1';
    @endphp
    <a href="{{ route('public.home') }}" class="navbar-logo">
      <img src="{{ asset($logoPath) }}?v={{ $logoVer }}" alt="AleCar S.r.l.">
    </a>
    <div class="navbar-menu">
      <a href="{{ route('public.home') }}" class="{{ request()->routeIs('public.home') ? 'active' : '' }}">Home</a>
      <a href="{{ route('public.vehicles.index') }}" class="{{ request()->routeIs('public.vehicles.*') ? 'active' : '' }}">Auto in vendita</a>
      <a href="{{ route('public.noleggio') }}" class="{{ request()->routeIs('public.noleggio*') ? 'active' : '' }}">Noleggio</a>
      <a href="{{ route('public.servizi') }}" class="{{ request()->routeIs('public.servizi') ? 'active' : '' }}">Servizi</a>
      <a href="{{ route('public.chi_siamo') }}" class="{{ request()->routeIs('public.chi_siamo') ? 'active' : '' }}">Chi siamo</a>
      <a href="{{ route('public.contatti') }}" class="navbar-cta">Contattaci</a>
    </div>
    <div class="hamburger" onclick="toggleMenu()">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

{{-- MOBILE MENU --}}
<div class="mobile-menu" id="mobile-menu">
  <a href="{{ route('public.home') }}">Home</a>
  <a href="{{ route('public.vehicles.index') }}">Auto in vendita</a>
  <a href="{{ route('public.noleggio') }}">Noleggio</a>
  <a href="{{ route('public.servizi') }}">Servizi</a>
  <a href="{{ route('public.chi_siamo') }}">Chi siamo</a>
  <a href="{{ route('public.contatti') }}" style="color:var(--orange)">Contattaci</a>
</div>

<main>@yield('content')</main>

{{-- FOOTER --}}
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><img src="{{ asset($logoPath) }}?v={{ $logoVer }}" alt="AleCar"></div>
        <p class="footer-desc">AleCar S.r.l. — Vendita auto usate selezionate e noleggio veicoli a Torino. Qualità garantita, prezzi trasparenti, assistenza dedicata.</p>
      </div>
      <div class="footer-col">
        <h4>Navigazione</h4>
        <a href="{{ route('public.vehicles.index') }}">Auto in vendita</a>
        <a href="{{ route('public.noleggio') }}">Noleggio</a>
        <a href="{{ route('public.servizi') }}">Servizi</a>
        <a href="{{ route('public.chi_siamo') }}">Chi siamo</a>
        <a href="{{ route('public.contatti') }}">Contatti</a>
      </div>
      <div class="footer-col">
        <h4>Contatti</h4>
        <a href="tel:+393278072650">+39 327 807 2650</a>
        <a href="mailto:alecarto7@gmail.com">alecarto7@gmail.com</a>
        <a href="mailto:alecar@legalmail.it">PEC: alecar@legalmail.it</a>
        <p style="margin-top:8px">Via Ignazio Collino 29<br>10100 Torino (TO)</p>
      </div>
      <div class="footer-col">
        <h4>Legale</h4>
        <a href="{{ route('public.privacy') }}">Privacy Policy</a>
        <a href="{{ route('public.cookie_policy') }}">Cookie Policy</a>
        <a href="{{ route('public.termini_vendita') }}">Termini di vendita</a>
        <a href="{{ route('public.termini_noleggio') }}">Termini noleggio</a>
        <a href="#" onclick="openCookieSettings();return false">Gestione cookie</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-copy">&copy; {{ date('Y') }} AleCar S.r.l. — P.IVA 11352180019 — Cod. SDI: M5UXCR1 — Iscritta CCIAA Torino</p>
      <div class="footer-legal">
        <a href="{{ route('public.privacy') }}">Privacy</a>
        <a href="{{ route('public.cookie_policy') }}">Cookie</a>
        <a href="{{ route('public.termini_vendita') }}">Termini vendita</a>
        <a href="{{ route('public.termini_noleggio') }}">Termini noleggio</a>
        <a href="{{ url('/') }}" style="color:var(--border2)">Gestionale</a>
      </div>
    </div>
  </div>
</footer>

{{-- COOKIE BANNER --}}
<div id="cookie-banner">
  <div class="cookie-inner">
    <div class="cookie-text">
      <h4>🍪 Informativa sui cookie</h4>
      <p>Questo sito utilizza cookie tecnici necessari al funzionamento e, con il tuo consenso, cookie analitici per migliorare l'esperienza. Leggi la nostra <a href="{{ route('public.cookie_policy') }}">Cookie Policy</a> e la <a href="{{ route('public.privacy') }}">Privacy Policy</a>.</p>
      <div class="cookie-details" id="cookie-details">
        <div style="margin-top:12px;display:flex;flex-direction:column;gap:8px">
          <div class="cookie-type">
            <div>
              <div style="font-weight:600;color:var(--text);font-size:12px">Cookie tecnici</div>
              <div class="cookie-type-label">Sessione, CSRF, preferenze. Necessari al funzionamento.</div>
            </div>
            <span class="cookie-type-badge cookie-always">Sempre attivi</span>
          </div>
          <div class="cookie-type">
            <div>
              <div style="font-weight:600;color:var(--text);font-size:12px">Cookie analitici</div>
              <div class="cookie-type-label">Statistiche di navigazione anonime (es. Google Analytics).</div>
            </div>
            <label class="cookie-toggle">
              <input type="checkbox" id="analytics-toggle">
              <span class="cookie-slider"></span>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="cookie-actions">
      <button class="cookie-btn-details" onclick="toggleCookieDetails()">Personalizza</button>
      <button class="cookie-btn cookie-btn-save" onclick="saveCookiePrefs()">Solo necessari</button>
      <button class="cookie-btn cookie-btn-accept" onclick="acceptAllCookies()">Accetta tutto</button>
    </div>
  </div>
</div>

{{-- MODAL LEGALI (richiamabili via JS) --}}
@foreach([
  ['modal-privacy', 'Informativa Privacy (GDPR)', route('public.privacy')],
  ['modal-cookie', 'Cookie Policy', route('public.cookie_policy')],
  ['modal-termini-vendita', 'Termini e Condizioni di Vendita', route('public.termini_vendita')],
  ['modal-termini-noleggio', 'Termini e Condizioni di Noleggio', route('public.termini_noleggio')],
] as [$id,$title,$url])
<div class="legal-modal" id="{{ $id }}">
  <div class="legal-modal-box">
    <div class="legal-modal-header">
      <h3>{{ $title }}</h3>
      <button class="legal-modal-close" onclick="closeModal('{{ $id }}')">Chiudi</button>
    </div>
    <div class="legal-modal-body" id="{{ $id }}-body">
      <p style="color:var(--text3)">Caricamento...</p>
    </div>
    <div class="legal-modal-footer">
      <a href="{{ $url }}" class="btn btn-ghost btn-sm">Apri pagina completa</a>
      <button class="btn btn-primary btn-sm" onclick="closeModal('{{ $id }}')">Chiudi</button>
    </div>
  </div>
</div>
@endforeach

<script>
// ── Navbar mobile ──
function toggleMenu(){document.getElementById('mobile-menu').classList.toggle('open')}

// ── Cookie banner ──
const COOKIE_KEY = 'alecar_cookie_prefs';

function getCookiePrefs(){
  try { return JSON.parse(localStorage.getItem(COOKIE_KEY)); } catch(e){ return null; }
}

function saveCookiePrefs(){
  const analytics = document.getElementById('analytics-toggle')?.checked || false;
  localStorage.setItem(COOKIE_KEY, JSON.stringify({decided:true, analytics}));
  document.getElementById('cookie-banner').style.display='none';
}

function acceptAllCookies(){
  const toggle = document.getElementById('analytics-toggle');
  if(toggle) toggle.checked = true;
  localStorage.setItem(COOKIE_KEY, JSON.stringify({decided:true, analytics:true}));
  document.getElementById('cookie-banner').style.display='none';
}

function toggleCookieDetails(){
  const d = document.getElementById('cookie-details');
  d.classList.toggle('open');
}

function openCookieSettings(){
  const prefs = getCookiePrefs();
  if(prefs && document.getElementById('analytics-toggle')){
    document.getElementById('analytics-toggle').checked = prefs.analytics || false;
  }
  document.getElementById('cookie-details').classList.add('open');
  document.getElementById('cookie-banner').style.display='block';
}

// Mostra banner se non ancora deciso
if(!getCookiePrefs()?.decided){
  document.getElementById('cookie-banner').style.display='block';
}

// ── Modal legali ──
const modalCache = {};

function openModal(id, url){
  const modal = document.getElementById(id);
  if(!modal) return;
  modal.classList.add('open');
  if(!modalCache[id]){
    fetch(url)
      .then(r => r.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const content = doc.querySelector('main') || doc.querySelector('.legal-content') || doc.body;
        const body = document.getElementById(id+'-body');
        if(body && content) {
          body.innerHTML = content.innerHTML;
          modalCache[id] = true;
        }
      })
      .catch(() => {
        const body = document.getElementById(id+'-body');
        if(body) body.innerHTML = '<p>Contenuto non disponibile. <a href="'+url+'" style="color:var(--orange)">Apri la pagina completa</a></p>';
      });
  }
}

function closeModal(id){
  document.getElementById(id)?.classList.remove('open');
}

// Chiudi modal cliccando fuori
document.querySelectorAll('.legal-modal').forEach(m => {
  m.addEventListener('click', e => { if(e.target === m) m.classList.remove('open'); });
});

// Helper globali per il footer
function showPrivacy(){ openModal('modal-privacy', '{{ route('public.privacy') }}') }
function showCookiePolicy(){ openModal('modal-cookie', '{{ route('public.cookie_policy') }}') }
function showTerminiVendita(){ openModal('modal-termini-vendita', '{{ route('public.termini_vendita') }}') }
function showTerminiNoleggio(){ openModal('modal-termini-noleggio', '{{ route('public.termini_noleggio') }}') }
</script>

{{-- WHATSAPP FLOATING --}}
<a href="https://wa.me/393278072650?text=Ciao%2C%20ho%20bisogno%20di%20informazioni" target="_blank" rel="noopener" title="Scrivici su WhatsApp" style="position:fixed;bottom:28px;right:28px;width:56px;height:56px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.4);z-index:500;transition:.2s;text-decoration:none" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

@stack('scripts')
</body>
</html>