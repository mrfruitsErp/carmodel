@extends('public.layout')
@section('title', $vehicle->brand.' '.$vehicle->model.' '.$vehicle->version.' - '.$vehicle->year.' | AleCar Torino')
@section('description', $vehicle->description ? Str::limit($vehicle->description, 160) : $vehicle->brand.' '.$vehicle->model.' '.$vehicle->year.' - AleCar S.r.l. Torino')

@section('content')

<div class="container" style="padding-top:32px;padding-bottom:48px">

  {{-- BREADCRUMB --}}
  <div style="margin-bottom:20px;font-size:13px;color:var(--text3)">
    <a href="{{ route('public.vehicles.index') }}" style="color:var(--orange);text-decoration:none">Auto in vendita</a>
    <span style="margin:0 8px">›</span>
    <span>{{ $vehicle->brand }} {{ $vehicle->model }}</span>
  </div>

  <div class="auto-detail-grid">

    {{-- COLONNA SINISTRA --}}
    <div>

      {{-- FOTO --}}
      @php $photos = $vehicle->getMedia('sale_photos'); @endphp
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px">
        @if($photos->count())
          <img src="{{ $photos->first()->getUrl() }}" id="mainPhoto"
               style="width:100%;height:auto;max-height:500px;object-fit:contain;background:var(--bg3);display:block"
               alt="{{ $vehicle->brand }} {{ $vehicle->model }}">
          @if($photos->count() > 1)
          <div style="display:flex;gap:8px;padding:12px;overflow-x:auto">
            @foreach($photos as $photo)
            <img src="{{ $photo->getUrl('thumb') ?: $photo->getUrl() }}"
                 onerror="this.src='{{ $photo->getUrl() }}'"
                 onclick="document.getElementById('mainPhoto').src='{{ $photo->getUrl() }}'"
                 style="width:80px;height:60px;object-fit:cover;background:var(--bg3);border-radius:6px;cursor:pointer;border:2px solid var(--border);flex-shrink:0;transition:.15s"
                 onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='var(--border)'"
                 alt="">
            @endforeach
          </div>
          @endif
        @else
          <div style="width:100%;height:280px;display:flex;align-items:center;justify-content:center;color:var(--text3);flex-direction:column;gap:8px">
            <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
            <span style="font-size:13px">Nessuna foto disponibile</span>
          </div>
        @endif
      </div>

      {{-- DATI TECNICI --}}
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:20px">
        <div class="orange-line"></div>
        <h3 style="font-size:16px;font-weight:700;margin-bottom:16px">Dati tecnici</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
          @foreach([
            ['Marca', $vehicle->brand],
            ['Modello', $vehicle->model],
            $vehicle->version ? ['Versione', $vehicle->version] : null,
            ['Anno', $vehicle->year],
            ['Chilometri', number_format($vehicle->mileage,0,',','.').' km'],
            ['Carburante', ucfirst(str_replace('_',' ',$vehicle->fuel_type))],
            $vehicle->transmission ? ['Cambio', ucfirst($vehicle->transmission)] : null,
            $vehicle->body_type ? ['Carrozzeria', ucfirst(str_replace('_',' ',$vehicle->body_type))] : null,
            $vehicle->color ? ['Colore', $vehicle->color] : null,
            $vehicle->power_hp ? ['Potenza', $vehicle->power_hp.' CV'] : null,
            $vehicle->engine_cc ? ['Cilindrata', number_format($vehicle->engine_cc).' cc'] : null,
            ['Condizione', ucfirst($vehicle->condition ?? '-')],
            $vehicle->previous_owners ? ['Proprietari prec.', $vehicle->previous_owners] : null,
            ($vehicle->plate && $vehicle->plate_visible) ? ['Targa', $vehicle->plate] : null,
          ] as $spec)
          @if($spec)
          <div style="background:var(--bg3);border-radius:8px;padding:10px 14px">
            <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px">{{ $spec[0] }}</div>
            <div style="font-size:14px;font-weight:600;color:var(--text)">
              @if($spec[0] === 'Targa')
                <span style="font-family:monospace;background:var(--bg4);padding:2px 8px;border-radius:4px">{{ $spec[1] }}</span>
              @else
                {{ $spec[1] }}
              @endif
            </div>
          </div>
          @endif
          @endforeach
        </div>
      </div>

      {{-- OPTIONAL --}}
      @if(!empty($vehicle->features))
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:20px">
        <div class="orange-line"></div>
        <h3 style="font-size:16px;font-weight:700;margin-bottom:14px">Optionals e dotazioni</h3>
        <div style="display:flex;flex-wrap:wrap;gap:8px">
          @foreach($vehicle->features as $f)
          <span style="background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:5px 12px;font-size:12px;color:var(--text2)">{{ ucfirst(str_replace('_',' ',$f)) }}</span>
          @endforeach
        </div>
      </div>
      @endif

      {{-- DESCRIZIONE --}}
      @if($vehicle->description)
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px">
        <div class="orange-line"></div>
        <h3 style="font-size:16px;font-weight:700;margin-bottom:14px">Descrizione</h3>
        <p style="font-size:14px;color:var(--text2);line-height:1.9;white-space:pre-wrap">{{ $vehicle->description }}</p>
      </div>
      @endif

    </div>

    {{-- COLONNA DESTRA --}}
    <div class="auto-detail-side">

      {{-- SCHEDA PREZZO --}}
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:16px">
        <h1 style="font-size:22px;font-weight:800;margin-bottom:4px">{{ $vehicle->brand }} {{ $vehicle->model }}</h1>
        @if($vehicle->version)
          <div style="color:var(--text3);font-size:14px;margin-bottom:12px">{{ $vehicle->version }}</div>
        @endif

        {{-- BADGE --}}
        @if($vehicle->status === 'venduto')
          <span style="display:inline-block;background:rgba(59,130,246,.15);color:#60a5fa;border:1px solid rgba(59,130,246,.3);border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700;margin-bottom:12px">Venduto</span>
        @elseif($vehicle->badge_label)
          <span style="display:inline-block;background:var(--orange-bg);color:var(--orange);border:1px solid rgba(255,107,0,.3);border-radius:6px;padding:4px 12px;font-size:12px;font-weight:700;margin-bottom:12px">{{ $vehicle->badge_label }}</span>
        @endif

        {{-- PREZZO --}}
        @if($vehicle->display_price)
          @if($vehicle->price_visible && !$vehicle->price_label && $vehicle->asking_price)
            <div style="font-size:34px;font-weight:800;color:var(--orange);margin:8px 0">€ {{ $vehicle->display_price }}</div>
          @else
            <div style="font-size:20px;font-weight:600;color:var(--text2);font-style:italic;margin:8px 0">{{ $vehicle->display_price }}</div>
          @endif
          @if($vehicle->price_negotiable && $vehicle->price_visible)
            <div style="font-size:12px;color:var(--text3);margin-bottom:8px">Prezzo trattabile</div>
          @endif
          @if($vehicle->vat_deductible && $vehicle->price_visible && !$vehicle->price_label)
            <span style="background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2);border-radius:4px;padding:3px 10px;font-size:11px;font-weight:700">IVA detraibile</span>
          @endif
        @endif

        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
          <span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px">{{ $vehicle->year }}</span>
          <span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
          <span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
        </div>
      </div>

      {{-- FORM CONTATTO --}}
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
        <div style="background:var(--orange);padding:14px 20px">
          <div style="font-size:14px;font-weight:700;color:#000">📩 Richiedi informazioni</div>
          <div style="font-size:12px;color:rgba(0,0,0,.7)">Risposta garantita entro 24 ore</div>
        </div>
        <div style="padding:20px">

          @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
          @endif

          @if($errors->any())
            <div class="alert-error">
              @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('public.vehicles.contact', $vehicle->id) }}">
            @csrf
            <div class="form-group">
              <label class="form-label">Nome e Cognome *</label>
              <input type="text" name="name" class="form-input" required placeholder="Mario Rossi" value="{{ old('name') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-input" required placeholder="mario@email.it" value="{{ old('email') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Telefono</label>
              <input type="tel" name="phone" class="form-input" placeholder="+39 333 1234567" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Messaggio</label>
              <textarea name="message" class="form-textarea" rows="3" placeholder="Sono interessato a questo veicolo...">{{ old('message') }}</textarea>
            </div>
            <div class="form-group">
              <label class="form-check">
                <input type="checkbox" name="gdpr_consent" value="1" required>
                <span>Accetto il trattamento dei dati ai sensi della <a href="{{ route('public.privacy') }}" target="_blank">Privacy Policy</a> *</span>
              </label>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
              Invia richiesta
            </button>
          </form>

          {{-- WHATSAPP DIRETTO --}}
          <a href="https://wa.me/393278072650?text=Ciao%2C%20sono%20interessato%20al%20{{ urlencode($vehicle->brand.' '.$vehicle->model.' '.$vehicle->year) }}"
             target="_blank" rel="noopener"
             style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px;background:#25d366;color:#fff;border-radius:8px;padding:11px;font-size:14px;font-weight:600;text-decoration:none;transition:.15s"
             onmouseover="this.style.background='#1da851'" onmouseout="this.style.background='#25d366'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Scrivici su WhatsApp
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

@push('styles')
<style>
.auto-detail-grid{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start}
.auto-detail-side{position:sticky;top:80px}

@media(max-width:900px){
  .auto-detail-grid{grid-template-columns:1fr;gap:20px}
  .auto-detail-side{position:static}
}
@media(max-width:600px){
  .auto-detail-grid{gap:16px}
  /* Galleria foto: thumb più piccoli */
  div[style*="display:flex;gap:8px;padding:12px"] img{width:60px!important;height:48px!important}
  /* Foto principale max-height ridotta */
  #mainPhoto{max-height:300px!important}
  /* Dati tecnici a 2 colonne piccole */
  div[style*="grid-template-columns:1fr 1fr"]:not([style*="auto-fit"]):not([style*="minmax"]){grid-template-columns:1fr 1fr!important;gap:10px!important}
  /* Padding card ridotti */
  .auto-detail-grid div[style*="padding:24px"]{padding:18px!important}
  /* Titolo h1 dentro la sticky più piccolo */
  .auto-detail-side h1{font-size:20px!important}
  /* Prezzo grande più piccolo */
  .auto-detail-side div[style*="font-size:34px"]{font-size:26px!important}
}
@media(max-width:380px){
  /* Su mobile stretto, dati tecnici in 1 sola colonna */
  .auto-detail-grid div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>
@endpush
@endsection