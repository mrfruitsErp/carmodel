@extends('layouts.app')
@section('title', 'Lesioni Personali')
@section('topbar-actions')
<a href="{{ route('lesioni.create') }}" class="btn btn-primary btn-sm">+ Nuova Lesione</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Cliente, avvocato, sinistro..." value="{{ request('search') }}"></div>
  <a href="{{ route('lesioni.index') }}" class="chip {{ !request('status') ? 'active' : '' }}">Tutte</a>
  <a href="{{ route('lesioni.index') }}?status=visita_medica" class="chip {{ request('status')==='visita_medica' ? 'active' : '' }}">Visita medica</a>
  <a href="{{ route('lesioni.index') }}?status=perizia_medica" class="chip {{ request('status')==='perizia_medica' ? 'active' : '' }}">Perizia medica</a>
  <a href="{{ route('lesioni.index') }}?status=trattativa" class="chip {{ request('status')==='trattativa' ? 'active' : '' }}">In trattativa</a>
  <a href="{{ route('lesioni.index') }}?status=liquidata" class="chip {{ request('status')==='liquidata' ? 'active' : '' }}">Liquidate</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>N° Lesione</th><th>Cliente</th><th>Sinistro</th><th>Tipo lesione</th><th>Avvocato</th><th>Medico legale</th><th>Stato</th><th>Risarcimento</th></tr></thead>
    <tbody>
      @forelse($lesioni as $l)
      <tr onclick="location.href='{{ route('lesioni.show', $l) }}'" style="cursor:pointer">
        <td><span style="font-family:var(--mono);font-size:11px;color:var(--orange)">#{{ $l->injury_number }}</span></td>
        <td><strong>{{ $l->customer->display_name }}</strong></td>
        <td><a href="{{ route('sinistri.show', $l->claim) }}" style="color:var(--green);text-decoration:none" onclick="event.stopPropagation()">#{{ $l->claim->claim_number }}</a></td>
        <td>{{ $l->injury_type ?? '—' }}</td>
        <td>{{ $l->lawyer?->name ?? '—' }}</td>
        <td>{{ $l->doctor?->name ?? '—' }}</td>
        <td>
          @php $sc = ['aperta'=>'badge-blue','visita_medica'=>'badge-amber','perizia_medica'=>'badge-blue','trattativa'=>'badge-amber','accordo'=>'badge-teal','liquidata'=>'badge-green','contenzioso'=>'badge-red','chiusa'=>'badge-gray']; @endphp
          <span class="badge {{ $sc[$l->status] ?? 'badge-gray' }}">{{ str_replace('_',' ',ucfirst($l->status)) }}</span>
        </td>
        <td>
          @if($l->paid_amount)
          <span style="color:var(--green);font-weight:500">€ {{ number_format($l->paid_amount,0,',','.') }}</span>
          @elseif($l->estimated_amount)
          <span style="color:var(--text3)">Est. € {{ number_format($l->estimated_amount,0,',','.') }}</span>
          @else
          —
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:30px">Nessuna lesione registrata</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $lesioni->appends(request()->query())->links() }}
@endsection
