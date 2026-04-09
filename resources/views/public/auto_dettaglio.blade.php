<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }} - CarModel</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#f6f7f9;color:#1f2937;font-size:14px}
.topbar{background:#111827;padding:14px 24px;display:flex;align-items:center;justify-content:space-between}
.logo{display:flex;align-items:center;gap:10px}
.logo-icon{width:36px;height:36px;background:#ff6b00;border-radius:7px;display:flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-size:15px;font-weight:700;color:#000}
.logo-name{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;color:#fff;letter-spacing:.06em}
.back-link{color:rgba(255,255,255,.6);text-decoration:none;font-size:13px}
.back-link:hover{color:#ff6b00}
.container{max-width:1100px;margin:0 auto;padding:24px 20px}
.grid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}
.photo-main{background:#e8ecf0;border-radius:12px;overflow:hidden;aspect-ratio:16/9;margin-bottom:8px;display:flex;align-items:center;justify-content:center}
.photo-main img{width:100%;height:100%;object-fit:cover}
.photo-thumbs{display:flex;gap:8px;overflow-x:auto}
.thumb{width:80px;height:60px;background:#e8ecf0;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;flex-shrink:0}
.thumb.active,.thumb:hover{border-color:#ff6b00}
.thumb img{width:100%;height:100%;object-fit:cover}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:16px}
.price-card{background:linear-gradient(135deg,#1a1f2e,#111827);border:none;color:#fff}
.price-big{font-family:'Rajdhani',sans-serif;font-size:36px;font-weight:700;color:#ff6b00;line-height:1}
.badge{display:inline-flex;padding:3px 10px;border-radius:4px;font-size:11px;font-weight:600}
.badge-green{background:rgba(34,197,94,.15);color:#16a34a;border:1px solid rgba(34,197,94,.2)}
.badge-blue{background:rgba(59,130,246,.1);color:#2563eb;border:1px solid rgba(59,130,246,.2)}
.spec-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.spec{background:#f6f7f9;border-radius:6px;padding:10px 12px}
.spec-label{font-size:10px;color:#9ca3af;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-bottom:3px}
.spec-value{font-size:13px;font-weight:600;color:#1f2937}
.features-grid{display:flex;flex-wrap:wrap;gap:6px}
.feature{background:#f6f7f9;border:1px solid #e5e7eb;border-radius:6px;padding:4px 10px;font-size:12px;color:#374151}
.form-input{width:100%;background:#f6f7f9;border:1px solid #e5e7eb;border-radius:6px;padding:9px 12px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s;margin-bottom:10px}
.form-input:focus{border-color:#ff6b00}
.btn-send{width:100%;background:#ff6b00;color:#000;border:none;border-radius:8px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s}
.btn-send:hover{background:#e55f00}
.section-title{font-family:'Rajdhani',sans-serif;font-size:14px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px;color:#1f2937}
h1{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;color:#1f2937;margin-bottom:4px}
.subtitle{font-size:14px;color:#6b7280;margin-bottom:16px}
@media(max-width:700px){.grid{grid-template-columns:1fr}.spec-grid{grid-template-columns:1fr 1fr}}
</style>
</head>
<body>
<div class="topbar">
  <div class="logo">
    <div class="logo-icon">CM</div>
    <span class="logo-name">CARMODEL</span>
  </div>
  <a href="{{ route('public.auto_in_vendita') }}" class="back-link">Tutti i veicoli</a>
</div>

<div class="container">
  <div class="grid">
    <div>
      {{-- Galleria foto --}}
      @php $photos = $vehicle->getMedia('sale_photos'); @endphp
      <div class="photo-main" id="mainPhoto">
        @if($photos->count())
          <img src="{{ $photos->first()->getUrl() }}" id="mainImg" alt="{{ $vehicle->brand }} {{ $vehicle->model }}">
        @else
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
        @endif
      </div>
      @if($photos->count() > 1)
      <div class="photo-thumbs">
        @foreach($photos as $i => $photo)
        <div class="thumb {{ $i===0 ? 'active' : '' }}" onclick="changePhoto('{{ $photo->getUrl() }}', this)">
          <img src="{{ $photo->getUrl('thumb') }}" alt="">
        </div>
        @endforeach
      </div>
      @endif

      <div style="margin-top:20px">
        <h1>{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
        <div class="subtitle">{{ $vehicle->version }} - {{ $vehicle->year }}</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
          <span class="badge badge-green">{{ ucfirst($vehicle->condition ?? 'buono') }}</span>
          @if($vehicle->previous_owners)<span class="badge badge-blue">{{ $vehicle->previous_owners }} {{ $vehicle->previous_owners===1 ? 'proprietario' : 'proprietari' }}</span>@endif
          @if($vehicle->vat_deductible)<span class="badge badge-blue">IVA detraibile</span>@endif
        </div>
      </div>

      {{-- Specifiche tecniche --}}
      <div class="card">
        <div class="section-title">Caratteristiche tecniche</div>
        <div class="spec-grid">
          <div class="spec"><div class="spec-label">Anno</div><div class="spec-value">{{ $vehicle->year }}</div></div>
          <div class="spec"><div class="spec-label">KM</div><div class="spec-value">{{ number_format($vehicle->mileage,0,',','.') }} km</div></div>
          <div class="spec"><div class="spec-label">Carburante</div><div class="spec-value">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</div></div>
          <div class="spec"><div class="spec-label">Cambio</div><div class="spec-value">{{ ucfirst($vehicle->transmission ?? '-') }}</div></div>
          @if($vehicle->power_hp)<div class="spec"><div class="spec-label">Potenza</div><div class="spec-value">{{ $vehicle->power_hp }} CV ({{ $vehicle->power_kw }} kW)</div></div>@endif
          @if($vehicle->engine_cc)<div class="spec"><div class="spec-label">Cilindrata</div><div class="spec-value">{{ number_format($vehicle->engine_cc) }} cc</div></div>@endif
          @if($vehicle->doors)<div class="spec"><div class="spec-label">Porte</div><div class="spec-value">{{ $vehicle->doors }}</div></div>@endif
          @if($vehicle->seats)<div class="spec"><div class="spec-label">Posti</div><div class="spec-value">{{ $vehicle->seats }}</div></div>@endif
          @if($vehicle->color)<div class="spec"><div class="spec-label">Colore</div><div class="spec-value">{{ $vehicle->color }}</div></div>@endif
          @if($vehicle->body_type)<div class="spec"><div class="spec-label">Carrozzeria</div><div class="spec-value">{{ ucfirst(str_replace('_',' ',$vehicle->body_type)) }}</div></div>@endif
        </div>
      </div>

      {{-- Optional --}}
      @if(!empty($vehicle->features))
      <div class="card">
        <div class="section-title">Optional e dotazioni</div>
        <div class="features-grid">
          @foreach($vehicle->features as $f)
          <div class="feature">{{ ucfirst(str_replace('_',' ',$f)) }}</div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Descrizione --}}
      @if($vehicle->description)
      <div class="card">
        <div class="section-title">Descrizione</div>
        <div style="font-size:13px;color:#374151;line-height:1.8;white-space:pre-wrap">{{ $vehicle->description }}</div>
      </div>
      @endif
    </div>

    {{-- Colonna destra --}}
    <div>
      <div class="card price-card">
        <div style="font-size:11px;color:rgba(255,255,255,.5);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">Prezzo</div>
        <div class="price-big">{{ number_format($vehicle->asking_price,0,',','.') }} euro</div>
        @if($vehicle->price_negotiable)<div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:6px">Prezzo trattabile</div>@endif
      </div>

      <div class="card">
        <div class="section-title">Richiedi informazioni</div>
        @if(session('lead_sent'))
          <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:14px;color:#16a34a;text-align:center;font-weight:500">Richiesta inviata! Sarai contattato presto.</div>
        @else
        <form method="POST" action="{{ route('public.lead.store', $vehicle->id) }}">
          @csrf
          <input type="text" name="name" class="form-input" placeholder="Nome *" required value="{{ old('name') }}">
          <input type="email" name="email" class="form-input" placeholder="Email *" required value="{{ old('email') }}">
          <input type="tel" name="phone" class="form-input" placeholder="Telefono" value="{{ old('phone') }}">
          <textarea name="message" class="form-input" rows="3" placeholder="Messaggio..." style="resize:none">{{ old('message') }}</textarea>
          <button type="submit" class="btn-send">Invia richiesta</button>
        </form>
        @endif
      </div>

      <div class="card" style="text-align:center">
        <div style="font-size:12px;color:#6b7280;margin-bottom:4px">Targa</div>
        <div style="font-family:monospace;font-size:18px;font-weight:700;background:#f6f7f9;padding:6px 16px;border-radius:6px;border:1px solid #e5e7eb;display:inline-block">{{ $vehicle->plate ?? 'N/D' }}</div>
        @if($vehicle->vin)<div style="font-size:11px;color:#9ca3af;margin-top:8px">VIN: {{ $vehicle->vin }}</div>@endif
      </div>
    </div>
  </div>
</div>

<script>
function changePhoto(url, thumb) {
  document.getElementById('mainImg').src = url;
  document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
}
</script>
</body>
</html>