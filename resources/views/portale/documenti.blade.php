@extends('portale.layout')
@section('title', 'Carica documenti')

@section('content')

{{-- Step bar --}}
<div class="step-bar">
  <div class="step"><div class="step-circle done">✓</div><div class="step-label">Identità</div></div>
  <div class="step-line done"></div>
  <div class="step"><div class="step-circle done">✓</div><div class="step-label">Privacy</div></div>
  <div class="step-line done"></div>
  <div class="step"><div class="step-circle active">3</div><div class="step-label">Documenti</div></div>
</div>

{{-- HEADER PRATICA --}}
<div class="card" style="padding:16px 20px">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div style="font-weight:600;font-size:15px">
        {{ $fascicolo->tipo_pratica_label }}
        @if($fascicolo->titolo) — {{ $fascicolo->titolo }} @endif
      </div>
      <div style="font-size:12px;color:var(--text2);margin-top:3px">
        Gentile <strong>{{ $fascicolo->cliente->display_name ?? ($fascicolo->cliente->nome . ' ' . $fascicolo->cliente->cognome) }}</strong>,
        carica i documenti richiesti qui sotto.
      </div>
    </div>
    <div style="text-align:right">
      <div style="font-size:11px;color:var(--text3);margin-bottom:4px">COMPLETAMENTO</div>
      <div style="font-family:var(--font-display);font-size:26px;font-weight:700;color:{{ $progresso == 100 ? 'var(--green)' : 'var(--orange)' }}">
        {{ $progresso }}%
      </div>
      <div class="progress" style="width:100px;margin-top:3px">
        <div class="progress-fill" style="width:{{ $progresso }}%"></div>
      </div>
    </div>
  </div>
</div>

{{-- LISTA DOCUMENTI --}}
@foreach($documenti as $doc)
<div class="doc-item {{ $doc->isCompletato() ? 'completato' : '' }} {{ $doc->obbligatorio ? 'obbligatorio' : '' }}">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px">
    <div>
      <div style="font-weight:600;font-size:14px;display:flex;align-items:center;gap:6px">
        @if($doc->isCompletato())
          <span style="color:var(--green)">✓</span>
        @else
          <span style="color:var(--text3)">○</span>
        @endif
        {{ $doc->nome }}
        @if($doc->obbligatorio)
          <span style="color:var(--red);font-size:11px">*obbligatorio</span>
        @endif
      </div>
      <div style="font-size:11px;color:var(--text3);margin-top:3px">
        @if($doc->richiede_upload) 📎 Carica file &nbsp; @endif
        @if($doc->richiede_firma) ✍ Firma richiesta @endif
      </div>
    </div>
    <span class="badge {{ $doc->isCompletato() ? 'badge-green' : 'badge-gray' }}" style="font-size:11px;white-space:nowrap">
      {{ $doc->isCompletato() ? '✓ ' . ucfirst($doc->stato) : ucfirst($doc->stato) }}
    </span>
  </div>

  {{-- FILE GIA CARICATO --}}
  @if($doc->getFirstMedia('file_documento'))
    <div style="background:var(--green-bg);border:1px solid rgba(34,197,94,.3);border-radius:var(--radius);padding:8px 12px;font-size:12px;color:var(--green-text);margin-bottom:10px">
      ✓ File caricato: {{ $doc->getFirstMedia('file_documento')->file_name }}
    </div>
  @endif

  {{-- UPLOAD FILE --}}
  @if($doc->richiede_upload && !$doc->isCompletato())
    <form method="POST" action="{{ route('portale.documenti.upload', [$token, $doc]) }}" enctype="multipart/form-data">
      @csrf
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <input type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" required
          style="flex:1;min-width:200px;font-size:13px;color:var(--text2);background:var(--bg3);border:1.5px dashed var(--border2);border-radius:var(--radius);padding:8px;cursor:pointer">
        <button type="submit" class="btn btn-primary" style="width:auto;padding:9px 16px;font-size:13px">
          📎 Carica
        </button>
      </div>
      <div style="font-size:11px;color:var(--text3);margin-top:5px">Formati accettati: JPG, PNG, PDF · Max 10MB</div>
    </form>
  @endif

  {{-- FIRMA DOCUMENTO --}}
  @if($doc->richiede_firma && in_array($doc->stato, ['richiesto','caricato']))
    @if(!$doc->firma_otp_scadenza || $doc->firma_otp_scadenza->isPast())
      {{-- Richiedi OTP firma --}}
      <form method="POST" action="{{ route('portale.documenti.firma', [$token, $doc]) }}" style="margin-top:8px">
        @csrf
        <button type="submit" class="btn btn-ghost" style="font-size:13px;width:auto;padding:9px 16px">
          ✍ Firma questo documento
        </button>
      </form>
    @else
      {{-- Inserisci OTP firma --}}
      <div style="background:var(--amber-bg);border:1px solid rgba(245,158,11,.3);border-radius:var(--radius);padding:12px;margin-top:8px">
        <div style="font-size:12px;color:#92400e;margin-bottom:8px">
          📧 Codice di firma inviato alla tua email. Inseriscilo per firmare.
        </div>
        <form method="POST" action="{{ route('portale.documenti.firma.otp', [$token, $doc]) }}">
          @csrf
          <div style="display:flex;flex-direction:column;gap:8px">
            <input type="text" name="nome" class="form-input" placeholder="Il tuo nome e cognome" required>
            <div style="display:flex;gap:8px">
              <input type="text" name="otp" class="form-input" maxlength="6" placeholder="Codice 6 cifre"
                style="letter-spacing:.2em;font-size:16px;text-align:center;flex:1" required>
              <button type="submit" class="btn btn-primary" style="width:auto;padding:9px 16px;font-size:13px">✓ Firma</button>
            </div>
          </div>
        </form>
        <form method="POST" action="{{ route('portale.documenti.firma', [$token, $doc]) }}" style="margin-top:6px">
          @csrf
          <button type="submit" style="background:none;border:none;font-size:11px;color:var(--text3);cursor:pointer">↺ Invia nuovo codice</button>
        </form>
      </div>
    @endif
  @endif

  {{-- DOCUMENTO FIRMATO --}}
  @if($doc->stato === 'firmato')
    <div style="font-size:11px;color:var(--green-text);margin-top:6px">
      ✓ Firmato da {{ $doc->firmato_da_nome }} il {{ $doc->firmato_il?->format('d/m/Y H:i') }}
    </div>
  @endif

</div>
@endforeach

{{-- PULSANTE COMPLETA --}}
<div style="margin-top:20px">
  @php
    $obbligatoriMancanti = $documenti->filter(fn($d) => $d->obbligatorio && !$d->isCompletato())->count();
  @endphp

  @if($obbligatoriMancanti > 0)
    <div class="alert alert-amber">
      ⚠ Mancano ancora <strong>{{ $obbligatoriMancanti }}</strong> documento/i obbligatorio/i prima di poter completare.
    </div>
  @endif

  <form method="POST" action="{{ route('portale.completa', $token) }}"
    onsubmit="return confirm('Confermi di aver caricato tutti i documenti richiesti?')">
    @csrf
    <button type="submit" class="btn btn-primary"
      style="background:{{ $obbligatoriMancanti > 0 ? 'var(--bg3)' : 'var(--green)' }};color:{{ $obbligatoriMancanti > 0 ? 'var(--text3)' : '#fff' }};box-shadow:none"
      {{ $obbligatoriMancanti > 0 ? 'disabled' : '' }}>
      ✓ Ho completato — invia tutto
    </button>
  </form>
</div>

@endsection