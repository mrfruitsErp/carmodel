<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auto in Vendita — CarModel</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--orange:#ff6b00;--text:#1a1a2e;--text2:#555;--bg:#f8f9fc;--white:#fff;--border:#e8ecf0;--radius:12px}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
.header{background:var(--text);padding:0 5%;position:sticky;top:0;z-index:100;box-shadow:0 2px 20px rgba(0,0,0,.3)}
.header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.logo{display:flex;align-items:center;gap:10px}
.logo-icon{width:36px;height:36px;background:var(--orange);border-radius:6px;display:flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-size:14px;font-weight:700;color:#000}
.logo-name{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:.06em}
.hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:60px 5% 50px;text-align:center}
.hero h1{font-family:'Rajdhani',sans-serif;font-size:clamp(32px,5vw,52px);font-weight:700;color:#fff;margin-bottom:16px}
.hero h1 span{color:var(--orange)}
.hero p{font-size:16px;color:rgba(255,255,255,.65);margin-bottom:28px}
.hero-stats{display:flex;justify-content:center;gap:32px;flex-wrap:wrap}
.hero-stat-val{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:var(--orange)}
.hero-stat-lab{font-size:12px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.1em}
.filtri{background:var(--white);border-bottom:1px solid var(--border);padding:16px 5%;position:sticky;top:64px;z-index:90;box-shadow:0 2px 10px rgba(0,0,0,.05)}
.filtri-inner{max-width:1200px;margin:0 auto;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.filtri input,.filtri select{border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;background:var(--bg)}
.filtri input:focus,.filtri select:focus{border-color:var(--orange)}
.filtri input{flex:1;min-width:200px}
.btn-cerca{background:var(--orange);color:#000;border:none;border-radius:8px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif}
.main{max-width:1200px;margin:0 auto;padding:32px 5%}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}
.car-card{background:var(--white);border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);transition:all .25s;display:flex;flex-direction:column}
.car-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.12);border-color:rgba(255,107,0,.3)}
.car-photo{height:200px;position:relative;overflow:hidden;background:#f0f2f5}
.car-photo img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.car-card:hover .car-photo img{transform:scale(1.05)}
.car-photo-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f0f2f5,#e8ecf2)}
.car-photos-count{position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;padding:3px 8px;border-radius:10px}
.car-body{padding:16px;flex:1;display:flex;flex-direction:column}
.car-title{font-size:17px;font-weight:700;color:var(--text);margin-bottom:4px}
.car-subtitle{font-size:12px;color:var(--text2);margin-bottom:12px}
.car-specs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px}
.car-spec{font-size:11px;color:var(--text2);background:var(--bg);border:1px solid var(--border);padding:3px 8px;border-radius:4px}
.car-footer{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:12px;border-top:1px solid var(--border)}
.car-price{font-family:'Rajdhani',sans-serif;font-size:24px;font-weight:700;color:var(--orange)}
.car-price-sub{font-size:11px;color:var(--text2)}
.btn-dettagli{background:var(--text);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.btn-dettagli:hover{background:var(--orange);color:#000}
.empty{text-align:center;padding:80px 20px;color:var(--text2)}
footer{background:var(--text);color:rgba(255,255,255,.4);text-align:center;padding:24px;font-size:12px;margin-top:60px}
footer a{color:var(--orange)}
@media(max-width:600px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <div class="logo">
      <div class="logo-icon">CM</div>
      <div class="logo-name">CARMODEL</div>
    </div>
    <span style="color:rgba(255,255,255,.5);font-size:13px">Auto usate selezionate</span>
  </div>
</header>

<section class="hero">
  <h1>Auto usate <span>selezionate</span> per te</h1>
  <p>Veicoli controllati, prezzi trasparenti.</p>
  <div class="hero-stats">
    <div class="hero-stat"><div class="hero-stat-val">{{ $vehicles->total() }}</div><div class="hero-stat-lab">Disponibili</div></div>
    <div class="hero-stat"><div class="hero-stat-val">100%</div><div class="hero-stat-lab">Garantiti</div></div>
    <div class="hero-stat"><div class="hero-stat-val">24h</div><div class="hero-stat-lab">Risposta</div></div>
  </div>
</section>

<div class="filtri">
  <div class="filtri-inner">
    <form method="GET" style="display:contents">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cerca marca, modello...">
      <select name="fuel">
        <option value="">Tutti i carburanti</option>
        @foreach(['benzina','diesel','elettrico','ibrido_benzina','gpl'] as $f)
          <option value="{{ $f }}" {{ request('fuel')===$f?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$f)) }}</option>
        @endforeach
      </select>
      <select name="price_max">
        <option value="">Qualsiasi prezzo</option>
        @foreach([5000,10000,15000,20000,30000,50000] as $p)
          <option value="{{ $p }}" {{ request('price_max')==$p?'selected':'' }}>fino a € {{ number_format($p,0,',','.') }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn-cerca">Cerca</button>
    </form>
    <div style="margin-left:auto;font-size:13px;color:var(--text2)">{{ $vehicles->total() }} risultati</div>
  </div>
</div>

<main class="main">
  @if($vehicles->isEmpty())
    <div class="empty">
      <div style="font-size:64px;margin-bottom:16px">🔍</div>
      <div style="font-size:20px;font-weight:700;color:var(--text);margin-bottom:8px">Nessun veicolo trovato</div>
      <div>Prova a modificare i filtri</div>
    </div>
  @else
  <div class="grid">
    @foreach($vehicles as $vehicle)
    @php
      $photoUrl = $vehicle->getFirstMediaUrl('sale_photos');
      $photoCount = $vehicle->getMedia('sale_photos')->count();
      $slug = str_replace(' ', '-', strtolower($vehicle->brand.'-'.$vehicle->model));
    @endphp
    <a href="{{ url('/auto-in-vendita/'.$vehicle->id.'-'.$slug) }}" class="car-card">
      <div class="car-photo">
        @if($photoUrl)
          <img src="{{ $photoUrl }}" alt="{{ $vehicle->full_name }}" loading="lazy">
        @else
          <div class="car-photo-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
          </div>
        @endif
        @if($photoCount > 1)<div class="car-photos-count">📷 {{ $photoCount }}</div>@endif
      </div>
      <div class="car-body">
        <div class="car-title">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
        <div class="car-subtitle">{{ $vehicle->version }} · {{ $vehicle->year }}</div>
        <div class="car-specs">
          <span class="car-spec">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
          <span class="car-spec">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
          <span class="car-spec">{{ ucfirst($vehicle->transmission) }}</span>
          @if($vehicle->power_hp)<span class="car-spec">{{ $vehicle->power_hp }} CV</span>@endif
        </div>
        <div class="car-footer">
          <div>
            <div class="car-price">€ {{ number_format($vehicle->asking_price,0,',','.') }}</div>
            @if($vehicle->price_negotiable)<div class="car-price-sub">Trattabile</div>@endif
          </div>
          <button class="btn-dettagli">Scopri →</button>
        </div>
      </div>
    </a>
    @endforeach
  </div>
  <div style="margin-top:32px;display:flex;justify-content:center">{{ $vehicles->links() }}</div>
  @endif
</main>

<footer>
  <p>© {{ date('Y') }} CarModel · <a href="/login">Area riservata</a></p>
</footer>
</body>
</html>