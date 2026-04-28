@extends('layouts.app')
@section('title', 'Nuovo Preventivo')

@section('topbar-actions')
<a href="{{ route('preventivi.index') }}" class="btn btn-ghost btn-sm">← Preventivi</a>
@endsection

@section('content')
<form method="POST" action="{{ route('preventivi.store') }}" id="form-preventivo">
@csrf

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start">

  {{-- COLONNA SINISTRA --}}
  <div>

    {{-- INTESTAZIONE --}}
    <div class="card">
      <div class="card-title">📋 Intestazione preventivo</div>
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Cliente <span style="color:var(--red)">*</span></label>
          <select name="customer_id" class="form-select" required id="sel-cliente" onchange="onClienteChange(this.value)">
            <option value="">— Seleziona cliente —</option>
            @foreach($clienti as $c)
              <option value="{{ $c->id }}"
                data-vehicles="{{ $c->vehicles->map(fn($v)=>['id'=>$v->id,'plate'=>$v->plate,'brand'=>$v->brand,'model'=>$v->model])->toJson() }}"
                {{ old('customer_id', $clienteId) == $c->id ? 'selected' : '' }}>
                {{ $c->display_name }}
              </option>
            @endforeach
          </select>
          @error('customer_id')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">Veicolo <span style="color:var(--red)">*</span></label>
          <select name="vehicle_id" class="form-select" required id="sel-vehicle">
            <option value="">— Prima seleziona cliente —</option>
          </select>
          @error('vehicle_id')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">Tipo intervento <span style="color:var(--red)">*</span></label>
          <select name="job_type" class="form-select" required>
            @foreach([
              'carrozzeria'=>'🚗 Carrozzeria',
              'meccanica'=>'🔧 Meccanica',
              'elettrauto'=>'⚡ Elettrauto',
              'tagliando'=>'🔩 Tagliando',
              'gommista'=>'🔄 Gommista',
              'detailing'=>'✨ Detailing',
              'perizia'=>'🔍 Perizia',
              'gomme'=>'🔄 Gomme',
              'altro'=>'📁 Altro'
            ] as $v => $l)
              <option value="{{ $v }}" {{ old('job_type') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
          @error('job_type')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label">Valido fino al</label>
          <input type="date" name="valid_until" class="form-input"
            value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Descrizione lavori</label>
        <textarea name="description" class="form-textarea" rows="3"
          placeholder="Descrivi i lavori da eseguire...">{{ old('description') }}</textarea>
      </div>
    </div>

    {{-- VOCI PREVENTIVO --}}
    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
        <div class="card-title" style="margin-bottom:0">📦 Voci preventivo</div>
        <button type="button" class="btn btn-ghost btn-sm" onclick="addRiga()">+ Aggiungi voce</button>
      </div>

      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse" id="tbl-righe">
          <thead>
            <tr style="border-bottom:1px solid var(--border2)">
              <th style="text-align:left;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px;width:120px">Tipo</th>
              <th style="text-align:left;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px">Descrizione</th>
              <th style="text-align:right;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px;width:70px">Qtà</th>
              <th style="text-align:right;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px;width:100px">Prezzo €</th>
              <th style="text-align:right;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px;width:70px">Sc.%</th>
              <th style="text-align:right;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase;padding:6px 8px;width:100px">Totale €</th>
              <th style="width:36px"></th>
            </tr>
          </thead>
          <tbody id="righe-body">
            {{-- righe dinamiche --}}
          </tbody>
        </table>
      </div>
      <div id="no-righe" style="text-align:center;padding:24px;color:var(--text3);font-size:13px">
        Nessuna voce — clicca <strong>+ Aggiungi voce</strong> per iniziare
      </div>
    </div>

    {{-- NOTE --}}
    <div class="card">
      <div class="card-title">📝 Note</div>
      <textarea name="notes" class="form-textarea" rows="3"
        placeholder="Note per il cliente (appariranno sul preventivo)...">{{ old('notes') }}</textarea>
    </div>

  </div>

  {{-- COLONNA DESTRA --}}
  <div>

    {{-- SINISTRO --}}
    <div class="card">
      <div class="card-title">⚡ Sinistro collegato</div>
      <select name="claim_id" class="form-select">
        <option value="">— Nessuno —</option>
        @foreach($sinistri as $s)
          <option value="{{ $s->id }}" {{ old('claim_id') == $s->id ? 'selected' : '' }}>
            #{{ $s->claim_number }} — {{ $s->customer?->display_name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- CALCOLI --}}
    <div class="card">
      <div class="card-title">💰 Calcoli</div>
      <div class="two-col" style="gap:10px;margin-bottom:14px">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Sconto %</label>
          <input type="number" name="discount_percent" id="inp-sconto" class="form-input"
            value="{{ old('discount_percent', 0) }}" min="0" max="100" step="0.01" oninput="ricalcola()">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">IVA %</label>
          <input type="number" name="vat_percent" id="inp-iva" class="form-input"
            value="{{ old('vat_percent', 22) }}" min="0" max="100" step="0.01" oninput="ricalcola()">
        </div>
      </div>
      <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;flex-direction:column;gap:8px">
        <div style="display:flex;justify-content:space-between;font-size:13px">
          <span style="color:var(--text3)">Subtotale</span>
          <span id="lbl-subtotale" style="font-family:var(--mono)">€ 0,00</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px">
          <span style="color:var(--text3)">Sconto</span>
          <span id="lbl-sconto" style="font-family:var(--mono);color:var(--red-text)">- € 0,00</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px">
          <span style="color:var(--text3)">Imponibile</span>
          <span id="lbl-imponibile" style="font-family:var(--mono)">€ 0,00</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px">
          <span style="color:var(--text3)">IVA</span>
          <span id="lbl-iva" style="font-family:var(--mono)">€ 0,00</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;border-top:1px solid var(--border);padding-top:8px;margin-top:4px">
          <span>TOTALE</span>
          <span id="lbl-totale" style="font-family:var(--mono);color:var(--orange)">€ 0,00</span>
        </div>
      </div>
    </div>

    {{-- AZIONI --}}
    <div style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        ✓ Crea preventivo
      </button>
      <a href="{{ route('preventivi.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">Annulla</a>
    </div>

  </div>
</div>
</form>

@push('scripts')
<script>
// ─── DATI VEICOLI ───────────────────────────────────────────────
const clientiVehicles = {};
document.querySelectorAll('#sel-cliente option[data-vehicles]').forEach(opt => {
  try { clientiVehicles[opt.value] = JSON.parse(opt.dataset.vehicles); } catch(e){}
});

function onClienteChange(cid) {
  const sel = document.getElementById('sel-vehicle');
  sel.innerHTML = '<option value="">— Seleziona veicolo —</option>';
  if (!cid || !clientiVehicles[cid]) return;
  clientiVehicles[cid].forEach(v => {
    const o = document.createElement('option');
    o.value = v.id;
    o.textContent = `${v.plate} — ${v.brand} ${v.model}`;
    sel.appendChild(o);
  });
}

// Pre-popola veicoli se cliente già selezionato (old input / param)
(function() {
  const cid = document.getElementById('sel-cliente').value;
  if (cid) {
    onClienteChange(cid);
    const vid = '{{ old('vehicle_id', $veicoloId ?? '') }}';
    if (vid) document.getElementById('sel-vehicle').value = vid;
  }
})();

// ─── RIGHE PREVENTIVO ──────────────────────────────────────────
let rigaIdx = 0;
const tipiVoce = {
  'manodopera': '🔧 Manodopera',
  'ricambio':   '🔩 Ricambio',
  'materiale':  '🪣 Materiale',
  'servizio':   '⚙️ Servizio',
  'altro':      '📁 Altro',
};

function addRiga(data) {
  const i = rigaIdx++;
  const d = data || {};
  document.getElementById('no-righe').style.display = 'none';

  const tr = document.createElement('tr');
  tr.id = `riga-${i}`;
  tr.style.cssText = 'border-bottom:1px solid var(--border)';

  const tipoOpts = Object.entries(tipiVoce).map(([v,l]) =>
    `<option value="${v}" ${(d.item_type||'manodopera')===v?'selected':''}>${l}</option>`
  ).join('');

  tr.innerHTML = `
    <td style="padding:6px 8px">
      <select name="items[${i}][item_type]" class="form-select" style="font-size:12px;padding:5px 8px">
        ${tipoOpts}
      </select>
    </td>
    <td style="padding:6px 8px">
      <input type="text" name="items[${i}][description]" class="form-input" style="font-size:12px;padding:5px 8px"
        placeholder="Descrizione voce..." value="${d.description||''}">
    </td>
    <td style="padding:6px 8px">
      <input type="number" name="items[${i}][quantity]" class="form-input riga-qta" style="font-size:12px;padding:5px 8px;text-align:right"
        value="${d.quantity||1}" min="0.01" step="0.01" oninput="aggiornaRiga(${i})">
    </td>
    <td style="padding:6px 8px">
      <input type="number" name="items[${i}][unit_price]" class="form-input riga-prezzo" style="font-size:12px;padding:5px 8px;text-align:right"
        value="${d.unit_price||''}" min="0" step="0.01" placeholder="0,00" oninput="aggiornaRiga(${i})">
    </td>
    <td style="padding:6px 8px">
      <input type="number" name="items[${i}][discount_percent]" class="form-input riga-sc" style="font-size:12px;padding:5px 8px;text-align:right"
        value="${d.discount_percent||0}" min="0" max="100" step="0.01" oninput="aggiornaRiga(${i})">
    </td>
    <td style="padding:6px 8px;text-align:right">
      <span id="tot-${i}" style="font-family:var(--mono);font-size:13px;font-weight:600;color:var(--green-text)">€ 0,00</span>
    </td>
    <td style="padding:6px 4px;text-align:center">
      <button type="button" onclick="removeRiga(${i})"
        style="background:none;border:none;color:var(--red);cursor:pointer;font-size:16px;padding:2px 6px;line-height:1"
        title="Rimuovi">×</button>
    </td>
  `;
  document.getElementById('righe-body').appendChild(tr);
  aggiornaRiga(i);
}

function removeRiga(i) {
  const tr = document.getElementById(`riga-${i}`);
  if (tr) tr.remove();
  checkNoRighe();
  ricalcola();
}

function checkNoRighe() {
  const empty = document.getElementById('righe-body').children.length === 0;
  document.getElementById('no-righe').style.display = empty ? '' : 'none';
}

function aggiornaRiga(i) {
  const tr = document.getElementById(`riga-${i}`);
  if (!tr) return;
  const qty  = parseFloat(tr.querySelector('.riga-qta').value)    || 0;
  const prc  = parseFloat(tr.querySelector('.riga-prezzo').value)  || 0;
  const sc   = parseFloat(tr.querySelector('.riga-sc').value)      || 0;
  const gross = qty * prc;
  const tot  = gross - (gross * sc / 100);
  const lbl  = document.getElementById(`tot-${i}`);
  if (lbl) lbl.textContent = '€ ' + tot.toFixed(2).replace('.', ',');
  ricalcola();
}

function ricalcola() {
  let sub = 0;
  document.querySelectorAll('[id^="tot-"]').forEach(el => {
    sub += parseFloat(el.textContent.replace('€ ','').replace(',','.')) || 0;
  });
  const sc  = parseFloat(document.getElementById('inp-sconto').value) || 0;
  const iva = parseFloat(document.getElementById('inp-iva').value)    || 0;
  const disc     = sub * sc / 100;
  const taxable  = sub - disc;
  const vatAmt   = taxable * iva / 100;
  const total    = taxable + vatAmt;

  const fmt = n => '€ ' + n.toFixed(2).replace('.', ',');
  document.getElementById('lbl-subtotale').textContent   = fmt(sub);
  document.getElementById('lbl-sconto').textContent      = '- ' + fmt(disc);
  document.getElementById('lbl-imponibile').textContent  = fmt(taxable);
  document.getElementById('lbl-iva').textContent         = fmt(vatAmt);
  document.getElementById('lbl-totale').textContent      = fmt(total);
}

// Una riga di default
addRiga();
</script>
@endpush
@endsection
