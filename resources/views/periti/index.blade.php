@extends('layouts.app')
@section('title', 'Periti & Avvocati')
@section('topbar-actions')
<a href="{{ route('periti.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Contatto</a>
@endsection
@section('content')
<form method="GET" class="filter-row">
  <div class="search-bar"><span style="color:var(--text3)">⌕</span><input name="search" placeholder="Nome, compagnia, email..." value="{{ request('search') }}"></div>
  <a href="{{ route('periti.index') }}" class="chip {{ !request('tipo') ? 'active' : '' }}">Tutti</a>
  <a href="{{ route('periti.index') }}?tipo=perito" class="chip {{ request('tipo')==='perito' ? 'active' : '' }}">Periti</a>
  <a href="{{ route('periti.index') }}?tipo=avvocato" class="chip {{ request('tipo')==='avvocato' ? 'active' : '' }}">Avvocati</a>
  <a href="{{ route('periti.index') }}?tipo=medico_legale" class="chip {{ request('tipo')==='medico_legale' ? 'active' : '' }}">Medici legali</a>
</form>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Nome</th><th>Ruolo</th><th>Compagnia / Studio</th><th>Telefono</th><th>Email</th><th>Sinistri assegnati</th><th>Valutazione</th></tr></thead>
    <tbody>
      @forelse($esperti as $e)
      <tr onclick="location.href='{{ route('periti.show', $e) }}'" style="cursor:pointer">
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div class="avatar" style="width:28px;height:28px;font-size:11px;background:var(--purple-bg);border-color:var(--purple);color:var(--purple)">{{ strtoupper(substr($e->name,0,2)) }}</div>
            <strong>{{ $e->title ? $e->title.' ' : '' }}{{ $e->name }}</strong>
          </div>
        </td>
        <td>
          @php $tc = ['perito'=>'badge-purple','avvocato'=>'badge-orange','medico_legale'=>'badge-blue','consulente'=>'badge-gray']; @endphp
          <span class="badge {{ $tc[$e->type] ?? 'badge-gray' }}">{{ ucfirst(str_replace('_',' ',$e->type)) }}</span>
        </td>
        <td>{{ $e->company_name ?? $e->insuranceCompany?->name ?? '—' }}</td>
        <td>{{ $e->phone ?? '—' }}</td>
        <td>{{ $e->email ?? '—' }}</td>
        <td><span class="badge badge-gray">Perito / Avvocato</span></td>
        <td style="color:var(--amber)">{{ str_repeat('★', $e->rating) }}{{ str_repeat('☆', 5 - $e->rating) }}</td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:30px">Nessun contatto trovato</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $esperti->appends(request()->query())->links() }}
@endsection