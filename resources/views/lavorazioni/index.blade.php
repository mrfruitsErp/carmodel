@extends('layouts.app')
@section('title', 'Lavorazioni')
@section('topbar-actions')
<a href="{{ route('lavorazioni.create') }}" class="btn btn-primary btn-sm">+ Nuova Commessa</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Commessa, targa, cliente..." value="{{ request('search') }}"></div>
  <a href="{{ route('lavorazioni.index') }}" class="chip {{ !request('status') ? 'active' : '' }}">Tutte</a>
  <a href="{{ route('lavorazioni.index') }}?status=in_lavorazione" class="chip {{ request('status')==='in_lavorazione' ? 'active' : '' }}">In lavorazione</a>
  <a href="{{ route('lavorazioni.index') }}?status=attesa" class="chip {{ request('status')==='attesa' ? 'active' : '' }}">In attesa</a>
  <a href="{{ route('lavorazioni.index') }}?filter=overdue" class="chip {{ request('filter')==='overdue' ? 'active' : '' }}" style="border-color:var(--red);color:var(--red)">⚠ Ritardo</a>
  <a href="{{ route('lavorazioni.index') }}?status=completato" class="chip {{ request('status')==='completato' ? 'active' : '' }}">Completate</a>
</form>
<div class="card" style="padding:0">
<table>
  <thead><tr><th>N° Commessa</th><th>Cliente / Targa</th><th>Sinistro</th><th>Tipo</th><th>Tecnico</th><th>Stato</th><th>Avanz.</th><th>Scadenza</th><th>Importo</th></tr></thead>
  <tbody>
    @forelse($lavorazioni as $l)
    <tr onclick="location.href='{{ route('lavorazioni.show', $l) }}'" style="cursor:pointer">
      <td><span style="font-family:var(--mono);font-size:11px;color:var(--teal)">#{{ $l->job_number }}</span></td>
      <td><div style="font-weight:500">{{ $l->customer->display_name }}</div><span class="targa">{{ $l->vehicle->plate }}</span></td>
      <td>{{ $l->claim ? '<span style="font-family:var(--mono);font-size:11px;color:var(--text3)">#'.$l->claim->claim_number.'</span>' : '—' }}</td>
      <td><span class="badge badge-teal">{{ ucfirst($l->job_type) }}</span></td>
      <td style="color:var(--text2)">{{ $l->assignedTo?->name ?? '—' }}</td>
      <td><span class="badge {{ $l->status==='in_lavorazione' ? 'badge-amber' : ($l->isOverdue() ? 'badge-red' : ($l->status==='completato' ? 'badge-green' : 'badge-blue')) }}">{{ str_replace('_',' ',ucfirst($l->status)) }}</span></td>
      <td><div style="display:flex;align-items:center;gap:6px"><span style="font-size:11px;color:var(--text3)">{{ $l->progress_percent }}%</span><div class="progress" style="width:60px"><div class="progress-fill" style="width:{{ $l->progress_percent }}%"></div></div></div></td>
      <td style="color:{{ $l->isOverdue() ? 'var(--red)' : 'var(--text2)' }}">{{ $l->expected_end_date ? $l->expected_end_date->format('d/m') : '—' }}{{ $l->isOverdue() ? ' ⚠' : '' }}</td>
      <td>{{ $l->estimated_amount ? '€ '.number_format($l->estimated_amount,0,',','.') : '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:30px">Nessuna lavorazione trovata</td></tr>
    @endforelse
  </tbody>
</table>
</div>
{{ $lavorazioni->appends(request()->query())->links() }}
@endsection
