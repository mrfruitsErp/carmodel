<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Auto Usate in Vendita - CarModel</title>
  <meta name="description" content="Scopri le nostre auto usate selezionate. Qualita garantita, prezzi trasparenti.">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f0f2f5;color:#1a1a2e;line-height:1.5}

    /* HEADER */
    .header{background:#fff;border-bottom:3px solid #ff6b00;box-shadow:0 2px 8px rgba(0,0,0,.08);position:sticky;top:0;z-index:100}
    .header-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;height:64px}
    .logo{display:flex;align-items:center;gap:10px;text-decoration:none}
    .logo-icon{width:40px;height:40px;background:#ff6b00;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:16px}
    .logo-text{font-size:20px;font-weight:800;color:#1a1a2e}
    .header-tagline{font-size:13px;color:#888}

    /* HERO */
    .hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:48px 20px;text-align:center}
    .hero h1{font-size:36px;font-weight:800;color:#fff;margin-bottom:8px}
    .hero h1 span{color:#ff6b00}
    .hero p{color:#aab8c2;font-size:16px;margin-bottom:32px}
    .hero-stats{display:flex;justify-content:center;gap:40px;margin-bottom:32px}
    .stat{text-align:center}
    .stat-num{font-size:28px;font-weight:800;color:#ff6b00}
    .stat-label{font-size:12px;color:#aab8c2;text-transform:uppercase;letter-spacing:.08em}

    /* SEARCH BAR */
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

    /* RISULTATI BAR */
    .results-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .results-count{font-size:15px;color:#555}
    .results-count strong{color:#1a1a2e}

    /* GRIGLIA AUTO */
    .cars-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px}

    /* CARD AUTO */
    .car-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.07);transition:transform .2s,box-shadow .2s;position:relative}
    .car-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.12)}
    .car-card a{text-decoration:none;color:inherit}

    .car-photo{position:relative;height:200px;overflow:hidden;background:#f0f2f5}
    .car-photo img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
    .car-card:hover .car-photo img{transform:scale(1.04)}
    .no-img{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#ccc;gap:8px}
    .no-img svg{opacity:.4}
    .no-img span{font-size:12px}

    .badge-stato{position:absolute;top:12px;left:12px;background:rgba(255,107,0,.92);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;backdrop-filter:blur(4px)}
    .badge-tratt{position:absolute;top:12px;right:12px;background:rgba(255,255,255,.92);color:#ff6b00;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;border:1px solid rgba(255,107,0,.3)}
    .photo-count{position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;padding:3px 8px;border-radius:8px;display:flex;align-items:center;gap:4px}

    .car-info{padding:16px}
    .car-title{font-size:17px;font-weight:700;color:#1a1a2e;margin-bottom:2px}
    .car-version{font-size:12px;color:#888;margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

    .car-specs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
    .spec-tag{background:#f0f2f5;color:#555;font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500}

    .car-price-row{display:flex;align-items:flex-end;justify-content:space-between}
    .car-price{font-size:22px;font-weight:800;color:#ff6b00}
    .car-price-note{font-size:11px;color:#aaa;margin-top:2px}
    .btn-detail{background:#1a1a2e;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block;transition:.2s}
    .btn-detail:hover{background:#ff6b00}

    /* VANTAGGI */
    .vantaggi{background:#fff;border-radius:14px;padding:24px;margin-bottom:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}
    .vantaggio{display:flex;align-items:center;gap:12px}
    .vantaggio-icon{width:40px;height:40px;background:#fff3e0;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .vantaggio-text{font-size:13px;color:#555;font-weight:500}

    /* EMPTY STATE */
    .empty{text-align:center;padding:60px 20px;background:#fff;border-radius:14px}
    .empty h2{font-size:20px;color:#555;margin-bottom:8px}
    .empty p{color:#888;font-size:14px}

    /* FOOTER */
    .footer{background:#1a1a2e;color:#aab8c2;text-align:center;padding:24px;font-size:13px;margin-top:40px}
    .footer a{color:#ff6b00;text-decoration:none}

    @media(max-width:600px){
      .hero h1{font-size:24px}
      .hero-stats{gap:20px}
      .search-box{flex-direction:column}
      .search-box input,.search-box select,.btn-search{width:100%}
    }
  </style>
</head>
<body>

<header class="header">
  <div class="header-inner">
    <a href="{{ url('/') }}" class="logo">
      <div class="logo-icon">CM</div>
      <span class="logo-text">CarModel</span>
    </a>
    <span class="header-tagline">Auto usate selezionate</span>
  </div>
</header>

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
      <div class="vantaggio-icon">
        <svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/></svg>
      </div>
      <span class="vantaggio-text">Veicoli controllati e garantiti</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon">
        <svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      </div>
      <span class="vantaggio-text">Prezzi trasparenti senza sorprese</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon">
        <svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
      </div>
      <span class="vantaggio-text">Risposta garantita in 24 ore</span>
    </div>
    <div class="vantaggio">
      <div class="vantaggio-icon">
        <svg width="20" height="20" fill="none" stroke="#ff6b00" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
      </div>
      <span class="vantaggio-text">Consegna a domicilio disponibile</span>
    </div>
  </div>

  {{-- RISULTATI --}}
  <div class="results-bar">
    <span class="results-count"><strong>{{ $vehicles->total() }}</strong> veicoli trovati</span>
  </div>

  @if($vehicles->isEmpty())
  <div class="empty">
    <svg width="64" height="64" fill="none" stroke="#ccc" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:16px"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
    <h2>Nessun veicolo trovato</h2>
    <p>Prova a modificare i filtri di ricerca</p>
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
          <span class="badge-stato">{{ $vehicle->year }}</span>
          @if($vehicle->price_negotiable)<span class="badge-tratt">Trattabile</span>@endif
          @if($photoCount > 1)<div class="photo-count"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>{{ $photoCount }}</div>@endif
        </div>
        <div class="car-info">
          <div class="car-title">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
          <div class="car-version">{{ $vehicle->version ?? '&nbsp;' }} &middot; {{ $vehicle->year }}</div>
          <div class="car-specs">
            <span class="spec-tag">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
            <span class="spec-tag">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
            @if($vehicle->transmission)<span class="spec-tag">{{ ucfirst($vehicle->transmission) }}</span>@endif
            @if($vehicle->power_hp)<span class="spec-tag">{{ $vehicle->power_hp }} CV</span>@endif
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

<footer class="footer">
  <p>CarModel &mdash; Auto usate selezionate con cura &mdash; <a href="{{ url('/') }}">Accedi all'area gestionale</a></p>
</footer>

</body>
</html>