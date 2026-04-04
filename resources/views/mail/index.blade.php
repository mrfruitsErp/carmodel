@extends('layouts.app')
@section('title', 'Mail & Notifiche')
@section('topbar-actions')
<a href="{{ route('mail.template.create') }}" class="btn btn-primary btn-sm">+ Nuovo Template</a>
@endsection
@section('content')
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Template automatici</div>
      @forelse($templates as $t)
      <div class="fleet-item">
        <div class="fleet-status {{ $t->active ? 'green' : 'gray' }}"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $t->name }}</div>
          <div style="font-size:11px;color:var(--text3)">Trigger: {{ str_replace('_',' ',ucfirst($t->trigger_event)) }}</div>
        </div>
        <span class="badge {{ $t->active ? 'badge-green' : 'badge-gray' }}">{{ $t->active ? 'Attivo' : 'Disattivo' }}</span>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px;padding:16px">Nessun template configurato</div>
      @endforelse
      <div style="margin-top:8px">
        <a href="{{ route('mail.template.create') }}" class="btn btn-ghost btn-sm" style="width:100%">+ Aggiungi template</a>
      </div>
    </div>

    <div class="card">
      <div class="card-title">Trigger disponibili</div>
      @php $triggers = ['claim_opened'=>'Apertura sinistro','cid_expiry_48h'=>'Scadenza CID 48h','survey_scheduled'=>'Perizia fissata','job_completed'=>'Lavorazione completata','vehicle_ready'=>'Veicolo pronto','rental_expiry_24h'=>'Scadenza noleggio 24h','invoice_overdue'=>'Fattura scaduta','quote_sent'=>'Preventivo inviato']; @endphp
      @foreach($triggers as $key => $label)
      <div class="info-row">
        <span class="info-label">{{ $label }}</span>
        <span class="info-value">
          @if($templates->where('trigger_event',$key)->where('active',true)->count())
          <span class="badge badge-green">✓ Attivo</span>
          @else
          <span class="badge badge-gray">Non configurato</span>
          @endif
        </span>
      </div>
      @endforeach
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title">Ultime mail inviate <span class="badge badge-blue" style="margin-left:8px">{{ $unread }} non lette</span></div>
      @forelse($log as $m)
      <div style="padding:12px 0;border-bottom:1px solid var(--border);display:flex;gap:10px;align-items:flex-start">
        <div style="width:8px;height:8px;border-radius:50%;background:{{ $m->status === 'sent' ? 'var(--green)' : ($m->status === 'failed' ? 'var(--red)' : 'var(--border2)') }};margin-top:5px;flex-shrink:0"></div>
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $m->to_name ?? $m->to_email }}</div>
          <div style="font-size:12px;color:var(--text3)">{{ $m->subject }}</div>
          <div style="font-size:11px;color:var(--text3);margin-top:2px">{{ $m->created_at->diffForHumans() }} · {{ $m->is_automatic ? 'Automatica' : 'Manuale' }}</div>
        </div>
        <span class="badge {{ $m->status === 'sent' ? 'badge-green' : ($m->status === 'failed' ? 'badge-red' : 'badge-gray') }}">{{ ucfirst($m->status) }}</span>
      </div>
      @empty
      <div style="color:var(--text3);font-size:13px;padding:16px;text-align:center">Nessuna mail inviata</div>
      @endforelse
    </div>

    <div class="card">
      <div class="card-title">Invia mail manuale</div>
      <form method="POST" action="{{ route('mail.template.store') }}">
        @csrf
        <div class="form-group"><label class="form-label">Destinatario (email)</label><input name="to_email" type="email" class="form-input" placeholder="cliente@email.it"></div>
        <div class="form-group"><label class="form-label">Oggetto</label><input name="subject" class="form-input" placeholder="Oggetto della mail"></div>
        <div class="form-group"><label class="form-label">Messaggio</label><textarea name="body" class="form-textarea" style="min-height:100px" placeholder="Testo della mail..."></textarea></div>
        <button type="submit" class="btn btn-primary" style="width:100%">✉ Invia mail</button>
      </form>
    </div>
  </div>
</div>
@endsection
