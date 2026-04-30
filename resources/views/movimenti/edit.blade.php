@extends('layouts.app')
@section('title', 'Modifica Movimento')

@section('topbar-actions')
<a href="{{ route('movimenti.show', $movimento) }}" class="btn btn-ghost btn-sm">← Annulla</a>
@endsection

@section('content')
<form method="POST" action="{{ route('movimenti.update', $movimento) }}">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start">

<div style="display:flex;flex-direction:column;gap:16px">

  <div class="card" style="padding:20px">
    <div class="form-section-title">Tipo Movimento</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label class="form-label">Tipo *</label>
        <select name="tipo" class="form-input" required>
          @foreach($tipi as $k => $t)
            <option value="{{ $k }}" {{ old('tipo',$movimento->tipo) == $k ? 'selected':'' }}>{{ $t['icon'] }} {{ $t['label'] }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Stato *</label>
        <select name="stato" class="form-input" required>
          @foreach($stati as $k => $s)
            <option value="{{ $k }}" {{ old('stato',$movimento->stato) == $k ? 'selected':'' }}>{{ $s['label'] }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div style="margin-top:12px">
      <label class="form-label">Titolo</label>
      <input type="text" name="titolo" class="form-input" value="{{ old('titolo',$movimento->titolo) }}">
    </div>
  </div>

  <div class="card" style="padding:20px">
    <div class="form-section-title">Data e Orario</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label class="form-label">Data/Ora Inizio *</label>
        <input type="datetime-local" name="data_inizio" class="form-input"
          value="{{ old('data_inizio', $movimento->data_inizio->format('Y-m-d\TH:i')) }}" required>
      </div>
      <div>
        <label class="form-label">Data/Ora Fine</label>
        <input type="datetime-local" name="data_fine" class="form-input"
          value="{{ old('data_fine', $movimento->data_fine?->format('Y-m-d\TH:i')) }}">
      </div>
    </div>
  </div>

  <div class="card" style="padding:20px">
    <div class="form-section-title">📍 Luogo di Partenza</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label class="form-label">Luogo</label>
        <input type="text" name="luogo_partenza" class="form-input" value="{{ old('luogo_partenza',$movimento->luogo_partenza) }}">
      </div>
      <div>
        <label class="form-label">Indirizzo completo</label>
        <input type="text" name="indirizzo_partenza" class="form-input" value="{{ old('indirizzo_partenza',$movimento->indirizzo_partenza) }}">
      </div>
    </div>
    <div class="form-section-title" style="margin-top:16px">🏁 Luogo di Arrivo / Destinazione</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label class="form-label">Luogo</label>
        <input type="text" name="luogo_arrivo" class="form-input" value="{{ old('luogo_arrivo',$movimento->luogo_arrivo) }}">
      </div>
      <div>
        <label class="form-label">Indirizzo completo</label>
        <input type="text" name="indirizzo_arrivo" class="form-input" value="{{ old('indirizzo_arrivo',$movimento->indirizzo_arrivo) }}">
      </div>
    </div>
  </div>

  <div class="card" style="padding:20px">
    <div class="form-section-title">Chilometri</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <label class="form-label">Km Partenza</label>
        <input type="number" name="km_partenza" class="form-input" value="{{ old('km_partenza',$movimento->km_partenza) }}">
      </div>
      <div>
        <label class="form-label">Km Arrivo</label>
        <input type="number" name="km_arrivo" class="form-input" value="{{ old('km_arrivo',$movimento->km_arrivo) }}">
      </div>
    </div>
  </div>

  <div class="card" style="padding:20px">
    <div class="form-section-title">Note</div>
    <textarea name="note" class="form-input" rows="3">{{ old('note',$movimento->note) }}</textarea>
  </div>

</div>

<div style="display:flex;flex-direction:column;gap:16px">

  <div class="card" style="padding:20px">
    <div class="form-section-title">Veicolo</div>
    <div style="margin-bottom:10px">
      <label class="form-label">Tipo Veicolo</label>
      <select name="vehicle_type" id="vehicle_type" class="form-input" onchange="cambiaVeicolo()">
        <option value="fleet"    {{ old('vehicle_type',$movimento->vehicle_type)=='fleet'    ?'selected':'' }}>🚗 Flotta Noleggio</option>
        <option value="sale"     {{ old('vehicle_type',$movimento->vehicle_type)=='sale'     ?'selected':'' }}>💰 In Vendita</option>
        <option value="customer" {{ old('vehicle_type',$movimento->vehicle_type)=='customer' ?'selected':'' }}>👤 Veicolo Cliente</option>
      </select>
    </div>
    <div id="sel_fleet">
      <label class="form-label">Veicolo Flotta</label>
      <select name="fleet_vehicle_id" class="form-input">
        <option value="">— Seleziona —</option>
        @foreach($fleetVehicles as $v)
          <option value="{{ $v->id }}" {{ old('fleet_vehicle_id',$movimento->fleet_vehicle_id)==$v->id?'selected':'' }}>
            {{ $v->brand }} {{ $v->model }} — {{ $v->plate }}
          </option>
        @endforeach
      </select>
    </div>
    <div id="sel_sale" style="display:none">
      <label class="form-label">Veicolo in Vendita</label>
      <select name="sale_vehicle_id" class="form-input">
        <option value="">— Seleziona —</option>
        @foreach($saleVehicles as $v)
          <option value="{{ $v->id }}" {{ old('sale_vehicle_id',$movimento->sale_vehicle_id)==$v->id?'selected':'' }}>
            {{ $v->brand }} {{ $v->model }} — {{ $v->plate }}
          </option>
        @endforeach
      </select>
    </div>
    <div id="sel_customer" style="display:none">
      <label class="form-label">Veicolo Cliente</label>
      <select name="vehicle_id" class="form-input">
        <option value="">— Seleziona —</option>
        @foreach($customerVehicles as $v)
          <option value="{{ $v->id }}" {{ old('vehicle_id',$movimento->vehicle_id)==$v->id?'selected':'' }}>
            {{ $v->brand }} {{ $v->model }} — {{ $v->plate }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="card" style="padding:20px">
    <div class="form-section-title">Persone</div>
    <div style="display:flex;flex-direction:column;gap:10px">
      <div>
        <label class="form-label">Cliente</label>
        <select name="cliente_id" class="form-input">
          <option value="">— Nessuno —</option>
          @foreach($clienti as $c)
            <option value="{{ $c->id }}" {{ old('cliente_id',$movimento->cliente_id)==$c->id?'selected':'' }}>
              {{ $c->display_name ?? $c->first_name.' '.$c->last_name }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Operatore responsabile</label>
        <select name="operatore_id" class="form-input">
          <option value="">— Nessuno —</option>
          @foreach($operatori as $u)
            <option value="{{ $u->id }}" {{ old('operatore_id',$movimento->operatore_id)==$u->id?'selected':'' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="form-label">Autista / Accompagnatore</label>
        <select name="autista_id" class="form-input">
          <option value="">— Nessuno —</option>
          @foreach($operatori as $u)
            <option value="{{ $u->id }}" {{ old('autista_id',$movimento->autista_id)==$u->id?'selected':'' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="card" style="padding:20px">
    <button type="submit" class="btn btn-primary" style="width:100%">💾 Salva Modifiche</button>
    <a href="{{ route('movimenti.show', $movimento) }}" class="btn btn-ghost" style="width:100%;margin-top:8px">Annulla</a>
  </div>

</div>
</div>
</form>

<script>
function cambiaVeicolo() {
    const t = document.getElementById('vehicle_type').value;
    document.getElementById('sel_fleet').style.display    = t==='fleet'    ? '' : 'none';
    document.getElementById('sel_sale').style.display     = t==='sale'     ? '' : 'none';
    document.getElementById('sel_customer').style.display = t==='customer' ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', cambiaVeicolo);
</script>
@endsection
