@extends('layouts.app')
@section('title', isset($vehicle) ? 'Modifica Veicolo' : 'Nuovo Veicolo in Vendita')
@section('content')
<div style="max-width:900px">
  <div style="margin-bottom:16px"><a href="{{ route('marketplace.vehicles.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&lt;- Veicoli in vendita</a></div>

  {{-- VIN DECODER --}}
  <div class="card" style="border:1px solid var(--orange-border);background:var(--orange-bg)">
    <div class="card-title" style="color:var(--orange)">🤖 Decodifica VIN con Claude AI</div>
    <div style="display:flex;gap:10px;align-items:flex-end">
      <div class="form-group" style="flex:1;margin-bottom:0">
        <label class="form-label">Numero telaio VIN (17 caratteri)</label>
        <input type="text" id="vin_input" class="form-input" placeholder="es. WF0DXXGAKDFC01137" maxlength="17" style="font-family:var(--mono);letter-spacing:.1em;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
      </div>
      <button type="button" onclick="decodeVin()" class="btn btn-primary" id="vin_btn" style="white-space:nowrap">🔍 Decodifica VIN</button>
    </div>
    <div id="vin_result" style="margin-top:10px;font-size:13px;line-height:1.6"></div>
    <div style="margin-top:8px;font-size:11px;color:var(--text3)">Compila automaticamente: marca, modello, versione, anno, carburante, cambio, carrozzeria, potenza, optional e descrizione.</div>
  </div>

  {{-- FOTO VEICOLO --}}
  <div class="card">
    <div class="card-title">Foto veicolo</div>
    @if(!empty($vehicle->id))
    <div id="foto-esistenti" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
      @forelse($vehicle->getMedia('sale_photos') as $photo)
      <div id="foto-{{ $photo->id }}" style="position:relative;width:80px;height:60px;border-radius:6px;overflow:hidden;border:2px solid var(--border2)">
        <img src="{{ $photo->getUrl('thumb') }}" style="width:100%;height:100%;object-fit:cover">
        <button type="button" onclick="eliminaFoto({{ $photo->id }})" style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:3px;width:18px;height:18px;cursor:pointer;font-size:12px;line-height:1">&times;</button>
      </div>
      @empty
      <div style="color:var(--text3);font-size:12px">Nessuna foto caricata</div>
      @endforelse
    </div>
    @endif
    <div id="drop-zone" onclick="document.getElementById('foto-input').click()" ondragover="event.preventDefault();this.style.borderColor='var(--orange)';this.style.background='var(--orange-bg)'" ondragleave="this.style.borderColor='var(--border2)';this.style.background='var(--bg3)'" ondrop="handleDrop(event)" style="border:2px dashed var(--border2);border-radius:8px;padding:20px;cursor:pointer;background:var(--bg3);transition:.15s;display:flex;align-items:center;justify-content:center;gap:12px">
      <svg width="28" height="28" fill="none" stroke="var(--text3)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      <div>
        <div style="font-size:13px;color:var(--text2);font-weight:500">Clicca o trascina le foto qui</div>
        <div style="font-size:11px;color:var(--text3)">JPG PNG WEBP - max 10MB - selezione multipla supportata</div>
      </div>
      <input type="file" id="foto-input" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple style="display:none" onchange="handleFiles(this.files)">
    </div>
    <div id="preview-container" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px"></div>
    <div id="upload-progress" style="display:none;margin-top:8px">
      <div style="height:4px;background:var(--bg3);border-radius:2px;overflow:hidden">
        <div id="progress-bar" style="height:100%;background:var(--orange);width:0%;transition:width .3s"></div>
      </div>
      <div id="progress-text" style="font-size:11px;color:var(--text3);margin-top:4px">Caricamento...</div>
    </div>
  </div>

  {{-- FORM PRINCIPALE --}}
  <div class="card">
    <div class="card-title">{{ isset($vehicle) ? 'Modifica veicolo' : 'Nuovo veicolo in vendita' }}</div>
    <form action="{{ isset($vehicle) && $vehicle->id ? route('marketplace.vehicles.update',$vehicle) : route('marketplace.vehicles.store') }}" method="POST" enctype="multipart/form-data">
      @csrf @if(isset($vehicle) && $vehicle->id) @method('PUT') @endif
      @if($errors->any())<div class="alert alert-red">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

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
          <label class="form-label">
            Targa
            <span style="margin-left:10px;font-weight:400;font-size:11px;color:var(--text3)">
              <label style="display:inline-flex;align-items:center;gap:5px;cursor:pointer">
                <input type="hidden" name="plate_visible" value="0">
                <input type="checkbox" name="plate_visible" value="1"
                  {{ old('plate_visible', $vehicle->plate_visible ?? true) ? 'checked' : '' }}
                  style="width:13px;height:13px;accent-color:var(--orange)">
                Mostra nel pubblico
              </label>
            </span>
          </label>
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
          <input type="text" name="color" id="f_color" value="{{ old('color', $vehicle->color ?? '') }}" class="form-input" placeholder="es. Nero Metallizzato">
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

        {{-- VISIBILITÀ PREZZO --}}
        <div style="margin-top:16px;padding:16px;background:var(--bg3);border-radius:10px;border:1px solid var(--border2)">
          <div style="font-size:12px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px">Visibilità prezzo nel pubblico</div>
          <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600">
              <input type="hidden" name="price_visible" value="0">
              <input type="checkbox" name="price_visible" value="1" id="price_visible_chk"
                {{ old('price_visible', $vehicle->price_visible ?? true) ? 'checked' : '' }}
                style="width:16px;height:16px;accent-color:var(--orange)"
                onchange="togglePriceLabel(this.checked)">
              Mostra prezzo
            </label>
            <div id="price_label_wrap" style="flex:1;min-width:220px;display:{{ old('price_visible', $vehicle->price_visible ?? true) ? 'block' : 'none' }}">
              <label class="form-label" style="font-size:11px">Testo alternativo (lascia vuoto per mostrare il numero)</label>
              <input type="text" name="price_label" id="price_label_input" class="form-input"
                placeholder='es. "Chiedi prezzo", "Trattabile", "Chiamaci"'
                maxlength="60"
                value="{{ old('price_label', $vehicle->price_label ?? '') }}"
                style="max-width:360px">
              <div style="font-size:11px;color:var(--text3);margin-top:4px">Se compilato, mostra questo testo al posto del numero.</div>
            </div>
          </div>
          <div style="margin-top:12px;font-size:12px;color:var(--text3)">
            Anteprima pubblica:
            <span id="price_preview" style="font-weight:700;color:var(--orange);margin-left:6px"></span>
            <span id="price_hidden_preview" style="font-weight:700;color:var(--text3);margin-left:6px;display:none">— (prezzo nascosto)</span>
          </div>
        </div>

        {{-- BADGE ETICHETTA --}}
        <div style="margin-top:20px">
          <label class="form-label" style="margin-bottom:10px;display:block">Etichetta badge visibile nell'annuncio</label>
          @php $currentBadge = old('badge_label', $vehicle->badge_label ?? ''); @endphp
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px">
            @foreach(\App\Models\SaleVehicle::BADGE_PRESETS as $preset)
            <button type="button" onclick="setBadge('{{ $preset }}', this)" class="badge-btn"
              style="padding:5px 16px;border-radius:20px;border:1px solid var(--orange);
                     background:{{ $currentBadge === $preset ? 'var(--orange)' : 'transparent' }};
                     color:{{ $currentBadge === $preset ? '#000' : 'var(--orange)' }};
                     font-size:12px;font-weight:600;cursor:pointer;transition:all .15s">
              {{ $preset }}
            </button>
            @endforeach
            <button type="button" onclick="setBadge('__custom__', this)" class="badge-btn"
              style="padding:5px 16px;border-radius:20px;border:1px solid var(--border2);
                     background:{{ !in_array($currentBadge, array_merge([''], \App\Models\SaleVehicle::BADGE_PRESETS)) ? 'var(--orange)' : 'transparent' }};
                     color:{{ !in_array($currentBadge, array_merge([''], \App\Models\SaleVehicle::BADGE_PRESETS)) ? '#000' : 'var(--text2)' }};
                     font-size:12px;cursor:pointer;transition:all .15s">
              ✏ Testo libero
            </button>
            <button type="button" onclick="setBadge('', this)" id="badge-none-btn"
              style="padding:5px 16px;border-radius:20px;border:1px solid var(--border2);
                     background:{{ $currentBadge === '' ? '#333' : 'transparent' }};
                     color:var(--text3);font-size:12px;cursor:pointer;transition:all .15s">
              ✕ Nessuno
            </button>
          </div>
          <div id="badge-custom-wrap" style="display:{{ !in_array($currentBadge, array_merge([''], \App\Models\SaleVehicle::BADGE_PRESETS)) ? 'block' : 'none' }}">
            <input type="text" id="badge_custom_input" class="form-input"
              placeholder="Scrivi testo badge..." maxlength="40" style="max-width:300px"
              value="{{ !in_array($currentBadge, array_merge([''], \App\Models\SaleVehicle::BADGE_PRESETS)) ? $currentBadge : '' }}"
              oninput="document.getElementById('badge_label_input').value=this.value">
          </div>
          <input type="hidden" name="badge_label" id="badge_label_input" value="{{ $currentBadge }}">
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
          <label class="form-label">Descrizione <span style="font-weight:400;color:var(--text3);font-size:11px">(compilata automaticamente dal VIN decoder)</span></label>
          <textarea name="description" class="form-textarea" rows="6" placeholder="Inserisci il VIN e premi Decodifica per generare automaticamente la descrizione...">{{ old('description', $vehicle->description ?? '') }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Note interne (non visibili nell'annuncio)</label>
          <textarea name="internal_notes" class="form-textarea" rows="3">{{ old('internal_notes', $vehicle->internal_notes ?? '') }}</textarea>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        @if(isset($vehicle) && $vehicle->id)
          <button type="submit" name="action" value="{{ $vehicle->status }}" class="btn btn-ghost" style="border-color:var(--orange);color:var(--orange)">
            💾 Salva modifiche
          </button>
        @endif
        <button type="submit" name="action" value="bozza" class="btn btn-ghost">Salva bozza</button>
        <button type="submit" name="action" value="attivo" class="btn btn-primary">Pubblica annuncio</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
// ===== BADGE PREZZO =====
function setBadge(val, btn) {
  document.querySelectorAll('.badge-btn').forEach(b => {
    b.style.background = 'transparent';
    b.style.color = b.style.borderColor === 'rgb(229, 231, 235)' ? 'var(--text2)' : 'var(--orange)';
  });
  document.getElementById('badge-none-btn').style.background = 'transparent';
  const customWrap = document.getElementById('badge-custom-wrap');
  const hidden = document.getElementById('badge_label_input');
  if (val === '__custom__') {
    customWrap.style.display = 'block';
    if (btn) { btn.style.background = 'var(--orange)'; btn.style.color = '#000'; }
    hidden.value = document.getElementById('badge_custom_input').value;
  } else if (val === '') {
    customWrap.style.display = 'none';
    document.getElementById('badge-none-btn').style.background = '#333';
    hidden.value = '';
  } else {
    customWrap.style.display = 'none';
    if (btn) { btn.style.background = 'var(--orange)'; btn.style.color = '#000'; }
    hidden.value = val;
  }
}

// ===== VISIBILITÀ PREZZO =====
function togglePriceLabel(visible) {
  document.getElementById('price_label_wrap').style.display = visible ? 'block' : 'none';
  updatePricePreview();
}

function updatePricePreview() {
  const visible = document.getElementById('price_visible_chk').checked;
  const label = document.getElementById('price_label_input').value.trim();
  const askingEl = document.querySelector('input[name="asking_price"]');
  const asking = askingEl ? parseFloat(askingEl.value) : 0;
  const preview = document.getElementById('price_preview');
  const hiddenPreview = document.getElementById('price_hidden_preview');
  if (!visible) {
    preview.style.display = 'none';
    hiddenPreview.style.display = 'inline';
    return;
  }
  hiddenPreview.style.display = 'none';
  preview.style.display = 'inline';
  if (label) {
    preview.textContent = label;
    preview.style.color = 'var(--text2)';
    preview.style.fontStyle = 'italic';
  } else if (asking > 0) {
    preview.textContent = '€ ' + asking.toLocaleString('it-IT', {maximumFractionDigits:0});
    preview.style.color = 'var(--orange)';
    preview.style.fontStyle = 'normal';
  } else {
    preview.textContent = '—';
    preview.style.color = 'var(--text3)';
  }
}
document.getElementById('price_label_input')?.addEventListener('input', updatePricePreview);
document.querySelector('input[name="asking_price"]')?.addEventListener('input', updatePricePreview);
document.getElementById('price_visible_chk')?.addEventListener('change', updatePricePreview);
document.addEventListener('DOMContentLoaded', updatePricePreview);

// ===== VIN DECODER con Claude AI =====
async function decodeVin() {
  const vin = document.getElementById('vin_input').value.trim();
  if (vin.length !== 17) { showVinResult('⚠️ Il VIN deve essere di esattamente 17 caratteri', 'error'); return; }
  const btn = document.getElementById('vin_btn');
  btn.textContent = '⏳ Analisi in corso...';
  btn.disabled = true;
  showVinResult('🤖 Claude AI sta analizzando il VIN... attendere ~5 secondi', 'info');

  try {
    const res = await fetch('/api/vin/decode', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
      body: JSON.stringify({vin})
    });
    const json = await res.json();

    if (json.success) {
      const d = json.data;
      const source = json.source || 'unknown';

      // ── Dati base ──
      if (d.brand)        setField('f_brand', d.brand);
      if (d.model)        setField('f_model', d.model);
      if (d.version)      setField('f_version', d.version);
      if (d.year)         setField('f_year', d.year);
      if (d.fuel_type)    setSelect('f_fuel', d.fuel_type);
      if (d.transmission) setSelect('f_trans', d.transmission);
      if (d.body_type)    setSelect('f_body', d.body_type);
      if (d.doors)        setField('f_doors', d.doors);
      if (d.seats)        setField('f_seats', d.seats);
      if (d.engine_cc)    setField('f_cc', d.engine_cc);
      if (d.power_kw)     setField('f_kw', d.power_kw);
      if (d.power_hp)     setField('f_hp', d.power_hp);
      if (d.color)        setField('f_color', d.color);

      // ── VIN nascosto ──
      document.getElementById('vin_hidden').value = vin;

      // ── Titolo annuncio ──
      const tf = document.getElementById('f_title');
      if (tf && !tf.value && d.brand && d.model) {
        const parts = [d.brand, d.model, d.version, d.year ? '('+d.year+')' : ''].filter(Boolean);
        tf.value = parts.join(' ');
      }

      // ── Descrizione ──
      const descField = document.querySelector('textarea[name="description"]');
      if (descField && !descField.value && d.description) {
        descField.value = d.description;
      }

      // ── Features/Optional ──
      if (d.features && d.features.length > 0) {
        d.features.forEach(function(feat) {
          const cb = document.querySelector('input[name="features[]"][value="'+feat+'"]');
          if (cb) cb.checked = true;
        });
      }

      // ── Messaggio risultato ──
      const vehicleName = [d.brand, d.model, d.version, d.year].filter(Boolean).join(' ');
      const confidenceLabel = {'alta':'🟢 Alta','media':'🟡 Media','bassa':'🔴 Bassa'}[d.confidence] || '';
      const sourceLabel = source === 'claude' ? '🤖 Claude AI' : '🏛️ NHTSA';
      const notesHtml = d.notes ? '<br><small style="opacity:.8">⚠️ '+d.notes+'</small>' : '';
      const featCount = d.features && d.features.length ? ' — '+d.features.length+' optional rilevati' : '';

      showVinResult(
        '✅ <strong>'+vehicleName+'</strong> — Fonte: '+sourceLabel+' — Confidenza: '+confidenceLabel+featCount+notesHtml,
        'success'
      );

    } else {
      showVinResult('❌ ' + (json.error || 'VIN non trovato nel database'), 'error');
    }
  } catch(e) {
    showVinResult('❌ Errore connessione: '+e.message, 'error');
  }

  btn.textContent = '🔍 Decodifica VIN';
  btn.disabled = false;
}

function setField(id, val) {
  const el = document.getElementById(id);
  if (el && val !== null && val !== undefined && val !== '') el.value = val;
}
function setSelect(id, val) {
  const el = document.getElementById(id);
  if (!el || !val) return;
  const valLow = val.toLowerCase();
  for (let o of el.options) {
    if (o.value === val || o.value === valLow || o.value.includes(valLow) || valLow.includes(o.value)) {
      o.selected = true; break;
    }
  }
}
function showVinResult(msg, type) {
  const el = document.getElementById('vin_result');
  el.style.color = type === 'success' ? 'var(--green-text)' : type === 'info' ? 'var(--orange)' : 'var(--red-text)';
  el.innerHTML = msg;
}

// ===== AUTO-CALCOLA CV/KW =====
document.getElementById('f_kw')?.addEventListener('input', function() {
  const kw=parseFloat(this.value); if(kw>0) document.getElementById('f_hp').value=Math.round(kw*1.36);
});
document.getElementById('f_hp')?.addEventListener('input', function() {
  const hp=parseFloat(this.value); if(hp>0) document.getElementById('f_kw').value=Math.round(hp/1.36);
});

// ===== GESTIONE FOTO =====
var _vid = {{ !empty($vehicle->id) ? $vehicle->id : 'null' }};
var _uurl = _vid ? '/marketplace/vehicles/'+_vid+'/foto' : null;
var _csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
var _pend = [];

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('drop-zone').style.borderColor='var(--border2)';
  document.getElementById('drop-zone').style.background='var(--bg3)';
  handleFiles(e.dataTransfer.files);
}
function handleFiles(files) {
  var arr = Array.from(files);
  if (_vid && _uurl) { uploadAjax(arr); }
  else { arr.forEach(function(f) { var i=_pend.length; _pend.push(f); showPrev(f,i); }); }
}
function showPrev(file, i) {
  var r = new FileReader();
  r.onload = function(e) {
    var d = document.createElement('div');
    d.id = 'prev-'+i;
    d.style.cssText = 'position:relative;width:80px;height:60px;border-radius:6px;overflow:hidden;border:2px solid var(--orange)';
    d.innerHTML = '<img src="'+e.target.result+'" style="width:100%;height:100%;object-fit:cover">'
      +'<button type="button" onclick="rmPrev('+i+')" style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:3px;width:18px;height:18px;cursor:pointer;font-size:12px">&times;</button>';
    document.getElementById('preview-container').appendChild(d);
  };
  r.readAsDataURL(file);
}
function rmPrev(i) { _pend[i]=null; var el=document.getElementById('prev-'+i); if(el) el.remove(); }

async function uploadAjax(files) {
  var p=document.getElementById('upload-progress'), b=document.getElementById('progress-bar'), t=document.getElementById('progress-text');
  p.style.display='block';
  for (var i=0; i<files.length; i++) {
    var fd = new FormData();
    fd.append('photo', files[i]);
    fd.append('_token', _csrf);
    try {
      var res = await fetch(_uurl, {method:'POST', body:fd});
      var data = await res.json();
      b.style.width = ((i+1)/files.length*100)+'%';
      t.textContent = 'Caricato '+(i+1)+' di '+files.length;
      var c = document.getElementById('foto-esistenti');
      if (c) {
        var empty = c.querySelector('div[style*="color:var(--text3)"]');
        if (empty) empty.remove();
        var d = document.createElement('div');
        d.id = 'foto-'+data.id;
        d.style.cssText = 'position:relative;width:80px;height:60px;border-radius:6px;overflow:hidden;border:2px solid var(--orange)';
        d.innerHTML = '<img src="'+data.thumb_url+'" style="width:100%;height:100%;object-fit:cover">'
          +'<button type="button" onclick="eliminaFoto('+data.id+')" style="position:absolute;top:2px;right:2px;background:rgba(220,38,38,.9);color:#fff;border:none;border-radius:3px;width:18px;height:18px;cursor:pointer;font-size:12px">&times;</button>';
        c.appendChild(d);
      }
    } catch(err) { t.textContent = 'Errore: '+files[i].name; }
  }
  setTimeout(function(){ p.style.display='none'; b.style.width='0%'; }, 2000);
}

async function eliminaFoto(mid) {
  if (!confirm('Eliminare questa foto?')) return;
  var res = await fetch('/marketplace/vehicles/'+_vid+'/foto/'+mid, {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN':_csrf, 'Accept':'application/json'}
  });
  if (res.ok) { var el=document.getElementById('foto-'+mid); if(el) el.remove(); }
}
</script>
@endpush
@endsection