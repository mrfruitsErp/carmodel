@extends('layouts.app')
@section('title', 'Modifica Fascicolo')

@section('topbar-actions')
<a href="{{ route('fascicoli.show', $fascicolo) }}" class="btn btn-ghost btn-sm">← Torna al fascicolo</a>
@endsection

@section('content')

<form method="POST" action="{{ route('fascicoli.update', $fascicolo) }}">
@csrf @method('PUT')

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

  <div>
    <div class="card">
      <div class="card-title">Modifica fascicolo #{{ $fascicolo->id }}</div>

      <div class="form-group">
        <label class="form-label">Cliente *</label>
        <select name="cliente_id" class="form-select" required>
          @foreach($clienti as $c)
            <option value="{{ $c->id }}" {{ $fascicolo->cliente_id == $c->id ? 'selected' : '' }}>
              {{ $c->display_name ?? ($c->nome . ' ' . $c->cognome) }}
              @if($c->tipo_soggetto === 'azienda') (Azienda) @endif
            </option>
          @endforeach
        </select>
      </div>

      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Tipo pratica *</label>
          <select name="tipo_pratica" class="form-select" required>
            @foreach($tipiPratica as $key => $label)
              <option value="{{ $key }}" {{ $fascicolo->tipo_pratica == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Stato</label>
          <select name="stato" class="form-select">
            @foreach($stati as $key => $info)
              <option value="{{ $key }}" {{ $fascicolo->stato == $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Titolo / riferimento interno</label>
        <input type="text" name="titolo" class="form-input" value="{{ old('titolo', $fascicolo->titolo) }}">
      </div>

      <div class="form-group">
        <label class="form-label">Note operative</label>
        <textarea name="note" class="form-textarea">{{ old('note', $fascicolo->note) }}</textarea>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Dati veicolo / periodo</div>
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Data inizio</label>
          <input type="date" name="data_inizio" class="form-input" value="{{ old('data_inizio', $fascicolo->data_inizio?->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Data fine</label>
          <input type="date" name="data_fine" class="form-input" value="{{ old('data_fine', $fascicolo->data_fine?->format('Y-m-d')) }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Veicolo di riferimento</label>
        <input type="text" name="riferimento_veicolo" class="form-input" value="{{ old('riferimento_veicolo', $fascicolo->riferimento_veicolo) }}">
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Assegnazione</div>
      <div class="form-group">
        <label class="form-label">Operatore assegnato</label>
        <select name="operatore_id" class="form-select">
          <option value="">Nessuno</option>
          @foreach($operatori as $u)
            <option value="{{ $u->id }}" {{ $fascicolo->operatore_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">✓ Salva modifiche</button>
      <a href="{{ route('fascicoli.show', $fascicolo) }}" class="btn btn-ghost" style="width:100%;justify-content:center">Annulla</a>
    </div>

    <div class="card" style="margin-top:16px;border-color:rgba(239,68,68,.3)">
      <div class="card-title" style="color:var(--red)">Zona pericolosa</div>
      <form method="POST" action="{{ route('fascicoli.destroy', $fascicolo) }}"
        onsubmit="return confirm('Eliminare definitivamente questo fascicolo?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">🗑 Elimina fascicolo</button>
      </form>
    </div>
  </div>

</div>
</form>

@endsection