@extends('layouts.app')
@section('title', 'Lead marketplace')
@section('content')
<div class="filter-row">
  <div class="search-bar">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" placeholder="Nome, email, telefono..." onkeyup="filterTable(this.value)">
  </div>
</div>
<div class="card" style="padding:0;overflow:hidden">
  <table id="leadsTable">
    <thead>
      <tr>
        <th>Data</th><th>Contatto</th><th>Veicolo</th><th>Piattaforma</th><th>Messaggio</th><th>Stato</th>
      </tr>
    </thead>
    <tbody>
    @forelse($leads as $lead)
    <tr>
      <td style="white-space:nowrap;color:var(--text3);font-size:12px">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
      <td>
        <div style="font-weight:500">{{ $lead->name }}</div>
        <div style="font-size:11px;color:var(--text3)">{{ $lead->email }}</div>
        @if($lead->phone)<div style="font-size:11px;color:var(--text3)">{{ $lead->phone }}</div>@endif
      </td>
      <td>
        @if($lead->vehicle)
          <a href="{{ route('marketplace.vehicles.show',$lead->vehicle) }}" style="color:var(--blue-text);font-weight:500">{{ $lead->vehicle->brand }} {{ $lead->vehicle->model }}</a>
          <div style="font-size:11px;color:var(--text3)">{{ $lead->vehicle->year }} - {{ number_format($lead->vehicle->asking_price,0,',','.') }} euro</div>
        @else - @endif
      </td>
      <td><span class="badge badge-blue">{{ ucfirst($lead->platform ?? 'web') }}</span></td>
      <td style="max-width:200px;font-size:12px;color:var(--text2)">{{ Str::limit($lead->message, 60) }}</td>
      <td><span class="badge {{ $lead->status==='nuovo' ? 'badge-orange' : ($lead->status==='contattato' ? 'badge-blue' : 'badge-green') }}">{{ ucfirst($lead->status ?? 'nuovo') }}</span></td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text3)">Nessun lead ancora</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
<div style="margin-top:16px">{{ $leads->links() }}</div>
@push('scripts')
<script>
function filterTable(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#leadsTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>
@endpush
@endsection