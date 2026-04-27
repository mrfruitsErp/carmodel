@extends('layouts.app')
@section('title', 'Messaggio da '.$messaggio->name)

@section('topbar-actions')
<form method="POST" action="{{ $messaggio->isNotLetto() ? route('messaggi.letto', $messaggio) : route('messaggi.non-letto', $messaggio) }}" style="display:inline">
  @csrf
  <button type="submit" class="btn btn-ghost btn-sm">
    {{ $messaggio->isNotLetto() ? '✓ Segna letto' : '↶ Segna non letto' }}
  </button>
</form>
<form method="POST" action="{{ route('messaggi.spam', $messaggio) }}" style="display:inline">
  @csrf
  <button type="submit" class="btn btn-ghost btn-sm" style="color:{{ $messaggio->is_spam ? 'var(--green-text)' : 'var(--red-text)' }}">
    {{ $messaggio->is_spam ? '✓ Non è spam' : '🚫 Segna spam' }}
  </button>
</form>
<form method="POST" action="{{ route('messaggi.destroy', $messaggio) }}" style="display:inline" onsubmit="return confirm('Eliminare definitivamente il messaggio?')">
  @csrf @method('DELETE')
  <button type="submit" class="btn btn-danger btn-sm">🗑 Elimina</button>
</form>
@endsection

@section('content')
<div style="margin-bottom:16px"><a href="{{ route('messaggi.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Tutti i messaggi</a></div>

@if($messaggio->is_spam)
<div style="background:var(--red-bg);border:1px solid rgba(239,68,68,.4);color:var(--red-text);border-radius:8px;padding:12px 16px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
  <span style="font-size:20px">🚫</span>
  <div>
    <strong>Messaggio classificato come SPAM</strong><br>
    <span style="font-size:12px">Motivo: <code style="font-family:var(--mono);background:rgba(0,0,0,.06);padding:1px 6px;border-radius:3px">{{ $messaggio->spam_reason ?? 'sconosciuto' }}</code>
    @if($messaggio->ip_address) · IP: <code style="font-family:var(--mono)">{{ $messaggio->ip_address }}</code>@endif
    </span>
  </div>
</div>
@endif

<div class="two-col">
  <div>
    {{-- DATI MITTENTE --}}
    <div class="card">
      <div class="card-title">Mittente</div>
      <div class="info-row"><span class="info-label">Nome</span><span class="info-value" style="font-weight:600">{{ $messaggio->name }}</span></div>
      <div class="info-row"><span class="info-label">Email</span><span class="info-value"><a href="mailto:{{ $messaggio->email }}" style="color:var(--green);text-decoration:none">{{ $messaggio->email }}</a></span></div>
      @if($messaggio->phone)
      <div class="info-row"><span class="info-label">Telefono</span><span class="info-value"><a href="tel:{{ $messaggio->phone }}" style="color:var(--green);text-decoration:none">{{ $messaggio->phone }}</a></span></div>
      @endif
      @if($messaggio->fleetVehicle)
      <div class="info-row"><span class="info-label">Veicolo</span><span class="info-value">{{ $messaggio->fleetVehicle->brand }} {{ $messaggio->fleetVehicle->model }} ({{ $messaggio->fleetVehicle->plate }})</span></div>
      @endif
      @if($messaggio->date_start && $messaggio->date_end)
      <div class="info-row"><span class="info-label">Periodo richiesto</span><span class="info-value">{{ $messaggio->date_start->format('d/m/Y') }} → {{ $messaggio->date_end->format('d/m/Y') }} ({{ $messaggio->days }} giorni)</span></div>
      @endif
      <div class="info-row"><span class="info-label">Tipo richiesta</span><span class="info-value">{{ $messaggio->tipo_label }}</span></div>
      <div class="info-row"><span class="info-label">Ricevuto</span><span class="info-value">{{ $messaggio->created_at->format('d/m/Y H:i') }} ({{ $messaggio->created_at->diffForHumans() }})</span></div>
      @if($messaggio->letto_at)
      <div class="info-row"><span class="info-label">Letto</span><span class="info-value" style="color:var(--text3)">{{ $messaggio->letto_at->format('d/m/Y H:i') }} da {{ $messaggio->lettoDa?->name ?? 'Operatore' }}</span></div>
      @endif
    </div>

    {{-- MESSAGGIO --}}
    <div class="card">
      <div class="card-title">Messaggio</div>
      <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:8px;padding:18px;font-size:14px;line-height:1.7;color:var(--text2);white-space:pre-wrap">{{ $messaggio->message ?: '— Nessun messaggio testuale —' }}</div>
    </div>

    {{-- AZIONI RAPIDE --}}
    <div class="card">
      <div class="card-title">Risposta rapida</div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="mailto:{{ $messaggio->email }}?subject={{ urlencode('Re: '.$messaggio->tipo_label) }}" class="btn btn-primary">✉️ Rispondi via email</a>
        @if($messaggio->phone)
          <a href="tel:{{ $messaggio->phone }}" class="btn btn-ghost">📞 Chiama</a>
          @php $waPhone = preg_replace('/[^0-9]/', '', $messaggio->phone); @endphp
          @if(strlen($waPhone) >= 9)
          <a href="https://wa.me/{{ $waPhone }}" target="_blank" rel="noopener" class="btn btn-ghost">💬 WhatsApp</a>
          @endif
        @endif
      </div>
    </div>
  </div>

  <div>
    {{-- STATO --}}
    <div class="card">
      <div class="card-title">Stato richiesta</div>
      <form method="POST" action="{{ route('messaggi.stato', $messaggio) }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Stato *</label>
          <select name="status" class="form-select" required>
            @foreach(['nuova'=>'Nuova','confermata'=>'Confermata','rifiutata'=>'Rifiutata','annullata'=>'Annullata'] as $val=>$lbl)
              <option value="{{ $val }}" {{ $messaggio->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Note interne</label>
          <textarea name="admin_notes" class="form-textarea" rows="6" placeholder="Annotazioni operatore (non visibili al cliente)">{{ $messaggio->admin_notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Salva stato e note</button>
      </form>
    </div>
  </div>
</div>
@endsection
