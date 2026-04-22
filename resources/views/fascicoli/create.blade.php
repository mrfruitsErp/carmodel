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
          <select name="tipo_pratica" class="form-select" required id="sel-tipo">
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
        <label class="form-label">Titolo / riferimento interno</label>
        <input type="text" name="titolo" class="form-input" placeholder="Es. Sinistro del 15/04/2026, Noleggio Fiat 500..." value="{{ old('titolo') }}">
      </div>

      <div class="form-group">
        <label class="form-label">Note operative</label>
        <textarea name="note" class="form-textarea" placeholder="Note interne per gli operatori...">{{ old('note') }}</textarea>
      </div>
    </div>

    {{-- SEZIONE NOLEGGIO/AUTO --}}
    <div class="card" id="card-noleggio">
      <div class="card-title">Dati veicolo / periodo</div>
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
      <div class="form-group">
        <label class="form-label">Veicolo di riferimento</label>
        <input type="text" name="riferimento_veicolo" class="form-input" placeholder="Es. Fiat 500 - AB123CD" value="{{ old('riferimento_veicolo') }}">
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

    <div class="card" style="background:var(--orange-bg);border-color:var(--orange-border)">
      <div style="font-size:12px;color:var(--text2);line-height:1.7">
        <strong style="color:var(--orange);font-size:13px">📋 Documenti automatici</strong><br><br>
        Dopo la creazione, il sistema caricherà automaticamente i documenti richiesti dal <strong>catalogo</strong> in base al tipo di pratica e al tipo cliente.<br><br>
        Potrai aggiungere o rimuovere documenti dalla scheda fascicolo.
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

@endsection