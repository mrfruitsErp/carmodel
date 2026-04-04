@extends('layouts.app')
@section('title', 'Ricambi & Magazzino')
@section('topbar-actions')
<a href="{{ route('ricambi.create') }}" class="btn btn-primary btn-sm">+ Nuovo Ricambio</a>
@endsection
@section('content')
<div class="three-col" style="margin-bottom:16px">
  <div class="stat-card green">
    <div class="stat-label">Articoli totali</div>
    <div class="stat-value">{{ $totale }}</div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Sotto scorta minima</div>
    <div class="stat-value">{{ $sotto_scorta }}</div>
    <div class="stat-sub">Da riordinare</div>
  </div>
  <div class="stat-card blue">
    <div class="stat-label">Valore magazzino</div>
    <div class="stat-value">€ {{ number_format($valore, 0, ',', '.') }}</div>
  </div>
</div>
@if($sotto_scorta > 0)
<div class="alert alert-amber"><span>⚠</span><span><strong>{{ $sotto_scorta }} articoli</strong> sotto la scorta minima — verificare i riordini.</span></div>
@endif
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Codice, nome, categoria..." value="{{ request('search') }}"></div>
  <a href="{{ route('ricambi.index') }}" class="chip {{ !request('filter') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('ricambi.index') }}?filter=sotto_scorta" class="chip {{ request('filter')==='sotto_scorta' ? 'active' : '' }}" style="border-color:var(--red);color:var(--red)">Sotto scorta</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Codice</th><th>Nome</th><th>Categoria</th><th>Brand</th><th>Scorta</th><th>Min. scorta</th><th>Prezzo acquisto</th><th>Prezzo vendita</th><th>Posizione</th></tr></thead>
    <tbody>
      @forelse($ricambi as $r)
      <tr onclick="location.href='{{ route('ricambi.show', $r) }}'" style="cursor:pointer">
        <td style="font-family:var(--mono);font-size:11px">{{ $r->code ?? '—' }}</td>
        <td><strong>{{ $r->name }}</strong></td>
        <td>{{ $r->category ?? '—' }}</td>
        <td>{{ $r->brand ?? '—' }}</td>
        <td style="color:{{ $r->isLowStock() ? 'var(--red)' : 'var(--green)' }};font-weight:500">
          {{ $r->stock_quantity }} {{ $r->unit }}
          @if($r->isLowStock()) ⚠ @endif
        </td>
        <td style="color:var(--text3)">{{ $r->min_stock }} {{ $r->unit }}</td>
        <td>{{ $r->purchase_price ? '€ '.number_format($r->purchase_price,2,',','.') : '—' }}</td>
        <td style="color:var(--green)">{{ $r->sale_price ? '€ '.number_format($r->sale_price,2,',','.') : '—' }}</td>
        <td style="color:var(--text3)">{{ $r->location ?? '—' }}</td>
      </tr>
      @empty
      <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:30px">Nessun ricambio trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $ricambi->appends(request()->query())->links() }}
@endsection
