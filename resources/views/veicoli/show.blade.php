@extends('layouts.app')
@section('title', $vehicle->plate.' — '.$vehicle->brand.' '.$vehicle->model)
@section('topbar-actions')
<a href="{{ route('veicoli.edit', ['veicoli' => $vehicle]) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('veicoli.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Veicoli</a></div>

<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Dati veicolo</div>
      <div class="info-row"><span class="info-label">Targa</span><span class="info-value"><span class="targa">{{ $vehicle->plate }}</span></span></div>
      <div class="info-row"><span class="info-label">VIN / Telaio</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $vehicle->vin ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Marca / Modello</span><span class="info-value">{{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->version }}</span></div>
      <div class="info-row"><span class="info-label">Anno</span><span class="info-value">{{ $vehicle->year ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Colore</span><span class="info-value">{{ $vehicle->color ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Alimentazione</span><span class="info-value">{{ ucfirst($vehicle->fuel_type ?? '—') }}</span></div>
      <div class="info-row"><span class="info-label">Km attuali</span><span class="info-value">{{ $vehicle->km_current ? number_format($vehicle->km_current,0,',','.') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Proprietario</span><span class="info-value"><a href="{{ route('clienti.show', $vehicle->customer) }}" style="color:var(--green);text-decoration:none">{{ $vehicle->customer->display_name }}</a></span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge {{ $vehicle->status==='in_officina' ? 'badge-amber' : ($vehicle->status==='pronto' ? 'badge-green' : 'badge-gray') }}">{{ str_replace('_',' ',ucfirst($vehicle->status)) }}</span></span></div>
    </div>

    <div class="card">
      <div class="card-title">Assicurazione & Scadenze</div>
      <div class="info-row"><span class="info-label">Compagnia</span><span class="info-value">{{ $vehicle->insurance_company ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">N° Polizza</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $vehicle->insurance_policy ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Scad. assicurazione</span><span class="info-value" style="color:{{ $vehicle->insurance_expiry && $vehicle->insurance_expiry->isPast() ? 'var(--red)' : ($vehicle->insurance_expiry && $vehicle->insurance_expiry->diffInDays(now()) <= 30 ? 'var(--amber)' : 'var(--text)') }}">{{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d/m/Y') : '—' }}</span></div>
      <div class="info-row"><span class="info-label">Scad. revisione</span><span class="info-value" style="color:{{ $vehicle->isRevisionExpiringSoon() ? 'var(--amber)' : 'var(--text)' }}">{{ $vehicle->revision_expiry ? $vehicle->revision_expiry->format('d/m/Y') : '—' }}</span></div>
    </div>

    {{-- DOCUMENTI VEICOLO --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="card-title" style="margin-bottom:0">Documenti</div>
        <button onclick="document.getElementById('modal-upload-doc').style.display='flex'" class="btn btn-ghost btn-sm">+ Carica documento</button>
      </div>

      @php
        $tipiDoc = \App\Models\VehicleDocument::tipi();
        $docsPerTipo = $vehicle->vehicleDocuments->groupBy('tipo');
      @endphp

      @if($vehicle->vehicleDocuments->isEmpty())
        <div style="color:var(--text3);font-size:13px;text-align:center;padding:16px">Nessun documento caricato</div>
      @else
        @foreach($tipiDoc as $tipo => $label)
          @if(isset($docsPerTipo[$tipo]))
          <div style="margin-bottom:12px">
            <div style="font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px">{{ $label }}</div>
            @foreach($docsPerTipo[$tipo] as $doc)
            @php $file = $doc->getFirstMedia('file'); @endphp
            <div class="fleet-item" style="padding:10px 12px">
              <div style="flex:1">
                <div style="display:flex;align-items:center;gap:8px">
                  <span style="font-size:13px;font-weight:500">{{ $doc->nome }}</span>
                  @if(!$doc->attivo)<span class="badge badge-gray" style="font-size:10px">Storico</span>@endif
                  @if($doc->isScaduto())<span class="badge badge-red" style="font-size:10px">Scaduto</span>
                  @elseif($doc->isInScadenza())<span class="badge badge-amber" style="font-size:10px">In scadenza</span>@endif
                </div>
                <div style="font-size:11px;color:var(--text3);margin-top:2px">
                  @if($doc->data_emissione) Emesso: {{ $doc->data_emissione->format('d/m/Y') }} @endif
                  @if($doc->data_scadenza) · Scade: {{ $doc->data_scadenza->format('d/m/Y') }} @endif
                  · {{ $doc->uploadedBy?->name ?? 'Sistema' }}
                </div>
              </div>
              <div style="display:flex;gap:6px">
                @if($file)
                <a href="{{ $file->getUrl() }}" target="_blank" class="btn btn-ghost btn-sm">📄 Apri</a>
                @endif
                <form method="POST" action="{{ route('veicoli.documento.delete', [$vehicle, $doc->id]) }}" onsubmit="return confirm('Eliminare?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">✕</button>
                </form>
              </div>
            </div>
            @endforeach
          </div>
          @endif
        @endforeach
      @endif
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Sinistri</div>
      @forelse($vehicle->claims->sortByDesc('event_date') as $c)
      <div class="tl-item">
        <div class="tl-dot {{ in_array($c->status,['chiuso','liquidato']) ? 'gray' : 'amber' }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('sinistri.show', $c) }}" style="color:var(--green);text-decoration:none">#{{ $c->claim_number }}</a></div>
          <div class="tl-meta">{{ $c->event_date?->format('d/m/Y') }} · {{ str_replace('_',' ',ucfirst($c->status)) }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun sinistro</div>
      @endforelse
      <a href="{{ route('sinistri.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuovo sinistro</a>
    </div>

    <div class="card">
      <div class="card-title">Lavorazioni</div>
      @forelse($vehicle->workOrders->sortByDesc('created_at')->take(5) as $wo)
      <div class="tl-item">
        <div class="tl-dot {{ $wo->status==='completato' ? 'gray' : 'blue' }}"></div>
        <div class="tl-body">
          <div class="tl-title"><a href="{{ route('lavorazioni.show', $wo) }}" style="color:var(--green);text-decoration:none">#{{ $wo->job_number }}</a> — {{ ucfirst($wo->job_type) }}</div>
          <div class="tl-meta">{{ $wo->created_at->format('d/m/Y') }} · {{ str_replace('_',' ',ucfirst($wo->status)) }}</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessuna lavorazione</div>
      @endforelse
      <a href="{{ route('lavorazioni.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">+ Nuova lavorazione</a>
    </div>
  </div>
</div>

{{-- MODAL UPLOAD DOCUMENTO --}}
<div id="modal-upload-doc" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:24px;width:480px;max-width:95vw">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <div style="font-size:15px;font-weight:600">Carica documento</div>
      <button onclick="document.getElementById('modal-upload-doc').style.display='none'" style="background:none;border:none;color:var(--text3);cursor:pointer;font-size:18px">×</button>
    </div>
    <form method="POST" action="{{ route('veicoli.documento', $vehicle) }}" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label class="form-label">Tipo documento *</label>
        <select name="tipo" class="form-select" required>
          @foreach(\App\Models\VehicleDocument::tipi() as $v => $l)
          <option value="{{ $v }}">{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Nome (opzionale)</label>
        <input name="nome" class="form-input" placeholder="Lascia vuoto per usare il tipo">
      </div>
      <div class="two-col" style="gap:10px">
        <div class="form-group">
          <label class="form-label">Data emissione</label>
          <input type="date" name="data_emissione" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Data scadenza</label>
          <input type="date" name="data_scadenza" class="form-input">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">File * (PDF, JPG, PNG, DOC — max 20MB)</label>
        <input type="file" name="file" class="form-input" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
      </div>
      <div class="form-group">
        <label class="form-label">Note</label>
        <textarea name="note" class="form-textarea" style="min-height:60px"></textarea>
      </div>
      <div style="display:flex;gap:8px;margin-top:8px">
        <button type="button" onclick="document.getElementById('modal-upload-doc').style.display='none'" class="btn btn-ghost" style="flex:1">Annulla</button>
        <button type="submit" class="btn btn-primary" style="flex:1">Carica</button>
      </div>
    </form>
  </div>
</div>
@endsection