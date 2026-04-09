@extends('layouts.app')
@section('title', isset($vehicle) ? 'Modifica Veicolo' : 'Nuovo Veicolo in Vendita')
@section('content')
<div style="max-width:900px">
  <div style="margin-bottom:16px"><a href="{{ route('marketplace.vehicles.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&lt;- Veicoli in vendita</a></div>

  {{-- VIN DECODER --}}
  <div class="card" style="border:1px solid var(--orange-border);background:var(--orange-bg)">
    <div class="card-title" style="color:var(--orange)">Decodifica VIN automatica</div>
    <div style="display:flex;gap:10px;align-items:flex-end">
      <div class="form-group" style="flex:1;margin-bottom:0">
        <label class="form-label">Numero telaio VIN (17 caratteri)</label>
        <input type="text" id="vin_input" class="form-input" placeholder="es. WBA3A5G59DNP26082" maxlength="17" style="font-family:var(--mono);letter-spacing:.1em;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      </div>
      <button type="button" onclick="decodeVin()" class="btn btn-primary" id="vin_btn" style="white-space:nowrap">Decodifica VIN</button>
    </div>
    <div id="vin_result" style="margin-top:10px;font-size:13px"></div>
  </div>

  <div class="card">
    <div class="card-title">{{ isset($vehicle) ? 'Modifica veicolo' : 'Nuovo veicolo in vendita' }}</div>
    <form action="{{ isset($vehicle) ? route('marketplace.vehicles.update',$vehicle) : route('marketplace.vehicles.store') }}" method="POST" enctype="multipart/form-data">
      @csrf @if(isset($vehicle)) @method('PUT') @endif
      @if($errors->any())<div class="alert alert-red">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

      {{-- VIN nascosto --}}
      <input type="hidden" name="vin" id="vin_hidden" value="{{ old('vin', $vehicle->vin ?? '') }}">

      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div class="form-group">
          <label class="form-label">Marca *</label>
          <input type="text" name="brand" id="f_brand" value="{{ old('brand', $vehicle->brand ?? '') }}" class="form-input" required placeholder="es. BMW">
        </div>
        <div class="form-group">
          <label class="form-label">Modello *</label>
          <input type="text" name="model" id="f_model" value="{{ old('model', $vehicle->model ?? '') }}" class="form-input" required placeholder="es. Serie 3">
        </div>
        <div class="form-group">
          <label class="form-label">Versione/Allestimento</label>
          <input type="text" name="version" id="f_version" value="{{ old('version', $vehicle->version ?? '') }}" class="form-input" placeholder="es. 320d xDrive Sport">
        </div>
        <div class="form-group">
          <label class="form-label">Anno *</label>
          <input type="number" name="year" id="f_year" value="{{ old('year', $vehicle->year ?? date('Y')) }}" class="form-input" required min="1990" max="{{ date('Y')+1 }}">
        </div>
        <div class="form-group">
          <label class="form-label">Targa</label>
          <input type="text" name="plate" value="{{ old('plate', $vehicle->plate ?? '') }}" class="form-input" style="font-family:var(--mono);text-transform:uppercase" placeholder="AB123CD">
        </div>
        <div class="form-group">
          <label class="form-label">Prima immatricolazione</label>
          <input type="date" name="first_registration" value="{{ old('first_registration', $vehicle->first_registration?->format('Y-m-d') ?? '') }}" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Chilometri *</label>
          <input type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage ?? '') }}" class="form-input" required min="0" placeholder="es. 45000">
        </div>
        <div class="form-group">
          <label class="form-label">Carburante</label>
          <select name="fuel_type" id="f_fuel" class="form-select">
            @foreach(['benzina','diesel','gpl','metano','elettrico','ibrido','ibrido_plug_in','altro'] as $f)
            <option value="{{ $f }}" {{ old('fuel_type',$vehicle->fuel_type??'')===$f?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$f)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Cambio</label>
          <select name="transmission" id="f_trans" class="form-select">
            @foreach(['manuale','automatico','semi_automatico'] as $t)
            <option value="{{ $t }}" {{ old('transmission',$vehicle->transmission??'')===$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Carrozzeria</label>
          <select name="body_type" id="f_body" class="form-select">
            @foreach(['berlina','hatchback','station_wagon','suv','crossover','coupe','cabrio','van','pickup','monovolume'] as $b)
            <option value="{{ $b }}" {{ old('body_type',$vehicle->body_type??'')===$b?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$b)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Colore</label>
          <input type="text" name="color" value="{{ old('color', $vehicle->color ?? '') }}" class="form-input" placeholder="es. Nero Metallizzato">
        </div>
        <div class="form-group">
          <label class="form-label">Tipo colore</label>
          <select name="color_type" class="form-select">
            @foreach(['solido','metallizzato','perlato','opaco'] as $c)
            <option value="{{ $c }}" {{ old('color_type',$vehicle->color_type??'')===$c?'selected':'' }}>{{ ucfirst($c) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Porte</label>
          <input type="number" name="doors" id="f_doors" value="{{ old('doors', $vehicle->doors ?? 5) }}" class="form-input" min="2" max="5">
        </div>
        <div class="form-group">
          <label class="form-label">Posti</label>
          <input type="number" name="seats" id="f_seats" value="{{ old('seats', $vehicle->seats ?? 5) }}" class="form-input" min="1" max="9">
        </div>
        <div class="form-group">
          <label class="form-label">Cilindrata (cc)</label>
          <input type="number" name="engine_cc" id="f_cc" value="{{ old('engine_cc', $vehicle->engine_cc ?? '') }}" class="form-input" placeholder="es. 1998">
        </div>
        <div class="form-group">
          <label class="form-label">Potenza (kW)</label>
          <input type="number" name="power_kw" id="f_kw" value="{{ old('power_kw', $vehicle->power_kw ?? '') }}" class="form-input" placeholder="es. 140">
        </div>
        <div class="form-group">
          <label class="form-label">Potenza (CV)</label>
          <input type="number" name="power_hp" id="f_hp" value="{{ old('power_hp', $vehicle->power_hp ?? '') }}" class="form-input" placeholder="es. 190">
        </div>
        <div class="form-group">
          <label class="form-label">Condizione</label>
          <select name="condition" class="form-select">
            @foreach(['ottimo','buono','discreto','da_revisionare'] as $c)
            <option value="{{ $c }}" {{ old('condition',$vehicle->condition??'buono')===$c?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Proprietari precedenti</label>
          <input type="number" name="previous_owners" value="{{ old('previous_owners', $vehicle->previous_owners ?? 1) }}" class="form-input" min="0" max="10">
        </div>
        <div class="form-group">
          <label class="form-label">Stato annuncio</label>
          <select name="status" class="form-select">
            @foreach(['bozza','attivo','sospeso','venduto','archiviato'] as $s)
            <option value="{{ $s }}" {{ old('status',$vehicle->status??'bozza')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- PREZZI --}}
      <div style="border-top:1px solid var(--border);margin:20px 0;padding-top:20px">
        <div class="card-title">Prezzi</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px">
          <div class="form-group">
            <label class="form-label">Prezzo richiesto (euro) *</label>
            <input type="number" name="asking_price" value="{{ old('asking_price', $vehicle->asking_price ?? '') }}" class="form-input" required step="100" min="0" placeholder="es. 18900">
          </div>
          <div class="form-group">
            <label class="form-label">Prezzo minimo (euro)</label>
            <input type="number" name="min_price" value="{{ old('min_price', $vehicle->min_price ?? '') }}" class="form-input" step="100" min="0">
          </div>
          <div class="form-group">
            <label class="form-label">Prezzo acquisto (euro)</label>
            <input type="number" name="purchase_price" value="{{ old('purchase_price', $vehicle->purchase_price ?? '') }}" class="form-input" step="100" min="0">
          </div>
          <div class="form-group" style="display:flex;flex-direction:column;gap:8px;padding-top:20px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
              <input type="checkbox" name="price_negotiable" value="1" {{ old('price_negotiable',$vehicle->price_negotiable??false)?'checked':'' }} style="width:15px;height:15px;accent-color:var(--orange)"> Prezzo trattabile
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
              <input type="checkbox" name="vat_deductible" value="1" {{ old('vat_deductible',$vehicle->vat_deductible??false)?'checked':'' }} style="width:15px;height:15px;accent-color:var(--orange)"> IVA detraibile
            </label>
          </div>
        </div>
      </div>

      {{-- OPTIONAL --}}
      <div style="border-top:1px solid var(--border);margin:20px 0;padding-top:20px">
        <div class="card-title">Optional e dotazioni</div>
        @php
        $allFeatures = [
          'Comfort' => ['aria_condizionata','clima_automatico','clima_bizona','sedili_riscaldati','sedili_ventilati','sedili_elettrici','sedili_memoria','volante_riscaldato','tetto_apribile','tetto_panoramico','cruise_control','cruise_control_adattivo'],
          'Sicurezza' => ['abs','esp','airbag_frontali','airbag_laterali','airbag_tendina','sensori_parcheggio_ant','sensori_parcheggio_post','telecamera_posteriore','telecamera_360','lane_assist','blind_spot','frenata_autonoma','riconoscimento_segnali'],
          'Infotainment' => ['radio','bluetooth','apple_carplay','android_auto','navigatore','schermo_touch','hifi','usb','wireless_charging','head_up_display'],
          'Esterno' => ['cerchi_lega','vernice_metallizzata','tetto_nero','barre_portapacchi','gancio_traino','specchi_elettrici','specchi_ripiegabili','luci_led','luci_matrix','fari_fendinebbia'],
          'Motore/Telaio' => ['start_stop','recupero_energia','paddleshift','launch_control','sospensioni_adattive','4x4','differenziale_sportivo','freni_sportivi'],
        ];
        $savedFeatures = old('features', isset($vehicle) ? ($vehicle->features ?? []) : []);
        @endphp
        @foreach($allFeatures as $gruppo => $items)
        <div style="margin-bottom:16px">
          <div style="font-size:11px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">{{ $gruppo }}</div>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            @foreach($items as $item)
            <label style="display:flex;align-items:center;gap:6px;background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:5px 10px;cursor:pointer;font-size:12px;transition:all .15s" onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='var(--border2)'">
              <input type="checkbox" name="features[]" value="{{ $item }}" {{ in_array($item, (array)$savedFeatures) ? 'checked' : '' }} style="accent-color:var(--orange)">
              {{ ucfirst(str_replace('_',' ',$item)) }}
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      {{-- TITOLO E DESCRIZIONE --}}
      <div style="border-top:1px solid var(--border);margin:20px 0;padding-top:20px">
        <div class="card-title">Annuncio</div>
        <div class="form-group">
          <label class="form-label">Titolo annuncio *</label>
          <input type="text" name="title" value="{{ old('title', $vehicle->title ?? '') }}" class="form-input" required placeholder="es. BMW 320d xDrive Sport - Unico proprietario" id="f_title">
        </div>
        <div class="form-group">
          <label class="form-label">Descrizione</label>
          <textarea name="description" class="form-textarea" rows="6" placeholder="Descrizione dettagliata del veicolo...">{{ old('description', $vehicle->description ?? '') }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Note interne (non visibili nell'annuncio)</label>
          <textarea name="internal_notes" class="form-textarea" rows="3">{{ old('internal_notes', $vehicle->internal_notes ?? '') }}</textarea>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('marketplace.vehicles.index') }}" class="btn btn-ghost">Annulla</a>
        <button type="submit" name="action" value="bozza" class="btn btn-ghost">Salva bozza</button>
        <button type="submit" name="action" value="attivo" class="btn btn-primary">Pubblica annuncio</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
async function decodeVin() {
  const vin = document.getElementById('vin_input').value.trim();
  if (vin.length !== 17) {
    showResult('Il VIN deve essere di esattamente 17 caratteri', 'error');
    return;
  }
  const btn = document.getElementById('vin_btn');
  btn.textContent = 'Decodifica in corso...';
  btn.disabled = true;
  try {
    const res = await fetch('/api/vin/decode', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
      body: JSON.stringify({vin})
    });
    const json = await res.json();
    if (json.success) {
      const d = json.data;
      if (d.brand)    setField('f_brand', d.brand);
      if (d.model)    setField('f_model', d.model);
      if (d.year)     setField('f_year', d.year);
      if (d.fuel_type)setSelect('f_fuel', d.fuel_type);
      if (d.transmission) setSelect('f_trans', d.transmission);
      if (d.body_type)setSelect('f_body', d.body_type);
      if (d.doors)    setField('f_doors', d.doors);
      if (d.engine_cc)setField('f_cc', d.engine_cc);
      if (d.power_kw) setField('f_kw', d.power_kw);
      if (d.power_hp) setField('f_hp', d.power_hp);
      document.getElementById('vin_hidden').value = vin;
      // Auto-genera titolo
      if (d.brand && d.model && d.year) {
        const titleField = document.getElementById('f_title');
        if (!titleField.value) titleField.value = d.brand+' '+d.model+' ('+d.year+')';
      }
      showResult('VIN decodificato: '+[d.brand,d.model,d.year].filter(Boolean).join(' ')+' - Dati compilati automaticamente!', 'success');
    } else {
      showResult('Errore: '+(json.error||'VIN non trovato nel database'), 'error');
    }
  } catch(e) {
    showResult('Errore connessione API', 'error');
  }
  btn.textContent = 'Decodifica VIN';
  btn.disabled = false;
}

function setField(id, val) {
  const el = document.getElementById(id);
  if (el && val) el.value = val;
}

function setSelect(id, val) {
  const el = document.getElementById(id);
  if (!el || !val) return;
  for (let opt of el.options) {
    if (opt.value === val || opt.value.includes(val)) { opt.selected = true; break; }
  }
}

function showResult(msg, type) {
  const el = document.getElementById('vin_result');
  el.style.color = type === 'success' ? 'var(--green-text)' : 'var(--red-text)';
  el.textContent = msg;
}

// Auto-calcola CV da kW
document.getElementById('f_kw')?.addEventListener('input', function() {
  const kw = parseFloat(this.value);
  if (kw > 0) document.getElementById('f_hp').value = Math.round(kw * 1.36);
});
document.getElementById('f_hp')?.addEventListener('input', function() {
  const hp = parseFloat(this.value);
  if (hp > 0) document.getElementById('f_kw').value = Math.round(hp / 1.36);
});
</script>
@endpush
@endsection