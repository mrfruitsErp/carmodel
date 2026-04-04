@extends('layouts.app')
@section('title', $vehicle->plate.' — '.$vehicle->brand.' '.$vehicle->model)
@section('topbar-actions')
<a href="{{ route('flotta.edit', $vehicle) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('flotta.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Flotta</a></div>
<div class="two-col">
  <div>
    <div class="card">
      <div class="info-row"><span class="info-label">Targa</span><span class="info-value"><span class="targa">{{ $vehicle->plate }}</span></span></div>
      <div class="info-row"><span class="info-label">Marca / Modello</span><span class="info-value">{{ $vehicle->brand }} {{ $vehicle->model }}</span></div>
      <div class="info-row"><span class="info-label">Anno</span><span class="info-value">{{ $vehicle->year }}</span></div>
      <div class="info-row"><span class="info-label">Categoria</span><span class="info-value"><span class="badge badge-gray">{{ $vehicle->category }}</span></span></div>
      <div class="info-row"><span class="info-label">Km attuali</span><span class="info-value">{{ number_format($vehicle->km_current,0,',','.') }}</span></div>
      <div class="info-row"><span class="info-label">Tariffa</span><span class="info-value">€ {{ $vehicle->daily_rate }}/gg</span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge {{ $vehicle->status === 'disponibile' ? 'badge-green' : ($vehicle->status === 'manutenzione' ? 'badge-red' : 'badge-amber') }}">{{ ucfirst($vehicle->status) }}</span></span></div>
      <div class="info-row"><span class="info-label">Revisione</span><span class="info-value" style="color:{{ $vehicle->revision_expiry && $vehicle->revision_expiry->isPast() ? 'var(--red)' : 'var(--text)' }}">{{ $vehicle->revision_expiry ? $vehicle->revision_expiry->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Assicurazione</span><span class="info-value">{{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : '—' }}</span></div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Noleggi recenti</div>
      @forelse($vehicle->rentals->sortByDesc('created_at')->take(5) as $r)
      <div class="tl-item">
        <div class="tl-dot {{ $r->status === 'chiuso' ? 'gray' : 'amber' }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('noleggio.show', $r) }}" style="color:var(--green);text-decoration:none">#{{ $r->rental_number }}</a> — {{ $r->customer->display_name }}</div>
          <div class="tl-meta">{{ $r->start_date->format('d/m') }} → {{ $r->expected_end_date->format('d/m/Y') }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun noleggio registrato</div>
      @endforelse
      <a href="{{ route('noleggio.create') }}?fleet_vehicle_id={{ $vehicle->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuovo noleggio</a>
    </div>
  </div>
</div>
@endsection
