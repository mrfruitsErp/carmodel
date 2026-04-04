@extends('layouts.app')
@section('title', 'Auto Sostitutive')
@section('topbar-actions')
<a href="{{ route('noleggio.create') }}?tipo=sostitutiva" class="btn btn-primary btn-sm">+ Assegna Sostitutiva</a>
@endsection
@section('content')

@if($scadute->count())
<div class="alert alert-red">
  <span>⚠</span>
  <span><strong>{{ $scadute->count() }} auto sostitutive</strong> con contratto scaduto — contattare i clienti immediatamente.</span>
</div>
@endif

<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Assegnazioni attive</div>
      @forelse($attive as $r)
      <div class="fleet-item" style="{{ $r->isOverdue() ? 'border-color:var(--red)' : '' }}">
        <div class="fleet-status {{ $r->isOverdue() ? 'red' : ($r->expected_end_date->diffInDays(now()) <= 2 ? 'amber' : 'green') }}"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $r->fleetVehicle->brand }} {{ $r->fleetVehicle->model }} <span class="targa" style="font-size:11px">{{ $r->fleetVehicle->plate }}</span></div>
          <div style="font-size:12px;color:var(--text2)">{{ $r->customer->display_name }} @if($r->claim) · <a href="{{ route('sinistri.show', $r->claim) }}" style="color:var(--green);text-decoration:none">#{{ $r->claim->claim_number }}</a> @endif</div>
          <div style="font-size:11px;display:flex;gap:10px;margin-top:3px">
            <span style="color:var(--text3)">Dal {{ $r->start_date->format('d/m') }}</span>
            <span style="color:{{ $r->isOverdue() ? 'var(--red)' : 'var(--amber)' }}">{{ $r->isOverdue() ? 'Scaduta' : 'Fino' }} {{ $r->expected_end_date->format('d/m/Y') }}</span>
          </div>
        </div>
        <a href="{{ route('noleggio.show', $r) }}" class="btn btn-ghost btn-sm">Dettagli</a>
      </div>
      @empty
      <div style="text-align:center;color:var(--text3);padding:20px;font-size:13px">Nessuna sostitutiva attiva</div>
      @endforelse
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Disponibili ora</div>
      @forelse($disponibili as $v)
      <div class="fleet-item">
        <div class="fleet-status green"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $v->brand }} {{ $v->model }} <span class="targa" style="font-size:11px">{{ $v->plate }}</span></div>
          <div style="font-size:11px;color:var(--text3)">Cat. {{ $v->category }} · {{ number_format($v->km_current,0,',','.') }} km</div>
        </div>
        <a href="{{ route('noleggio.create') }}?fleet_vehicle_id={{ $v->id }}&tipo=sostitutiva" class="btn btn-primary btn-sm">Assegna</a>
      </div>
      @empty
      <div style="text-align:center;color:var(--text3);padding:20px;font-size:13px">Nessun veicolo disponibile</div>
      @endforelse
    </div>

    @if($scadute->count())
    <div class="card" style="border-color:var(--red)">
      <div class="card-title" style="color:var(--red)">⚠ Contratti scaduti</div>
      @foreach($scadute as $r)
      <div class="fleet-item" style="border-color:var(--red)">
        <div class="fleet-status red"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $r->fleetVehicle->brand }} {{ $r->fleetVehicle->model }}</div>
          <div style="font-size:11px;color:var(--red)">{{ $r->customer->display_name }} · Scaduta {{ $r->expected_end_date->format('d/m/Y') }}</div>
        </div>
        <a href="{{ route('noleggio.show', $r) }}" class="btn btn-danger btn-sm">Chiudi</a>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</div>
@endsection
