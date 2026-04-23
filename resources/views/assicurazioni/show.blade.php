@extends('layouts.app')
@section('title', $assicurazioni->name)
@section('topbar-actions')
<a href="{{ route('assicurazioni.edit', $assicurazioni) }}" class="btn btn-ghost btn-sm">Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px">
  <a href="{{ route('assicurazioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Compagnie</a>
</div>
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati compagnia</div>
      <div class="info-row"><span class="info-label">Codice</span><span class="info-value"><span class="targa">{{ $assicurazioni->code ?? '—' }}</span></span></div>
      <div class="info-row"><span class="info-label">P.IVA</span><span class="info-value">{{ $assicurazioni->piva ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Cod. Fiscale</span><span class="info-value">{{ $assicurazioni->codice_fiscale ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $assicurazioni->email ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">PEC</span><span class="info-value">{{ $assicurazioni->pec ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{{ $assicurazioni->phone ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Fax</span><span class="info-value">{{ $assicurazioni->fax ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">SDI</span><span class="info-value">{{ $assicurazioni->codice_sdi ?? '—' }}</span></div>
      @if($assicurazioni->portal_url)
      <div class="info-row"><span class="info-label">Portale</span><span class="info-value"><a href="{{ $assicurazioni->portal_url }}" target="_blank" style="color:var(--orange)">Apri portale →</a></span></div>
      @endif
      <div class="info-row"><span class="info-label">Indirizzo</span><span class="info-value">{{ $assicurazioni->address ?? '—' }}</span></div>
    </div>

    @if($assicurazioni->referente)
    <div class="card">
      <div class="card-title">Referente</div>
      <div class="info-row"><span class="info-label">Nome</span><span class="info-value">{{ $assicurazioni->referente }}</span></div>
      <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $assicurazioni->referente_email ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{{ $assicurazioni->referente_phone ?? '—' }}</span></div>
    </div>
    @endif
  </div>
  <div>
    <div class="card">
      <div class="card-title">Periti & Liquidatori collegati</div>
      @forelse($periti as $p)
      <div class="fleet-item">
        <div class="fleet-status {{ $p->active ? 'green' : 'amber' }}"></div>
        <div style="flex:1">
          <div style="font-weight:600;font-size:13px">{{ $p->name }}</div>
          <div style="font-size:11px;color:var(--text3)">{{ ucfirst(str_replace('_',' ',$p->type)) }} · {{ $p->phone ?? '—' }}</div>
        </div>
        <a href="{{ route('periti.show', $p) }}" class="btn btn-ghost btn-sm">Vedi</a>
      </div>
      @empty
      <p style="color:var(--text3);font-size:13px">Nessun esperto collegato</p>
      @endforelse
      <div style="margin-top:12px">
        <a href="{{ route('periti.create') }}?insurance_company_id={{ $assicurazioni->id }}" class="btn btn-ghost btn-sm">+ Aggiungi esperto</a>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Ultimi sinistri</div>
      @forelse($sinistri as $s)
      <div class="tl-item">
        <div class="tl-dot"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('sinistri.show', $s) }}" style="color:var(--text);text-decoration:none">{{ $s->claim_number }} — {{ $s->customer?->display_name }}</a></div>
          <div class="tl-meta">{{ $s->event_date?->format('d/m/Y') }} · {{ ucfirst($s->status) }}</div>
        </div>
      </div>
      @empty
      <p style="color:var(--text3);font-size:13px">Nessun sinistro</p>
      @endforelse
    </div>
  </div>
</div>
@endsection