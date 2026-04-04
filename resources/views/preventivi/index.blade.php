@extends('layouts.app')
@section('title', 'Preventivi')
@section('topbar-actions')
<a href="{{ route('preventivi.create') }}" class="btn btn-primary btn-sm">+ Nuovo Preventivo</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="N° preventivo, cliente..." value="{{ request('search') }}"></div>
  <a href="{{ route('preventivi.index') }}" class="chip {{ !request('status') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('preventivi.index') }}?status=bozza" class="chip {{ request('status')==='bozza' ? 'active' : '' }}">Bozze</a>
  <a href="{{ route('preventivi.index') }}?status=inviato" class="chip {{ request('status')==='inviato' ? 'active' : '' }}">Inviati</a>
  <a href="{{ route('preventivi.index') }}?status=accettato" class="chip {{ request('status')==='accettato' ? 'active' : '' }}">Accettati</a>
  <a href="{{ route('preventivi.index') }}?status=rifiutato" class="chip {{ request('status')==='rifiutato' ? 'active' : '' }}">Rifiutati</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>N° Preventivo</th><th>Cliente</th><th>Veicolo</th><th>Tipo</th><th>Valido fino</th><th>Importo</th><th>Stato</th><th>Azioni</th></tr></thead>
    <tbody>
      @forelse($preventivi as $p)
      <tr>
        <td><span style="font-family:var(--mono);font-size:11px;color:var(--text2)">#{{ $p->quote_number }}</span></td>
        <td><a href="{{ route('clienti.show', $p->customer) }}" style="color:var(--text);text-decoration:none"><strong>{{ $p->customer->display_name }}</strong></a></td>
        <td><span class="targa">{{ $p->vehicle->plate }}</span></td>
        <td><span class="badge badge-teal">{{ ucfirst($p->job_type) }}</span></td>
        <td style="color:{{ $p->valid_until && $p->valid_until->isPast() ? 'var(--red)' : 'var(--text2)' }}">
          {{ $p->valid_until ? $p->valid_until->format('d/m/Y') : '—' }}
        </td>
        <td style="font-weight:500">€ {{ number_format($p->total,2,',','.') }}</td>
        <td>
          @php $sc = ['bozza'=>'badge-gray','inviato'=>'badge-blue','accettato'=>'badge-green','rifiutato'=>'badge-red','scaduto'=>'badge-gray']; @endphp
          <span class="badge {{ $sc[$p->status] ?? 'badge-gray' }}">{{ ucfirst($p->status) }}</span>
        </td>
        <td>
          <div style="display:flex;gap:4px">
            <a href="{{ route('preventivi.show', $p) }}" class="btn btn-ghost btn-sm">Apri</a>
            @if($p->status === 'accettato' && !$p->converted_to_job_id)
            <form method="POST" action="{{ route('preventivi.converti', $p) }}">
              @csrf
              <button type="submit" class="btn btn-primary btn-sm">→ Converti</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:30px">Nessun preventivo trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $preventivi->appends(request()->query())->links() }}
@endsection
