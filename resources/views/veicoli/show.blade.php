@extends('layouts.app')
@section('title', $vehicle->plate.' — '.$vehicle->brand.' '.$vehicle->model)
@section('topbar-actions')
<a href="{{ route('veicoli.edit', ['veicoli' => $vehicle]) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('veicoli.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Veicoli</a></div>
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati veicolo</div>
      <div class="info-row"><span class="info-label">Targa</span><span class="info-value"><span class="targa">{{ $vehicle->plate }}</span></span></div>
      <div class="info-row"><span class="info-label">VIN / Telaio</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $vehicle->vin ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Marca / Modello</span><span class="info-value">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->version }}</span></div>
      <div class="info-row"><span class="info-label">Anno</span><span class="info-value">{{ $vehicle->year ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Colore</span><span class="info-value">{{ $vehicle->color ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Alimentazione</span><span class="info-value">{{ ucfirst($vehicle->fuel_type ?? '—') }}</span></div>
      <div class="info-row"><span class="info-label">Km attuali</span><span class="info-value">{{ $vehicle->km_current ? number_format($vehicle->km_current,0,',','.') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Proprietario</span><span class="info-value"><a href="{{ route('clienti.show', $vehicle->customer) }}" style="color:var(--green);text-decoration:none">{{ $vehicle->customer->display_name }}</a></span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge {{ $vehicle->status==='in_officina' ? 'badge-amber' : ($vehicle->status==='pronto' ? 'badge-green' : 'badge-gray') }}">{{ str_replace('_',' ',ucfirst($vehicle->status)) }}</span></span></div>
    </div>
    <div class="card">
      <div class="card-title">Assicurazione & Documenti</div>
      <div class="info-row"><span class="info-label">Compagnia</span><span class="info-value">{{ $vehicle->insurance_company ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">N° Polizza</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $vehicle->insurance_policy ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Scad. assicurazione</span><span class="info-value" style="color:{{ $vehicle->insurance_expiry && $vehicle->insurance_expiry->isPast() ? 'var(--red)' : 'var(--text)' }}">{{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Scad. revisione</span><span class="info-value" style="color:{{ $vehicle->isRevisionExpiringSoon() ? 'var(--amber)' : 'var(--text)' }}">{{ $vehicle->revision_expiry ? $vehicle->revision_expiry->format('d/m/Y') : '—' }}</span></div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Sinistri</div>
      @forelse($vehicle->claims->sortByDesc('event_date') as $c)
      <div class="tl-item">
        <div class="tl-dot {{ in_array($c->status,['chiuso','liquidato']) ? 'gray' : 'amber' }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('sinistri.show', $c) }}" style="color:var(--green);text-decoration:none">#{{ $c->claim_number }}</a></div>
          <div class="tl-meta">{{ $c->event_date->format('d/m/Y') }} · {{ str_replace('_',' ',ucfirst($c->status)) }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun sinistro</div>
      @endforelse
      <a href="{{ route('sinistri.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuovo sinistro</a>
    </div>
    <div class="card">
      <div class="card-title">Lavorazioni</div>
      @forelse($vehicle->workOrders->sortByDesc('created_at')->take(5) as $wo)
      <div class="tl-item">
        <div class="tl-dot {{ $wo->status==='completato' ? 'gray' : 'blue' }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('lavorazioni.show', $wo) }}" style="color:var(--green);text-decoration:none">#{{ $wo->job_number }}</a> — {{ ucfirst($wo->job_type) }}</div>
          <div class="tl-meta">{{ $wo->created_at->format('d/m/Y') }} · {{ str_replace('_',' ',ucfirst($wo->status)) }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessuna lavorazione</div>
      @endforelse
      <a href="{{ route('lavorazioni.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuova lavorazione</a>
    </div>
  </div>
</div>
@endsection
