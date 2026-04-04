@extends('layouts.app')
@section('title', 'Fatture & DDT')
@section('topbar-actions')
<a href="{{ route('documenti.create') }}" class="btn btn-primary btn-sm">+ Nuovo Documento</a>
@endsection
@section('content')
<div class="three-col" style="margin-bottom:16px">
  <div class="stat-card amber">
    <div class="stat-label">Da incassare</div>
    <div class="stat-value">€ {{ number_format($totale_da_pagare, 0, ',', '.') }}</div>
    <div class="stat-sub">{{ $count_da_pagare }} fatture aperte</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Incassato questo mese</div>
    <div class="stat-value">€ {{ number_format($totale_pagato_mese, 0, ',', '.') }}</div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">Scadute</div>
    <div class="stat-value">{{ $count_scadute }}</div>
    <div class="stat-sub">Da sollecitare</div>
  </div>
</div>
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="N° documento, cliente..." value="{{ request('search') }}"></div>
  <a href="{{ route('documenti.index') }}" class="chip {{ !request('tipo') && !request('status') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('documenti.index') }}?tipo=fattura" class="chip {{ request('tipo')==='fattura' ? 'active' : '' }}">Fatture</a>
  <a href="{{ route('documenti.index') }}?tipo=ddt" class="chip {{ request('tipo')==='ddt' ? 'active' : '' }}">DDT</a>
  <a href="{{ route('documenti.index') }}?status=da_pagare" class="chip {{ request('status')==='da_pagare' ? 'active' : '' }}">Da pagare</a>
  <a href="{{ route('documenti.index') }}?status=scaduta" class="chip {{ request('status')==='scaduta' ? 'active' : '' }}" style="border-color:var(--red);color:var(--red)">Scadute</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Numero</th><th>Tipo</th><th>Cliente</th><th>Data</th><th>Scadenza</th><th>Imponibile</th><th>IVA</th><th>Totale</th><th>Stato</th><th></th></tr></thead>
    <tbody>
      @forelse($documenti as $d)
      <tr>
        <td style="font-family:var(--mono);font-size:11px">{{ $d->document_number }}</td>
        <td><span class="badge {{ $d->document_type === 'fattura' ? 'badge-blue' : 'badge-gray' }}">{{ strtoupper($d->document_type) }}</span></td>
        <td><a href="{{ route('clienti.show', $d->customer) }}" style="color:var(--text);text-decoration:none">{{ $d->customer->display_name }}</a></td>
        <td>{{ $d->issue_date->format('d/m/Y') }}</td>
        <td style="color:{{ $d->due_date && $d->due_date->isPast() && $d->payment_status !== 'pagata' ? 'var(--red)' : 'var(--text2)' }}">
          {{ $d->due_date ? $d->due_date->format('d/m/Y') : '—' }}
        </td>
        <td>€ {{ number_format($d->subtotal,2,',','.') }}</td>
        <td>€ {{ number_format($d->vat_amount,2,',','.') }}</td>
        <td style="font-weight:600">€ {{ number_format($d->total,2,',','.') }}</td>
        <td>
          @php $sc = ['da_pagare'=>'badge-amber','pagata'=>'badge-green','parziale'=>'badge-amber','scaduta'=>'badge-red','stornata'=>'badge-gray']; @endphp
          <span class="badge {{ $sc[$d->payment_status] ?? 'badge-gray' }}">{{ str_replace('_',' ',ucfirst($d->payment_status)) }}</span>
        </td>
        <td>
          <div style="display:flex;gap:4px">
            <a href="{{ route('documenti.show', $d) }}" class="btn btn-ghost btn-sm">↓ PDF</a>
            @if($d->payment_status !== 'pagata')
            <form method="POST" action="{{ route('documenti.pagato', $d) }}" onsubmit="return confirm('Segna come pagata?')">
              @csrf
              <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--green);border-color:var(--green)">✓ Pagata</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="10" style="text-align:center;color:var(--text3);padding:30px">Nessun documento trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $documenti->appends(request()->query())->links() }}
@endsection
