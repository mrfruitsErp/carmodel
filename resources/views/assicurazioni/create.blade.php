@extends('layouts.app')
@section('title', isset($company) ? 'Modifica Compagnia' : 'Nuova Compagnia')
@section('content')
<div style="margin-bottom:16px">
  <a href="{{ route('assicurazioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Compagnie</a>
</div>
<form method="POST" action="{{ isset($company) ? route('assicurazioni.update', $company) : route('assicurazioni.store') }}">
  @csrf
  @if(isset($company)) @method('PUT') @endif
  <div class="two-col">
    <div>
      <div class="card">
        <div class="card-title">Dati principali</div>
        <div class="form-group">
          <label class="form-label">Nome compagnia *</label>
          <input name="name" class="form-input" required value="{{ old('name', $company->name ?? '') }}">
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Codice</label>
            <input name="code" class="form-input" value="{{ old('code', $company->code ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">P.IVA</label>
            <input name="piva" class="form-input" value="{{ old('piva', $company->piva ?? '') }}">
          </div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Codice Fiscale</label>
            <input name="codice_fiscale" class="form-input" style="text-transform:uppercase" value="{{ old('codice_fiscale', $company->codice_fiscale ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Codice SDI</label>
            <input name="codice_sdi" class="form-input" value="{{ old('codice_sdi', $company->codice_sdi ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">PEC</label>
          <input name="pec" type="email" class="form-input" value="{{ old('pec', $company->pec ?? '') }}">
        </div>
      </div>
      <div class="card">
        <div class="card-title">Contatti</div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-input" value="{{ old('email', $company->email ?? '') }}">
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Telefono</label>
            <input name="phone" class="form-input" value="{{ old('phone', $company->phone ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Fax</label>
            <input name="fax" class="form-input" value="{{ old('fax', $company->fax ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Portale sinistri (URL)</label>
          <input name="portal_url" type="url" class="form-input" placeholder="https://..." value="{{ old('portal_url', $company->portal_url ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Indirizzo</label>
          <textarea name="address" class="form-textarea" rows="2">{{ old('address', $company->address ?? '') }}</textarea>
        </div>
      </div>
    </div>
    <div>
      <div class="card">
        <div class="card-title">Referente</div>
        <div class="form-group">
          <label class="form-label">Nome referente</label>
          <input name="referente" class="form-input" value="{{ old('referente', $company->referente ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Email referente</label>
          <input name="referente_email" type="email" class="form-input" value="{{ old('referente_email', $company->referente_email ?? '') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Telefono referente</label>
          <input name="referente_phone" class="form-input" value="{{ old('referente_phone', $company->referente_phone ?? '') }}">
        </div>
      </div>
      <div class="card">
        <div class="card-title">Note</div>
        <div class="form-group">
          <textarea name="notes" class="form-textarea" rows="4">{{ old('notes', $company->notes ?? '') }}</textarea>
        </div>
        <div class="form-group">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
            <input type="checkbox" name="active" value="1" {{ old('active', $company->active ?? true) ? 'checked' : '' }} style="accent-color:var(--orange)">
            Compagnia attiva
          </label>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <a href="{{ route('assicurazioni.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">
          {{ isset($company) ? 'Salva modifiche' : 'Crea compagnia' }}
        </button>
      </div>
    </div>
  </div>
</form>
@endsection