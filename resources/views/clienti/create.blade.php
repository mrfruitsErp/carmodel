@extends('layouts.app')
@section('title', isset($customer) ? 'Modifica Cliente' : 'Nuovo Cliente')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('clienti.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Clienti</a></div>
<form method="POST" action="{{ isset($customer) ? route('clienti.update', $customer) : route('clienti.store') }}">
  @csrf
  @if(isset($customer)) @method('PUT') @endif
  <div class="two-col">
    <div>
      <div class="card">
        <div class="card-title">Dati principali</div>
        <div class="form-group">
          <label class="form-label">Tipo cliente</label>
          <select name="type" class="form-select" onchange="toggleType(this.value)">
            <option value="private" {{ old('type', $customer->type ?? '') === 'private' ? 'selected' : '' }}>Privato</option>
            <option value="company" {{ old('type', $customer->type ?? '') === 'company' ? 'selected' : '' }}>Azienda</option>
            <option value="individual" {{ old('type', $customer->type ?? '') === 'individual' ? 'selected' : '' }}>Impresa Individuale</option>
          </select>
        </div>

        {{-- PRIVATO --}}
        <div id="private-fields">
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Nome</label>
              <input name="first_name" class="form-input" value="{{ old('first_name', $customer->first_name ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Cognome</label>
              <input name="last_name" class="form-input" value="{{ old('last_name', $customer->last_name ?? '') }}">
            </div>
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Codice Fiscale</label>
              <input name="fiscal_code" class="form-input" style="text-transform:uppercase" value="{{ old('fiscal_code', $customer->fiscal_code ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Data di nascita</label>
              <input name="date_of_birth" type="date" class="form-input" value="{{ old('date_of_birth', isset($customer->date_of_birth) ? $customer->date_of_birth->format('Y-m-d') : '') }}">
            </div>
          </div>
        </div>

        {{-- AZIENDA --}}
        <div id="company-fields" style="display:none">
          <div class="form-group">
            <label class="form-label">Ragione Sociale</label>
            <input name="company_name" class="form-input" value="{{ old('company_name', $customer->company_name ?? '') }}">
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">P.IVA</label>
              <input name="vat_number" class="form-input" value="{{ old('vat_number', $customer->vat_number ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Codice SDI</label>
              <input name="sdi_code" class="form-input" value="{{ old('sdi_code', $customer->sdi_code ?? '') }}">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">PEC</label>
            <input name="pec_email" type="email" class="form-input" value="{{ old('pec_email', $customer->pec_email ?? '') }}">
          </div>
        </div>

        {{-- IMPRESA INDIVIDUALE --}}
        <div id="individual-fields" style="display:none">
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Nome</label>
              <input name="first_name_ind" class="form-input" value="{{ old('first_name_ind', $customer->first_name ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">Cognome</label>
              <input name="last_name_ind" class="form-input" value="{{ old('last_name_ind', $customer->last_name ?? '') }}">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Ragione Sociale / Insegna</label>
            <input name="company_name_ind" class="form-input" value="{{ old('company_name_ind', $customer->company_name ?? '') }}">
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Codice Fiscale</label>
              <input name="fiscal_code_ind" class="form-input" style="text-transform:uppercase" value="{{ old('fiscal_code_ind', $customer->fiscal_code ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">P.IVA</label>
              <input name="vat_number_ind" class="form-input" value="{{ old('vat_number_ind', $customer->vat_number ?? '') }}">
            </div>
          </div>
          <div class="two-col" style="gap:10px">
            <div class="form-group">
              <label class="form-label">Codice SDI</label>
              <input name="sdi_code_ind" class="form-input" value="{{ old('sdi_code_ind', $customer->sdi_code ?? '') }}">
            </div>
            <div class="form-group">
              <label class="form-label">PEC</label>
              <input name="pec_email_ind" type="email" class="form-input" value="{{ old('pec_email_ind', $customer->pec_email ?? '') }}">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-title">Contatti</div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-input" value="{{ old('email', $customer->email ?? '') }}">
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Telefono</label>
            <input name="phone" class="form-input" value="{{ old('phone', $customer->phone ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Telefono 2</label>
            <input name="phone2" class="form-input" value="{{ old('phone2', $customer->phone2 ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">WhatsApp</label>
          <input name="whatsapp" class="form-input" value="{{ old('whatsapp', $customer->whatsapp ?? '') }}">
        </div>
      </div>

      <div class="card">
        <div class="card-title">Dati bancari</div>
        <div class="form-group">
          <label class="form-label">IBAN</label>
          <input name="iban" class="form-input" style="text-transform:uppercase;font-family:var(--mono);letter-spacing:.05em" maxlength="34" value="{{ old('iban', $customer->iban ?? '') }}" placeholder="IT60 X054 2811 1010 0000 0123 456">
        </div>
        <div class="form-group">
          <label class="form-label">Intestatario conto</label>
          <input name="intestatario_iban" class="form-input" value="{{ old('intestatario_iban', $customer->intestatario_iban ?? '') }}">
        </div>
      </div>
    </div>

    <div>
      <div class="card">
        <div class="card-title">Indirizzo</div>
        <div class="form-group">
          <label class="form-label">Via / Indirizzo</label>
          <input name="address" class="form-input" value="{{ old('address', $customer->address ?? '') }}">
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group">
            <label class="form-label">Città</label>
            <input name="city" class="form-input" value="{{ old('city', $customer->city ?? '') }}">
          </div>
          <div class="form-group">
            <label class="form-label">CAP</label>
            <input name="postal_code" class="form-input" maxlength="5" value="{{ old('postal_code', $customer->postal_code ?? '') }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Provincia</label>
          <input name="province" class="form-input" maxlength="2" style="text-transform:uppercase;width:80px" value="{{ old('province', $customer->province ?? '') }}">
        </div>
      </div>

      <div class="card">
        <div class="card-title">Gestionale</div>
        <div class="form-group">
          <label class="form-label">Provenienza</label>
          <select name="source" class="form-select">
            @foreach(['walk-in'=>'Walk-in','referral'=>'Referral','web'=>'Web','phone'=>'Telefono','insurance'=>'Assicurazione'] as $v => $l)
            <option value="{{ $v }}" {{ old('source', $customer->source ?? 'walk-in') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Note interne</label>
          <textarea name="notes" class="form-textarea">{{ old('notes', $customer->notes ?? '') }}</textarea>
        </div>
      </div>

      <div style="display:flex;gap:8px">
        <a href="{{ route('clienti.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">{{ isset($customer) ? 'Salva modifiche' : 'Crea cliente' }}</button>
      </div>
    </div>
  </div>
</form>

<script>
function toggleType(v) {
  document.getElementById('private-fields').style.display   = v === 'private'    ? '' : 'none';
  document.getElementById('company-fields').style.display   = v === 'company'    ? '' : 'none';
  document.getElementById('individual-fields').style.display = v === 'individual' ? '' : 'none';
}
toggleType('{{ old('type', $customer->type ?? 'private') }}');
</script>
@endsection