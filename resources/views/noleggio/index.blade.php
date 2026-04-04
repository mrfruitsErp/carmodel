@extends('layouts.app')
@section('title', 'Contratti Noleggio')
@section('topbar-actions')
<a href="{{ route('noleggio.create') }}" class="btn btn-primary btn-sm">+ Nuovo Contratto</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="N° contratto, cliente, targa..." value="{{ request('search') }}"></div>
  <a href="{{ route('noleggio.index') }}" class="chip {{ !request('tipo') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('noleggio.index') }}?tipo=sostitutiva" class="chip {{ request('tipo')==='sostitutiva' ? 'active' : '' }}">Sostitutive</a>
  <a href="{{ route('noleggio.index') }}?tipo=breve_termine" class="chip {{ request('tipo')==='breve_termine' ? 'active' : '' }}">Breve termine</a>
  <a href="{{ route('noleggio.index') }}?tipo=lungo_termine" class="chip {{ request('tipo')==='lungo_termine' ? 'active' : '' }}">Lungo termine</a>
  <a href="{{ route('noleggio.index') }}?status=scaduto" class="chip {{ request('status')==='scaduto' ? 'active' : '' }}" style="border-color:var(--red);color:var(--red)">⚠ Scaduti</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>N° Contratto</th><th>Cliente</th><th>Veicolo</th><th>Tipo</th><th>Inizio</th><th>Fine prevista</th><th>Tariffa</th><th>Totale</th><th>Stato</th></tr></thead>
    <tbody>
      @forelse($noleggi as $n)
      <tr onclick="location.href='{{ route('noleggio.show', $n) }}'" style="cursor:pointer">
        <td><span style="font-family:var(--mono);font-size:11px;color:var(--teal)">#{{ $n->rental_number }}</span></td>
        <td><strong>{{ $n->customer->display_name }}</strong></td>
        <td><span class="targa">{{ $n->fleetVehicle->plate }}</span> {{ $n->fleetVehicle->brand }} {{ $n->fleetVehicle->model }}</td>
        <td>
          @php $tc = ['sostitutiva'=>'badge-orange','breve_termine'=>'badge-blue','lungo_termine'=>'badge-purple']; @endphp
          <span class="badge {{ $tc[$n->rental_type] ?? 'badge-gray' }}">{{ str_replace('_',' ',ucfirst($n->rental_type)) }}</span>
        </td>
        <td>{{ $n->start_date->format('d/m/Y') }}</td>
        <td style="color:{{ $n->isOverdue() ? 'var(--red)' : ($n->expected_end_date->diffInDays(now()) <= 2 ? 'var(--amber)' : 'var(--text2)') }}">
          {{ $n->expected_end_date->format('d/m/Y') }} @if($n->isOverdue()) ⚠ @endif
        </td>
        <td>{{ $n->daily_rate > 0 ? '€ '.$n->daily_rate.'/gg' : 'Gratuito' }}</td>
        <td style="font-weight:500">{{ $n->total > 0 ? '€ '.number_format($n->total,2,',','.') : '—' }}</td>
        <td>
          @php $sc = ['prenotato'=>'badge-blue','attivo'=>'badge-amber','scaduto'=>'badge-red','chiuso'=>'badge-green','annullato'=>'badge-gray']; @endphp
          <span class="badge {{ $sc[$n->status] ?? 'badge-gray' }}">{{ ucfirst($n->status) }}</span>
        </td>
      </tr>
      @empty
      <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:30px">Nessun contratto trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $noleggi->appends(request()->query())->links() }}
@endsection
