@extends('layouts.app')
@section('title', 'Nuova Lavorazione')

@section('content')
<div style="max-width:800px">
  <div style="margin-bottom:16px">
    <a href="{{ route('lavorazioni.index') }}" style="font-size:13px;color:var(--text3);text-decoration:none">â† Torna alle Lavorazioni</a>
  </div>
  <div class="card">
    <div class="card-title">ðŸ”§ Nuova commessa di lavorazione</div>
    <form action="{{ route('lavorazioni.store') }}" method="POST">
      @csrf
      @if($errors->any())
        <div class="alert alert-red">@foreach($errors->all() as $e)<div>âœ— {{ $e }}</div>@endforeach</div>
      @endif
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Cliente <span style="color:var(--red)">*</span></label>
          <select name="customer_id" class="form-select" required id="sel-cliente" onchange="loadVehicles(this.value)">
            <option value="">â€” Seleziona cliente â€”</option>
            @foreach($clienti as $c)
              <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->display_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Veicolo <span style="color:var(--red)">*</span></label>
          <select name="vehicle_id" class="form-select" required id="sel-vehicle">
            <option value="">â€” Prima seleziona cliente â€”</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tipo lavorazione <span style="color:var(--red)">*</span></label>
          <select name="job_type" class="form-select" required>
            <option value="">â€” Seleziona â€”</option>
            @foreach(['carrozzeria','meccanica','elettrauto','tagliando','gommista','detailing','altro'] as $t)
              <option value="{{ $t }}" {{ old('job_type')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">PrioritÃ </label>
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
          <label class="form-label">Importo stimato (â‚¬)</label>
          <input type="number" name="estimated_amount" value="{{ old('estimated_amount') }}" class="form-input" min="0" step="0.01">
        </div>
        <div class="form-group">
          <label class="form-label">Tecnico assegnato</label>
          <select name="assigned_to" class="form-select">
            <option value="">â€” Nessuno â€”</option>
            @foreach($tecnici as $t)
              <option value="{{ $t->id }}" {{ old('assigned_to')===$t->id?'selected':'' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sinistro collegato</label>
          <select name="claim_id" class="form-select">
            <option value="">â€” Nessuno â€”</option>
            @foreach($sinistri as $s)
              <option value="{{ $s->id }}" {{ old('claim_id')===$s->id?'selected':'' }}>#{{ $s->claim_number }} â€” {{ $s->customer?->display_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Descrizione lavori</label>
        <textarea name="description" class="form-textarea" rows="4" placeholder="Descrivere i lavori da eseguire...">{{ old('description') }}</textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
        <a href="{{ route('lavorazioni.index') }}" class="btn btn-ghost">â† Annulla</a>
        <button type="submit" class="btn btn-primary">ðŸ”§ Crea commessa</button>
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
  sel.innerHTML = '<option value="">â€” Seleziona veicolo â€”</option>';
  if (!customerId || !vehiclesByCustomer[customerId]) return;
  vehiclesByCustomer[customerId].forEach(v => {
    sel.innerHTML += `<option value="${v.id}">${v.plate || ''} ${v.brand || ''} ${v.model || ''}</option>`;
  });
}
</script>
@endpush
@endsection