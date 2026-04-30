@extends('layouts.app')
@section('title', 'Sinistro #'.$claim->claim_number)
@section('topbar-actions')
<a href="{{ route('sinistri.stampa', $claim) }}" class="btn btn-ghost btn-sm" target="_blank">🖨️ Stampa</a>
<a href="{{ route('sinistri.edit', $claim) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
<button onclick="document.getElementById('modal-stato').style.display='flex'" class="btn btn-primary btn-sm">Aggiorna stato</button>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('sinistri.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Sinistri</a></div>

@if($claim->isCidExpiringSoon())
<div class="alert alert-amber"><span>⚠</span><span>Scadenza CID il <strong>{{ $claim->cid_expiry->format('d/m/Y') }}</strong> — mancano {{ (int)now()->diffInDays($claim->cid_expiry) }} giorni.</span></div>
@endif
@if($claim->isOverdue())
<div class="alert alert-red"><span>⚠</span><span>CID <strong>scaduto</strong> il {{ $claim->cid_expiry->format('d/m/Y') }} — contattare immediatamente la compagnia.</span></div>
@endif

@php
$stepsMap = [
    0 => ['aperto'],
    1 => ['cid_presentato'],
    2 => ['perizia_attesa','perizia_effettuata'],
    3 => ['in_riparazione','riparazione_completata'],
    4 => ['liquidazione_attesa','liquidato'],
    5 => ['chiuso','archiviato'],
];
$currentStep = 0;
foreach($stepsMap as $idx => $stati) {
    if (in_array($claim->status, $stati)) { $currentStep = $idx; break; }
}
@endphp
<div class="sinistro-stati">
  @foreach(['Apertura','CID','Perizia','Riparazione','Liquidazione','Chiuso'] as $i => $step)
  <div class="stato-step {{ $i < $currentStep ? 'done' : ($i === $currentStep ? 'current' : '') }}">{{ $i+1 }}. {{ $step }}</div>
  @endforeach
</div>

<div class="main-side">
  <div>
    <div class="card">
      <div class="card-title">Dati sinistro <span style="font-family:var(--mono);font-size:11px;color:var(--text3);font-weight:400">#{{ $claim->claim_number }}</span></div>
      <div class="two-col">
        <div>
          <div class="info-row"><span class="info-label">Cliente</span><span class="info-value"><a href="{{ route('clienti.show', $claim->customer) }}" style="color:var(--green);text-decoration:none">{{ $claim->customer->display_name }}</a></span></div>
          <div class="info-row"><span class="info-label">Veicolo</span><span class="info-value">@if($claim->vehicle)
    <span class="targa">{{ $claim->vehicle->plate }}</span> {{ $claim->vehicle->brand }} {{ $claim->vehicle->model }}
@else
    <span class="text-muted">Veicolo non associato</span>
@endif</span></div>
          <div class="info-row"><span class="info-label">Compagnia</span><span class="info-value">{{ $claim->insuranceCompany?->name ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">N° Polizza</span><span class="info-value" style="font-family:var(--mono);font-size:12px">{{ $claim->policy_number ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">Tipo sinistro</span><span class="info-value"><span class="badge badge-blue">{{ strtoupper($claim->claim_type) }}</span></span></div>
        </div>
        <div>
          <div class="info-row"><span class="info-label">Data sinistro</span><span class="info-value">{{ $claim->event_date ? $claim->event_date->format('d/m/Y') : '—' }}</span></div>
          <div class="info-row"><span class="info-label">Luogo</span><span class="info-value">{{ $claim->event_location ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">Controparte targa</span><span class="info-value">{!! $claim->counterpart_plate ? '<span class="targa">'.$claim->counterpart_plate.'</span>' : '—' !!}</span></div>
          <div class="info-row"><span class="info-label">Scadenza CID</span><span class="info-value" style="color:{{ $claim->isCidExpiringSoon() ? 'var(--amber)' : 'var(--text)' }}">{{ $claim->cid_expiry ? $claim->cid_expiry->format('d/m/Y') : '—' }}</span></div>
          <div class="info-row"><span class="info-label">Importo stimato</span><span class="info-value" style="color:var(--green)">{{ $claim->estimated_amount ? '€ '.number_format($claim->estimated_amount,2,',','.') : '—' }}</span></div>
        </div>
      </div>
      @if($claim->event_description)
      <div style="margin-top:12px;padding:12px;background:var(--bg3);border-radius:var(--radius);font-size:13px;color:var(--text2)">{{ $claim->event_description }}</div>
      @endif
    </div>

    @if($claim->workOrders->count())
    <div class="card">
      <div class="card-title">Lavorazione collegata</div>
      @foreach($claim->workOrders as $wo)
      <div class="info-row"><span class="info-label">Commessa</span><span class="info-value"><a href="{{ route('lavorazioni.show', $wo) }}" style="color:var(--green);text-decoration:none">#{{ $wo->job_number }}</a></span></div>
      <div class="info-row"><span class="info-label">Tipo</span><span class="info-value">{{ ucfirst($wo->job_type) }}</span></div>
      <div class="info-row"><span class="info-label">Stato</span><span class="info-value"><span class="badge badge-amber">{{ str_replace('_',' ',ucfirst($wo->status)) }}</span></span></div>
      <div class="info-row"><span class="info-label">Avanzamento</span><span class="info-value">{{ $wo->progress_percent }}%</span></div>
      @endforeach
    </div>
    @endif

    <div class="card">
      <div class="card-title">Storico stati</div>
      @forelse($statusHistory as $h)
      <div class="tl-item">
        <div class="tl-dot blue"></div>
        <div class="tl-body">
          <div class="tl-title">{{ str_replace('_',' ',ucfirst($h->status)) }}</div>
          <div class="tl-meta">{{ \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i') }} @if(isset($h->changedBy) && $h->changedBy) · {{ $h->changedBy->name }} @endif @if($h->notes) · {{ $h->notes }} @endif</div>
        </div>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun cambio stato registrato</div>
      @endforelse
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Perito</div>
      @if($claim->expert)
      <div class="info-row"><span class="info-label">Nome</span><span class="info-value">{{ $claim->expert->name }}</span></div>
      <div class="info-row"><span class="info-label">Compagnia</span><span class="info-value">{{ $claim->expert->insuranceCompany?->name ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Tel</span><span class="info-value">{{ $claim->expert->phone ?? '—' }}</span></div>
      <div class="info-row"><span class="info-label">Data perizia</span><span class="info-value" style="color:var(--amber)">{{ $claim->survey_date ? $claim->survey_date->format('d/m/Y') : 'Da fissare' }}</span></div>
      <div style="margin-top:10px">
        <form method="POST" action="{{ route('sinistri.mail', $claim) }}">
          @csrf
          <input type="hidden" name="trigger_event" value="survey_scheduled">
          <button type="submit" class="btn btn-ghost btn-sm" style="width:100%">✉ Invia mail perito</button>
        </form>
      </div>
      @else
      <div style="color:var(--text3);font-size:13px">Nessun perito assegnato</div>
      @endif
    </div>

    <div class="card">
      <div class="card-title">Lesioni personali</div>
      @forelse($claim->personalInjuries as $li)
      <div class="info-row">
        <span class="info-label"><a href="{{ route('lesioni.show', $li) }}" style="color:var(--green);text-decoration:none">#{{ $li->injury_number }}</a></span>
        <span class="info-value"><span class="badge badge-orange">{{ ucfirst($li->status) }}</span></span>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px">Nessuna lesione registrata</div>
      @endforelse
      <div style="margin-top:10px">
        <a href="{{ route('lesioni.create') }}?claim_id={{ $claim->id }}" class="btn btn-ghost btn-sm" style="width:100%">+ Registra lesione</a>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Mail automatiche</div>
      <form method="POST" action="{{ route('sinistri.mail', $claim) }}" style="margin-bottom:8px">
        @csrf
        <input type="hidden" name="trigger_event" value="claim_opened">
        <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;margin-bottom:6px">✉ Conferma apertura → cliente</button>
      </form>
      <form method="POST" action="{{ route('sinistri.mail', $claim) }}">
        @csrf
        <input type="hidden" name="trigger_event" value="cid_expiry_48h">
        <button type="submit" class="btn btn-ghost btn-sm" style="width:100%">✉ Sollecito CID → compagnia</button>
      </form>
    </div>

    {{-- Liquidatore --}}
    @if($claim->liquidatore)
    <div class="card">
      <div class="card-title">💼 Liquidatore</div>
      <div class="info-row"><span class="info-label">Nome</span><span class="info-value"><strong>{{ $claim->liquidatore->name }}</strong></span></div>
      @if($claim->liquidatore->company_name)<div class="info-row"><span class="info-label">Studio</span><span class="info-value">{{ $claim->liquidatore->company_name }}</span></div>@endif
      @if($claim->liquidatore->phone)<div class="info-row"><span class="info-label">Tel</span><span class="info-value"><a href="tel:{{ $claim->liquidatore->phone }}" style="color:var(--green)">{{ $claim->liquidatore->phone }}</a></span></div>@endif
      @if($claim->liquidatore->orario_disponibilita)<div class="info-row"><span class="info-label">Orario</span><span class="info-value">{{ $claim->liquidatore->orario_disponibilita }}</span></div>@endif
      @if($claim->liquidatore->email)<div class="info-row"><span class="info-label">Email</span><span class="info-value"><a href="mailto:{{ $claim->liquidatore->email }}" style="color:var(--green)">{{ $claim->liquidatore->email }}</a></span></div>@endif
    </div>
    @endif

    {{-- Importi & scadenze --}}
    <div class="card">
      <div class="card-title">💰 Importi</div>
      @if($claim->importo_richiesto)<div class="info-row"><span class="info-label">Richiesto</span><span class="info-value" style="color:var(--amber)">€ {{ number_format($claim->importo_richiesto,2,',','.') }}</span></div>@endif
      @if($claim->importo_perizia)<div class="info-row"><span class="info-label">Perizia</span><span class="info-value">€ {{ number_format($claim->importo_perizia,2,',','.') }}</span></div>@endif
      @if($claim->importo_concordato)<div class="info-row"><span class="info-label">Concordato</span><span class="info-value" style="color:var(--green)">€ {{ number_format($claim->importo_concordato,2,',','.') }}</span></div>@endif
      @if($claim->noleggio_importo)<div class="info-row"><span class="info-label">Noleggio</span><span class="info-value">€ {{ number_format($claim->noleggio_importo,2,',','.') }}{{ $claim->noleggio_giorni ? ' ('.$claim->noleggio_giorni.'gg)' : '' }}</span></div>@endif
      @if($claim->fermo_tecnico_importo)<div class="info-row"><span class="info-label">Fermo tecnico</span><span class="info-value">€ {{ number_format($claim->fermo_tecnico_importo,2,',','.') }}{{ $claim->fermo_tecnico_giorni ? ' ('.$claim->fermo_tecnico_giorni.'gg)' : '' }}</span></div>@endif
      @if($claim->paid_amount)<div class="info-row"><span class="info-label">Liquidato</span><span class="info-value" style="color:var(--green);font-weight:600">€ {{ number_format($claim->paid_amount,2,',','.') }}</span></div>@endif
      @if($claim->onorario_percentuale)<div class="info-row"><span class="info-label">Onorario</span><span class="info-value">{{ $claim->onorario_percentuale }}%</span></div>@endif
      <div class="info-row"><span class="info-label">Recupero IVA</span><span class="info-value">{{ $claim->recupera_iva ? '✅ Sì' : '❌ No' }}</span></div>
    </div>

    @if($claim->scadenza_nomina_perito || $claim->scadenza_chiusura_perito || $claim->scadenza_chiusura_totale)
    <div class="card">
      <div class="card-title">📅 Scadenze legali</div>
      @if($claim->scadenza_nomina_perito)
      <div class="info-row">
        <span class="info-label">10gg — Nomina perito</span>
        <span class="info-value" style="color:{{ $claim->scadenza_nomina_perito->isPast() ? 'var(--red)' : 'var(--amber)' }}">{{ $claim->scadenza_nomina_perito->format('d/m/Y') }}</span>
      </div>
      @endif
      @if($claim->scadenza_chiusura_perito)
      <div class="info-row">
        <span class="info-label">35gg — Chiusura perito</span>
        <span class="info-value" style="color:{{ $claim->scadenza_chiusura_perito->isPast() ? 'var(--red)' : 'var(--amber)' }}">{{ $claim->scadenza_chiusura_perito->format('d/m/Y') }}</span>
      </div>
      @endif
      @if($claim->scadenza_chiusura_totale)
      <div class="info-row">
        <span class="info-label">60gg — Chiusura totale</span>
        <span class="info-value" style="color:{{ $claim->scadenza_chiusura_totale->isPast() ? 'var(--red)' : 'var(--text2)' }}">{{ $claim->scadenza_chiusura_totale->format('d/m/Y') }}</span>
      </div>
      @endif
    </div>
    @endif

    <div class="card">
      <div class="card-title">Note interne</div>
      <form method="POST" action="{{ route('sinistri.update', $claim) }}">
        @csrf @method('PUT')
        <textarea name="internal_notes" class="form-textarea" style="min-height:80px">{{ $claim->internal_notes }}</textarea>
        <button type="submit" class="btn btn-ghost btn-sm" style="margin-top:8px;width:100%">Salva note</button>
      </form>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- DIARIO COMUNICAZIONI                                       --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="card" style="margin-top:16px">
  <div class="section-header">
    <span class="card-title" style="margin-bottom:0">📋 Diario comunicazioni</span>
    <button onclick="document.getElementById('modal-diary').style.display='flex'" class="btn btn-primary btn-sm">+ Aggiungi voce</button>
  </div>

  @forelse($claim->diary as $d)
  <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border1)">
    <div style="min-width:50px;text-align:center">
      <div style="font-size:18px">{{ $d->tipo_icon }}</div>
      <div style="font-size:10px;color:var(--text3)">{{ $d->data_evento->format('d/m') }}</div>
      <div style="font-size:10px;color:var(--text3)">{{ $d->data_evento->format('Y') }}</div>
    </div>
    <div style="flex:1">
      @if($d->oggetto)<div style="font-size:13px;font-weight:600;margin-bottom:3px">{{ $d->oggetto }}</div>@endif
      <div style="font-size:13px;color:var(--text2);white-space:pre-line">{{ $d->testo }}</div>
      <div style="display:flex;gap:10px;margin-top:6px;font-size:11px;color:var(--text3)">
        @if($d->importo)<span style="color:var(--green)">€ {{ number_format($d->importo,2,',','.') }}</span>@endif
        <span>{{ $d->user?->name ?? 'Sistema' }}</span>
        <span class="badge badge-{{ $d->tipo_color }}" style="font-size:10px">{{ $d->tipo }}</span>
      </div>
    </div>
    <form method="POST" action="{{ route('sinistri.diario.destroy', [$claim, $d]) }}">
      @csrf @method('DELETE')
      <button type="submit" style="background:none;border:none;color:var(--text3);cursor:pointer;font-size:16px" onclick="return confirm('Elimina questa voce?')" title="Elimina">×</button>
    </form>
  </div>
  @empty
  <div style="text-align:center;color:var(--text3);padding:20px;font-size:13px">Nessuna voce nel diario</div>
  @endforelse
</div>

{{-- MODAL DIARIO --}}
<div id="modal-diary" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:24px;width:480px;max-height:90vh;overflow-y:auto">
    <div style="font-size:15px;font-weight:600;margin-bottom:16px;display:flex;justify-content:space-between">
      📋 Aggiungi voce al diario
      <button onclick="document.getElementById('modal-diary').style.display='none'" style="background:none;border:none;color:var(--text3);cursor:pointer;font-size:18px">×</button>
    </div>
    <form method="POST" action="{{ route('sinistri.diario.store', $claim) }}">
      @csrf
      <div class="two-col" style="gap:10px">
        <div class="form-group">
          <label class="form-label">Data *</label>
          <input type="date" name="data_evento" class="form-input" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">Tipo *</label>
          <select name="tipo" class="form-select" required>
            @foreach($diaryTipi as $k => $l)
            <option value="{{ $k }}">{{ $l }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Oggetto / titolo</label>
        <input name="oggetto" class="form-input" placeholder="es. Mail liquidatore Emilio Mesto">
      </div>
      <div class="form-group">
        <label class="form-label">Testo *</label>
        <textarea name="testo" class="form-textarea" style="min-height:100px" required placeholder="Descrivi l'azione o la comunicazione..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Importo € (opzionale)</label>
        <input type="number" name="importo" class="form-input" step="0.01" placeholder="0.00">
      </div>
      <div style="display:flex;gap:8px">
        <button type="button" onclick="document.getElementById('modal-diary').style.display='none'" class="btn btn-ghost" style="flex:1">Annulla</button>
        <button type="submit" class="btn btn-primary" style="flex:2">Salva voce</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL AGGIORNA STATO --}}
<div id="modal-stato" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:24px;width:420px">
    <div style="font-size:15px;font-weight:600;margin-bottom:16px;display:flex;justify-content:space-between">
      Aggiorna stato sinistro
      <button onclick="document.getElementById('modal-stato').style.display='none'" style="background:none;border:none;color:var(--text3);cursor:pointer;font-size:18px">×</button>
    </div>
    <form method="POST" action="{{ route('sinistri.stato', $claim) }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nuovo stato</label>
        <select name="status" class="form-select">
          @foreach(['aperto','cid_presentato','perizia_attesa','perizia_effettuata','in_riparazione','riparazione_completata','liquidazione_attesa','liquidato','contestato','chiuso','archiviato'] as $st)
          <option value="{{ $st }}" {{ $claim->status === $st ? 'selected' : '' }}>{{ str_replace('_',' ',ucfirst($st)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Note cambio stato</label>
        <textarea name="notes" class="form-textarea" style="min-height:60px" placeholder="Motivazione, riferimento, ecc..."></textarea>
      </div>
      <div style="display:flex;gap:8px">
        <button type="button" onclick="document.getElementById('modal-stato').style.display='none'" class="btn btn-ghost" style="flex:1">Annulla</button>
        <button type="submit" class="btn btn-primary" style="flex:1">Salva stato</button>
      </div>
    </form>
  </div>
</div>
@endsection