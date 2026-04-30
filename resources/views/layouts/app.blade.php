<!DOCTYPE html>
<html lang="it" translate="no">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="google" content="notranslate">
<meta http-equiv="Content-Language" content="it">
<title>CarModel Software - @yield('title', 'Dashboard')</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#f6f7f9;--bg2:#ffffff;--bg3:#f1f3f6;--bg4:#e9edf2;--bg5:#dde3ea;
  --border:#e5e7eb;--border2:#dfe3e8;--border3:#cfd6de;
  --text:#1f2937;--text2:#6b7280;--text3:#9ca3af;
  --orange:#ff6b00;--orange2:#e55f00;--orange3:#ff8c33;
  --orange-bg:rgba(255,107,0,.08);--orange-border:rgba(255,107,0,.25);--orange-text:#ff6b00;
  --green:#22c55e;--green-bg:rgba(34,197,94,.08);--green-text:#16a34a;
  --amber:#f59e0b;--amber-bg:rgba(245,158,11,.08);--amber-text:#d97706;
  --red:#ef4444;--red-bg:rgba(239,68,68,.08);--red-text:#dc2626;
  --blue:#3b82f6;--blue-bg:rgba(59,130,246,.08);--blue-text:#2563eb;
  --purple:#a855f7;--purple-bg:rgba(168,85,247,.08);--purple-text:#9333ea;
  --teal:#14b8a6;--teal-bg:rgba(20,184,166,.08);--teal-text:#0d9488;
  --font-display:'Rajdhani',sans-serif;--font-body:'DM Sans',sans-serif;--mono:'DM Mono',monospace;
  --radius:6px;--radius-lg:10px;--sidebar:220px;
}
body{font-family:var(--font-body);background:var(--bg);color:var(--text);font-size:14px;line-height:1.5;min-height:100vh}
.app{display:flex;min-height:100vh;overflow-x:hidden}
.sidebar{width:var(--sidebar);min-width:var(--sidebar);background:#111827;border-right:none;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:9999;overflow-y:auto}
.sidebar::-webkit-scrollbar{width:10px}.sidebar::-webkit-scrollbar-track{background:#0d1117}.sidebar::-webkit-scrollbar-thumb{background:#ff6b00;border-radius:4px}.sidebar::-webkit-scrollbar-thumb:hover{background:#ff8c00}
.main{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-height:100vh}
.logo{padding:18px 16px 14px;border-bottom:1px solid rgba(255,255,255,.06);position:relative}
.logo::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(90deg,var(--orange),transparent)}
.nav{flex:1;padding:6px 0}
.nav-section{color:rgba(255,255,255,.25);font-size:9px;font-weight:600;padding:14px 16px 4px;letter-spacing:.18em;text-transform:uppercase}
.nav-item{display:flex;align-items:center;gap:9px;padding:7px 16px;font-size:12px;color:rgba(255,255,255,.55);transition:all .15s;border-left:2px solid transparent;text-decoration:none;margin:0 4px;border-radius:6px}
.nav-item:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)}
.nav-item.active{background:rgba(255,107,0,.12);color:var(--orange);border-left:2px solid var(--orange);font-weight:600;margin-left:4px;border-radius:0 6px 6px 0}
.nav-item svg{opacity:.6;flex-shrink:0}
.nav-item.active svg,.nav-item:hover svg{opacity:1}
.nav-badge{margin-left:auto;background:var(--red);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:8px;min-width:16px;text-align:center}
.user-area{padding:12px 14px;border-top:1px solid rgba(255,255,255,.06);margin-top:auto}
.avatar{width:30px;height:30px;border-radius:50%;background:var(--orange-bg);border:1.5px solid var(--orange);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:12px;font-weight:700;color:var(--orange);flex-shrink:0}
.topbar{background:var(--bg2);border-bottom:1px solid var(--border2);padding:10px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 1px 8px rgba(0,0,0,.06)}
.page-title{font-family:var(--font-display);font-size:18px;font-weight:600;color:var(--text);letter-spacing:.04em;text-transform:uppercase}
.content{flex:1;padding:22px 24px}
.btn{padding:7px 16px;font-size:13px;border-radius:var(--radius);cursor:pointer;font-family:var(--font-body);font-weight:500;transition:all .15s;border:none;display:inline-flex;align-items:center;gap:6px;text-decoration:none}
.btn-primary{background:var(--orange);color:#000;font-weight:600;box-shadow:0 0 12px rgba(255,107,0,.3)}.btn-primary:hover{background:var(--orange2);box-shadow:0 0 20px rgba(255,107,0,.5)}
.btn-ghost{background:transparent;border:1px solid var(--border2);color:var(--text2)}.btn-ghost:hover{background:var(--bg3);color:var(--text);border-color:var(--border3)}
.btn-danger{background:var(--red-bg);border:1px solid rgba(239,68,68,.3);color:var(--red-text)}
.btn-sm{padding:5px 11px;font-size:12px}
.card{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:20px;margin-bottom:16px;position:relative;overflow:hidden}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--border3),transparent)}
.card-title{font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--text);margin-bottom:14px;letter-spacing:.06em;text-transform:uppercase}
.stat-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:20px}
.stat-card{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:16px;position:relative;overflow:hidden}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px}
.stat-card.orange::after{background:var(--orange);box-shadow:0 0 8px var(--orange)}
.stat-card.green::after{background:var(--green)}.stat-card.amber::after{background:var(--amber)}.stat-card.red::after{background:var(--red)}.stat-card.blue::after{background:var(--blue)}.stat-card.purple::after{background:var(--purple)}
.stat-label{font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px}
.stat-value{font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--text);letter-spacing:-.5px;line-height:1}
.stat-sub{font-size:11px;color:var(--text3);margin-top:6px}
table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;padding:9px 12px;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;border-bottom:1px solid var(--border2);white-space:nowrap}
td{padding:11px 12px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tr:last-child td{border-bottom:none}
tbody tr{transition:background .1s}
tbody tr:hover td{background:var(--bg3)}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:4px;font-size:11px;font-weight:600;white-space:nowrap;letter-spacing:.02em}
.badge-green{background:var(--green-bg);color:var(--green-text);border:1px solid rgba(34,197,94,.2)}
.badge-amber{background:var(--amber-bg);color:var(--amber-text);border:1px solid rgba(245,158,11,.2)}
.badge-red{background:var(--red-bg);color:var(--red-text);border:1px solid rgba(239,68,68,.2)}
.badge-blue{background:var(--blue-bg);color:var(--blue-text);border:1px solid rgba(59,130,246,.2)}
.badge-purple{background:var(--purple-bg);color:var(--purple-text);border:1px solid rgba(168,85,247,.2)}
.badge-teal{background:var(--teal-bg);color:var(--teal-text);border:1px solid rgba(20,184,166,.2)}
.badge-orange{background:var(--orange-bg);color:var(--orange-text);border:1px solid var(--orange-border)}
.badge-gray{background:var(--bg4);color:var(--text2);border:1px solid var(--border2)}
.targa{font-family:var(--mono);font-size:12px;background:var(--bg4);color:var(--text);padding:3px 8px;border-radius:4px;border:1px solid var(--border3);letter-spacing:.1em;font-weight:500}
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
.form-label{font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase}
.form-input,.form-select,.form-textarea{background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:8px 11px;color:var(--text);font-size:13px;font-family:var(--font-body);outline:none;width:100%;transition:border-color .15s}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--orange)}
.form-input::placeholder,.form-textarea::placeholder{color:var(--text3)}
.form-select option{background:var(--bg3)}
.form-textarea{resize:vertical;min-height:80px;line-height:1.6}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.main-side{display:grid;grid-template-columns:2fr 1fr;gap:16px}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.alert{padding:10px 14px;border-radius:var(--radius);margin-bottom:16px;font-size:13px;display:flex;align-items:flex-start;gap:10px;border-left:3px solid}
.alert-amber{background:var(--amber-bg);border-color:var(--amber);color:var(--amber-text)}
.alert-red{background:var(--red-bg);border-color:var(--red);color:var(--red-text)}
.alert-green{background:var(--green-bg);border-color:var(--green);color:var(--green-text)}
.alert-orange{background:var(--orange-bg);border-color:var(--orange);color:var(--orange-text)}
.info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-label{color:var(--text3)}.info-value{color:var(--text);font-weight:500;text-align:right}
.progress{height:3px;background:var(--border2);border-radius:2px;overflow:hidden;margin-top:6px}
.progress-fill{height:100%;background:var(--orange);border-radius:2px;box-shadow:0 0 6px rgba(255,107,0,.4)}
.fleet-item{background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:14px;display:flex;align-items:center;gap:14px;margin-bottom:8px;transition:border-color .15s}
.fleet-item:hover{border-color:var(--border3)}
.fleet-status{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.fleet-status.green{background:var(--green);box-shadow:0 0 6px var(--green)}.fleet-status.red{background:var(--red);box-shadow:0 0 6px var(--red)}.fleet-status.amber{background:var(--amber);box-shadow:0 0 6px var(--amber)}
.tl-item{display:flex;gap:12px;margin-bottom:12px;font-size:13px}
.tl-dot{width:10px;height:10px;border-radius:50%;background:var(--orange);margin-top:4px;flex-shrink:0;box-shadow:0 0 6px rgba(255,107,0,.4)}
.tl-dot.amber{background:var(--amber);box-shadow:none}.tl-dot.blue{background:var(--blue);box-shadow:none}.tl-dot.gray{background:var(--border2);box-shadow:none}.tl-dot.red{background:var(--red);box-shadow:none}.tl-dot.green{background:var(--green);box-shadow:none}
.tl-body{flex:1}.tl-title{font-weight:500;color:var(--text);margin-bottom:2px}.tl-meta{font-size:11px;color:var(--text3)}
.filter-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center}
.search-bar{display:flex;align-items:center;gap:8px;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:7px 12px;flex:1;max-width:320px;transition:border-color .15s}
.search-bar:focus-within{border-color:var(--orange)}
.search-bar input{background:none;border:none;color:var(--text);font-size:13px;flex:1;outline:none;font-family:var(--font-body)}
.chip{display:inline-flex;align-items:center;gap:5px;background:var(--bg4);border:1px solid var(--border2);border-radius:4px;padding:5px 12px;font-size:12px;font-weight:500;color:var(--text2);cursor:pointer;transition:all .15s;text-decoration:none}
.chip:hover,.chip.active{background:var(--orange-bg);border-color:var(--orange-border);color:var(--orange-text)}
.sinistro-stati{display:flex;background:var(--bg3);border-radius:var(--radius-lg);padding:4px;border:1px solid var(--border2);margin-bottom:20px;gap:2px}
.stato-step{flex:1;padding:8px 4px;text-align:center;border-radius:6px;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.05em;text-transform:uppercase}
.stato-step.done{background:var(--orange-bg);color:var(--orange-text);border:1px solid var(--orange-border)}
.stato-step.current{background:var(--amber-bg);color:var(--amber-text);border:1px solid rgba(245,158,11,.3)}
::-webkit-scrollbar{width:4px;height:4px}::-webkit-scrollbar-track{background:var(--bg)}::-webkit-scrollbar-thumb{background:var(--border2);border-radius:2px}::-webkit-scrollbar-thumb:hover{background:var(--orange)}

/* ═══════════════════════════════════════════════════════════════
   +1pt GLOBALE — tutti i font del gestionale aumentati di 1px
   per migliore leggibilità. Override delle regole sopra.
   ═══════════════════════════════════════════════════════════════ */
body{font-size:15px}
.nav-section{font-size:10px}
.nav-item{font-size:13px}
.page-title{font-size:19px}
.btn{font-size:14px;padding:8px 17px}
.btn-sm{font-size:13px;padding:6px 12px}
.card-title{font-size:15px}
.stat-value{font-size:29px}
.stat-label{font-size:11px}
.stat-sub{font-size:12px}
table{font-size:14px}
th{font-size:11px}
td{font-size:14px}
.badge{font-size:12px;padding:3px 10px}
.targa{font-size:13px}
.form-label{font-size:11px}
.form-input,.form-select,.form-textarea{font-size:14px;padding:9px 12px}
.info-row{font-size:14px}
.search-bar input{font-size:14px}
.chip{font-size:13px;padding:6px 13px}
.alert{font-size:14px}
.tl-item{font-size:14px}
.tl-meta{font-size:12px}
.stato-step{font-size:11px}
.avatar{font-size:13px}
.nav-badge{font-size:10px}
.user-area .name{font-size:13px}
/* Bump anche stili inline più frequenti */
[style*="font-size:10px"]{font-size:11px!important}
[style*="font-size:11px"]:not(.badge):not(.targa):not(.tl-meta){font-size:12px!important}
[style*="font-size:12px"]{font-size:13px!important}
[style*="font-size:13px"]{font-size:14px!important}
[style*="font-size:14px"]{font-size:15px!important}
[style*="font-size:15px"]{font-size:16px!important}
[style*="font-size:16px"]{font-size:17px!important}
[style*="font-size:18px"]{font-size:19px!important}

@media(max-width:1100px){.stat-grid{grid-template-columns:repeat(3,1fr)}.main-side{grid-template-columns:1fr}}
@media(max-width:800px){.two-col,.three-col{grid-template-columns:1fr}.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){
  .sidebar{transform:translateX(-100%);transition:transform .3s ease;width:var(--sidebar)!important;min-width:var(--sidebar)!important}
  .sidebar.open{transform:translateX(0)}
  .main{margin-left:0!important}
  .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9998}
  .sidebar-overlay.show{display:block}
  .mobile-header{display:flex!important}
  .topbar{display:none}
  .content{padding:16px}
  .two-col,.three-col{grid-template-columns:1fr}
  .stat-grid{grid-template-columns:repeat(2,1fr)}
}
.mobile-header{display:none;background:#111827;padding:12px 16px;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(255,255,255,.06)}
.hamburger{background:none;border:none;cursor:pointer;padding:4px;color:rgba(255,255,255,.7);display:flex;flex-direction:column;gap:4px}
.hamburger span{display:block;width:20px;height:2px;background:currentColor;border-radius:2px;transition:all .3s}
.hamburger.open span:nth-child(1){transform:rotate(45deg) translate(4px,4px)}
.hamburger.open span:nth-child(2){opacity:0}
.hamburger.open span:nth-child(3){transform:rotate(-45deg) translate(4px,-4px)}
.pagination-row{display:flex;align-items:center;gap:4px;padding:16px 20px;border-top:1px solid var(--border);flex-wrap:wrap}
.btn-page{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;border:1px solid var(--border2);background:var(--bg3);color:var(--text2);font-size:13px;text-decoration:none;transition:all .15s}
.btn-page:hover{background:var(--bg4);color:var(--text)}
.btn-page.active{background:var(--orange);border-color:var(--orange);color:#fff;font-weight:600}
.btn-page.disabled{opacity:.35;pointer-events:none;cursor:default}
.pagination-info{margin-left:auto;font-size:12px;color:var(--text3)}
@stack('styles')
</style>
</head>
<body>
<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>
<div class="mobile-header">
  <div style="display:flex;align-items:center;gap:10px">
    <button class="hamburger" id="hamburger" onclick="toggleSidebar()"><span></span><span></span><span></span></button>
    <img src="{{ asset('images/logo-alecar-compact.png') }}" alt="AleCar" style="height:28px;width:auto;object-fit:contain">
  </div>
  <span style="font-size:12px;color:rgba(255,255,255,.4)">@yield('title', 'Dashboard')</span>
</div>
<div class="app">
<div class="sidebar" translate="no">
  <div class="logo">
    <img src="{{ asset('images/logo-alecar-compact.png') }}" alt="AleCar" style="height:44px;width:auto;object-fit:contain;filter:brightness(1.1);display:block">
  </div>
  <nav class="nav">

    {{-- PRINCIPALE --}}
    <div class="nav-section">Principale</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard
    </a>

    {{-- ANAGRAFICA --}}
    @if(optional(auth()->user())->canDo('clienti.view') || optional(auth()->user())->canDo('veicoli.view'))
    <div class="nav-section">Anagrafica</div>
    @if(optional(auth()->user())->canDo('clienti.view'))
    <a href="{{ route('clienti.index') }}" class="nav-item {{ request()->routeIs('clienti.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>Clienti
    </a>
    @endif
    @if(optional(auth()->user())->canDo('veicoli.view'))
    <a href="{{ route('veicoli.index') }}" class="nav-item {{ request()->routeIs('veicoli.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>Veicoli
    </a>
    @endif
    @endif

    {{-- SINISTRI --}}
    @if(optional(auth()->user())->canDo('sinistri.view') || optional(auth()->user())->canDo('lesioni.view') || optional(auth()->user())->canDo('periti.view'))
    <div class="nav-section">Sinistri & Lesioni</div>
    @if(optional(auth()->user())->canDo('sinistri.view'))
    <a href="{{ route('sinistri.index') }}" class="nav-item {{ request()->routeIs('sinistri.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>Sinistri
    </a>
    @endif
    @if(optional(auth()->user())->canDo('lesioni.view'))
    <a href="{{ route('lesioni.index') }}" class="nav-item {{ request()->routeIs('lesioni.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Lesioni Personali
    </a>
    @endif
    @if(optional(auth()->user())->canDo('periti.view'))
    <a href="{{ route('periti.index') }}" class="nav-item {{ request()->routeIs('periti.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>Esperti & Contatti
    </a>
    @endif
    @if(optional(auth()->user())->canDo('periti.view'))
    <a href="{{ route('assicurazioni.index') }}" class="nav-item {{ request()->routeIs('assicurazioni.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Assicurazioni
    </a>
    @endif
    @endif

    {{-- OFFICINA --}}
    @if(optional(auth()->user())->canDo('lavorazioni.view') || optional(auth()->user())->canDo('preventivi.view'))
    <div class="nav-section">Officina</div>
    @if(optional(auth()->user())->canDo('lavorazioni.view'))
    <a href="{{ route('lavorazioni.index') }}" class="nav-item {{ request()->routeIs('lavorazioni.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>Lavorazioni
    </a>
    @endif
    @if(optional(auth()->user())->canDo('preventivi.view'))
    <a href="{{ route('preventivi.index') }}" class="nav-item {{ request()->routeIs('preventivi.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/></svg>Preventivi
    </a>
    @endif
    @endif

    {{-- MOVIMENTI --}}
    <div class="nav-section">Movimenti</div>
    <a href="{{ route('movimenti.index') }}" class="nav-item {{ request()->routeIs('movimenti.*') && !request()->routeIs('movimenti.calendario') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Lista Movimenti
    </a>
    <a href="{{ route('movimenti.calendario') }}" class="nav-item {{ request()->routeIs('movimenti.calendario') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendario
    </a>

    {{-- NOLEGGIO --}}
    @if(optional(auth()->user())->canDo('noleggio.view'))
    @php
      $isMarketplaceRental = request()->routeIs('marketplace.vehicles.*') && request('type') === 'noleggio';
    @endphp
    <div class="nav-section">Noleggio</div>
    <a href="{{ route('marketplace.vehicles.index', ['type'=>'noleggio']) }}" class="nav-item {{ $isMarketplaceRental || request()->routeIs('flotta.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><circle cx="12" cy="14" r="2"/></svg>Veicoli a noleggio
    </a>
    <a href="{{ route('noleggio.index') }}" class="nav-item {{ request()->routeIs('noleggio.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Contratti Noleggio
    </a>
    <a href="{{ route('sostitutive.index') }}" class="nav-item {{ request()->routeIs('sostitutive.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>Auto Sostitutive
    </a>
    @endif

    {{-- AMMINISTRAZIONE --}}
    @if(optional(auth()->user())->canDo('fatture.view') || optional(auth()->user())->canDo('ricambi.view') || optional(auth()->user())->canDo('utenti.manage'))
    <div class="nav-section">Amministrazione</div>
    @if(optional(auth()->user())->canDo('clienti.view'))
    <a href="{{ route('fascicoli.index') }}" class="nav-item {{ request()->routeIs('fascicoli.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>Fascicoli
    </a>
    @endif
    @if(optional(auth()->user())->canDo('fatture.view'))
    <a href="{{ route('documenti.index') }}" class="nav-item {{ request()->routeIs('documenti.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>Fatture & DDT
    </a>
    @endif
    @if(optional(auth()->user())->canDo('clienti.view'))
    @php
      try {
        $msgNonLetti = \App\Models\WebBooking::forTenant(auth()->user()->tenant_id)->whereNull('letto_at')->where('is_spam', false)->count();
      } catch (\Throwable $e) { $msgNonLetti = 0; }
    @endphp
    <a href="{{ route('messaggi.index') }}" class="nav-item {{ request()->routeIs('messaggi.*') ? 'active' : '' }}" style="display:flex;align-items:center;justify-content:space-between">
      <span style="display:flex;align-items:center;gap:8px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Messaggi sito
      </span>
      @if($msgNonLetti > 0)
        <span style="background:#ff6b00;color:#000;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;line-height:1.4">{{ $msgNonLetti }}</span>
      @endif
    </a>
    @endif
    <a href="{{ route('mail.index') }}" class="nav-item {{ request()->routeIs('mail.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>Mail & Notifiche
    </a>
    @if(optional(auth()->user())->canDo('ricambi.view'))
    <a href="{{ route('ricambi.index') }}" class="nav-item {{ request()->routeIs('ricambi.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>Ricambi
    </a>
    @endif
    @if(optional(auth()->user())->canDo('utenti.manage'))
    <a href="{{ route('utenti.index') }}" class="nav-item {{ request()->routeIs('utenti.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Utenti
    </a>
    @endif
    @if(auth()->user()->isAdmin())
    <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Impostazioni
    </a>
    @endif
    @endif

    {{-- VENDITA AUTO --}}
    @if(optional(auth()->user())->canDo('marketplace.view'))
    @php
      $isMarketplaceSale = request()->routeIs('marketplace.vehicles.*') && request('type') !== 'noleggio';
    @endphp
    <div class="nav-section" style="color:rgba(255,107,0,.5)">Vendita Auto</div>
    <a href="{{ route('marketplace.dashboard') }}" class="nav-item {{ request()->routeIs('marketplace.dashboard') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard
    </a>
    <a href="{{ route('marketplace.vehicles.index', ['type'=>'vendita']) }}" class="nav-item {{ $isMarketplaceSale ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>Veicoli in vendita
    </a>
    <a href="{{ route('marketplace.leads.index') }}" class="nav-item {{ request()->routeIs('marketplace.leads.*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Lead
    </a>
    @if(optional(auth()->user())->canDo('impostazioni.manage'))
    <a href="{{ route('marketplace.settings') }}" class="nav-item {{ request()->routeIs('marketplace.settings*') ? 'active' : '' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Piattaforme
    </a>
    @endif
    @endif

  </nav>
  <div class="user-area">
    <div style="display:flex;align-items:center;gap:10px">
      <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</div>
      <div style="flex:1;min-width:0">
        <div style="font-size:12px;font-weight:500;color:rgba(255,255,255,.8);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->name }}</div>
        <div style="font-size:10px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.05em">{{ ucfirst(auth()->user()->role) }}</div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="background:none;border:none;color:rgba(255,255,255,.3);cursor:pointer;padding:4px;transition:color .15s;display:flex;align-items:center" onmouseover="this.style.color='#ff6b00'" onmouseout="this.style.color='rgba(255,255,255,.3)'" title="Logout">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
      </form>
    </div>
  </div>
</div>
<div class="main">
  <div class="topbar">
    <span class="page-title">@yield('title', 'Dashboard')</span>
    <div style="display:flex;gap:8px;align-items:center">
      @if(optional(auth()->user())->canDo('clienti.view'))
      @php
        try {
          $bellCount = \App\Models\WebBooking::forTenant(auth()->user()->tenant_id)->whereNull('letto_at')->where('is_spam', false)->count();
        } catch (\Throwable $e) { $bellCount = 0; }
      @endphp
      <a href="{{ route('messaggi.index') }}" title="Messaggi dal sito" style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:var(--bg2);border:1px solid var(--border2);color:var(--text2);text-decoration:none;transition:.15s" onmouseover="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'" onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        @if($bellCount > 0)
          <span style="position:absolute;top:-6px;right:-6px;background:#ff6b00;color:#000;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;min-width:18px;text-align:center;border:2px solid var(--bg)">{{ $bellCount > 99 ? '99+' : $bellCount }}</span>
        @endif
      </a>
      @endif
      @yield('topbar-actions')
    </div>
  </div>
  <div class="content">
    @if(session('success'))<div class="alert alert-green"><span>✔</span><span>{{ session('success') }}</span></div>@endif
    @if(session('warning'))<div class="alert alert-amber"><span>⚠</span><span>{{ session('warning') }}</span></div>@endif
    @if(session('error'))<div class="alert alert-red"><span>✗</span><span>{{ session('error') }}</span></div>@endif
    @yield('content')
  </div>
</div>
</div>
@stack('scripts')
<script>
function toggleSidebar(){const s=document.querySelector('.sidebar'),o=document.getElementById('overlay'),h=document.getElementById('hamburger');s.classList.toggle('open');o.classList.toggle('show');h.classList.toggle('open')}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('open');document.getElementById('overlay').classList.remove('show');document.getElementById('hamburger').classList.remove('open')}
document.querySelectorAll('.nav-item').forEach(el=>{el.addEventListener('click',()=>{if(window.innerWidth<=640)closeSidebar()})});
</script>
</body>
</html>