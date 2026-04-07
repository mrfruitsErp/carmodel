@extends('layouts.app')
@section('title', $saleVehicle->full_name)

@section('topbar-actions')
@if($saleVehicle->status !== 'venduto')
  <a href="{{ route('marketplace.vehicles.edit', $saleVehicle) }}" class="btn btn-ghost btn-sm">✏️ Modifica</a>
  <form action="{{ route('marketplace.vehicles.sold', $saleVehicle) }}" method="POST" onsubmit="return confirm('Segnare come venduto?')" style="display:inline">
    @csrf
    <input type="hidden" name="sold_price" value="{{ $saleVehicle->asking_price }}">
    <button type="submit" class="btn btn-sm" style="background:var(--blue-bg);color:var(--blue-text);border:1px solid rgba(59,130,246,.3)">✓ Venduto</button>
  </form>
@endif
@endsection

@section('content')

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text3);margin-bottom:16px">
  <a href="{{ route('marketplace.vehicles.index') }}" style="color:var(--text3);text-decoration:none;hover:color:var(--text)">Veicoli</a>
  <span>/</span>
  <span style="color:var(--text)">{{ $saleVehicle->brand }} {{ $saleVehicle->model }}</span>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start">

  {{-- COLONNA SINISTRA --}}
  <div>

    {{-- Galleria foto --}}
    <div class="card" style="padding:16px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div class="card-title" style="margin-bottom:0">📷 Foto</div>
        <label for="foto-upload" style="font-size:12px;color:var(--orange);cursor:pointer;font-weight:600">+ Aggiungi foto</label>
        <input id="foto-upload" type="file" accept="image/*" multiple style="display:none" onchange="uploadFotos(this)">
      </div>

      @php $fotos = $saleVehicle->getMedia('sale_photos'); @endphp

      @if($fotos->isEmpty())
        <div id="foto-grid" style="border:2px dashed var(--border2);border-radius:8px;padding:40px;text-align:center;cursor:pointer" onclick="document.getElementById('foto-upload').click()">
          <div style="font-size:32px;margin-bottom:8px">📷</div>
          <div style="font-size:13px;color:var(--text3)">Clicca per aggiungere foto</div>
          <div style="font-size:11px;color:var(--text3);margin-top:4px">JPG, PNG, WebP — max 10MB</div>
        </div>
      @else
        {{-- Foto principale --}}
        <div style="position:relative;height:280px;border-radius:8px;overflow:hidden;margin-bottom:8px;background:#f0f0f0">
          <img id="main-photo" src="{{ $fotos->first()->getUrl() }}" style="width:100%;height:100%;object-fit:cover">
          <div style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;font-size:11px;padding:4px 10px;border-radius:10px">
            {{ $fotos->count() }} foto
          </div>
        </div>
        {{-- Thumbnails --}}
        <div id="foto-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px">
          @foreach($fotos as $i => $media)
          <div class="foto-thumb" data-id="{{ $media->id }}" style="position:relative;aspect-ratio:4/3;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid {{ $i===0?'var(--orange)':'transparent' }}" onclick="setMainPhoto('{{ $media->getUrl() }}', this)">
            <img src="{{ $media->getUrl('thumb') }}" style="width:100%;height:100%;object-fit:cover">
            <button onclick="deleteFoto({{ $media->id }}, event)" style="position:absolute;top:2px;right:2px;background:rgba(239,68,68,.8);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:10px;cursor:pointer;display:none;align-items:center;justify-content:center;line-height:1">×</button>
          </div>
          @endforeach
          <div style="aspect-ratio:4/3;border:2px dashed var(--border2);border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;background:var(--bg3)" onclick="document.getElementById('foto-upload').click()">
            <span style="font-size:20px;color:var(--text3)">+</span>
          </div>
        </div>
      @endif
    </div>

    {{-- Dati tecnici --}}
    <div class="card">
      <div class="card-title">🔧 Dati tecnici</div>
      <div class="three-col">
        @foreach([
          ['Marca',        $saleVehicle->brand],
          ['Modello',      $saleVehicle->model],
          ['Versione',     $saleVehicle->version ?? '—'],
          ['Anno',         $saleVehicle->year],
          ['Chilometri',   number_format($saleVehicle->mileage,0,',','.').' km'],
          ['Carburante',   ucfirst(str_replace('_',' ',$saleVehicle->fuel_type))],
          ['Cambio',       ucfirst($saleVehicle->transmission)],
          ['Carrozzeria',  $saleVehicle->body_type ?? '—'],
          ['Colore',       $saleVehicle->color ?? '—'],
          ['Potenza',      $saleVehicle->power_hp ? $saleVehicle->power_hp.' CV' : '—'],
          ['Cilindrata',   $saleVehicle->engine_cc ? $saleVehicle->engine_cc.' cc' : '—'],
          ['Condizione',   ucfirst(str_replace('_',' ',$saleVehicle->condition))],
          ['Proprietari',  $saleVehicle->previous_owners ?? '—'],
          ['Targa',        $saleVehicle->plate ?? '—'],
          ['VIN',          $saleVehicle->vin ?? '—'],
        ] as [$label, $value])
        <div>
          <div class="form-label">{{ $label }}</div>
          <div style="font-size:13px;font-weight:500;color:var(--text);margin-top:3px">{{ $value }}</div>
        </div>
        @endforeach
      </div>
      @if($saleVehicle->features)
      <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
        <div class="form-label" style="margin-bottom:8px">Optionals</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach($saleVehicle->features as $f)
            <span style="background:var(--bg3);border:1px solid var(--border2);border-radius:4px;padding:3px 8px;font-size:11px;color:var(--text2)">{{ ucfirst(str_replace('_',' ',$f)) }}</span>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- Descrizione --}}
    <div class="card">
      <div class="card-title">📝 Descrizione annuncio</div>
      @if($saleVehicle->title)
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:10px">{{ $saleVehicle->title }}</div>
      @endif
      <div style="font-size:13px;color:var(--text2);line-height:1.7;white-space:pre-wrap">{{ $saleVehicle->description ?? 'Nessuna descrizione.' }}</div>
    </div>

    {{-- Lead ricevuti --}}
    @if($saleVehicle->leads->isNotEmpty())
    <div class="card">
      <div class="card-title">💬 Lead ricevuti ({{ $saleVehicle->leads->count() }})</div>
      <div>
        @foreach($saleVehicle->leads as $lead)
        <div style="display:flex;align-items:start;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
          <div style="width:32px;height:32px;border-radius:50%;background:var(--orange-bg);border:1px solid var(--orange-border);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--orange);flex-shrink:0">
            {{ strtoupper(substr($lead->lead_name ?? 'U',0,1)) }}
          </div>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
              <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $lead->lead_name ?? 'Anonimo' }}</span>
              @include('marketplace.partials._platform_badge', ['platform' => $lead->platform])
              <span style="font-size:11px;color:var(--text3)">{{ $lead->created_at->diffForHumans() }}</span>
            </div>
            @if($lead->lead_message)
              <div style="font-size:12px;color:var(--text2);margin-top:4px">{{ $lead->lead_message }}</div>
            @endif
            <div style="display:flex;gap:10px;margin-top:4px">
              @if($lead->lead_email)<a href="mailto:{{ $lead->lead_email }}" style="font-size:11px;color:var(--blue-text)">{{ $lead->lead_email }}</a>@endif
              @if($lead->lead_phone)<a href="tel:{{ $lead->lead_phone }}" style="font-size:11px;color:var(--blue-text)">{{ $lead->lead_phone }}</a>@endif
            </div>
          </div>
          <span class="badge badge-{{ match($lead->status){'nuovo'=>'green','contattato'=>'blue','trattativa'=>'amber','vinto'=>'green','perso'=>'gray',default=>'gray'} }}">{{ ucfirst($lead->status) }}</span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>

  {{-- COLONNA DESTRA --}}
  <div>

    {{-- Prezzi --}}
    <div class="card">
      <div class="card-title">💰 Prezzi</div>
      <div style="background:linear-gradient(135deg,var(--orange-bg),transparent);border:1px solid var(--orange-border);border-radius:8px;padding:14px;margin-bottom:14px;text-align:center">
        <div style="font-size:11px;color:var(--orange-text);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:4px">Prezzo richiesta</div>
        <div style="font-family:var(--font-display);font-size:32px;font-weight:800;color:var(--orange)">€ {{ number_format($saleVehicle->asking_price,0,',','.') }}</div>
        @if($saleVehicle->price_negotiable)<div style="font-size:11px;color:var(--text3);margin-top:2px">Trattabile</div>@endif
      </div>
      @if($saleVehicle->purchase_price)
      <div class="info-row">
        <span class="info-label">Prezzo acquisto</span>
        <span class="info-value">€ {{ number_format($saleVehicle->purchase_price,0,',','.') }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Margine</span>
        <span class="info-value" style="color:var(--green-text);font-weight:700">€ {{ number_format($saleVehicle->margin,0,',','.') }} ({{ $saleVehicle->margin_percent }}%)</span>
      </div>
      @endif
      {{-- Aggiorna prezzo --}}
      <form action="{{ route('marketplace.update-price', $saleVehicle) }}" method="POST" style="display:flex;gap:8px;margin-top:14px">
        @csrf
        <input type="number" name="price" value="{{ $saleVehicle->asking_price }}" step="100" class="form-input" style="flex:1">
        <button type="submit" class="btn btn-ghost btn-sm">Aggiorna</button>
      </form>
    </div>

    {{-- Pubblica su piattaforme --}}
    <div class="card">
      <div class="card-title">🚀 Pubblica annuncio</div>
      @php
        $allPlatforms = ['autoscout24'=>'AutoScout24','automobile_it'=>'Automobile.it','ebay_motors'=>'eBay Motors','subito_it'=>'Subito.it','facebook_marketplace'=>'Facebook'];
        $listingsByPlatform = $saleVehicle->listings->keyBy('platform');
      @endphp
      <form action="{{ route('marketplace.publish', $saleVehicle) }}" method="POST">
        @csrf
        <div style="margin-bottom:12px">
          @foreach($allPlatforms as $pk => $plabel)
          @php
            $listing = $listingsByPlatform->get($pk);
            $lstatus = $listing?->status ?? 'none';
            $isEnabled = $enabledPlatforms->contains($pk);
            $validation = $validations[$pk] ?? ['valid'=>true,'errors'=>[]];
          @endphp
          <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;margin-bottom:4px;border:1px solid {{ $lstatus==='published'?'rgba(34,197,94,.2)':($lstatus==='error'?'rgba(239,68,68,.2)':'var(--border)') }};background:{{ $lstatus==='published'?'var(--green-bg)':($lstatus==='error'?'var(--red-bg)':'var(--bg3)') }}">
            <div style="display:flex;align-items:center;gap:8px">
              @if($isEnabled && !in_array($lstatus,['published']))
                <input type="checkbox" name="platforms[]" value="{{ $pk }}" id="p_{{ $pk }}" {{ !$validation['valid']?'disabled':'' }} style="width:14px;height:14px;accent-color:var(--orange)">
              @elseif($lstatus==='published')
                <span style="color:var(--green-text);font-size:14px">✓</span>
              @else
                <span style="width:14px;height:14px;background:var(--border2);border-radius:3px;display:inline-block"></span>
              @endif
              <label for="p_{{ $pk }}" style="font-size:12px;font-weight:600;color:var(--text);cursor:pointer">{{ $plabel }}</label>
              @if(!$isEnabled)<span style="font-size:10px;color:var(--text3)">(non conf.)</span>@endif
              @if($lstatus==='error')<span style="font-size:10px;color:var(--red-text)">⚠ Errore</span>@endif
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              @if($listing?->views > 0)<span style="font-size:10px;color:var(--text3)">{{ $listing->views }} views</span>@endif
              @if($lstatus==='published' && $listing?->external_url)
                <a href="{{ $listing->external_url }}" target="_blank" style="font-size:10px;color:var(--blue-text)">Vedi →</a>
              @endif
              @if(in_array($lstatus,['published','error']))
                <form action="{{ route('marketplace.unpublish', $listing) }}" method="POST" style="display:inline">@csrf @method('DELETE')
                  <button type="submit" style="background:none;border:none;font-size:10px;color:var(--red-text);cursor:pointer">Rimuovi</button>
                </form>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        <div style="display:flex;gap:8px">
          <input type="number" name="price" placeholder="Prezzo (default: {{ $saleVehicle->asking_price }})" step="100" class="form-input" style="flex:1;font-size:12px">
          <button type="submit" class="btn btn-primary btn-sm">Pubblica</button>
        </div>
      </form>
    </div>

    {{-- Statistiche --}}
    <div class="card">
      <div class="card-title">📊 Statistiche</div>
      <div class="info-row"><span class="info-label">Visualizzazioni totali</span><span class="info-value">{{ $saleVehicle->totalViews() }}</span></div>
      <div class="info-row"><span class="info-label">Lead ricevuti</span><span class="info-value" style="color:var(--green-text)">{{ $saleVehicle->totalContacts() }}</span></div>
      <div class="info-row"><span class="info-label">Annunci live</span><span class="info-value">{{ $saleVehicle->listings->where('status','published')->count() }}</span></div>
      <div class="info-row"><span class="info-label">Stato</span>
        <span class="badge badge-{{ match($saleVehicle->status){'attivo'=>'green','venduto'=>'blue','sospeso'=>'amber',default=>'gray'} }}">{{ ucfirst($saleVehicle->status) }}</span>
      </div>
    </div>

    {{-- Sito proprietario --}}
    <div class="card">
      <div class="card-title">🌐 Sito proprietario</div>
      <div style="font-size:12px;color:var(--text3);margin-bottom:10px">Link diretto alla scheda pubblica del veicolo</div>
      @php $publicUrl = url('/auto-in-vendita/'.$saleVehicle->id.'-'.str_replace(' ','-',strtolower($saleVehicle->brand.'-'.$saleVehicle->model))); @endphp
      <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:8px 10px;font-family:var(--mono);font-size:11px;color:var(--text2);word-break:break-all;margin-bottom:8px">{{ $publicUrl }}</div>
      <div style="display:flex;gap:8px">
        <a href="{{ $publicUrl }}" target="_blank" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">👁 Anteprima</a>
        <button onclick="navigator.clipboard.writeText('{{ $publicUrl }}');this.textContent='✓ Copiato!';setTimeout(()=>this.textContent='📋 Copia',2000)" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">📋 Copia link</button>
      </div>
    </div>

  </div>
</div>

@push('scripts')
<script>
function setMainPhoto(url, el) {
  document.getElementById('main-photo').src = url;
  document.querySelectorAll('.foto-thumb').forEach(t => t.style.borderColor = 'transparent');
  el.style.borderColor = 'var(--orange)';
}
document.querySelectorAll('.foto-thumb').forEach(t => {
  t.addEventListener('mouseenter', () => t.querySelector('button').style.display = 'flex');
  t.addEventListener('mouseleave', () => t.querySelector('button').style.display = 'none');
});
function deleteFoto(mediaId, e) {
  e.stopPropagation();
  if (!confirm('Eliminare questa foto?')) return;
  fetch(`/marketplace/vehicles/{{ $saleVehicle->id }}/foto/${mediaId}`, {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Accept':'application/json'}
  }).then(r=>r.json()).then(d=>{ if(d.ok) location.reload(); });
}
function uploadFotos(input) {
  const files = [...input.files];
  files.forEach(file => {
    const form = new FormData();
    form.append('photo', file);
    form.append('_token', '{{ csrf_token() }}');
    fetch(`/marketplace/vehicles/{{ $saleVehicle->id }}/foto`, {method:'POST',body:form})
      .then(r=>r.json()).then(()=>location.reload());
  });
}
</script>
@endpush
@endsection