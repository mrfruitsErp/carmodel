@extends('layouts.app')
@section('title', 'Lavorazione #'.$workOrder->job_number)
@section('topbar-actions')
<a href="{{ route('lavorazioni.edit', $workOrder) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('lavorazioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Lavorazioni</a></div>
<div class="main-side">
  <div>
    <div class="card">
      <div class="card-title">Dati commessa</div>
      <div class="info-row"><span class="info-label">Cliente</span><span class="info-value"><a href="{{ route('clienti.show', $workOrder->customer) }}" style="color:var(--green);text-decoration:none">{{ $workOrder->customer->display_name }}</a></span></div>
      <div class="info-row"><span class="info-label">Veicolo</span><span class="info-value"><span class="targa">{{ $workOrder->vehicle->plate }}</span> {{ $workOrder->vehicle->brand }} {{ $workOrder->vehicle->model }}</span></div>
      <div class="info-row"><span class="info-label">Tipo intervento</span><span class="info-value"><span class="badge badge-teal">{{ ucfirst($workOrder->job_type) }}</span></span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge badge-amber">{{ str_replace('_',' ',ucfirst($workOrder->status)) }}</span></span></div>
      <div class="info-row"><span class="info-label">Avanzamento</span><span class="info-value">{{ $workOrder->progress_percent }}%</span></div>
      <div class="info-row"><span class="info-label">Tecnico assegnato</span><span class="info-value">{{ $workOrder->assignedTo?->name ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Data inizio</span><span class="info-value">{{ $workOrder->start_date ? $workOrder->start_date->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Scadenza prevista</span><span class="info-value" style="color:{{ $workOrder->isOverdue() ? 'var(--red)' : 'var(--text)' }}">{{ $workOrder->expected_end_date ? $workOrder->expected_end_date->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Importo stimato</span><span class="info-value" style="color:var(--green)">{{ $workOrder->estimated_amount ? '€ '.number_format($workOrder->estimated_amount,2,',','.') : '—' }}</span></div>
      @if($workOrder->claim)
      <div class="info-row"><span class="info-label">Sinistro collegato</span><span class="info-value"><a href="{{ route('sinistri.show', $workOrder->claim) }}" style="color:var(--green);text-decoration:none">#{{ $workOrder->claim->claim_number }}</a></span></div>
      @endif
    </div>
    @if($workOrder->description)
    <div class="card">
      <div class="card-title">Descrizione intervento</div>
      <div style="font-size:13px;color:var(--text2);line-height:1.6">{{ $workOrder->description }}</div>
    </div>
    @endif
    <div class="card">
      <div class="card-title">Voci lavorazione</div>
      @if($workOrder->items->count())
      <table>
        <thead><tr><th>Descrizione</th><th>Tipo</th><th>Qtà</th><th>Prezzo unit.</th><th>Totale</th></tr></thead>
        <tbody>
          @foreach($workOrder->items as $item)
          <tr>
            <td>{{ $item->description }}</td>
            <td><span class="badge badge-gray">{{ ucfirst($item->item_type) }}</span></td>
            <td>{{ $item->quantity }}</td>
            <td>€ {{ number_format($item->unit_price,2,',','.') }}</td>
            <td style="font-weight:500">€ {{ number_format($item->total_price,2,',','.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="text-align:right;padding:12px;font-size:16px;font-weight:500;color:var(--green)">Totale: € {{ number_format($workOrder->items->sum('total_price'),2,',','.') }}</div>
      @else
      <div style="color:var(--text3);font-size:13px">Nessuna voce inserita</div>
      @endif
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Aggiorna avanzamento</div>
      <form method="POST" action="{{ route('lavorazioni.progresso', $workOrder) }}">
        @csrf
        <div class="form-group"><label class="form-label">Avanzamento %</label><input type="range" name="progress" min="0" max="100" value="{{ $workOrder->progress_percent }}" oninput="this.nextElementSibling.textContent=this.value+'%'" style="width:100%;margin:8px 0"><span style="color:var(--text2);font-size:13px">{{ $workOrder->progress_percent }}%</span></div>
        <div class="form-group"><label class="form-label">Nuovo stato</label>
          <select name="status" class="form-select">
            @foreach(['attesa','in_lavorazione','attesa_ricambi','completato','consegnato','annullato'] as $st)
            <option value="{{ $st }}" {{ $workOrder->status === $st ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($st)) }}</option>
            @endforeach
          </select></div>
        <button type="submit" class="btn btn-primary" style="width:100%">Aggiorna</button>
      </form>
    </div>
    <div class="card">
      <div class="card-title">Note tecniche</div>
      <form method="POST" action="{{ route('lavorazioni.stato', $workOrder) }}">
        @csrf
        <textarea name="technical_notes" class="form-textarea" style="min-height:100px">{{ $workOrder->technical_notes }}</textarea>
        <button type="submit" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">Salva note</button>
      </form>
    </div>
  </div>
</div>
@endsection
