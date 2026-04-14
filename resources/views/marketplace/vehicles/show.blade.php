@extends('layouts.app')
@section('title', $saleVehicle->brand.' '.$saleVehicle->model)

@section('topbar-actions')
<a href="{{ route('marketplace.vehicles.edit', $saleVehicle) }}" class="btn btn-ghost btn-sm">Modifica</a>

@if($saleVehicle->status !== 'archiviato')
<form action="{{ route('marketplace.vehicles.status', $saleVehicle) }}" method="POST" style="display:inline">
  @csrf
  <input type="hidden" name="status" value="archiviato">
  <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red-text)"
    onclick="return confirm('Archiviare questo veicolo?')">
    Archivia
  </button>
</form>
@else
<form action="{{ route('marketplace.vehicles.status', $saleVehicle) }}" method="POST" style="display:inline">
  @csrf
  <input type="hidden" name="status" value="bozza">
  <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--green-text)">
    Recupera
  </button>
</form>
<form action="{{ route('marketplace.vehicles.destroy', $saleVehicle) }}" method="POST" style="display:inline"
  onsubmit="return confirm('Eliminare definitivamente? Azione irreversibile.')">
  @csrf @method('DELETE')
  <button type="submit" class="btn btn-danger btn-sm">🗑 Elimina</button>
</form>
@endif

<form action="{{ route('marketplace.vehicles.status', $saleVehicle) }}" method="POST" style="display:flex;align-items:center;gap:8px">
  @csrf
  <select name="status" onchange="this.form.submit()" class="form-select" style="padding:5px 10px;font-size:12px;width:auto">
    @foreach(['attivo'=>'Attivo','sospeso'=>'Sospeso','venduto'=>'Venduto','bozza'=>'Bozza'] as $val=>$label)
    <option value="{{ $val }}" {{ $saleVehicle->status===$val ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
</form>
@endsection
@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('marketplace.vehicles.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&larr; Veicoli</a>
  <span style="color:var(--text3);font-size:13px"> / {{ $saleVehicle->brand }} {{ $saleVehicle->model }}</span>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">

  {{-- COLONNA SINISTRA --}}
  <div>

    {{-- FOTO --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="card-title" style="margin:0">Foto ({{ $saleVehicle->getMedia('sale_photos')->count() }})</div>
        <div style="display:flex;gap:8px;align-items:center">
          <button type="button" onclick="document.getElementById('foto-upload-input').click()" class="btn btn-ghost btn-sm">+ Aggiungi foto</button>
          <input type="file" id="foto-upload-input" accept="image/jpeg,image/png,image/webp" multiple style="display:none" onchange="uploadFotoShow(this.files)">
        </div>
      </div>

      @php $photos = $saleVehicle->getMedia('sale_photos'); @endphp
      @if($photos->count())
        <div style="border-radius:8px;overflow:hidden;margin-bottom:10px;background:var(--bg3)">
          <img src="{{ $photos->first()->getUrl() }}" id="mainPhoto"
               style="width:100%;height:360px;object-fit:contain;background:#f8f8f8"
               alt="{{ $saleVehicle->brand }} {{ $saleVehicle->model }}">
        </div>
        <div id="foto-gallery" style="display:flex;gap:8px;flex-wrap:wrap;padding-bottom:4px">
          @foreach($photos as $i => $photo)
          @php $thumbUrl = $photo->getUrl('thumb') ?: $photo->getUrl(); @endphp
          <div id="show-foto-{{ $photo->id }}"
               style="position:relative;width:80px;height:60px;border-radius:6px;overflow:hidden;flex-shrink:0;border:2px solid {{ $i===0 ? 'var(--orange)' : 'var(--border2)' }};cursor:pointer;background:var(--bg3)"
               onclick="selectFoto(this, '{{ $photo->getUrl() }}')"
               onmouseover="this.style.borderColor='var(--orange)'"
               onmouseout="if(!this.classList.contains('selected'))this.style.borderColor='var(--border2)'">
            <img src="{{ $thumbUrl }}"
                 style="width:100%;height:100%;object-fit:contain;background:#f8f8f8"
                 onerror="this.src='{{ $photo->getUrl() }}'">
            <button type="button" onclick="event.stopPropagation();eliminaFotoShow({{ $photo->id }})"
                    style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:3px;width:18px;height:18px;cursor:pointer;font-size:12px;line-height:1;display:flex;align-items:center;justify-content:center">&times;</button>
          </div>
          @endforeach
        </div>
        <div style="font-size:11px;color:var(--text3);margin-top:8px">{{ $photos->count() }} foto &mdash; clicca per ingrandire</div>
      @else
        <div id="foto-gallery" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px"></div>
        <div id="no-foto-msg" style="background:var(--bg3);border:2px dashed var(--border2);border-radius:8px;padding:40px;text-align:center">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text3)" stroke-width="1.5" style="margin-bottom:10px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <div style="font-size:13px;color:var(--text3)">Nessuna foto &mdash; clicca "+ Aggiungi foto"</div>
        </div>
      @endif

      <div id="upload-progress" style="display:none;margin-top:10px">
        <div style="height:4px;background:var(--bg3);border-radius:2px;overflow:hidden">
          <div id="progress-bar" style="height:100%;background:var(--orange);width:0%;transition:width .3s"></div>
        </div>
        <div id="progress-text" style="font-size:11px;color:var(--text3);margin-top:4px">Caricamento...</div>
      </div>
    </div>

    {{-- DATI TECNICI --}}
    <div class="card">
      <div class="card-title">Dati tecnici</div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div><div class="form-label">Marca</div><div style="font-weight:500">{{ $saleVehicle->brand }}</div></div>
        <div><div class="form-label">Modello</div><div style="font-weight:500">{{ $saleVehicle->model }}</div></div>
        <div><div class="form-label">Versione</div><div style="font-weight:500">{{ $saleVehicle->version ?? '-' }}</div></div>
        <div><div class="form-label">Anno</div><div style="font-weight:500">{{ $saleVehicle->year }}</div></div>
        <div><div class="form-label">Chilometri</div><div style="font-weight:500">{{ number_format($saleVehicle->mileage,0,',','.') }} km</div></div>
        <div><div class="form-label">Carburante</div><div style="font-weight:500">{{ ucfirst(str_replace('_',' ',$saleVehicle->fuel_type)) }}</div></div>
        <div><div class="form-label">Cambio</div><div style="font-weight:500">{{ ucfirst($saleVehicle->transmission ?? '-') }}</div></div>
        <div><div class="form-label">Carrozzeria</div><div style="font-weight:500">{{ ucfirst(str_replace('_',' ',$saleVehicle->body_type ?? '-')) }}</div></div>
        <div><div class="form-label">Colore</div><div style="font-weight:500">{{ $saleVehicle->color ?? '-' }}</div></div>
        @if($saleVehicle->power_hp)<div><div class="form-label">Potenza</div><div style="font-weight:500">{{ $saleVehicle->power_hp }} CV</div></div>@endif
        @if($saleVehicle->engine_cc)<div><div class="form-label">Cilindrata</div><div style="font-weight:500">{{ number_format($saleVehicle->engine_cc) }} cc</div></div>@endif
        <div><div class="form-label">Condizione</div><div style="font-weight:500">{{ ucfirst($saleVehicle->condition ?? '-') }}</div></div>
        @if($saleVehicle->previous_owners !== null)<div><div class="form-label">Proprietari</div><div style="font-weight:500">{{ $saleVehicle->previous_owners }}</div></div>@endif
        @if($saleVehicle->plate)<div><div class="form-label">Targa</div><div style="font-family:var(--mono);font-weight:500">{{ $saleVehicle->plate }}</div></div>@endif
        @if($saleVehicle->vin)<div style="grid-column:span 2"><div class="form-label">VIN</div><div style="font-family:var(--mono);font-size:12px;font-weight:500">{{ $saleVehicle->vin }}</div></div>@endif
      </div>
    </div>

    {{-- OPTIONAL --}}
    @if(!empty($saleVehicle->features))
    <div class="card">
      <div class="card-title">Optionals</div>
      <div style="display:flex;flex-wrap:wrap;gap:6px">
        @foreach($saleVehicle->features as $f)
        <span style="background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:4px 10px;font-size:12px">{{ ucfirst(str_replace('_',' ',$f)) }}</span>
        @endforeach
      </div>
    </div>
    @endif

    {{-- DESCRIZIONE --}}
    @if($saleVehicle->title || $saleVehicle->description)
    <div class="card">
      <div class="card-title">Descrizione</div>
      @if($saleVehicle->title)<div style="font-size:15px;font-weight:600;margin-bottom:10px">{{ $saleVehicle->title }}</div>@endif
      @if($saleVehicle->description)<div style="font-size:13px;color:var(--text2);line-height:1.8;white-space:pre-wrap">{{ $saleVehicle->description }}</div>@endif
    </div>
    @endif

  </div>

  {{-- COLONNA DESTRA --}}
  <div>

    {{-- STATO --}}
    <div class="card" style="padding:12px 16px;margin-bottom:0">
      <div style="display:flex;align-items:center;justify-content:space-between">
        <span style="font-size:13px;color:var(--text3)">Stato annuncio</span>
        <span class="badge badge-{{ $saleVehicle->status==='attivo'?'green':($saleVehicle->status==='venduto'?'blue':($saleVehicle->status==='sospeso'?'amber':'gray')) }}">
          {{ ucfirst($saleVehicle->status) }}
        </span>
      </div>
    </div>

    {{-- PREZZI --}}
    <div class="card">
      <div class="card-title">Prezzi</div>
      <div style="background:var(--orange-bg);border:1px solid var(--orange-border);border-radius:8px;padding:16px;margin-bottom:14px;text-align:center">
        <div style="font-size:10px;color:var(--orange);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px">Prezzo richiesta</div>
        <div style="font-family:var(--font-display);font-size:28px;font-weight:800;color:var(--orange)">euro {{ number_format($saleVehicle->asking_price,0,',','.') }}</div>
        @if($saleVehicle->price_negotiable)<div style="font-size:11px;color:var(--orange-text);margin-top:4px">Trattabile</div>@endif
      </div>
      <div class="info-row"><span class="info-label">Prezzo acquisto</span><span class="info-value">{{ $saleVehicle->purchase_price ? 'euro '.number_format($saleVehicle->purchase_price,0,',','.') : '-' }}</span></div>
      @if($saleVehicle->margin)<div class="info-row"><span class="info-label">Margine</span><span class="info-value" style="color:var(--green-text);font-weight:600">euro {{ number_format($saleVehicle->margin,0,',','.') }} ({{ $saleVehicle->margin_percent }}%)</span></div>@endif
      <form action="{{ route('marketplace.update-price', $saleVehicle) }}" method="POST" style="display:flex;gap:8px;margin-top:14px">
        @csrf
        <input type="number" name="asking_price" value="{{ $saleVehicle->asking_price }}" class="form-input" step="100" style="flex:1;margin:0">
        <button type="submit" class="btn btn-ghost btn-sm">Salva</button>
      </form>
    </div>

    {{-- PUBBLICA --}}
    <div class="card">
      <div class="card-title">Pubblica annuncio</div>
      @forelse($saleVehicle->listings as $listing)
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
        <div>
          <div style="font-weight:500;font-size:13px">{{ ucfirst(str_replace('_',' ',$listing->platform)) }}</div>
          <div style="font-size:11px;color:var(--{{ $listing->status==='published'?'green':'amber' }}-text)">{{ ucfirst($listing->status) }}</div>
        </div>
        <form action="{{ route('marketplace.unpublish', $listing) }}" method="POST">@csrf<button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red-text)">Rimuovi</button></form>
      </div>
      @empty
      <div style="font-size:13px;color:var(--text3);padding:10px 0">Nessun annuncio pubblicato</div>
      @endforelse
      <div style="margin-top:12px">
        <a href="{{ route('marketplace.settings') }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">Configura piattaforme</a>
      </div>
    </div>

    {{-- STATISTICHE --}}
    <div class="card">
      <div class="card-title">Statistiche</div>
      <div class="info-row"><span class="info-label">Visualizzazioni</span><span class="info-value">{{ $saleVehicle->totalViews() }}</span></div>
      <div class="info-row"><span class="info-label">Lead ricevuti</span><span class="info-value">{{ $saleVehicle->totalContacts() }}</span></div>
      <div class="info-row"><span class="info-label">Annunci live</span><span class="info-value">{{ $saleVehicle->active_listings_count }}</span></div>
    </div>

    {{-- LINK PUBBLICO --}}
    <div class="card">
      <div class="card-title">Anteprima pubblica</div>
      @php $publicUrl = url('auto-in-vendita/'.$saleVehicle->id.'-'.Str::slug($saleVehicle->brand.'-'.$saleVehicle->model)); @endphp
      <div style="background:var(--bg3);border-radius:6px;padding:8px 10px;font-size:11px;font-family:var(--mono);color:var(--text2);word-break:break-all;margin-bottom:10px">{{ $publicUrl }}</div>
      <div style="display:flex;gap:8px">
        <a href="{{ $publicUrl }}" target="_blank" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">Anteprima</a>
        <button onclick="navigator.clipboard.writeText('{{ $publicUrl }}').then(()=>this.textContent='Copiato!')" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">Copia link</button>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
var _svid = {{ $saleVehicle->id }};
var _svcsrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

function selectFoto(el, url) {
  document.querySelectorAll('#foto-gallery > div').forEach(function(d){ d.style.borderColor='var(--border2)'; });
  el.style.borderColor = 'var(--orange)';
  var main = document.getElementById('mainPhoto');
  if (main) main.src = url;
}

async function uploadFotoShow(files) {
  var arr = Array.from(files);
  var prog = document.getElementById('upload-progress');
  var bar = document.getElementById('progress-bar');
  var txt = document.getElementById('progress-text');
  if (prog) prog.style.display = 'block';
  for (var i = 0; i < arr.length; i++) {
    var fd = new FormData();
    fd.append('photo', arr[i]);
    fd.append('_token', _svcsrf);
    try {
      var res = await fetch('/marketplace/vehicles/' + _svid + '/foto', {method:'POST', body:fd});
      var data = await res.json();
      if (bar) bar.style.width = ((i+1)/arr.length*100) + '%';
      if (txt) txt.textContent = 'Caricato ' + (i+1) + ' di ' + arr.length;
      var noFoto = document.getElementById('no-foto-msg');
      if (noFoto) noFoto.style.display = 'none';
      var gallery = document.getElementById('foto-gallery');
      if (!document.getElementById('mainPhoto')) {
        var wrap = document.createElement('div');
        wrap.style.cssText = 'border-radius:8px;overflow:hidden;margin-bottom:10px;background:var(--bg3)';
        wrap.innerHTML = '<img src="' + data.url + '" id="mainPhoto" style="width:100%;height:360px;object-fit:contain;background:#f8f8f8">';
        gallery.parentNode.insertBefore(wrap, gallery);
      }
      if (gallery) {
        var div = document.createElement('div');
        div.id = 'show-foto-' + data.id;
        div.style.cssText = 'position:relative;width:80px;height:60px;border-radius:6px;overflow:hidden;flex-shrink:0;border:2px solid var(--orange);cursor:pointer;background:var(--bg3)';
        var thumbSrc = data.thumb_url || data.url;
        div.innerHTML = '<img src="' + thumbSrc + '" style="width:100%;height:100%;object-fit:contain;background:#f8f8f8" onerror="this.src=\'' + data.url + '\'" onclick="selectFoto(this.parentNode,\'' + data.url + '\')">'
          + '<button type="button" onclick="event.stopPropagation();eliminaFotoShow(' + data.id + ')" style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:3px;width:18px;height:18px;cursor:pointer;font-size:12px">&times;</button>';
        gallery.appendChild(div);
      }
    } catch(e) {
      if (txt) txt.textContent = 'Errore: ' + arr[i].name;
    }
  }
  setTimeout(function(){ if(prog) prog.style.display='none'; if(bar) bar.style.width='0%'; }, 2000);
}

async function eliminaFotoShow(mid) {
  if (!confirm('Eliminare questa foto?')) return;
  var res = await fetch('/marketplace/vehicles/' + _svid + '/foto/' + mid, {
    method: 'DELETE', headers: {'X-CSRF-TOKEN': _svcsrf, 'Accept': 'application/json'}
  });
  if (res.ok) {
    var el = document.getElementById('show-foto-' + mid);
    if (el) el.remove();
  }
}
</script>
@endpush
@endsection