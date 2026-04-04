@extends('layouts.app')
@section('title', 'Sinistri')
@section('topbar-actions')
<a href="{{ route('sinistri.create') }}" class="btn btn-primary btn-sm">+ Apri Sinistro</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="N° sinistro, targa, cliente, compagnia..." value="{{ request('search') }}"></div>
  <a href="{{ route('sinistri.index') }}" class="chip {{ !request('filter') && !request('status') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('sinistri.index') }}?filter=urgenti" class="chip {{ request('filter')==='urgenti' ? 'active' : '' }}" style="{{ request('filter')==='urgenti' ? '' : 'border-color:var(--red);color:var(--red)' }}">⚠ Urgenti</a>
  <a href="{{ route('sinistri.index') }}?status=perizia_attesa" class="chip {{ request('status')==='perizia_attesa' ? 'active' : '' }}">Perizia attesa</a>
  <a href="{{ route('sinistri.index') }}?status=in_riparazione" class="chip {{ request('status')==='in_riparazione' ? 'active' : '' }}">In riparazione</a>
  <a href="{{ route('sinistri.index') }}?status=liquidato" class="chip {{ request('status')==='liquidato' ? 'active' : '' }}">Liquidati</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>N° Sinistro</th><th>Cliente</th><th>Targa</th><th>Compagnia</th><th>Tipo</th><th>Perito</th><th>Stato</th><th>Scad. CID</th><th>Importo</th></tr></thead>
    <tbody>
      @forelse($sinistri as $s)
      <tr onclick="location.href='{{ route('sinistri.show', $s) }}'" style="cursor:pointer">
        <td><span style="font-family:var(--mono);font-size:11px;color:var(--green)">#{{ $s->claim_number }}</span></td>
        <td><strong>{{ $s->customer->display_name }}</strong></td>
        <td><span class="targa">{{ $s->vehicle->plate }}</span></td>
        <td>{{ $s->insuranceCompany?->name ?? '—' }}</td>
        <td><span class="badge badge-blue">{{ strtoupper($s->claim_type) }}</span></td>
        <td style="color:var(--text2)">{{ $s->expert?->name ?? '—' }}</td>
        <td>
          @php
          $badges = ['aperto'=>'badge-blue','cid_presentato'=>'badge-blue','perizia_attesa'=>'badge-amber',
            'perizia_effettuata'=>'badge-teal','in_riparazione'=>'badge-teal','riparazione_completata'=>'badge-teal',
            'liquidazione_attesa'=>'badge-amber','liquidato'=>'badge-green','contestato'=>'badge-red',
            'chiuso'=>'badge-gray','archiviato'=>'badge-gray'];
          @endphp
          <span class="badge {{ $badges[$s->status] ?? 'badge-gray' }}">{{ str_replace('_',' ',ucfirst($s->status)) }}</span>
        </td>
        <td style="color:{{ $s->isOverdue() ? 'var(--red)' : ($s->isCidExpiringSoon() ? 'var(--amber)' : 'var(--text2)') }};font-weight:{{ $s->isCidExpiringSoon() ? '500' : '400' }}">
          {{ $s->cid_expiry ? $s->cid_expiry->format('d/m/Y') : '—' }} {{ $s->isOverdue() ? '⚠' : '' }}
        </td>
        <td>{{ $s->estimated_amount ? '€ '.number_format($s->estimated_amount,0,',','.') : '—' }}</td>
      </tr>
      @empty
      <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:30px">Nessun sinistro trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $sinistri->appends(request()->query())->links() }}
@endsection
