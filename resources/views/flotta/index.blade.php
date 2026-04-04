@extends('layouts.app')
@section('title', 'Flotta Veicoli')
@section('topbar-actions')
<a href="{{ route('flotta.create') }}" class="btn btn-primary btn-sm">+ Aggiungi Veicolo</a>
@endsection
@section('content')
<div class="three-col" style="margin-bottom:16px">
  <div class="stat-card green">
    <div class="stat-label">Disponibili</div>
    <div class="stat-value">{{ $disponibili }}</div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label">Noleggiate / Sostitutive</div>
    <div class="stat-value">{{ $occupate }}</div>
  </div>
  <div class="stat-card red">
    <div class="stat-label">In manutenzione</div>
    <div class="stat-value">{{ $manutenzione }}</div>
  </div>
</div>
<div class="card" style="padding:0">
  <table>
    <thead><tr><th>Targa</th><th>Veicolo</th><th>Cat.</th><th>Anno</th><th>Km</th><th>Revisione</th><th>Assicurazione</th><th>Stato</th><th>Tariffa/gg</th></tr></thead>
    <tbody>
      @forelse($flotta as $v)
      <tr onclick="location.href='{{ route('flotta.show', $v) }}'" style="cursor:pointer">
        <td><span class="targa">{{ $v->plate }}</span></td>
        <td><strong>{{ $v->brand }} {{ $v->model }}</strong></td>
        <td><span class="badge badge-gray">{{ $v->category }}</span></td>
        <td>{{ $v->year ?? '—' }}</td>
        <td>{{ $v->km_current ? number_format($v->km_current,0,',','.') : '—' }}</td>
        <td style="color:{{ $v->revision_expiry && $v->revision_expiry->isPast() ? 'var(--red)' : ($v->revision_expiry && $v->revision_expiry->diffInDays(now()) <= 30 ? 'var(--amber)' : 'var(--text2)') }}">
          {{ $v->revision_expiry ? $v->revision_expiry->format('d/m/Y') : '—' }}
          @if($v->revision_expiry && $v->revision_expiry->isPast()) ⚠ @endif
        </td>
        <td style="color:{{ $v->insurance_expiry && $v->insurance_expiry->isPast() ? 'var(--red)' : 'var(--text2)' }}">
          {{ $v->insurance_expiry ? $v->insurance_expiry->format('d/m/Y') : '—' }}
        </td>
        <td>
          @php $colors = ['disponibile'=>'badge-green','noleggiato'=>'badge-amber','sostitutiva'=>'badge-amber','manutenzione'=>'badge-red','dismissione'=>'badge-gray']; @endphp
          <span class="badge {{ $colors[$v->status] ?? 'badge-gray' }}">{{ ucfirst($v->status) }}</span>
        </td>
        <td>{{ $v->daily_rate > 0 ? '€ '.$v->daily_rate.'/gg' : 'Gratuito' }}</td>
      </tr>
      @empty
      <tr><td colspan="9" style="text-align:center;color:var(--text3);padding:30px">Nessun veicolo in flotta</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
{{ $flotta->links() }}
@endsection
