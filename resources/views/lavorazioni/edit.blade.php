@extends('layouts.app')
@section('title', 'Modifica Lavorazione #'.$workOrder->job_number)

@section('topbar-actions')
<a href="{{ route('lavorazioni.show', $workOrder) }}" class="btn btn-ghost btn-sm">← Torna alla commessa</a>
@endsection

@section('content')
<div style="max-width:800px">
  <div class="card">
    <div class="card-title">🔧 Modifica commessa — <span style="font-family:var(--mono);color:var(--orange)">{{ $workOrder->job_number }}</span></div>
    <form action="{{ route('lavorazioni.update', $workOrder) }}" method="POST">
      @csrf @method('PUT')
      @if($errors->any())
        <div class="alert alert-red">@foreach($errors->all() as $e)<div>✗ {{ $e }}</div>@endforeach</div>
      @endif
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Cliente <span style="color:var(--red)">*</span></label>
          <select name="customer_id" class="form-select" required id="sel-cliente" onchange="loadVehicles(this.value)">
            <option value="">— Seleziona cliente —</option>
            @foreach($clienti as $c)
              <option value="{{ $c->id }}"
                {{ old('customer_id', $workOrder->customer_id) == $c->id ? 'selected' : '' }}>
                {{ $c->display_name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Veicolo <span style="color:var(--red)">*</span></label>
          <select name="vehicle_id" class="form-select" required id="sel-vehicle">
            <option value="">— Seleziona veicolo —</option>
            @foreach($veicoli->where('customer_id', $workOrder->customer_id) as $v)
              <option value="{{ $v->id }}" {{ old('vehicle_id', $workOrder->vehicle_id) == $v->id ? 'selected' : '' }}>
                {{ $v->plate }} — {{ $v->brand }} {{ $v->model }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tipo lavorazione <span style="color:var(--red)">*</span></label>
          <select name="job_type" class="form-select" required>
            <option value="">— Seleziona —</option>
            @foreach(['carrozzeria'=>'Carrozzeria','meccanica'=>'Meccanica','elettrauto'=>'Elettrauto','tagliando'=>'Tagliando','gommista'=>'Gommista','detailing'=>'Detailing','altro'=>'Altro'] as $v=>$l)
              <option value="{{ $v }}" {{ old('job_type', $workOrder->job_type) === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Priorità</label>
          <select name="priority" class="form-select">
            @foreach(['normale'=>'Normale','alta'=>'Alta','urgente'=>'Urgente'] as $v=>$l)
              <option value="{{ $v }}" {{ old('priority', $workOrder->priority) === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Stato</label>
          <select name="status" class="form-select">
            @foreach(['attesa'=>'In attesa','in_lavorazione'=>'In lavorazione','attesa_ricambi'=>'Attesa ricambi','completato'=>'Completato','consegnato'=>'Consegnato','annullato'=>'Annullato'] as $v=>$l)
              <option value="{{ $v }}" {{ old('status', $workOrder->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Avanzamento %</label>
          <input type="number" name="progress_percent" value="{{ old('progress_percent', $workOrder->progress_percent) }}" class="form-input" min="0" max="100">
        </div>
        <div class="form-group">
          <label class="form-label">Data consegna prevista</label>
          <input type="date" name="expected_end_date" value="{{ old('expected_end_date', $workOrder->expected_end_date?->format('Y-m-d')) }}" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Data consegna effettiva</label>
          <input type="date" name="delivery_date" value="{{ old('delivery_date', $workOrder->delivery_date?->format('Y-m-d')) }}" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Importo stimato (€)</label>
          <input type="number" name="estimated_amount" value="{{ old('estimated_amount', $workOrder->estimated_amount) }}" class="form-input" min="0" step="0.01">
        </div>
        <div class="form-group">
          <label class="form-label">Importo finale (€)</label>
          <input type="number" name="actual_amount" value="{{ old('actual_amount', $workOrder->actual_amount) }}" class="form-input" min="0" step="0.01">
        </div>
        <div class="form-group">
          <label class="form-label">Tecnico assegnato</label>
          <select name="assigned_to" class="form-select">
            <option value="">— Nessuno —</option>
            @foreach($tecnici as $t)
              <option value="{{ $t->id }}" {{ old('assigned_to', $workOrder->assigned_to) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Sinistro collegato</label>
          <select name="claim_id" class="form-select">
            <option value="">— Nessuno —</option>
            @foreach($sinistri as $s)
              <option value="{{ $s->id }}" {{ old('claim_id', $workOrder->claim_id) == $s->id ? 'selected' : '' }}>
                #{{ $s->claim_number }} — {{ $s->customer?->display_name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Descrizione lavori</label>
        <textarea name="description" class="form-textarea" rows="4" placeholder="Descrivere i lavori da eseguire...">{{ old('description', $workOrder->description) }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Note tecniche</label>
        <textarea name="technical_notes" class="form-textarea" rows="3" placeholder="Note per il tecnico...">{{ old('technical_notes', $workOrder->technical_notes) }}</textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Note interne</label>
        <textarea name="internal_notes" class="form-textarea" rows="3" placeholder="Note interne (non visibili al cliente)...">{{ old('internal_notes', $workOrder->internal_notes) }}</textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
        <a href="{{ route('lavorazioni.show', $workOrder) }}" class="btn btn-ghost">← Annulla</a>
        <button type="submit" class="btn btn-primary">💾 Salva modifiche</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
@php
  $veicoli_js = $veicoli->map(function($v) {
    return ['id'=>$v->id,'customer_id'=>$v->customer_id,'plate'=>$v->plate,'brand'=>$v->brand,'model'=>$v->model];
  })->values();
@endphp
<script>
const allVehicles = {!! json_encode($veicoli_js) !!};
const currentVehicleId = {{ $workOrder->vehicle_id ?? 'null' }};

function loadVehicles(customerId) {
  const sel = document.getElementById('sel-vehicle');
  const prev = sel.value;
  sel.innerHTML = '<option value="">— Seleziona veicolo —</option>';
  if (!customerId) return;
  allVehicles
    .filter(v => v.customer_id == customerId)
    .forEach(v => {
      const opt = document.createElement('option');
      opt.value = v.id;
      opt.textContent = `${v.plate} — ${v.brand} ${v.model}`;
      if (v.id == (prev || currentVehicleId)) opt.selected = true;
      sel.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
  const customerId = document.getElementById('sel-cliente').value;
  if (customerId) loadVehicles(customerId);
});
</script>
@endpush
@endsection
