<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->version }} - {{ $vehicle->year }}</title>
  <meta name="description" content="{{ $vehicle->description ? Str::limit($vehicle->description, 160) : $vehicle->brand.' '.$vehicle->model.' '.$vehicle->year.' - '.number_format($vehicle->asking_price,0,',','.').' euro' }}">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f5;color:#1a1a1a;line-height:1.5}
    .container{max-width:1100px;margin:0 auto;padding:0 16px}
    .header{background:#1a1a1a;padding:14px 0}
    .header a{color:#ff6b00;font-weight:700;font-size:18px;text-decoration:none}
    .breadcrumb{padding:12px 0;font-size:13px;color:#888}
    .breadcrumb a{color:#ff6b00;text-decoration:none}
    .grid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;padding:20px 0 40px}
    @media(max-width:768px){.grid{grid-template-columns:1fr}}
    .card{background:#fff;border-radius:12px;padding:20px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
    .main-photo{width:100%;height:380px;object-fit:cover;border-radius:10px;margin-bottom:10px}
    .thumbs{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}
    .thumb{width:80px;height:60px;object-fit:cover;border-radius:6px;cursor:pointer;border:2px solid transparent;flex-shrink:0}
    .thumb:hover{border-color:#ff6b00}
    .no-photo{width:100%;height:280px;background:#f0f0f0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:14px}
    h1{font-size:22px;font-weight:700;margin-bottom:4px}
    .price{font-size:32px;font-weight:800;color:#ff6b00;margin:12px 0}
    .badge{display:inline-block;background:#f0f0f0;border-radius:6px;padding:4px 10px;font-size:12px;margin:2px}
    .spec-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}
    .spec-item .label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.05em}
    .spec-item .value{font-size:14px;font-weight:600}
    .section-title{font-size:16px;font-weight:700;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #ff6b00}
    .form-group{margin-bottom:14px}
    .form-group label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#444}
    .form-group input,.form-group textarea,.form-group select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px;outline:none}
    .form-group input:focus,.form-group textarea:focus{border-color:#ff6b00}
    .btn{width:100%;padding:13px;background:#ff6b00;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer}
    .btn:hover{background:#e05e00}
    .alert-success{background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:14px;font-size:14px}
    .targa{font-family:monospace;background:#1a1a1a;color:#fff;padding:2px 8px;border-radius:4px;font-size:13px}
    .features{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
    .feature{background:#f5f5f5;border:1px solid #e0e0e0;border-radius:6px;padding:4px 10px;font-size:12px}
    .back-link{display:inline-block;margin-bottom:16px;color:#ff6b00;text-decoration:none;font-size:14px}
  </style>
</head>
<body>

<div class="header">
  <div class="container">
    <a href="{{ url('/') }}">CarModel</a>
  </div>
</div>

<div class="container">

  <div class="breadcrumb">
    <a href="{{ url('auto-in-vendita') }}">Auto in vendita</a> &rsaquo; {{ $vehicle->brand }} {{ $vehicle->model }}
  </div>

  <div class="grid">

    {{-- COLONNA SINISTRA --}}
    <div>

      @php $photos = $vehicle->getMedia('sale_photos'); @endphp
      @if($photos->count())
        <img src="{{ $photos->first()->getUrl() }}" id="mainPhoto" class="main-photo" alt="{{ $vehicle->brand }} {{ $vehicle->model }}">
        @if($photos->count() > 1)
        <div class="thumbs">
          @foreach($photos as $photo)
          <img src="{{ $photo->getUrl('thumb') }}" class="thumb" onclick="document.getElementById('mainPhoto').src='{{ $photo->getUrl() }}'" alt="">
          @endforeach
        </div>
        @endif
      @else
        <div class="no-photo">Nessuna foto disponibile</div>
      @endif

      {{-- DATI TECNICI --}}
      <div class="card" style="margin-top:16px">
        <div class="section-title">Dati tecnici</div>
        <div class="spec-grid">
          <div class="spec-item"><div class="label">Marca</div><div class="value">{{ $vehicle->brand }}</div></div>
          <div class="spec-item"><div class="label">Modello</div><div class="value">{{ $vehicle->model }}</div></div>
          @if($vehicle->version)<div class="spec-item"><div class="label">Versione</div><div class="value">{{ $vehicle->version }}</div></div>@endif
          <div class="spec-item"><div class="label">Anno</div><div class="value">{{ $vehicle->year }}</div></div>
          <div class="spec-item"><div class="label">Chilometri</div><div class="value">{{ number_format($vehicle->mileage,0,',','.') }} km</div></div>
          <div class="spec-item"><div class="label">Carburante</div><div class="value">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</div></div>
          @if($vehicle->transmission)<div class="spec-item"><div class="label">Cambio</div><div class="value">{{ ucfirst($vehicle->transmission) }}</div></div>@endif
          @if($vehicle->body_type)<div class="spec-item"><div class="label">Carrozzeria</div><div class="value">{{ ucfirst(str_replace('_',' ',$vehicle->body_type)) }}</div></div>@endif
          @if($vehicle->color)<div class="spec-item"><div class="label">Colore</div><div class="value">{{ $vehicle->color }}</div></div>@endif
          @if($vehicle->power_hp)<div class="spec-item"><div class="label">Potenza</div><div class="value">{{ $vehicle->power_hp }} CV</div></div>@endif
          @if($vehicle->engine_cc)<div class="spec-item"><div class="label">Cilindrata</div><div class="value">{{ number_format($vehicle->engine_cc) }} cc</div></div>@endif
          <div class="spec-item"><div class="label">Condizione</div><div class="value">{{ ucfirst($vehicle->condition ?? '-') }}</div></div>
          @if($vehicle->previous_owners)<div class="spec-item"><div class="label">Proprietari prec.</div><div class="value">{{ $vehicle->previous_owners }}</div></div>@endif
          @if($vehicle->plate)<div class="spec-item"><div class="label">Targa</div><div class="value"><span class="targa">{{ $vehicle->plate }}</span></div></div>@endif
        </div>
      </div>

      {{-- OPTIONAL --}}
      @if(!empty($vehicle->features))
      <div class="card">
        <div class="section-title">Optionals</div>
        <div class="features">
          @foreach($vehicle->features as $f)
          <span class="feature">{{ ucfirst(str_replace('_',' ',$f)) }}</span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- DESCRIZIONE --}}
      @if($vehicle->description)
      <div class="card">
        <div class="section-title">Descrizione</div>
        <div style="font-size:14px;color:#444;line-height:1.8;white-space:pre-wrap">{{ $vehicle->description }}</div>
      </div>
      @endif

    </div>

    {{-- COLONNA DESTRA --}}
    <div>

      <div class="card">
        <h1>{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
        @if($vehicle->version)<div style="color:#666;font-size:14px">{{ $vehicle->version }}</div>@endif
        <div class="price">euro {{ number_format($vehicle->asking_price,0,',','.') }}</div>
        @if($vehicle->price_negotiable)<div style="font-size:13px;color:#888;margin-bottom:8px">Prezzo trattabile</div>@endif
        @if($vehicle->vat_deductible)<span class="badge" style="background:#e8f5e9;color:#2e7d32">IVA detraibile</span>@endif

        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px">
          <span class="badge">{{ $vehicle->year }}</span>
          <span class="badge">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
          <span class="badge">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
        </div>
      </div>

      {{-- FORM CONTATTO --}}
      <div class="card">
        <div class="section-title">Richiedi informazioni</div>

        @if(session('contact_sent'))
          <div class="alert-success">Messaggio inviato! Sarai contattato a breve.</div>
        @endif

        <form method="POST" action="{{ route('public.vehicles.contact', $vehicle->id) }}">
          @csrf
          <div class="form-group">
            <label>Nome e Cognome *</label>
            <input type="text" name="name" required placeholder="Mario Rossi" value="{{ old('name') }}">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required placeholder="mario@email.it" value="{{ old('email') }}">
          </div>
          <div class="form-group">
            <label>Telefono</label>
            <input type="tel" name="phone" placeholder="+39 333 1234567" value="{{ old('phone') }}">
          </div>
          <div class="form-group">
            <label>Messaggio</label>
            <textarea name="message" rows="3" placeholder="Sono interessato a questo veicolo...">{{ old('message') }}</textarea>
          </div>
          <button type="submit" class="btn">Invia richiesta</button>
        </form>
      </div>

    </div>
  </div>
</div>

</body>
</html>