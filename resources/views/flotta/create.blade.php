@extends('layouts.app')
@section('title', isset($vehicle) ? 'Modifica Veicolo Flotta' : 'Nuovo Veicolo Flotta')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('flotta.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Flotta</a></div>
<form method="POST" action="{{ isset($vehicle) ? route('flotta.update', $vehicle) : route('flotta.store') }}">
@csrf @if(isset($vehicle)) @method('PUT') @endif
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati veicolo</div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Targa *</label><input name="plate" class="form-input" style="text-transform:uppercase" value="{{ old('plate', $vehicle->plate ?? '') }}" required></div>
        <div class="form-group"><label class="form-label">VIN</label><input name="vin" class="form-input" value="{{ old('vin', $vehicle->vin ?? '') }}"></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Marca</label><input name="brand" class="form-input" value="{{ old('brand', $vehicle->brand ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Modello</label><input name="model" class="form-input" value="{{ old('model', $vehicle->model ?? '') }}"></div>
      </div>
      <div class="three-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Anno</label><input name="year" type="number" class="form-input" value="{{ old('year', $vehicle->year ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Colore</label><input name="color" class="form-input" value="{{ old('color', $vehicle->color ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Categoria</label>
          <select name="category" class="form-select">
            @foreach(['A','B','C','D','E','F'] as $c)
            <option value="{{ $c }}" {{ old('category', $vehicle->category ?? 'B') === $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Alimentazione</label>
          <select name="fuel_type" class="form-select">
            @foreach(['benzina','diesel','elettrico','ibrido','altro'] as $f)
            <option value="{{ $f }}" {{ old('fuel_type', $vehicle->fuel_type ?? '') === $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group"><label class="form-label">Posti</label><input name="seats" type="number" class="form-input" value="{{ old('seats', $vehicle->seats ?? 5) }}"></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Km attuali</label><input name="km_current" type="number" class="form-input" value="{{ old('km_current', $vehicle->km_current ?? 0) }}"></div>
        <div class="form-group"><label class="form-label">Tariffa giornaliera €</label><input name="daily_rate" type="number" step="0.01" class="form-input" value="{{ old('daily_rate', $vehicle->daily_rate ?? 0) }}"></div>
      </div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Documenti & Stato</div>
      <div class="form-group"><label class="form-label">Scadenza revisione</label><input type="date" name="revision_expiry" class="form-input" value="{{ old('revision_expiry', isset($vehicle->revision_expiry) ? $vehicle->revision_expiry->format('Y-m-d') : '') }}"></div>
      <div class="form-group"><label class="form-label">Compagnia assicurativa</label><input name="insurance_company" class="form-input" value="{{ old('insurance_company', $vehicle->insurance_company ?? '') }}"></div>
      <div class="form-group"><label class="form-label">N° Polizza</label><input name="insurance_policy" class="form-input" value="{{ old('insurance_policy', $vehicle->insurance_policy ?? '') }}"></div>
      <div class="form-group"><label class="form-label">Scadenza assicurazione</label><input type="date" name="insurance_expiry" class="form-input" value="{{ old('insurance_expiry', isset($vehicle->insurance_expiry) ? $vehicle->insurance_expiry->format('Y-m-d') : '') }}"></div>
      <div class="form-group"><label class="form-label">Stato</label>
        <select name="status" class="form-select">
          @foreach(['disponibile','noleggiato','sostitutiva','manutenzione','dismissione'] as $s)
          <option value="{{ $s }}" {{ old('status', $vehicle->status ?? 'disponibile') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Prezzo acquisto €</label><input name="purchase_price" type="number" step="0.01" class="form-input" value="{{ old('purchase_price', $vehicle->purchase_price ?? '') }}"></div>
        <div class="form-group"><label class="form-label">Data acquisto</label><input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date', isset($vehicle->purchase_date) ? $vehicle->purchase_date->format('Y-m-d') : '') }}"></div>
      </div>
      <div class="form-group"><label class="form-label">Note</label><textarea name="notes" class="form-textarea">{{ old('notes', $vehicle->notes ?? '') }}</textarea></div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="{{ route('flotta.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
      <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">{{ isset($vehicle) ? 'Salva' : 'Aggiungi veicolo' }}</button>
    </div>
  </div>
</div>
</form>
@endsection
