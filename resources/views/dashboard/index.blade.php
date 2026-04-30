@extends('layouts.app')
@section('title', 'Dashboard')

@section('topbar-actions')
<a href="{{ route('sinistri.create') }}" class="btn btn-primary btn-sm">+ Nuovo Sinistro</a>
@endsection

@section('content')

@if($kpi['sinistri_urgenti'] > 0 || $kpi['lavorazioni_ritardo'] > 0)
<div class="alert alert-amber">
  <span>⚠</span>
  <span>
    @if($kpi['sinistri_urgenti'] > 0)<strong>{{ $kpi['sinistri_urgenti'] }} sinistri</strong> in scadenza entro 7 giorni &nbsp;·&nbsp; @endif
    @if($kpi['lavorazioni_ritardo'] > 0)<strong>{{ $kpi['lavorazioni_ritardo'] }} lavorazioni</strong> in ritardo @endif
  </span>
</div>
@endif

<div class="stat-grid">
  <div class="stat-card green">
    <div class="stat-label">Sinistri aperti</div>
    <div class="stat-value">{{ $kpi['sinistri_aperti'] }}</div>
    <div class="stat-sub">{{ $kpi['sinistri_urgenti'] }} in scadenza</div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label">Lavorazioni attive</div>
    <div class="stat-value">{{ $kpi['lavorazioni_attive'] }}</div>
    <div class="stat-sub">{{ $kpi['lavorazioni_ritardo'] }} in ritardo</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-label">Fatturato mese</div>
    <div class="stat-value">€ {{ number_format($kpi['fatturato_mese'], 0, ',', '.') }}</div>
    <div class="stat-sub">Incassato questo mese</div>
  </div>
  <div class="stat-card purple">
    <div class="stat-label">Auto noleggiate</div>
    <div class="stat-value">{{ $kpi['auto_noleggiate'] }} / {{ $kpi['auto_noleggiate'] + $kpi['auto_disponibili'] }}</div>
    <div class="stat-sub">{{ $kpi['auto_disponibili'] }} disponibili</div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Lesioni aperte</div>
    <div class="stat-value">{{ $kpi['lesioni_aperte'] }}</div>
    <div class="stat-sub">Pratiche in corso</div>
  </div>
</div>

<div class="main-side">
  <div>
    <div class="card">
      <div class="section-header">
        <span class="card-title" style="margin-bottom:0">Sinistri urgenti</span>
        <a href="{{ route('sinistri.index') }}?filter=urgenti" class="btn btn-ghost btn-sm">Vedi tutti</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>N° Sinistro</th>
            <th>Cliente</th>
            <th>Targa</th>
            <th>Compagnia</th>
            <th>Stato</th>
            <th>Scad. CID</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sinistri_urgenti as $s)
          <tr onclick="location.href='{{ route('sinistri.show', $s) }}'" style="cursor:pointer">
            <td><span style="font-family:var(--mono);font-size:11px;color:var(--green)">#{{ $s->claim_number }}</span></td>
            <td><strong>{{ $s->customer->display_name }}</strong></td>
            <td><span class="targa">{{ $s->vehicle->plate }}</span></td>
            <td>{{ $s->insuranceCompany?->name ?? '—' }}</td>
            <td>
              @php
              $badgeMap = [
                'aperto' => 'badge-blue',
                'cid_presentato' => 'badge-blue',
                'perizia_attesa' => 'badge-amber',
                'in_riparazione' => 'badge-teal',
                'liquidato' => 'badge-green',
                'contestato' => 'badge-red',
              ];
              $badge = $badgeMap[$s->status] ?? 'badge-gray';
              @endphp
              <span class="badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($s->status)) }}</span>
            </td>
            <td style="color:{{ $s->isOverdue() ? 'var(--red)' : 'var(--amber)' }};font-weight:500">
              {{ $s->cid_expiry ? $s->cid_expiry->format('d/m/Y') : '—' }}
              @if($s->isOverdue()) ⚠ @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:20px">Nessun sinistro urgente</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="section-header">
        <span class="card-title" style="margin-bottom:0">Lavorazioni in corso</span>
        <a href="{{ route('lavorazioni.index') }}" class="btn btn-ghost btn-sm">Vedi tutte</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>Commessa</th>
            <th>Veicolo</th>
            <th>Tipo</th>
            <th>Stato</th>
            <th>Avanz.</th>
            <th>Scadenza</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lavorazioni as $l)
          <tr onclick="location.href='{{ route('lavorazioni.show', $l) }}'" style="cursor:pointer">
            <td><span style="font-family:var(--mono);font-size:11px;color:var(--teal)">#{{ $l->job_number }}</span></td>
            <td><span class="targa">{{ $l->vehicle->plate }}</span></td>
            <td><span class="badge badge-teal">{{ ucfirst($l->job_type) }}</span></td>
            <td>
              @if($l->isOverdue())
                <span class="badge badge-red">In ritardo</span>
              @elseif($l->status === 'in_lavorazione')
                <span class="badge badge-amber">In lavorazione</span>
              @else
                <span class="badge badge-blue">{{ str_replace('_', ' ', ucfirst($l->status)) }}</span>
              @endif
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <span style="font-size:11px;color:var(--text3);min-width:28px">{{ $l->progress_percent }}%</span>
                <div class="progress" style="width:70px">
                  <div class="progress-fill" style="width:{{ $l->progress_percent }}%;background:{{ $l->isOverdue() ? 'var(--red)' : 'var(--green)' }}"></div>
                </div>
              </div>
            </td>
            <td style="color:{{ $l->isOverdue() ? 'var(--red)' : 'var(--text2)' }}">
              {{ $l->expected_end_date ? $l->expected_end_date->format('d/m') : '—' }}
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:20px">Nessuna lavorazione attiva</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Auto sostitutive attive</div>
      @forelse($sostitutive as $r)
      <div class="fleet-item">
        <div class="fleet-status {{ $r->isOverdue() ? 'red' : 'amber' }}"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">
            {{ $r->fleetVehicle->brand }} {{ $r->fleetVehicle->model }}
            <span class="targa" style="font-size:11px">{{ $r->fleetVehicle->plate }}</span>
          </div>
          <div style="font-size:11px;color:var(--text2)">{{ $r->customer->display_name }}</div>
          <div style="font-size:11px;color:{{ $r->isOverdue() ? 'var(--red)' : 'var(--amber)' }}">
            {{ $r->isOverdue() ? 'Scaduta' : 'Scade' }} {{ $r->expected_end_date->format('d/m/Y') }}
          </div>
        </div>
      </div>
      @empty
      <div style="text-align:center;color:var(--text3);padding:16px;font-size:13px">Nessuna sostitutiva attiva</div>
      @endforelse
    </div>

    <div class="card">
      <div class="section-header">
        <span class="card-title" style="margin-bottom:0">🚗 Movimenti Oggi</span>
        <a href="{{ route('movimenti.calendario') }}" class="btn btn-ghost btn-sm">Calendario</a>
      </div>
      @forelse($movimenti_oggi ?? [] as $m)
      <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid var(--border1)">
        <div style="min-width:44px;text-align:center">
          <div style="font-size:16px">{{ $m->tipo_icon }}</div>
          <div style="font-size:10px;color:var(--text3)">{{ $m->data_inizio->format('H:i') }}</div>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $m->titolo ?: $m->tipo_label }}
          </div>
          <div style="font-size:11px;color:var(--text2)">{{ $m->veicolo_label }}</div>
          @if($m->luogo_partenza || $m->luogo_arrivo)
          <div style="font-size:11px;color:var(--text3)">
            @if($m->luogo_partenza) 📍{{ $m->luogo_partenza }} @endif
            @if($m->luogo_arrivo) → 🏁{{ $m->luogo_arrivo }} @endif
          </div>
          @endif
        </div>
        <span class="badge badge-{{ $m->stato_color }}" style="font-size:10px;white-space:nowrap">{{ $m->stato_label }}</span>
      </div>
      @empty
      <div style="text-align:center;color:var(--text3);padding:16px;font-size:13px">Nessun movimento programmato oggi</div>
      @endforelse
      <a href="{{ route('movimenti.create') }}" class="btn btn-ghost btn-sm" style="width:100%;margin-top:10px;text-align:center">+ Nuovo movimento</a>
    </div>

    <div class="card">
      <div class="card-title">Fatturato per tipo (30gg)</div>
      @php
      $tipi = ['carrozzeria' => 'Carrozzeria', 'meccanica' => 'Meccanica', 'detailing' => 'Detailing', 'tagliando' => 'Tagliando'];
      $max = $fatturato_tipo->max() ?: 1;
      @endphp
      @foreach($tipi as $key => $label)
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
          <span style="color:var(--text2)">{{ $label }}</span>
          <span style="font-weight:500">€ {{ number_format($fatturato_tipo[$key] ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="progress">
          <div class="progress-fill" style="width:{{ round(($fatturato_tipo[$key] ?? 0) / $max * 100) }}%"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection