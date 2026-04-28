@extends('layouts.app')
@section('title', 'Nuovo Fascicolo')

@section('topbar-actions')
<a href="{{ route('fascicoli.index') }}" class="btn btn-ghost btn-sm">← Torna ai fascicoli</a>
@endsection

@section('content')

<form method="POST" action="{{ route('fascicoli.store') }}">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

  {{-- COLONNA SINISTRA --}}
  <div>
    <div class="card">
      <div class="card-title">Dati fascicolo</div>

      <div class="form-group">
        <label class="form-label">Cliente *</label>
        <select name="cliente_id" class="form-select" required id="sel-cliente">
          <option value="">Seleziona cliente...</option>
          @foreach($clienti as $c)
            <option value="{{ $c->id }}"
              data-tipo="{{ $c->tipo_soggetto }}"
              {{ (old('cliente_id', $clienteId) == $c->id) ? 'selected' : '' }}>
              {{ $c->display_name ?? ($c->nome . ' ' . $c->cognome) }}
              @if($c->tipo_soggetto === 'azienda') (Azienda) @endif
            </option>
          @endforeach
        </select>
        @error('cliente_id')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
      </div>

      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Tipo pratica *</label>
          <select name="tipo_pratica" class="form-select" required id="sel-tipo" onchange="aggiornaTipo(this.value)">
            @foreach($tipiPratica as $key => $label)
              <option value="{{ $key }}" {{ old('tipo_pratica', 'noleggio') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Stato</label>
          <select name="stato" class="form-select">
            @foreach($stati as $key => $info)
              <option value="{{ $key }}" {{ old('stato', 'bozza') == $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">
          Titolo / riferimento interno
          <span style="color:var(--text3);font-weight:400;font-size:10px"> — si compila automaticamente dalla pratica selezionata</span>
        </label>
        <input type="text" name="titolo" class="form-input" id="input-titolo"
          placeholder="Compilato automaticamente oppure inserisci manualmente..."
          value="{{ old('titolo') }}">
      </div>

      <div class="form-group">
        <label class="form-label">Note operative</label>
        <textarea name="note" class="form-textarea" placeholder="Note interne per gli operatori...">{{ old('note') }}</textarea>
      </div>
    </div>

    {{-- SEZIONE VEICOLO --}}
    <div class="card" id="card-veicolo">
      <div class="card-title">🚗 Veicolo collegato</div>

      {{-- Veicolo flotta (noleggio, auto sostitutiva) --}}
      <div id="box-fleet" class="form-group">
        <label class="form-label">Veicolo flotta (noleggio)</label>
        <select name="fleet_vehicle_id" class="form-select" id="sel-fleet" onchange="onVeicoloFlotta(this)">
          <option value="">Nessuno / inserisci manuale</option>
          @foreach($fleetVehicles as $v)
            <option value="{{ $v->id }}"
              data-label="{{ $v->brand }} {{ $v->model }} - {{ $v->plate }}"
              data-titolo="Noleggio {{ $v->brand }} {{ $v->model }} ({{ $v->plate }})"
              {{ old('fleet_vehicle_id') == $v->id ? 'selected' : '' }}>
              {{ $v->brand }} {{ $v->model }} — {{ $v->plate }}
              @if($v->status !== 'disponibile') ({{ $v->status }}) @endif
            </option>
          @endforeach
        </select>
      </div>

      {{-- Veicolo vendita --}}
      <div id="box-sale" class="form-group" style="display:none">
        <label class="form-label">Veicolo in vendita</label>
        <select name="sale_vehicle_id" class="form-select" id="sel-sale" onchange="onVeicoloVendita(this)">
          <option value="">Nessuno / inserisci manuale</option>
          @foreach($saleVehicles as $v)
            <option value="{{ $v->id }}"
              data-label="{{ $v->brand }} {{ $v->model }} {{ $v->version }} - {{ $v->plate }}"
              data-titolo="Vendita {{ $v->brand }} {{ $v->model }} {{ $v->version }} ({{ $v->plate }}){{ $v->asking_price ? ' — €'.number_format($v->asking_price,0,',','.') : '' }}"
              {{ old('sale_vehicle_id') == $v->id ? 'selected' : '' }}>
              {{ $v->brand }} {{ $v->model }} {{ $v->version }} — {{ $v->plate }} — €{{ number_format($v->asking_price,0,',','.') }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Riferimento a sinistro --}}
      <div id="box-sinistro" class="form-group" style="display:none">
        <label class="form-label">Sinistro collegato</label>
        <select name="pratica_id" class="form-select" onchange="onPraticaSelezionata(this, 'App\\\\Models\\\\Claim')">
          <option value="">Nessuno</option>
          @foreach($sinistri as $s)
            <option value="{{ $s->id }}"
              data-label="Sinistro #{{ $s->claim_number }} — {{ $s->counterpart_plate }}"
              data-titolo="Sinistro #{{ $s->claim_number }}{{ $s->counterpart_plate ? ' — Controparte '.$s->counterpart_plate : '' }}{{ $s->event_description ? ' — '.Str::limit($s->event_description,50) : '' }}"
              {{ old('pratica_id') == $s->id ? 'selected' : '' }}>
              #{{ $s->claim_number }} — {{ Str::limit($s->event_description ?? '',40) }} ({{ $s->counterpart_plate }})
            </option>
          @endforeach
        </select>
        <input type="hidden" name="pratica_type" id="pratica_type_sinistro" value="">
      </div>

      {{-- Riferimento a lavorazione --}}
      <div id="box-lavorazione" class="form-group" style="display:none">
        <label class="form-label">Lavorazione collegata</label>
        <select name="pratica_id" class="form-select" onchange="onPraticaSelezionata(this, 'App\\\\Models\\\\WorkOrder')">
          <option value="">Nessuna</option>
          @foreach($lavorazioni as $l)
            <option value="{{ $l->id }}"
              data-label="Lavorazione #{{ $l->job_number }} — {{ $l->vehicle?->plate }}"
              data-titolo="Lavorazione #{{ $l->job_number }}{{ $l->vehicle?->plate ? ' — '.$l->vehicle->plate : '' }}{{ $l->vehicle?->brand ? ' '.$l->vehicle->brand.' '.$l->vehicle->model : '' }}{{ $l->description ? ' — '.Str::limit($l->description,50) : '' }}"
              {{ old('pratica_id') == $l->id ? 'selected' : '' }}>
              #{{ $l->job_number }} — {{ Str::limit($l->description,40) }} ({{ $l->vehicle?->plate }})
            </option>
          @endforeach
        </select>
        <input type="hidden" name="pratica_type" id="pratica_type_lavorazione" value="">
      </div>

      {{-- Riferimento a noleggio --}}
      <div id="box-noleggio-ref" class="form-group" style="display:none">
        <label class="form-label">Noleggio collegato</label>
        <select name="pratica_id" class="form-select" onchange="onPraticaSelezionata(this, 'App\\\\Models\\\\Rental')">
          <option value="">Nessuno</option>
          @foreach($noleggi as $n)
            <option value="{{ $n->id }}"
              data-label="Noleggio #{{ $n->id }} — {{ $n->vehicle?->brand }} {{ $n->vehicle?->plate }}"
              data-titolo="Noleggio #{{ $n->id }}{{ $n->vehicle?->plate ? ' — '.$n->vehicle->brand.' '.$n->vehicle->model.' ('.$n->vehicle->plate.')' : '' }}{{ $n->start_date ? ' dal '.$n->start_date->format('d/m/Y') : '' }}{{ $n->end_date ? ' al '.$n->end_date->format('d/m/Y') : '' }}"
              {{ old('pratica_id') == $n->id ? 'selected' : '' }}>
              #{{ $n->id }} — {{ $n->vehicle?->brand }} {{ $n->vehicle?->model }} ({{ $n->vehicle?->plate }})
              {{ $n->start_date?->format('d/m/Y') }} → {{ $n->end_date?->format('d/m/Y') }}
            </option>
          @endforeach
        </select>
        <input type="hidden" name="pratica_type" id="pratica_type_noleggio" value="">
      </div>

      {{-- Campo manuale targa/veicolo --}}
      <div class="form-group">
        <label class="form-label">Targa / riferimento veicolo manuale
          <span style="color:var(--text3);font-weight:400">(compilato automaticamente se selezioni sopra)</span>
        </label>
        <input type="text" name="riferimento_veicolo" id="input-rifveicolo" class="form-input"
          placeholder="Es. AB123CD — Fiat 500" value="{{ old('riferimento_veicolo') }}">
      </div>

      {{-- Date periodo --}}
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Data inizio</label>
          <input type="date" name="data_inizio" class="form-input" value="{{ old('data_inizio') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Data fine</label>
          <input type="date" name="data_fine" class="form-input" value="{{ old('data_fine') }}">
        </div>
      </div>
    </div>
  </div>

  {{-- COLONNA DESTRA --}}
  <div>
    <div class="card">
      <div class="card-title">Assegnazione</div>
      <div class="form-group">
        <label class="form-label">Operatore assegnato</label>
        <select name="operatore_id" class="form-select">
          <option value="">Nessuno</option>
          @foreach(\App\Models\User::where('tenant_id', auth()->user()->tenant_id)->get() as $u)
            <option value="{{ $u->id }}" {{ old('operatore_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Riepilogo collegamento --}}
    <div class="card" id="card-riepilogo" style="display:none;background:var(--bg3);border-color:var(--border2)">
      <div style="font-size:11px;font-weight:700;color:var(--text3);margin-bottom:8px">COLLEGAMENTO SELEZIONATO</div>
      <div id="riepilogo-testo" style="font-size:13px;color:var(--text)"></div>
    </div>

    <div class="card" style="background:var(--orange-bg);border-color:var(--orange-border)">
      <div style="font-size:12px;color:var(--text2);line-height:1.7">
        <strong style="color:var(--orange);font-size:13px">📋 Documenti automatici</strong><br><br>
        Dopo la creazione, il sistema caricherà automaticamente i documenti richiesti dal <strong>catalogo</strong> in base al tipo di pratica e al tipo cliente.
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        ✓ Crea fascicolo
      </button>
      <a href="{{ route('fascicoli.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">Annulla</a>
    </div>
  </div>

</div>
</form>

<script>
// Mappa tipo pratica → quali box mostrare
const tipoConfig = {
  'noleggio':          { fleet: true,  sale: false, sinistro: false, lavorazione: false, noleggioRef: true  },
  'sinistro':          { fleet: false, sale: false, sinistro: true,  lavorazione: false, noleggioRef: false },
  'riparazione':       { fleet: false, sale: false, sinistro: false, lavorazione: true,  noleggioRef: false },
  'perizia':           { fleet: true,  sale: true,  sinistro: false, lavorazione: false, noleggioRef: false },
  'auto_sostitutiva':  { fleet: true,  sale: false, sinistro: true,  lavorazione: false, noleggioRef: false },
  'lesioni_personali': { fleet: false, sale: false, sinistro: true,  lavorazione: false, noleggioRef: false },
  'vendita_auto':      { fleet: false, sale: true,  sinistro: false, lavorazione: false, noleggioRef: false },
  'altro':             { fleet: true,  sale: true,  sinistro: true,  lavorazione: true,  noleggioRef: false },
};

// Tiene traccia se il titolo è stato auto-compilato (per non sovrascrivere quello manuale)
let titoloAutoCompilato = false;

function setTitoloAuto(testo) {
  const inp = document.getElementById('input-titolo');
  if (!inp.value || titoloAutoCompilato) {
    inp.value = testo;
    titoloAutoCompilato = true;
  }
}

// Se l'utente digita manualmente, disabilita auto-fill
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('input-titolo').addEventListener('input', function() {
    titoloAutoCompilato = false;
  });
});

function aggiornaTipo(tipo) {
  const cfg = tipoConfig[tipo] || { fleet: true, sale: false, sinistro: false, lavorazione: false, noleggioRef: false };
  document.getElementById('box-fleet').style.display       = cfg.fleet       ? '' : 'none';
  document.getElementById('box-sale').style.display        = cfg.sale        ? '' : 'none';
  document.getElementById('box-sinistro').style.display    = cfg.sinistro    ? '' : 'none';
  document.getElementById('box-lavorazione').style.display = cfg.lavorazione ? '' : 'none';
  document.getElementById('box-noleggio-ref').style.display= cfg.noleggioRef ? '' : 'none';

  // Reset selezioni nascoste
  if (!cfg.fleet)       document.getElementById('sel-fleet').value = '';
  if (!cfg.sale)        document.getElementById('sel-sale').value  = '';

  // Suggerisci titolo in base al tipo pratica se nessuna pratica ancora selezionata
  const titoliDefault = {
    'noleggio':          'Noleggio veicolo',
    'sinistro':          'Pratica sinistro',
    'riparazione':       'Riparazione veicolo',
    'perizia':           'Perizia veicolo',
    'auto_sostitutiva':  'Auto sostitutiva',
    'lesioni_personali': 'Lesioni personali',
    'vendita_auto':      'Vendita auto',
    'altro':             '',
  };
  if (titoliDefault[tipo]) setTitoloAuto(titoliDefault[tipo]);
}

function onVeicoloFlotta(sel) {
  const opt = sel.options[sel.selectedIndex];
  const label  = opt.dataset.label  || '';
  const titolo = opt.dataset.titolo || '';
  if (label) {
    document.getElementById('input-rifveicolo').value = label;
    mostraRiepilogo('🚗 Flotta: ' + label);
    if (titolo) setTitoloAuto(titolo);
  } else {
    nascondiRiepilogo();
  }
  document.getElementById('sel-sale').value = '';
}

function onVeicoloVendita(sel) {
  const opt = sel.options[sel.selectedIndex];
  const label  = opt.dataset.label  || '';
  const titolo = opt.dataset.titolo || '';
  if (label) {
    document.getElementById('input-rifveicolo').value = label;
    mostraRiepilogo('💰 Vendita: ' + label);
    if (titolo) setTitoloAuto(titolo);
  } else {
    nascondiRiepilogo();
  }
  document.getElementById('sel-fleet').value = '';
}

function onPraticaSelezionata(sel, type) {
  const opt = sel.options[sel.selectedIndex];
  if (opt.value) {
    document.querySelectorAll('[name=pratica_type]').forEach(el => el.value = type);
    const label  = opt.dataset.label  || opt.text;
    const titolo = opt.dataset.titolo || label;
    mostraRiepilogo('🔗 ' + label);
    if (opt.dataset.label) {
      document.getElementById('input-rifveicolo').value = opt.dataset.label;
    }
    if (titolo) setTitoloAuto(titolo);
  } else {
    document.querySelectorAll('[name=pratica_type]').forEach(el => el.value = '');
    nascondiRiepilogo();
  }
}

function mostraRiepilogo(testo) {
  const card = document.getElementById('card-riepilogo');
  document.getElementById('riepilogo-testo').textContent = testo;
  card.style.display = '';
}
function nascondiRiepilogo() {
  document.getElementById('card-riepilogo').style.display = 'none';
}

// Init
aggiornaTipo(document.getElementById('sel-tipo').value);
</script>

@endsection
