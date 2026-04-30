@extends('layouts.app')
@section('title', 'Movimenti Veicoli')

@section('topbar-actions')
<a href="{{ route('movimenti.calendario') }}" class="btn btn-ghost btn-sm">📅 Calendario</a>
<a href="{{ route('movimenti.create') }}" class="btn btn-primary btn-sm">+ Nuovo Movimento</a>
@endsection

@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:20px">
  <div class="card" style="padding:16px;text-align:center">
    <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.05em">Oggi</div>
    <div style="font-size:28px;font-weight:700;color:var(--orange)">{{ $stats['oggi'] }}</div>
  </div>
  <div class="card" style="padding:16px;text-align:center">
    <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.05em">In Corso</div>
    <div style="font-size:28px;font-weight:700;color:#f59e0b">{{ $stats['in_corso'] }}</div>
  </div>
  <div class="card" style="padding:16px;text-align:center">
    <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.05em">Programmati</div>
    <div style="font-size:28px;font-weight:700;color:#3b82f6">{{ $stats['programmati'] }}</div>
  </div>
  <div class="card" style="padding:16px;text-align:center">
    <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.05em">In Ritardo</div>
    <div style="font-size:28px;font-weight:700;color:#ef4444">{{ $stats['in_ritardo'] }}</div>
  </div>
</div>

{{-- Filtri --}}
<div class="card" style="padding:16px;margin-bottom:16px">
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:180px">
      <label class="form-label">Cerca</label>
      <input type="text" name="search" class="form-input" value="{{ request('search') }}" placeholder="Titolo, luogo, cliente...">
    </div>
    <div style="min-width:160px">
      <label class="form-label">Tipo</label>
      <select name="tipo" class="form-input">
        <option value="">Tutti i tipi</option>
        @foreach($tipi as $k => $t)
          <option value="{{ $k }}" {{ request('tipo') == $k ? 'selected' : '' }}>{{ $t['icon'] }} {{ $t['label'] }}</option>
        @endforeach
      </select>
    </div>
    <div style="min-width:140px">
      <label class="form-label">Stato</label>
      <select name="stato" class="form-input">
        <option value="">Tutti gli stati</option>
        @foreach($stati as $k => $s)
          <option value="{{ $k }}" {{ request('stato') == $k ? 'selected' : '' }}>{{ $s['label'] }}</option>
        @endforeach
      </select>
    </div>
    <div style="min-width:150px">
      <label class="form-label">Data</label>
      <input type="date" name="data" class="form-input" value="{{ request('data') }}">
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" class="btn btn-primary btn-sm">Filtra</button>
      <a href="{{ route('movimenti.index') }}" class="btn btn-ghost btn-sm">Reset</a>
    </div>
  </form>
</div>

{{-- Lista --}}
<div class="card">
  @if($movimenti->isEmpty())
    <div style="text-align:center;padding:48px;color:var(--text3)">
      <div style="font-size:40px;margin-bottom:12px">🗂️</div>
      <div>Nessun movimento trovato</div>
      <a href="{{ route('movimenti.create') }}" class="btn btn-primary btn-sm" style="margin-top:12px">+ Crea il primo</a>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th>Data / Ora</th>
          <th>Tipo</th>
          <th>Veicolo</th>
          <th>Da → A</th>
          <th>Cliente</th>
          <th>Operatore</th>
          <th>Stato</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($movimenti as $m)
        <tr>
          <td style="white-space:nowrap">
            <div style="font-weight:600">{{ $m->data_inizio->format('d/m/Y') }}</div>
            <div style="font-size:12px;color:var(--text3)">{{ $m->data_inizio->format('H:i') }}
              @if($m->data_fine) → {{ $m->data_fine->format('H:i') }} @endif
            </div>
          </td>
          <td>
            <span class="badge badge-{{ $m->tipo_color }}" style="font-size:11px">
              {{ $m->tipo_icon }} {{ $m->tipo_label }}
            </span>
            @if($m->titolo)
              <div style="font-size:11px;color:var(--text3);margin-top:3px">{{ $m->titolo }}</div>
            @endif
          </td>
          <td style="font-size:13px">{{ $m->veicolo_label }}</td>
          <td style="font-size:12px">
            @if($m->luogo_partenza)
              <div>📍 {{ $m->luogo_partenza }}</div>
            @endif
            @if($m->luogo_arrivo)
              <div style="color:var(--orange)">🏁 {{ $m->luogo_arrivo }}</div>
            @endif
          </td>
          <td style="font-size:13px">{{ $m->cliente?->display_name ?? '—' }}</td>
          <td style="font-size:12px;color:var(--text2)">{{ $m->operatore?->name ?? '—' }}</td>
          <td>
            <span class="badge badge-{{ $m->stato_color }}" style="font-size:11px">{{ $m->stato_label }}</span>
          </td>
          <td style="text-align:right">
            <a href="{{ route('movimenti.show', $m) }}" class="btn btn-ghost btn-sm">Dettaglio →</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div style="padding:16px">{{ $movimenti->links() }}</div>
  @endif
</div>

@endsection
