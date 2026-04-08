<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }} â€” CarModel</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--orange:#ff6b00;--text:#1a1a2e;--text2:#555;--bg:#f8f9fc;--white:#fff;--border:#e8ecf0;--radius:12px}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
.header{background:var(--text);padding:0 5%;position:sticky;top:0;z-index:100}
.header-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.logo{display:flex;align-items:center;gap:10px}
.logo-icon{width:36px;height:36px;background:var(--orange);border-radius:6px;display:flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-size:14px;font-weight:700;color:#000}
.logo-name{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:.06em}
.back-link{color:rgba(255,255,255,.6);font-size:13px;display:flex;align-items:center;gap:6px}
.back-link:hover{color:#fff}
.main{max-width:1200px;margin:0 auto;padding:32px 5%;display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start}
.gallery{background:var(--white);border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);margin-bottom:20px}
.gallery-main{height:380px;overflow:hidden;background:#f0f2f5}
.gallery-main img{width:100%;height:100%;object-fit:cover}
.gallery-thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:4px;padding:8px}
.gallery-thumb{height:70px;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .15s}
.gallery-thumb:hover,.gallery-thumb.active{border-color:var(--orange)}
.gallery-thumb img{width:100%;height:100%;object-fit:cover}
.info-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:16px}
.info-card h2{font-family:'Rajdhani',sans-serif;font-size:16px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)}
.specs-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.spec-label{font-size:10px;color:var(--text2);text-transform:uppercase;letter-spacing:.08em;font-weight:600;display:block;margin-bottom:2px}
.spec-value{font-size:13px;font-weight:600;color:var(--text)}
.price-card{background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:var(--radius);padding:24px;margin-bottom:16px;color:#fff}
.price-label{font-size:11px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px}
.price-value{font-family:'Rajdhani',sans-serif;font-size:38px;font-weight:700;color:var(--orange);line-height:1}
.price-note{font-size:12px;color:rgba(255,255,255,.4);margin-top:6px}
.contact-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:20px}
.contact-card h3{font-family:'Rajdhani',sans-serif;font-size:16px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px}
.form-group{margin-bottom:12px}
.form-label{font-size:11px;font-weight:600;color:var(--text2);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:4px}
.form-input{width:100%;border:1px solid var(--border);border-radius:8px;padding:9px 12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s;background:var(--bg)}
.form-input:focus{border-color:var(--orange)}
.btn-contatto{width:100%;background:var(--orange);color:#000;border:none;border-radius:8px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;margin-top:4px}
.btn-contatto:hover{background:#e55f00}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;color:#166534;font-size:13px;margin-bottom:16px}
.platform-link{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;color:var(--text2);display:inline-flex;align-items:center;gap:6px;transition:all .15s;margin:4px 4px 0 0}
.platform-link:hover{border-color:var(--orange);color:var(--orange)}
footer{background:var(--text);color:rgba(255,255,255,.4);text-align:center;padding:20px;font-size:12px;margin-top:40px}
footer a{color:var(--orange)}
@media(max-width:768px){.main{grid-template-columns:1fr}.gallery-main{height:250px}}
</style>
</head>
<body>
<header class="header">
  <div class="header-inner">
    <div class="logo">
      <div class="logo-icon">CM</div>
      <div class="logo-name">CARMODEL</div>
    </div>
    <a href="{{ url('/auto-in-vendita') }}" class="back-link">â† Tutti i veicoli</a>
  </div>
</header>

<div class="main">
  <div>
    @php $fotos = $vehicle->getMedia('sale_photos'); @endphp
    <div class="gallery">
      <div class="gallery-main">
        @if($fotos->isNotEmpty())
          <img id="main-img" src="{{ $fotos->first()->getUrl() }}" alt="{{ $vehicle->display_name }}">
        @else
          <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
          </div>
        @endif
      </div>
      @if($fotos->count() > 1)
      <div class="gallery-thumbs">
        @foreach($fotos as $i => $m)
          <div class="gallery-thumb {{ $i===0?'active':'' }}" onclick="setPhoto('{{ $m->getUrl() }}', this)">
            <img src="{{ $m->getUrl('thumb') }}" alt="">
          </div>
        @endforeach
      </div>
      @endif
    </div>

    <div style="margin-bottom:20px">
      <h1 style="font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
      @if($vehicle->version)<p style="font-size:15px;color:var(--text2);margin-top:4px">{{ $vehicle->version }} Â· {{ $vehicle->year }}</p>@endif
    </div>

    <div class="info-card">
      <h2>Caratteristiche tecniche</h2>
      <div class="specs-grid">
        @foreach([
          ['Anno',       $vehicle->year],
          ['Km',         number_format($vehicle->mileage,0,',','.').' km'],
          ['Carburante', ucfirst(str_replace('_',' ',$vehicle->fuel_type))],
          ['Cambio',     ucfirst($vehicle->transmission)],
          ['Colore',     $vehicle->color ?? 'â€”'],
          ['Carrozzeria',$vehicle->body_type ?? 'â€”'],
          ['Potenza',    $vehicle->power_hp ? $vehicle->power_hp.' CV' : 'â€”'],
          ['Proprietari',$vehicle->previous_owners ?? 'â€”'],
        ] as [$l,$v])
        <div><span class="spec-label">{{ $l }}</span><span class="spec-value">{{ $v }}</span></div>
        @endforeach
      </div>
      @if($vehicle->features)
      <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
        <span class="spec-label" style="display:block;margin-bottom:8px">Dotazioni</span>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach($vehicle->features as $f)
            <span style="background:var(--bg);border:1px solid var(--border);border-radius:4px;padding:3px 9px;font-size:12px;color:var(--text2)">{{ ucfirst(str_replace('_',' ',$f)) }}</span>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    @if($vehicle->description)
    <div class="info-card">
      <h2>Descrizione</h2>
      <p style="font-size:14px;color:var(--text2);line-height:1.7;white-space:pre-wrap">{{ $vehicle->description }}</p>
    </div>
    @endif
  </div>

  <div>
    <div class="price-card">
      <div class="price-label">Prezzo</div>
      <div class="price-value">â‚¬ {{ number_format($vehicle->asking_price,0,',','.') }}</div>
      @if($vehicle->price_negotiable)<div class="price-note">ðŸ’¬ Prezzo trattabile</div>@endif
      @if($vehicle->vat_deductible)<div class="price-note">ðŸ“‹ IVA detraibile</div>@endif
    </div>

    <div class="contact-card">
      <h3>ðŸ“© Richiedi informazioni</h3>
      @if(session('success'))
        <div class="alert-success">âœ“ {{ session('success') }}</div>
      @endif
      <form action="{{ url('/auto-in-vendita/'.$vehicle->id.'/contatto') }}" method="POST">
        @csrf
        <div class="form-group">
          <label class="form-label">Nome *</label>
          <input type="text" name="name" class="form-input" required placeholder="Mario Rossi">
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input type="email" name="email" class="form-input" required placeholder="mario@email.it">
        </div>
        <div class="form-group">
          <label class="form-label">Telefono</label>
          <input type="tel" name="phone" class="form-input" placeholder="+39 333 000 0000">
        </div>
        <div class="form-group">
          <label class="form-label">Messaggio</label>
          <textarea name="message" class="form-input" rows="3" placeholder="Vorrei maggiori informazioni..."></textarea>
        </div>
        <button type="submit" class="btn-contatto">âœ‰ï¸ Invia richiesta</button>
      </form>

      @php $publishedListings = $vehicle->listings->where('status','published'); @endphp
      @if($publishedListings->isNotEmpty())
      <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border)">
        <div style="font-size:11px;color:var(--text2);font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Trovaci anche su</div>
        @foreach($publishedListings as $listing)
          @if($listing->external_url)
            <a href="{{ $listing->external_url }}" target="_blank" class="platform-link">
              ðŸ”— {{ ucwords(str_replace('_',' ',$listing->platform)) }}
            </a>
          @endif
        @endforeach
      </div>
      @endif
    </div>
  </div>
</div>

<footer>
  <p>Â© {{ date('Y') }} CarModel Â· <a href="/auto-in-vendita">Tutti i veicoli</a> Â· <a href="/login">Area riservata</a></p>
</footer>

<script>
function setPhoto(url, el) {
  document.getElementById('main-img').src = url;
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}
</script>
</body>
</html>