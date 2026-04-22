@extends('layouts.app')
@section('title', isset($documento) ? 'Modifica documento catalogo' : 'Nuovo documento catalogo')

@section('topbar-actions')
<a href="{{ route('documenti-catalogo.index') }}" class="btn btn-ghost btn-sm">← Catalogo</a>
@endsection

@section('content')

<form method="POST" action="{{ isset($documento) ? route('documenti-catalogo.update', $documento) : route('documenti-catalogo.store') }}">
@csrf
@if(isset($documento)) @method('PUT') @endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

  <div>
    <div class="card">
      <div class="card-title">{{ isset($documento) ? 'Modifica documento' : 'Nuovo documento' }}</div>

      <div class="form-group">
        <label class="form-label">Nome documento *</label>
        <input type="text" name="nome" class="form-input" required
          value="{{ old('nome', $documento->nome ?? '') }}"
          placeholder="Es. Patente di Guida (fronte)">
        @error('nome')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Descrizione</label>
        <textarea name="descrizione" class="form-textarea" placeholder="Descrizione breve per gli operatori...">{{ old('descrizione', $documento->descrizione ?? '') }}</textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Sezioni collegate *</label>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px">
          @foreach(\App\Models\DocumentoCatalogo::sezioniDisponibili() as $key => $label)
          @php
            $sezioniSelezionate = old('sezioni_collegate', $documento->sezioni_collegate ?? []);
          @endphp
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px">
            <input type="checkbox" name="sezioni_collegate[]" value="{{ $key }}"
              {{ in_array($key, $sezioniSelezionate) ? 'checked' : '' }}>
            {{ $label }}
          </label>
          @endforeach
        </div>
        @error('sezioni_collegate')<div style="color:var(--red);font-size:11px;margin-top:3px">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Template testo documento</label>
        <textarea name="template_testo" class="form-textarea" style="min-height:120px"
          placeholder="Testo del documento da generare/mostrare al cliente per la firma...">{{ old('template_testo', $documento->template_testo ?? '') }}</textarea>
        <div style="font-size:11px;color:var(--text3);margin-top:3px">Lascia vuoto se il documento è solo un upload</div>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Configurazione</div>

      <div class="form-group">
        <label class="form-label">Tipo soggetto</label>
        <select name="tipo_soggetto" class="form-select">
          <option value="entrambi" {{ old('tipo_soggetto', $documento->tipo_soggetto ?? 'entrambi') == 'entrambi' ? 'selected' : '' }}>Tutti (privato + azienda)</option>
          <option value="privato" {{ old('tipo_soggetto', $documento->tipo_soggetto ?? '') == 'privato' ? 'selected' : '' }}>Solo privato</option>
          <option value="azienda" {{ old('tipo_soggetto', $documento->tipo_soggetto ?? '') == 'azienda' ? 'selected' : '' }}>Solo azienda</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Modalità firma</label>
        <select name="modalita_firma" class="form-select">
          <option value="self_hosted" {{ old('modalita_firma', $documento->modalita_firma ?? 'self_hosted') == 'self_hosted' ? 'selected' : '' }}>Self-hosted (OTP email)</option>
          <option value="provider_esterno" {{ old('modalita_firma', $documento->modalita_firma ?? '') == 'provider_esterno' ? 'selected' : '' }}>Provider esterno</option>
          <option value="entrambi" {{ old('modalita_firma', $documento->modalita_firma ?? '') == 'entrambi' ? 'selected' : '' }}>Entrambi (scelta operatore)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Ordine di visualizzazione</label>
        <input type="number" name="ordine" class="form-input" min="0"
          value="{{ old('ordine', $documento->ordine ?? 0) }}">
      </div>

      <div style="display:flex;flex-direction:column;gap:10px;background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:14px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
          <input type="checkbox" name="richiede_upload" value="1"
            {{ old('richiede_upload', $documento->richiede_upload ?? false) ? 'checked' : '' }}>
          <span>📎 Richiede upload file</span>
        </label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
          <input type="checkbox" name="richiede_firma" value="1"
            {{ old('richiede_firma', $documento->richiede_firma ?? false) ? 'checked' : '' }}>
          <span>✍ Richiede firma</span>
        </label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
          <input type="checkbox" name="obbligatorio_default" value="1"
            {{ old('obbligatorio_default', $documento->obbligatorio_default ?? false) ? 'checked' : '' }}>
          <span>⚠ Obbligatorio di default</span>
        </label>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
          <input type="checkbox" name="attivo" value="1"
            {{ old('attivo', $documento->attivo ?? true) ? 'checked' : '' }}>
          <span>✓ Attivo nel catalogo</span>
        </label>
      </div>

    </div>

    <div style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        ✓ {{ isset($documento) ? 'Salva modifiche' : 'Crea documento' }}
      </button>
      <a href="{{ route('documenti-catalogo.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">Annulla</a>
    </div>
  </div>

</div>
</form>

@endsection