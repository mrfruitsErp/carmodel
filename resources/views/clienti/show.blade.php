@extends('layouts.app')
@section('title', $customer->display_name)
@section('topbar-actions')
<a href="{{ route('fascicoli.index') }}?cliente_id={{ $customer->id }}" class="btn btn-ghost btn-sm">📁 Fascicoli</a>
<a href="{{ route('fascicoli.create') }}?cliente_id={{ $customer->id }}" class="btn btn-ghost btn-sm">+ Fascicolo</a>
<a href="{{ route('clienti.edit', $customer) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
<form method="POST" action="{{ route('clienti.destroy', $customer) }}" onsubmit="return confirm('Eliminare il cliente {{ $customer->display_name }}? Questa azione è reversibile.')">
  @csrf @method('DELETE')
  <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
</form>
<a href="{{ route('sinistri.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm">+ Sinistro</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('clienti.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Clienti</a></div>
<div class="two-col">
  <div>
    <div class="card">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div class="avatar" style="width:52px;height:52px;font-size:18px">{{ $customer->initials }}</div>
        <div>
          <div style="font-size:17px;font-weight:600">{{ $customer->display_name }}</div>
          <div style="font-size:12px;color:var(--text3)">
            Cliente dal {{ $customer->created_at->format('M Y') }} &nbsp;·&nbsp;
            {{ $customer->vehicles->count() }} veicoli &nbsp;·&nbsp;
            {{ $customer->claims->count() }} sinistri
          </div>
        </div>
      </div>
      <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $customer->email ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">PEC</span><span class="info-value">{{ $customer->pec_email ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{{ $customer->phone ?? '—' }}</span></div>
      @if($customer->phone2)<div class="info-row"><span class="info-label">Telefono 2</span><span class="info-value">{{ $customer->phone2 }}</span></div>@endif
      <div class="info-row"><span class="info-label">WhatsApp</span><span class="info-value">{{ $customer->whatsapp ?? '—' }}</span></div>
      @if($customer->type === 'private')
      <div class="info-row"><span class="info-label">Codice Fiscale</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $customer->fiscal_code ?? '—' }}</span></div>
      @else
      <div class="info-row"><span class="info-label">P.IVA</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $customer->vat_number ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Codice SDI</span><span class="info-value">{{ $customer->sdi_code ?? '—' }}</span></div>
      @endif
      @if($customer->iban)<div class="info-row"><span class="info-label">IBAN</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $customer->iban }}</span></div>@endif
      @if($customer->intestatario_iban)<div class="info-row"><span class="info-label">Intestatario</span><span class="info-value">{{ $customer->intestatario_iban }}</span></div>@endif
      <div class="info-row"><span class="info-label">Indirizzo</span><span class="info-value">{{ $customer->address ? $customer->address.', '.$customer->city : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Valore totale</span><span class="info-value" style="color:var(--green)">€ {{ number_format($customer->total_value, 2, ',', '.') }}</span></div>
    </div>
    <div class="card">
      <div class="card-title">Note interne</div>
      <form method="POST" action="{{ route('clienti.update', $customer) }}">
        @csrf @method('PUT')
        <textarea name="notes" class="form-textarea" style="min-height:100px">{{ $customer->notes }}</textarea>
        <button type="submit" class="btn btn-ghost btn-sm" style="margin-top:8px">Salva note</button>
      </form>
    </div>
    <div class="card">
      <div class="card-title">Veicoli associati</div>
      <table>
        <thead><tr><th>Targa</th><th>Veicolo</th><th>Anno</th><th>Stato</th></tr></thead>
        <tbody>
          @foreach($customer->vehicles as $v)
          <tr onclick="location.href='{{ route('veicoli.show', $v) }}'" style="cursor:pointer">
            <td><span class="targa">{{ $v->plate }}</span></td>
            <td>{{ $v->brand }} {{ $v->model }}</td>
            <td>{{ $v->year }}</td>
            <td><span class="badge {{ $v->status === 'in_officina' ? 'badge-amber' : ($v->status === 'pronto' ? 'badge-green' : 'badge-gray') }}">{{ str_replace('_',' ',ucfirst($v->status)) }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <div style="margin-top:10px"><a href="{{ route('veicoli.create') }}?customer_id={{ $customer->id }}" class="btn btn-ghost btn-sm">+ Aggiungi veicolo</a></div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Sinistri</div>
      @forelse($customer->claims->sortByDesc('created_at')->take(5) as $claim)
      <div class="tl-item">
        <div class="tl-dot {{ in_array($claim->status,['chiuso','liquidato']) ? 'gray' : ($claim->isOverdue() ? 'red' : 'amber') }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('sinistri.show', $claim) }}" style="color:var(--green);text-decoration:none">#{{ $claim->claim_number }}</a> — {{ $claim->insuranceCompany?->name }}</div>
          <div class="tl-meta">{{ $claim->event_date?->format('d/m/Y') }} · {{ $claim->estimated_amount ? '€ '.number_format($claim->estimated_amount,0,',','.') : '' }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun sinistro</div>
      @endforelse
      <a href="{{ route('sinistri.create') }}?customer_id={{ $customer->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuovo sinistro</a>
    </div>
    <div class="card">
      <div class="card-title">Noleggi / Sostitutive attivi</div>
      @forelse($customer->rentals->where('status','attivo') as $r)
      <div class="fleet-item">
        <div class="fleet-status {{ $r->isOverdue() ? 'red' : 'amber' }}"></div>
        <div>
          <div style="font-weight:500;font-size:13px">{{ $r->fleetVehicle->brand }} {{ $r->fleetVehicle->model }}</div>
          <div style="font-size:11px;color:{{ $r->isOverdue() ? 'var(--red)' : 'var(--amber)' }}">Scade {{ $r->expected_end_date->format('d/m/Y') }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun noleggio attivo</div>
      @endforelse
    </div>
    <div class="card">
      <div class="card-title">Ultime fatture</div>
      @forelse($customer->documents->sortByDesc('issue_date')->take(4) as $d)
      <div class="info-row">
        <span class="info-label" style="font-family:var(--mono);font-size:11px">{{ $d->document_number }}</span>
        <span class="info-value">
          € {{ number_format($d->total,2,',','.') }}
          <span class="badge {{ $d->payment_status === 'pagata' ? 'badge-green' : 'badge-amber' }}" style="margin-left:6px">{{ ucfirst($d->payment_status) }}</span>
        </span>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessuna fattura</div>
      @endforelse
    </div>
  </div>
</div>
@endsection