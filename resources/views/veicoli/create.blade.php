@extends('layouts.app')
@section('title', isset($vehicle) ? 'Modifica Veicolo' : 'Nuovo Veicolo')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('veicoli.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Veicoli</a></div>
<form method="POST" action="{{ isset($vehicle) ? route('veicoli.update', $vehicle) : route('veicoli.store') }}">
@csrf @if(isset($vehicle)) @method('PUT') @endif
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati veicolo</div>
      <div class="form-group"><label class="form-label">Proprietario *</label>
        <select name="customer_id" class="form-select" required>
          <option value="">— Seleziona cliente —</option>
          @foreach($clienti as $c)<option value="{{ $c->id }}" {{ old('customer_id', request('customer_id', $vehicle->customer_id ?? '')) == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
        </select></div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Targa *</label><input name="plate" class="form-input" style="text-transform:uppercase" value="{{ old('plate', $vehicle->plate ?? '') }}" required></div>
        <div class="form-group"><label class="form-label">VIN / Telaio</label><input name="vin" class="form-input" style="text-transform:uppercase" value="{{ old('vin', $vehicle->vin ?? '') }}"></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Marca</label><input name="brand" class="form-input" value="{{ old('brand', $vehicle->brand ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Modello</label><input name="model" class="form-input" value="{{ old('model', $vehicle->model ?? '') }}"></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Anno</label><input name="year" type="number" class="form-input" min="1900" max="2030" value="{{ old('year', $vehicle->year ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Colore</label><input name="color" class="form-input" value="{{ old('color', $vehicle->color ?? '') }}"></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Alimentazione</label>
          <select name="fuel_type" class="form-select">
            @foreach(['benzina','diesel','gpl','metano','elettrico','ibrido','altro'] as $f)
            <option value="{{ $f }}" {{ old('fuel_type', $vehicle->fuel_type ?? '') === $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
            @endforeach
          </select></div>
        <div class="form-group"><label class="form-label">Km attuali</label><input name="km_current" type="number" class="form-input" value="{{ old('km_current', $vehicle->km_current ?? '') }}"></div>
      </div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Assicurazione</div>
      <div class="form-group"><label class="form-label">Compagnia assicurativa</label><input name="insurance_company" class="form-input" value="{{ old('insurance_company', $vehicle->insurance_company ?? '') }}"></div>
      <div class="form-group"><label class="form-label">N° Polizza</label><input name="insurance_policy" class="form-input" value="{{ old('insurance_policy', $vehicle->insurance_policy ?? '') }}"></div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Scad. assicurazione</label><input type="date" name="insurance_expiry" class="form-input" value="{{ old('insurance_expiry', isset($vehicle->insurance_expiry) ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}"></div>
        <div class="form-group"><label class="form-label">Scad. revisione</label><input type="date" name="revision_expiry" class="form-input" value="{{ old('revision_expiry', isset($vehicle->revision_expiry) ? $vehicle->revision_expiry->format('Y-m-d') : '') }}"></div>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Note</label><textarea name="notes" class="form-textarea">{{ old('notes', $vehicle->notes ?? '') }}</textarea></div>
    <div style="display:flex;gap:8px">
      <a href="{{ route('veicoli.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
      <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">{{ isset($vehicle) ? 'Salva' : 'Crea veicolo' }}</button>
    </div>
  </div>
</div>
</form>
@endsection
