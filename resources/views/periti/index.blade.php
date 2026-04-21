@extends('layouts.app')
@section('title', isset($isLiquidatori) && $isLiquidatori ? 'Liquidatori' : (isset($isMedici) && $isMedici ? 'Medici Legali' : 'Periti & Avvocati'))
@section('topbar-actions')
@if(isset($isLiquidatori) && $isLiquidatori)
<a href="{{ route('liquidatori.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Liquidatore</a>
@elseif(isset($isMedici) && $isMedici)
<a href="{{ route('medici.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Medico</a>
@else
<a href="{{ route('periti.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Contatto</a>
@endif
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Nome, compagnia, email..." value="{{ request('search') }}"></div>
  @if(!isset($isLiquidatori) && !isset($isMedici))
  <a href="{{ route('periti.index') }}" class="chip {{ !request('tipo') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('periti.index') }}?tipo=perito" class="chip {{ request('tipo')==='perito' ? 'active' : '' }}">Periti</a>
  <a href="{{ route('periti.index') }}?tipo=avvocato" class="chip {{ request('tipo')==='avvocato' ? 'active' : '' }}">Avvocati</a>
  <a href="{{ route('periti.index') }}?tipo=legale" class="chip {{ request('tipo')==='legale' ? 'active' : '' }}">Legali</a>
  @endif
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Nome</th><th>Ruolo</th><th>Compagnia / Studio</th><th>Telefono</th><th>Email</th><th>Sinistri assegnati</th><th>Valutazione</th></tr></thead>
    <tbody>
      @forelse($esperti as $e)
      @php
        $route = isset($isLiquidatori) && $isLiquidatori ? 'liquidatori.show' : (isset($isMedici) && $isMedici ? 'medici.show' : 'periti.show');
      @endphp
      <tr onclick="location.href='{{ route($route, $e) }}'" style="cursor:pointer">
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div class="avatar" style="width:28px;height:28px;font-size:11px;background:var(--purple-bg);border-color:var(--purple);color:var(--purple)">{{ strtoupper(substr($e->name,0,2)) }}</div>
            <strong>{{ $e->title ? $e->title.' ' : '' }}{{ $e->name }}</strong>
          </div>
        </td>
        <td>
          @php $tc = ['perito'=>'badge-purple','avvocato'=>'badge-orange','medico_legale'=>'badge-blue','consulente'=>'badge-gray','legale'=>'badge-orange','liquidatore'=>'badge-teal']; @endphp
          <span class="badge {{ $tc[$e->type] ?? 'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$e->type)) }}</span>
        </td>
        <td>{{ $e->company_name ?? $e->insuranceCompany?->name ?? '—' }}</td>
        <td>{{ $e->phone ?? '—' }}</td>
        <td>{{ $e->email ?? '—' }}</td>
        <td><span class="badge badge-gray">{{ $e->claims_count ?? 0 }} sinistri</span></td>
        <td style="color:var(--amber)">{{ str_repeat('★', $e->rating) }}{{ str_repeat('☆', 5 - $e->rating) }}</td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:30px">Nessun contatto trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $esperti->appends(request()->query())->links('vendor.pagination.custom') }}
@endsection