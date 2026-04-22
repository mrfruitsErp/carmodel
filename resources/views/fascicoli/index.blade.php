@extends('layouts.app')
@section('title', 'Fascicoli')

@section('topbar-actions')
<a href="{{ route('fascicoli.create') }}" class="btn btn-primary btn-sm">+ Nuovo Fascicolo</a>
@endsection

@section('content')

{{-- FILTRI --}}
<div class="card" style="margin-bottom:16px">
  <form method="GET" action="{{ route('fascicoli.index') }}" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
    <div class="form-group" style="flex:2;min-width:180px;margin-bottom:0">
      <label class="form-label">Cerca cliente</label>
      <input type="text" name="search" class="form-input" placeholder="Nome, cognome, ragione sociale..." value="{{ request('search') }}">
    </div>
    <div class="form-group" style="flex:1;min-width:140px;margin-bottom:0">
      <label class="form-label">Tipo pratica</label>
      <select name="tipo_pratica" class="form-select">
        <option value="">Tutti i tipi</option>
        @foreach($tipiPratica as $key => $label)
          <option value="{{ $key }}" {{ request('tipo_pratica') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group" style="flex:1;min-width:140px;margin-bottom:0">
      <label class="form-label">Stato</label>
      <select name="stato" class="form-select">
        <option value="">Tutti gli stati</option>
        @foreach($stati as $key => $info)
          <option value="{{ $key }}" {{ request('stato') == $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="btn btn-ghost btn-sm">Filtra</button>
    @if(request()->hasAny(['search','tipo_pratica','stato']))
      <a href="{{ route('fascicoli.index') }}" class="btn btn-ghost btn-sm">✕ Reset</a>
    @endif
  </form>
</div>

{{-- TABELLA --}}
<div class="card" style="padding:0">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between">
    <span class="card-title" style="margin-bottom:0">Fascicoli ({{ $fascicoli->total() }})</span>
  </div>
  <table>
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Tipo pratica</th>
        <th>Titolo</th>
        <th>Stato</th>
        <th>Progresso</th>
        <th>Link portale</th>
        <th>Data</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($fascicoli as $f)
      <tr onclick="location.href='{{ route('fascicoli.show', $f) }}'" style="cursor:pointer">
        <td>
          <div style="font-weight:500">
            {{ $f->cliente->display_name ?? ($f->cliente->nome . ' ' . $f->cliente->cognome) }}
          </div>
          <div style="font-size:11px;color:var(--text3)">
            {{ $f->cliente->tipo_soggetto === 'azienda' ? '🏢 Azienda' : '👤 Privato' }}
          </div>
        </td>
        <td>
          <span class="badge badge-blue">{{ $f->tipo_pratica_label }}</span>
        </td>
        <td style="color:var(--text2);font-size:12px">{{ $f->titolo ?? '—' }}</td>
        <td>
          @php $color = $f->stato_color; @endphp
          <span class="badge badge-{{ $color }}">{{ $f->stato_label }}</span>
        </td>
        <td>
          @php
            $totale = $f->documenti_count ?? 0;
            $prog = $f->progresso;
          @endphp
          <div style="display:flex;align-items:center;gap:6px">
            <div class="progress" style="width:60px">
              <div class="progress-fill" style="width:{{ $prog }}%;background:{{ $prog == 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
            </div>
            <span style="font-size:11px;color:var(--text3)">{{ $prog }}%</span>
          </div>
        </td>
        <td onclick="event.stopPropagation()">
          @if($f->tokenAttivo)
            <span class="badge badge-green" style="font-size:10px">✓ Attivo</span>
          @else
            <span style="color:var(--text3);font-size:11px">—</span>
          @endif
        </td>
        <td style="color:var(--text3);font-size:11px">{{ $f->created_at->format('d/m/Y') }}</td>
        <td onclick="event.stopPropagation()">
          <a href="{{ route('fascicoli.show', $f) }}" class="btn btn-ghost btn-sm">Apri</a>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:32px">Nessun fascicolo trovato</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($fascicoli->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border2)">
    {{ $fascicoli->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection