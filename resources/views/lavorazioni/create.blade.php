@extends('layouts.app')
@section('title', 'Nuova Lavorazione')

@section('topbar-actions')
<a href="{{ route('lavorazioni.index') }}" class="btn btn-ghost btn-sm">← Torna alle Lavorazioni</a>
@endsection

@section('content')
<div style="max-width:800px">
  <div class="card">
    <div class="card-title">🔧 Nuova commessa di lavorazione</div>
    <form action="{{ route('lavorazioni.store') }}" method="POST">
      @csrf
      @if($errors->any())
        <div class="alert alert-red">@foreach($errors->all() as $e)<div>✗ {{ $e }}</div>@endforeach</div>
      @endif
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Cliente <span style="color:var(--red)">*</span></label>
          <select name="customer_id" class="form-select" required id="sel-cliente" onchange="loadVehicles(this.value)">
            <option value="">— Seleziona cliente —</option>
            @foreach($clienti as $c)
              <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->display_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Veicolo <span style="color:var(--red)">*</span></label>
          <select name="vehicle_id" class="form-select" required id="sel-vehicle">
            <option value="">— Prima seleziona cliente —</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tipo lavorazione <span style="color:var(--red)">*</span></label>
          <select name="job_type" class="form-select" required>
            <option value="">— Seleziona —</option>
            @foreach(['carrozzeria'=>'Carrozzeria','meccanica'=>'Meccanica','elettrauto'=>'Elettrauto','tagliando'=>'Tagliando','gommista'=>'Gommista','detailing'=>'Detailing','altro'=>'Altro'] as $v=>$l)
              <option value="{{ $v }}" {{ old('job_type')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Priorità</label>
          <select name="priority" class="form-select">
            @foreach(['normale'=>'Normale','alta'=>'Alta','urgente'=>'Urgente'] as $v=>$l)
              <option value="{{ $v }}" {{ old('priority')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Data consegna prevista</label>
          <input type="date" name="expected_end_date" value="{{ old('expected_end_date') }}" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Importo stimato (€)</label>
          <input type="number" name="estimated_amount" value="{{ old('estimated_amount') }}" class="form-input" min="0" step="0.01">
        </div>
        <div class="form-group">
          <label class="form-label">Tecnico assegnato</label>
          <select name="assigned_to" class="form-select">
            <option value="">— Nessuno —</option>
            @foreach($tecnici as $t)
              <option value="{{ $t->id }}" {{ old('assigned_to')==$t->id?'selected':'' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sinistro collegato</label>
          <select name="claim_id" class="form-select">
            <option value="">— Nessuno —</option>
            @foreach($sinistri as $s)
              <option value="{{ $s->id }}" {{ old('claim_id')==$s->id?'selected':'' }}>#{{ $s->claim_number }} — {{ $s->customer?->display_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Descrizione lavori</label>
        <textarea name="description" class="form-textarea" rows="4" placeholder="Descrivere i lavori da eseguire...">{{ old('description') }}</textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
        <a href="{{ route('lavorazioni.index') }}" class="btn btn-ghost">← Annulla</a>
        <button type="submit" class="btn btn-primary">🔧 Crea commessa</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const vehiclesByCustomer = {};
@foreach($clienti as $c)
  vehiclesByCustomer[{{ $c->id }}] = @json($c->vehicles ?? []);
@endforeach

function loadVehicles(customerId) {
  const sel = document.getElementById('sel-vehicle');
  sel.innerHTML = '<option value="">— Seleziona veicolo —</option>';
  if (!customerId || !vehiclesByCustomer[customerId]) return;
  vehiclesByCustomer[customerId].forEach(v => {
    sel.innerHTML += `<option value="${v.id}">${v.plate || ''} ${v.brand || ''} ${v.model || ''}</option>`;
  });
}

// Pre-seleziona veicolo se old value presente
@if(old('customer_id'))
  document.addEventListener('DOMContentLoaded', function() {
    loadVehicles({{ old('customer_id') }});
    const selV = document.getElementById('sel-vehicle');
    if (selV) selV.value = '{{ old('vehicle_id') }}';
  });
@endif
</script>
@endpush
@endsection
