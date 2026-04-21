@extends('layouts.app')
@section('title', isset($esperto) ? 'Modifica '.$esperto->name : 'Nuovo Contatto')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('periti.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Esperti & Contatti</a></div>
<form method="POST" action="{{ isset($esperto) ? route('periti.update', $esperto) : route('periti.store') }}">
  @csrf
  @if(isset($esperto)) @method('PUT') @endif
  <div class="two-col">
    <div>
      <div class="card">
        <div class="card-title">Dati anagrafici</div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Tipo *</label>
            <select name="type" class="form-select" required>
              <option value="">— Seleziona —</option>
              <option value="perito" {{ old('type', $esperto->type ?? '') === 'perito' ? 'selected' : '' }}>Perito</option>
              <option value="avvocato" {{ old('type', $esperto->type ?? '') === 'avvocato' ? 'selected' : '' }}>Avvocato</option>
              <option value="legale" {{ old('type', $esperto->type ?? '') === 'legale' ? 'selected' : '' }}>Legale</option>
              <option value="liquidatore" {{ old('type', $esperto->type ?? '') === 'liquidatore' ? 'selected' : '' }}>Liquidatore</option>
              <option value="medico_legale" {{ old('type', $esperto->type ?? '') === 'medico_legale' ? 'selected' : '' }}>Medico Legale</option>
              <option value="consulente" {{ old('type', $esperto->type ?? '') === 'consulente' ? 'selected' : '' }}>Consulente</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Titolo</label>
            <input name="title" class="form-input" placeholder="Avv., Dott., Ing...." value="{{ old('title', $esperto->title ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nome e Cognome *</label>
          <input name="name" class="form-input" required value="{{ old('name', $esperto->name ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Ragione sociale / Studio</label>
          <input name="company_name" class="form-input" value="{{ old('company_name', $esperto->company_name ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Compagnia assicurativa collegata</label>
          <select name="insurance_company_id" class="form-select">
            <option value="">— Nessuna —</option>
            @foreach($compagnie as $c)
            <option value="{{ $c->id }}" {{ old('insurance_company_id', $esperto->insurance_company_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="card">
        <div class="card-title">Contatti</div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Telefono</label>
            <input name="phone" class="form-input" value="{{ old('phone', $esperto->phone ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Telefono 2</label>
            <input name="phone2" class="form-input" value="{{ old('phone2', $esperto->phone2 ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-input" value="{{ old('email', $esperto->email ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Indirizzo</label>
          <textarea name="address" class="form-textarea" style="min-height:60px">{{ old('address', $esperto->address ?? '') }}</textarea>
        </div>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-title">Dati fiscali</div>
        <div class="form-group">
          <label class="form-label">Codice fiscale</label>
          <input name="fiscal_code" class="form-input" style="text-transform:uppercase" value="{{ old('fiscal_code', $esperto->fiscal_code ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Partita IVA</label>
          <input name="vat_number" class="form-input" value="{{ old('vat_number', $esperto->vat_number ?? '') }}">
        </div>
      </div>
      <div class="card">
        <div class="card-title">Valutazione</div>
        <div class="form-group">
          <label class="form-label">Valutazione (1-5)</label>
          <select name="rating" class="form-select">
            @for($i=1;$i<=5;$i++)
            <option value="{{ $i }}" {{ old('rating', $esperto->rating ?? 3) == $i ? 'selected' : '' }}>{{ str_repeat('★',$i) }}{{ str_repeat('☆',5-$i) }}</option>
            @endfor
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Note</label>
          <textarea name="notes" class="form-textarea">{{ old('notes', $esperto->notes ?? '') }}</textarea>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <a href="{{ route('periti.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">{{ isset($esperto) ? 'Salva modifiche' : 'Aggiungi' }}</button>
      </div>
    </div>
  </div>
</form>
@endsection
