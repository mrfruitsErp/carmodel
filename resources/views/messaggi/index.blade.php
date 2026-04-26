@extends('layouts.app')
@section('title', 'Messaggi dal sito')

@section('content')

{{-- STAT CLICCABILI --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px">
  @foreach([
    ['Totale',     $stats['totale'],    'var(--blue)',   '',          ''],
    ['Non letti',  $stats['non_letti'], 'var(--orange)', 'non_letti', '1'],
    ['Nuovi',      $stats['nuove'],     'var(--green)',  'status',    'nuova'],
    ['Noleggio',   $stats['noleggio'],  'var(--purple)', 'type',      'noleggio'],
    ['Contatti',   $stats['contatti'],  'var(--amber)',  'type',      'contatto'],
  ] as [$label, $val, $color, $key, $value])
  <a href="{{ route('messaggi.index', $key && $value ? [$key=>$value] : []) }}" style="text-decoration:none">
    <div style="background:var(--bg2);border:1px solid {{ ($key && request($key)===$value) ? $color : 'var(--border2)' }};border-radius:10px;padding:12px 14px;position:relative;overflow:hidden;transition:.15s">
      <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $color }}"></div>
      <div style="font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px">{{ $label }}</div>
      <div style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--text);line-height:1">{{ $val }}</div>
    </div>
  </a>
  @endforeach
</div>

{{-- SEARCH --}}
<form method="GET" style="display:flex;gap:10px;margin-bottom:18px;align-items:center;flex-wrap:wrap">
  @foreach(request()->except(['search','page']) as $k=>$v)
    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
  @endforeach
  <div class="search-bar" style="max-width:320px">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email, telefono, messaggio...">
  </div>
  <button type="submit" class="btn btn-ghost btn-sm">Cerca</button>
  @if(request()->hasAny(['search','status','type','non_letti']))
    <a href="{{ route('messaggi.index') }}" class="btn btn-ghost btn-sm">✕ Reset</a>
  @endif
  <div style="margin-left:auto;font-size:12px;color:var(--text3)">{{ $messaggi->total() }} messaggi</div>
</form>

@if($messaggi->isEmpty())
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:12px;padding:60px 24px;text-align:center">
    <div style="font-size:48px;margin-bottom:12px">📭</div>
    <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px">Nessun messaggio</div>
    <div style="font-size:13px;color:var(--text3)">Quando un visitatore invia il form contatti o richiede info su un veicolo, il messaggio apparirà qui.</div>
  </div>
@else

<div class="card" style="padding:0">
<table>
  <thead>
    <tr>
      <th style="width:30px"></th>
      <th>Nome</th>
      <th>Tipo</th>
      <th>Messaggio</th>
      <th>Veicolo</th>
      <th>Ricevuto</th>
      <th>Stato</th>
    </tr>
  </thead>
  <tbody>
    @foreach($messaggi as $m)
    <tr onclick="location.href='{{ route('messaggi.show', $m) }}'" style="cursor:pointer;{{ $m->isNotLetto() ? 'background:rgba(255,107,0,.04)' : '' }}">
      <td style="text-align:center">
        @if($m->isNotLetto())
          <span style="display:inline-block;width:8px;height:8px;background:var(--orange);border-radius:50%" title="Non letto"></span>
        @endif
      </td>
      <td>
        <div style="font-weight:{{ $m->isNotLetto() ? '700' : '500' }};color:var(--text)">{{ $m->name }}</div>
        <div style="font-size:11px;color:var(--text3);font-family:var(--mono)">{{ $m->email }}</div>
        @if($m->phone)
          <div style="font-size:11px;color:var(--text3)">{{ $m->phone }}</div>
        @endif
      </td>
      <td>
        @php
          $typeColor = match($m->type) {
            'noleggio' => 'badge-blue',
            'contatto_veicolo' => 'badge-orange',
            'contatto' => 'badge-gray',
            default => 'badge-gray',
          };
        @endphp
        <span class="badge {{ $typeColor }}">{{ $m->tipo_label }}</span>
      </td>
      <td style="max-width:280px">
        <div style="font-size:12px;color:var(--text2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
          {{ Str::limit($m->message, 80) ?: '—' }}
        </div>
        @if($m->date_start && $m->date_end)
          <div style="font-size:11px;color:var(--text3);margin-top:2px">📅 {{ $m->date_start->format('d/m') }} → {{ $m->date_end->format('d/m/Y') }} ({{ $m->days }}gg)</div>
        @endif
      </td>
      <td>
        @if($m->fleetVehicle)
          <span style="font-size:12px">{{ $m->fleetVehicle->brand }} {{ $m->fleetVehicle->model }}</span>
          <div style="font-size:10px;color:var(--text3);font-family:var(--mono)">{{ $m->fleetVehicle->plate }}</div>
        @else
          <span style="color:var(--text3);font-size:12px">—</span>
        @endif
      </td>
      <td>
        <div style="font-size:12px;color:var(--text2)">{{ $m->created_at->diffForHumans() }}</div>
        <div style="font-size:10px;color:var(--text3)">{{ $m->created_at->format('d/m/Y H:i') }}</div>
      </td>
      <td>
        @php
          $statusCfg = match($m->status) {
            'nuova'      => ['cl'=>'badge-amber','lb'=>'Nuova'],
            'confermata' => ['cl'=>'badge-green','lb'=>'Confermata'],
            'rifiutata'  => ['cl'=>'badge-red','lb'=>'Rifiutata'],
            'annullata'  => ['cl'=>'badge-gray','lb'=>'Annullata'],
            default      => ['cl'=>'badge-gray','lb'=>$m->status],
          };
        @endphp
        <span class="badge {{ $statusCfg['cl'] }}">{{ $statusCfg['lb'] }}</span>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>

<div style="margin-top:16px">{{ $messaggi->links() }}</div>
@endif

@endsection
