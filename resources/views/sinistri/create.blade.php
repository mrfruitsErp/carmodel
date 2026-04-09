@extends('layouts.app')
@section('title', 'Apri Sinistro')
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('sinistri.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">â† Sinistri</a></div>
<form method="POST" action="{{ route('sinistri.store') }}">
@csrf
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati sinistro</div>
      <div class="form-group"><label class="form-label">Cliente *</label>
        <select name="customer_id" class="form-select" required onchange="loadVehicles(this.value)">
          <option value="">â€” Seleziona cliente â€”</option>
          @foreach($clienti as $c)<option value="{{ $c->id }}" {{ old('customer_id', request('customer_id')) == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
        </select></div>
      <div class="form-group"><label class="form-label">Veicolo *</label>
        <select name="vehicle_id" class="form-select" required>
          <option value="">â€” Prima seleziona cliente â€”</option>
          @foreach($veicoli as $v)<option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate }} â€” {{ $v->brand }} {{ $v->model }}</option>@endforeach
        </select></div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Compagnia assicurativa</label>
          <select name="insurance_company_id" class="form-select">
            <option value="">â€” Seleziona â€”</option>
            @foreach($compagnie as $comp)<option value="{{ $comp->id }}" {{ old('insurance_company_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>@endforeach
          </select></div>
        <div class="form-group"><label class="form-label">Tipo sinistro</label>
          <select name="claim_type" class="form-select">
            @foreach(['rca'=>'RCA','kasko'=>'Kasko','grandine'=>'Grandine','furto'=>'Furto','incendio'=>'Incendio','altro'=>'Altro'] as $v => $l)
            <option value="{{ $v }}" {{ old('claim_type','rca') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select></div>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Data sinistro *</label><input type="date" name="event_date" class="form-input" value="{{ old('event_date', date('Y-m-d')) }}" required></div>
        <div class="form-group"><label class="form-label">Importo stimato â‚¬</label><input type="number" name="estimated_amount" class="form-input" value="{{ old('estimated_amount') }}" step="0.01"></div>
      </div>
      <div class="form-group"><label class="form-label">Luogo sinistro</label><input name="event_location" class="form-input" value="{{ old('event_location') }}"></div>
      <div class="form-group"><label class="form-label">Dinamica sinistro</label><textarea name="event_description" class="form-textarea">{{ old('event_description') }}</textarea></div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Controparte</div>
      <div class="form-group"><label class="form-label">Targa controparte</label><input name="countSoftwareart_plate" class="form-input" style="text-transform:uppercase" value="{{ old('countSoftwareart_plate') }}"></div>
      <div class="form-group"><label class="form-label">Compagnia controparte</label><input name="countSoftwareart_insurance" class="form-input" value="{{ old('countSoftwareart_insurance') }}"></div>
    </div>
    <div class="card">
      <div class="card-title">CID</div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
          <input type="checkbox" name="cid_signed" value="1" {{ old('cid_signed') ? 'checked' : '' }}>
          <span class="form-label" style="margin:0">CID firmato da entrambe le parti</span>
        </label>
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group"><label class="form-label">Data CID</label><input type="date" name="cid_date" class="form-input" value="{{ old('cid_date') }}"></div>
        <div class="form-group"><label class="form-label">Scadenza CID</label><input type="date" name="cid_expiry" class="form-input" value="{{ old('cid_expiry') }}"></div>
      </div>
    </div>
    <div class="card">
      <div class="card-title">Polizza cliente</div>
      <div class="form-group"><label class="form-label">NÂ° Polizza</label><input name="policy_number" class="form-input" value="{{ old('policy_number') }}"></div>
      <div class="form-group"><label class="form-label">Perito assegnato</label>
        <select name="expert_id" class="form-select">
          <option value="">â€” Nessuno â€”</option>
          @foreach($periti as $p)<option value="{{ $p->id }}" {{ old('expert_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach
        </select></div>
    </div>
    <div class="form-group"><label class="form-label">Note</label><textarea name="notes" class="form-textarea">{{ old('notes') }}</textarea></div>
    <div style="display:flex;gap:8px">
      <a href="{{ route('sinistri.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
      <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Apri sinistro</button>
    </div>
  </div>
</div>
</form>
@endsection
