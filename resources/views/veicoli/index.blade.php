@extends('layouts.app')
@section('title', 'Veicoli')
@section('topbar-actions')
<a href="{{ route('veicoli.create') }}" class="btn btn-primary btn-sm">+ Nuovo Veicolo</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Targa, VIN, marca, modello..." value="{{ request('search') }}"></div>
  <a href="{{ route('veicoli.index') }}" class="chip {{ !request('status') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('veicoli.index') }}?status=in_officina" class="chip {{ request('status')==='in_officina' ? 'active' : '' }}">In officina</a>
  <a href="{{ route('veicoli.index') }}?status=pronto" class="chip {{ request('status')==='pronto' ? 'active' : '' }}">Pronti</a>
</form>
<div class="card" style="padding:0">
<table>
  <thead><tr><th>Targa</th><th>Veicolo</th><th>Anno</th><th>VIN</th><th>Proprietario</th><th>Km</th><th>Stato</th><th>Sinistri</th></tr></thead>
  <tbody>
    @forelse($veicoli as $v)
    <tr onclick="location.href='{{ route('veicoli.show', $v) }}'" style="cursor:pointer">
      <td><span class="targa">{{ $v->plate }}</span></td>
      <td><strong>{{ $v->brand }} {{ $v->model }}</strong></td>
      <td>{{ $v->year ?? '—' }}</td>
      <td style="font-family:var(--mono);font-size:11px;color:var(--text3)">{{ $v->vin ?? '—' }}</td>
      <td>{{ $v->customer->display_name }}</td>
      <td>{{ $v->km_current ? number_format($v->km_current,0,',','.') : '—' }}</td>
      <td><span class="badge {{ $v->status==='in_officina' ? 'badge-amber' : ($v->status==='pronto' ? 'badge-green' : 'badge-gray') }}">{{ str_replace('_',' ',ucfirst($v->status)) }}</span></td>
      <td>@php $open=$v->claims->whereNotIn('status',['chiuso','archiviato'])->count(); @endphp
        <span class="badge {{ $open>0 ? 'badge-amber' : 'badge-gray' }}">{{ $open }} aperti</span></td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:30px">Nessun veicolo trovato</td></tr>
    @endforelse
  </tbody>
</table>
</div>
{{ $veicoli->appends(request()->query())->links() }}
@endsection
