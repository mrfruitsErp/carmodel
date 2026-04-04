@extends('layouts.app')
@section('title', 'Clienti')
@section('topbar-actions')
<a href="{{ route('clienti.create') }}" class="btn btn-primary btn-sm">+ Nuovo Cliente</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Cerca cliente, P.IVA, CF, telefono..." value="{{ request('search') }}"></div>
  <a href="{{ route('clienti.index') }}" class="chip {{ !request('type') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('clienti.index') }}?type=private" class="chip {{ request('type')==='private' ? 'active' : '' }}">Privati</a>
  <a href="{{ route('clienti.index') }}?type=company" class="chip {{ request('type')==='company' ? 'active' : '' }}">Aziende</a>
  <a href="{{ route('clienti.index') }}?filter=sinistro_aperto" class="chip {{ request('filter')==='sinistro_aperto' ? 'active' : '' }}">Con sinistro aperto</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Cliente</th><th>Tipo</th><th>P.IVA / CF</th><th>Telefono</th><th>Sinistri</th><th>Veicoli</th><th>Ultima att.</th><th>Valore tot.</th></tr></thead>
    <tbody>
      @forelse($clienti as $c)
      <tr onclick="location.href='{{ route('clienti.show', $c) }}'" style="cursor:pointer">
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div class="avatar" style="width:28px;height:28px;font-size:11px">{{ $c->initials }}</div>
            <div>
              <div style="font-weight:500">{{ $c->display_name }}</div>
              <div style="font-size:11px;color:var(--text3)">{{ $c->email }}</div>
            </div>
          </div>
        </td>
        <td><span class="badge {{ $c->type === 'company' ? 'badge-amber' : 'badge-blue' }}">{{ $c->type === 'company' ? 'Azienda' : 'Privato' }}</span></td>
        <td style="font-family:var(--mono);font-size:11px">{{ $c->fiscal_code ?? $c->vat_number ?? '—' }}</td>
        <td>{{ $c->phone ?? '—' }}</td>
        <td>
          @php $openClaims = $c->claims->whereNotIn('status',['chiuso','archiviato'])->count(); @endphp
          <span class="badge {{ $openClaims > 0 ? 'badge-amber' : 'badge-green' }}">{{ $openClaims }} aperti</span>
        </td>
        <td>{{ $c->vehicles->count() }}</td>
        <td style="color:var(--text3)">{{ $c->updated_at->format('d/m/Y') }}</td>
        <td style="font-weight:500;color:var(--green)">€ {{ number_format($c->total_value, 0, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:30px">Nessun cliente trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div style="margin-top:8px">{{ $clienti->appends(request()->query())->links() }}</div>
@endsection
