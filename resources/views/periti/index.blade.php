@extends('layouts.app')
@section('title', 'Esperti & Contatti')
@section('topbar-actions')
<a href="{{ route('periti.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Contatto</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar">
    <span style="color:var(--text3)">⌕</span>
    <input name="search" placeholder="Nome, compagnia, email..." value="{{ request('search') }}">
  </div>
  <a href="{{ route('periti.index') }}" class="chip {{ !request('tipo') ? 'active' : '' }}">
    Tutti <span style="font-size:10px;opacity:.7">({{ $contatori['tutti'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=perito" class="chip {{ request('tipo')==='perito' ? 'active' : '' }}">
    Periti <span style="font-size:10px;opacity:.7">({{ $contatori['perito'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=avvocato" class="chip {{ request('tipo')==='avvocato' ? 'active' : '' }}">
    Avvocati <span style="font-size:10px;opacity:.7">({{ $contatori['avvocato'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=legale" class="chip {{ request('tipo')==='legale' ? 'active' : '' }}">
    Legali <span style="font-size:10px;opacity:.7">({{ $contatori['legale'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=liquidatore" class="chip {{ request('tipo')==='liquidatore' ? 'active' : '' }}">
    Liquidatori <span style="font-size:10px;opacity:.7">({{ $contatori['liquidatore'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=medico_legale" class="chip {{ request('tipo')==='medico_legale' ? 'active' : '' }}">
    Medici Legali <span style="font-size:10px;opacity:.7">({{ $contatori['medico_legale'] }})</span>
  </a>
  <a href="{{ route('periti.index') }}?tipo=consulente" class="chip {{ request('tipo')==='consulente' ? 'active' : '' }}">
    Consulenti <span style="font-size:10px;opacity:.7">({{ $contatori['consulente'] }})</span>
  </a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead>
      <tr>
        <th>Nome</th>
        <th>Ruolo</th>
        <th>Compagnia / Studio</th>
        <th>Telefono</th>
        <th>Email</th>
        <th>Valutazione</th>
      </tr>
    </thead>
    <tbody>
      @forelse($esperti as $e)
      <tr onclick="location.href='{{ route('periti.show', $e) }}'" style="cursor:pointer">
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div class="avatar" style="width:28px;height:28px;font-size:11px;background:var(--purple-bg);border-color:var(--purple);color:var(--purple)">
              {{ strtoupper(substr($e->name,0,2)) }}
            </div>
            <div>
              <div style="font-weight:600">{{ $e->title ? $e->title.' ' : '' }}{{ $e->name }}</div>
              @if($e->company_name)
              <div style="font-size:11px;color:var(--text3)">{{ $e->company_name }}</div>
              @endif
            </div>
          </div>
        </td>
        <td>
          @php
          $tc = ['perito'=>'badge-purple','avvocato'=>'badge-orange','legale'=>'badge-orange','medico_legale'=>'badge-blue','liquidatore'=>'badge-teal','consulente'=>'badge-gray'];
          $tl = ['perito'=>'Perito','avvocato'=>'Avvocato','legale'=>'Legale','medico_legale'=>'Medico Legale','liquidatore'=>'Liquidatore','consulente'=>'Consulente'];
          @endphp
          <span class="badge {{ $tc[$e->type] ?? 'badge-gray' }}">{{ $tl[$e->type] ?? ucfirst($e->type) }}</span>
        </td>
        <td style="color:var(--text2)">{{ $e->company_name ?? $e->insuranceCompany?->name ?? '—' }}</td>
        <td style="color:var(--text2)">{{ $e->phone ?? '—' }}</td>
        <td style="color:var(--text2)">{{ $e->email ?? '—' }}</td>
        <td style="color:var(--amber)">@for($i=1;$i<=5;$i++){{ $i <= $e->rating ? '★' : '☆' }}@endfor</td>
      </tr>
      @empty
      <tr><td colspan="6" style="text-align:center;color:var(--text3);padding:40px">Nessun contatto trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $esperti->appends(request()->query())->links('vendor.pagination.custom') }}
@endsection