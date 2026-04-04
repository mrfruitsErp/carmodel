@extends('layouts.app')
@section('title', 'Contratto #'.$noleggio->rental_number)
@section('topbar-actions')
@if($noleggio->status === 'attivo')
<button onclick="document.getElementById('modal-chiudi').style.display='flex'" class="btn btn-danger btn-sm">Chiudi contratto</button>
@endif
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('noleggio.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Noleggi</a></div>
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati contratto <span style="font-family:var(--mono);font-size:11px;color:var(--text3)">#{{ $noleggio->rental_number }}</span></div>
      <div class="info-row"><span class="info-label">Cliente</span><span class="info-value"><a href="{{ route('clienti.show', $noleggio->customer) }}" style="color:var(--green);text-decoration:none">{{ $noleggio->customer->display_name }}</a></span></div>
      <div class="info-row"><span class="info-label">Veicolo</span><span class="info-value"><span class="targa">{{ $noleggio->fleetVehicle->plate }}</span> {{ $noleggio->fleetVehicle->brand }} {{ $noleggio->fleetVehicle->model }}</span></div>
      <div class="info-row"><span class="info-label">Tipo</span><span class="info-value"><span class="badge badge-blue">{{ str_replace('_',' ',ucfirst($noleggio->rental_type)) }}</span></span></div>
      @if($noleggio->claim)<div class="info-row"><span class="info-label">Sinistro</span><span class="info-value"><a href="{{ route('sinistri.show', $noleggio->claim) }}" style="color:var(--green);text-decoration:none">#{{ $noleggio->claim->claim_number }}</a></span></div>@endif
      <div class="info-row"><span class="info-label">Inizio</span><span class="info-value">{{ $noleggio->start_date->format('d/m/Y') }}</span></div>
      <div class="info-row"><span class="info-label">Fine prevista</span><span class="info-value" style="color:{{ $noleggio->isOverdue() ? 'var(--red)' : 'var(--text)' }}">{{ $noleggio->expected_end_date->format('d/m/Y') }} {{ $noleggio->isOverdue() ? '⚠' : '' }}</span></div>
      <div class="info-row"><span class="info-label">Km partenza</span><span class="info-value">{{ number_format($noleggio->km_start,0,',','.') }}</span></div>
      <div class="info-row"><span class="info-label">Carburante partenza</span><span class="info-value">{{ $noleggio->fuel_level_start }}%</span></div>
      <div class="info-row"><span class="info-label">Tariffa</span><span class="info-value">{{ $noleggio->daily_rate > 0 ? '€ '.$noleggio->daily_rate.'/gg' : 'Gratuito' }}</span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge {{ $noleggio->status === 'attivo' ? 'badge-amber' : ($noleggio->status === 'chiuso' ? 'badge-green' : 'badge-red') }}">{{ ucfirst($noleggio->status) }}</span></span></div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Note condizioni</div>
      @if($noleggio->damage_notes_start)<div class="info-row"><span class="info-label">Danni alla consegna</span><span class="info-value">{{ $noleggio->damage_notes_start }}</span></div>@endif
      @if($noleggio->damage_notes_end)<div class="info-row"><span class="info-label">Danni alla restituzione</span><span class="info-value">{{ $noleggio->damage_notes_end }}</span></div>@endif
      @if($noleggio->notes)<div style="margin-top:10px;font-size:13px;color:var(--text2)">{{ $noleggio->notes }}</div>@endif
    </div>
    @if($noleggio->status === 'chiuso')
    <div class="card">
      <div class="card-title">Restituzione</div>
      <div class="info-row"><span class="info-label">Data restituzione</span><span class="info-value">{{ $noleggio->actual_end_date ? $noleggio->actual_end_date->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Km restituzione</span><span class="info-value">{{ $noleggio->km_end ? number_format($noleggio->km_end,0,',','.') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Carburante</span><span class="info-value">{{ $noleggio->fuel_level_end ? $noleggio->fuel_level_end.'%' : '—' }}</span></div>
    </div>
    @endif
  </div>
</div>
{{-- MODAL CHIUDI --}}
<div id="modal-chiudi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:24px;width:400px">
    <div style="font-size:15px;font-weight:600;margin-bottom:16px">Chiudi contratto</div>
    <form method="POST" action="{{ route('noleggio.chiudi', $noleggio) }}">
      @csrf
      <div class="form-group"><label class="form-label">Km alla restituzione</label><input name="km_end" type="number" class="form-input" value="{{ $noleggio->km_start }}"></div>
      <div class="form-group"><label class="form-label">Carburante restituzione (%)</label><input name="fuel_level_end" type="number" min="0" max="100" class="form-input" value="{{ $noleggio->fuel_level_start }}"></div>
      <div class="form-group"><label class="form-label">Note danni restituzione</label><textarea name="damage_notes_end" class="form-textarea"></textarea></div>
      <div style="display:flex;gap:8px">
        <button type="button" onclick="document.getElementById('modal-chiudi').style.display='none'" class="btn btn-ghost" style="flex:1">Annulla</button>
        <button type="submit" class="btn btn-primary" style="flex:1">Chiudi contratto</button>
      </div>
    </form>
  </div>
</div>
@endsection
