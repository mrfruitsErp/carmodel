@extends('layouts.app')
@section('title', 'Movimento — ' . ($movimento->titolo ?: $movimento->tipo_label))

@section('topbar-actions')
<a href="{{ route('movimenti.index') }}" class="btn btn-ghost btn-sm">← Movimenti</a>
<a href="{{ route('movimenti.edit', $movimento) }}" class="btn btn-ghost btn-sm">✏ Modifica</a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start">

{{-- SINISTRA --}}
<div style="display:flex;flex-direction:column;gap:16px">

  {{-- Header --}}
  <div class="card" style="padding:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <div style="font-size:18px;font-weight:700">
          {{ $movimento->tipo_icon }} {{ $movimento->titolo ?: $movimento->tipo_label }}
        </div>
        <div style="font-size:12px;color:var(--text3);margin-top:4px">
          {{ $movimento->data_inizio->format('d/m/Y H:i') }}
          @if($movimento->data_fine) → {{ $movimento->data_fine->format('d/m/Y H:i') }} @endif
        </div>
      </div>
      <span class="badge badge-{{ $movimento->stato_color }}" style="font-size:13px;padding:6px 14px">
        {{ $movimento->stato_label }}
      </span>
    </div>
  </div>

  {{-- Rotta: Partenza → Arrivo --}}
  <div class="card" style="padding:20px">
    <div class="form-section-title">Itinerario</div>
    <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:12px;align-items:center">
      <div style="background:var(--bg2);border-radius:var(--radius);padding:14px">
        <div style="font-size:11px;color:var(--text3);margin-bottom:4px;text-transform:uppercase">📍 Partenza</div>
        <div style="font-weight:600;font-size:14px">{{ $movimento->luogo_partenza ?: '—' }}</div>
        @if($movimento->indirizzo_partenza)
          <div style="font-size:12px;color:var(--text2);margin-top:3px">{{ $movimento->indirizzo_partenza }}</div>
        @endif
        @if($movimento->km_partenza)
          <div style="font-size:11px;color:var(--text3);margin-top:6px">🛣 {{ number_format($movimento->km_partenza) }} km</div>
        @endif
      </div>
      <div style="font-size:24px;color:var(--orange)">→</div>
      <div style="background:var(--bg2);border-radius:var(--radius);padding:14px">
        <div style="font-size:11px;color:var(--text3);margin-bottom:4px;text-transform:uppercase">🏁 Arrivo / Destinazione</div>
        <div style="font-weight:600;font-size:14px">{{ $movimento->luogo_arrivo ?: '—' }}</div>
        @if($movimento->indirizzo_arrivo)
          <div style="font-size:12px;color:var(--text2);margin-top:3px">{{ $movimento->indirizzo_arrivo }}</div>
        @endif
        @if($movimento->km_arrivo)
          <div style="font-size:11px;color:var(--text3);margin-top:6px">🛣 {{ number_format($movimento->km_arrivo) }} km</div>
        @endif
      </div>
    </div>
    @if($movimento->km_partenza && $movimento->km_arrivo)
      <div style="margin-top:10px;font-size:13px;color:var(--text2);text-align:right">
        Percorso: <strong>{{ number_format($movimento->km_arrivo - $movimento->km_partenza) }} km</strong>
      </div>
    @endif
  </div>

  {{-- Veicolo --}}
  <div class="card" style="padding:20px">
    <div class="form-section-title">Veicolo</div>
    <div style="font-size:15px;font-weight:600">{{ $movimento->veicolo_label }}</div>
    @if($movimento->fleetVehicle)
      <a href="{{ route('flotta.show', $movimento->fleetVehicle) }}" class="btn btn-ghost btn-sm" style="margin-top:8px">Apri scheda veicolo →</a>
    @elseif($movimento->saleVehicle)
      <a href="{{ route('vendita.show', $movimento->saleVehicle) }}" class="btn btn-ghost btn-sm" style="margin-top:8px">Apri scheda veicolo →</a>
    @elseif($movimento->vehicle)
      <a href="{{ route('veicoli.show', $movimento->vehicle) }}" class="btn btn-ghost btn-sm" style="margin-top:8px">Apri scheda veicolo →</a>
    @endif
  </div>

  {{-- Note --}}
  @if($movimento->note)
  <div class="card" style="padding:20px">
    <div class="form-section-title">Note</div>
    <div style="font-size:13px;color:var(--text2);white-space:pre-wrap">{{ $movimento->note }}</div>
  </div>
  @endif

  {{-- Cambia stato --}}
  <div class="card" style="padding:20px">
    <div class="form-section-title">Aggiorna Stato</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      @foreach(\App\Models\VehicleMovement::stati() as $k => $s)
        @if($k !== $movimento->stato)
          <form method="POST" action="{{ route('movimenti.stato', $movimento) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="stato" value="{{ $k }}">
            <button type="submit" class="btn btn-ghost btn-sm">→ {{ $s['label'] }}</button>
          </form>
        @endif
      @endforeach
    </div>
  </div>

</div>

{{-- DESTRA --}}
<div style="display:flex;flex-direction:column;gap:16px">

  {{-- Persone --}}
  <div class="card" style="padding:16px">
    <div class="form-section-title">Persone</div>
    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px">
      <div>
        <span style="color:var(--text3)">Cliente</span><br>
        @if($movimento->cliente)
          <a href="{{ route('clienti.show', $movimento->cliente) }}" style="color:var(--orange);font-weight:600">
            {{ $movimento->cliente->display_name }}
          </a>
        @else
          <span style="color:var(--text3)">—</span>
        @endif
      </div>
      <div>
        <span style="color:var(--text3)">Operatore</span><br>
        <strong>{{ $movimento->operatore?->name ?? '—' }}</strong>
      </div>
      <div>
        <span style="color:var(--text3)">Autista</span><br>
        <strong>{{ $movimento->autista?->name ?? '—' }}</strong>
      </div>
    </div>
  </div>

  {{-- Collegamento pratiche --}}
  @if($movimento->rental || $movimento->workOrder || $movimento->claim || $movimento->fascicolo)
  <div class="card" style="padding:16px">
    <div class="form-section-title">Pratiche Collegate</div>
    <div style="display:flex;flex-direction:column;gap:6px;font-size:12px">
      @if($movimento->rental)
        <a href="{{ route('noleggio.show', $movimento->rental) }}" style="color:var(--orange)">🔑 Noleggio {{ $movimento->rental->rental_number }}</a>
      @endif
      @if($movimento->workOrder)
        <a href="{{ route('lavorazioni.show', $movimento->workOrder) }}" style="color:var(--orange)">🔧 {{ $movimento->workOrder->job_number }}</a>
      @endif
      @if($movimento->claim)
        <a href="{{ route('sinistri.show', $movimento->claim) }}" style="color:var(--orange)">⚠️ {{ $movimento->claim->claim_number }}</a>
      @endif
      @if($movimento->fascicolo)
        <a href="{{ route('fascicoli.show', $movimento->fascicolo) }}" style="color:var(--orange)">📁 Fascicolo #{{ $movimento->fascicolo->id }}</a>
      @endif
    </div>
  </div>
  @endif

  {{-- Meta --}}
  <div class="card" style="padding:16px">
    <div class="form-section-title">Info</div>
    <div style="font-size:12px;color:var(--text3);display:flex;flex-direction:column;gap:4px">
      <div>Creato il {{ $movimento->created_at->format('d/m/Y H:i') }}</div>
      @if($movimento->createdBy)
        <div>Da {{ $movimento->createdBy->name }}</div>
      @endif
    </div>
  </div>

  {{-- Elimina --}}
  <form method="POST" action="{{ route('movimenti.destroy', $movimento) }}"
    onsubmit="return confirm('Eliminare questo movimento?')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;color:#ef4444">🗑 Elimina</button>
  </form>

</div>
</div>
@endsection
