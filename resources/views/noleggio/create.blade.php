@extends('layouts.app')
@section('title', isset($rental) ? 'Modifica Contratto' : 'Nuovo Contratto Noleggio')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('noleggio.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Noleggi</a></div>
<form method="POST" action="{{ isset($rental) ? route('noleggio.update', $rental) : route('noleggio.store') }}">
@csrf @if(isset($rental)) @method('PUT') @endif
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati contratto</div>
      <div class="form-group"><label class="form-label">Cliente *</label>
        <select name="customer_id" class="form-select" required>
          <option value="">— Seleziona cliente —</option>
          @foreach($clienti as $c)<option value="{{ $c->id }}" {{ old('customer_id', $rental->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Veicolo flotta *</label>
        <select name="fleet_vehicle_id" class="form-select" required>
          <option value="">— Seleziona veicolo —</option>
          @foreach($flotta as $v)<option value="{{ $v->id }}" {{ old('fleet_vehicle_id', $rental->fleet_vehicle_id ?? '') == $v->id ? 'selected' : '' }}>{{ $v->plate }} — {{ $v->brand }} {{ $v->model }} ({{ ucfirst($v->status) }})</option>@endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Tipo contratto</label>
        <select name="rental_type" class="form-select">
          @foreach(['sostitutiva'=>'Auto sostitutiva','breve_termine'=>'Breve termine','lungo_termine'=>'Lungo termine'] as $v => $l)
          <option value="{{ $v }}" {{ old('rental_type', $rental->rental_type ?? 'sostitutiva') === $v ? 'selected' : '' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group"><label class="form-label">Sinistro collegato (opzionale)</label>
        <select name="claim_id" class="form-select">
          <option value="">— Nessuno —</option>
          @foreach($sinistri as $s)<option value="{{ $s->id }}" {{ old('claim_id', $rental->claim_id ?? '') == $s->id ? 'selected' : '' }}>#{{ $s->claim_number }} — {{ $s->customer->display_name }}</option>@endforeach
        </select>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Data inizio *</label><input type="date" name="start_date" class="form-input" value="{{ old('start_date', isset($rental->start_date) ? $rental->start_date->format('Y-m-d') : date('Y-m-d')) }}" required></div>
        <div class="form-group"><label class="form-label">Data fine prevista *</label><input type="date" name="expected_end_date" class="form-input" value="{{ old('expected_end_date', isset($rental->expected_end_date) ? $rental->expected_end_date->format('Y-m-d') : '') }}" required></div>
      </div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Condizioni economiche</div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Tariffa giornaliera €</label><input name="daily_rate" type="number" step="0.01" class="form-input" value="{{ old('daily_rate', $rental->daily_rate ?? 0) }}"></div>
        <div class="form-group"><label class="form-label">Km inclusi</label><input name="km_included" type="number" class="form-input" value="{{ old('km_included', $rental->km_included ?? '') }}"></div>
      </div>
      <div class="form-group"><label class="form-label">Km extra (€/km)</label><input name="km_extra_price" type="number" step="0.01" class="form-input" value="{{ old('km_extra_price', $rental->km_extra_price ?? 0) }}"></div>
      <div class="form-group"><label class="form-label">Km alla consegna</label><input name="km_start" type="number" class="form-input" value="{{ old('km_start', $rental->km_start ?? 0) }}"></div>
      <div class="form-group"><label class="form-label">Livello carburante (0-100%)</label><input name="fuel_level_start" type="number" min="0" max="100" class="form-input" value="{{ old('fuel_level_start', $rental->fuel_level_start ?? 100) }}"></div>
    </div>
    <div class="card">
      <div class="card-title">Note condizioni veicolo</div>
      <div class="form-group"><label class="form-label">Danni / note alla consegna</label><textarea name="damage_notes_start" class="form-textarea">{{ old('damage_notes_start', $rental->damage_notes_start ?? '') }}</textarea></div>
      <div class="form-group"><label class="form-label">Note generali</label><textarea name="notes" class="form-textarea">{{ old('notes', $rental->notes ?? '') }}</textarea></div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="{{ route('noleggio.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
      <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">{{ isset($rental) ? 'Salva' : 'Crea contratto' }}</button>
    </div>
  </div>
</div>
</form>
@endsection
