@extends('layouts.app')
@section('title', 'Compagnie Assicurative')
@section('topbar-actions')
<a href="{{ route('assicurazioni.create') }}" class="btn btn-primary btn-sm">+ Nuova Compagnia</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar">
    <span style="color:var(--text3)">⌕</span>
    <input name="search" placeholder="Nome, codice, email..." value="{{ request('search') }}">
  </div>
  <span style="font-size:12px;color:var(--text3);margin-left:auto">{{ $totale }} compagnie totali</span>
</form>
<div class="card" style="padding:0">
  <table>
    <thead>
      <tr>
        <th>Compagnia</th>
        <th>Codice</th>
        <th>Email</th>
        <th>Telefono</th>
        <th>Referente</th>
        <th>Stato</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($compagnie as $c)
      <tr onclick="location.href='{{ route('assicurazioni.show', $c) }}'" style="cursor:pointer">
        <td>
          <div style="font-weight:600">{{ $c->name }}</div>
          @if($c->piva)<div style="font-size:11px;color:var(--text3)">P.IVA {{ $c->piva }}</div>@endif
        </td>
        <td><span class="targa">{{ $c->code ?? '—' }}</span></td>
        <td style="font-size:12px;color:var(--text2)">{{ $c->email ?? '—' }}</td>
        <td style="font-size:12px;color:var(--text2)">{{ $c->phone ?? '—' }}</td>
        <td style="font-size:12px;color:var(--text2)">{{ $c->referente ?? '—' }}</td>
        <td><span class="badge {{ $c->active ? 'badge-green' : 'badge-gray' }}">{{ $c->active ? 'Attiva' : 'Inattiva' }}</span></td>
        <td>
          <a href="{{ route('assicurazioni.edit', $c) }}" class="btn btn-ghost btn-sm" onclick="event.stopPropagation()">Modifica</a>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:40px">Nessuna compagnia</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $compagnie->appends(request()->query())->links('vendor.pagination.custom') }}
@endsection