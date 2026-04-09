@extends('layouts.app')
@section('title', 'Lavorazione #'.$workOrder->job_number)
@section('topbar-actions')
<a href="{{ route('lavorazioni.edit', $workOrder) }}" class="btn btn-ghost btn-sm">Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('lavorazioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&lt;- Lavorazioni</a></div>
<div class="main-side">
  <div>
    <div class="card">
      <div class="card-title">Dati commessa</div>
      <div class="info-row"><span class="info-label">Cliente</span><span class="info-value"><a href="{{ route('clienti.show', $workOrder->customer) }}" style="color:var(--green);text-decoration:none">{{ $workOrder->customer->display_name }}</a></span></div>
      <div class="info-row"><span class="info-label">Veicolo</span><span class="info-value"><span class="targa">{{ $workOrder->vehicle->plate }}</span> {{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }}</span></div>
      <div class="info-row"><span class="info-label">Tipo intervento</span><span class="info-value"><span class="badge badge-teal">{{ ucfirst($workOrder->job_type) }}</span></span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge {{ $workOrder->status==='completato' ? 'badge-green' : ($workOrder->status==='annullato' ? 'badge-gray' : 'badge-amber') }}">{{ str_replace('_',' ',ucfirst($workOrder->status)) }}</span></span></div>
      <div class="info-row"><span class="info-label">Priorita</span><span class="info-value"><span class="badge {{ $workOrder->priority==='urgente' ? 'badge-red' : ($workOrder->priority==='alta' ? 'badge-orange' : 'badge-gray') }}">{{ ucfirst($workOrder->priority ?? '-') }}</span></span></div>
      <div class="info-row"><span class="info-label">Avanzamento</span><span class="info-value">{{ $workOrder->progress_percent }}%</span></div>
      <div class="info-row"><span class="info-label">Tecnico assegnato</span><span class="info-value">{{ $workOrder->assignedTo?->name ?? '-' }}</span></div>
      <div class="info-row"><span class="info-label">Data inizio</span><span class="info-value">{{ $workOrder->start_date?->format('d/m/Y') ?? '-' }}</span></div>
      <div class="info-row"><span class="info-label">Scadenza prevista</span><span class="info-value">{{ $workOrder->expected_end_date?->format('d/m/Y') ?? '-' }}</span></div>
      <div class="info-row"><span class="info-label">Importo stimato</span><span class="info-value" style="color:var(--green-text);font-weight:600">{{ $workOrder->estimated_amount ? 'euro '.number_format($workOrder->estimated_amount,2,',','.') : '-' }}</span></div>
      @if($workOrder->claim)
      <div class="info-row"><span class="info-label">Sinistro</span><span class="info-value"><a href="{{ route('sinistri.show',$workOrder->claim) }}" style="color:var(--blue-text);font-family:var(--mono);font-size:12px">#{{ $workOrder->claim->claim_number }}</a></span></div>
      @endif
    </div>
    @if($workOrder->description)
    <div class="card">
      <div class="card-title">Descrizione intervento</div>
      <div style="font-size:13px;color:var(--text2);line-height:1.7">{{ $workOrder->description }}</div>
    </div>
    @endif
    @if($workOrder->technical_notes)
    <div class="card">
      <div class="card-title">Note tecniche</div>
      <div style="font-size:13px;color:var(--text2);line-height:1.7">{{ $workOrder->technical_notes }}</div>
    </div>
    @endif
  </div>
  <div>
    <div class="card">
      <div class="card-title">Aggiorna avanzamento</div>
      <form action="{{ route('lavorazioni.progresso',$workOrder) }}" method="POST">
        @csrf
        <div class="form-group">
          <label class="form-label">Avanzamento %</label>
          <input type="range" name="progress" min="0" max="100" value="{{ $workOrder->progress_percent }}" class="form-input" oninput="this.nextElementSibling.textContent=this.value+'%'">
          <span style="font-size:13px;color:var(--text2)">{{ $workOrder->progress_percent }}%</span>
        </div>
        <div class="form-group">
          <label class="form-label">Nuovo stato</label>
          <select name="status" class="form-select">
            @foreach(['attesa','in_lavorazione','attesa_ricambi','completato','consegnato','annullato'] as $s)
            <option value="{{ $s }}" {{ $workOrder->status===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%">Aggiorna</button>
      </form>
    </div>
    <div class="card">
      <div class="card-title">Note tecniche</div>
      <form action="{{ route('lavorazioni.stato',$workOrder) }}" method="POST">
        @csrf
        <input type="hidden" name="status" value="{{ $workOrder->status }}">
        <div class="form-group">
          <textarea name="technical_notes" class="form-textarea" rows="5" placeholder="Note per il tecnico...">{{ $workOrder->technical_notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-ghost" style="width:100%">Salva note</button>
      </form>
    </div>
    <div class="card">
      <div class="card-title">Azioni</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('lavorazioni.edit',$workOrder) }}" class="btn btn-ghost" style="justify-content:center">Modifica commessa</a>
        <form action="{{ route('lavorazioni.destroy',$workOrder) }}" method="POST" onsubmit="return confirm('Eliminare questa commessa?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">Elimina</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection