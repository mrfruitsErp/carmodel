@extends('portale.layout')
@section('title', 'Completato')
@section('content')

<div class="card" style="text-align:center;padding:40px 24px">
  <div style="font-size:56px;margin-bottom:16px">🎉</div>
  <div class="card-title" style="font-size:22px;margin-bottom:8px;color:var(--green-text)">
    Documenti inviati con successo!
  </div>
  <div style="font-size:14px;color:var(--text2);line-height:1.8;margin-bottom:24px">
    Grazie! Abbiamo ricevuto tutti i tuoi documenti per la pratica<br>
    <strong>{{ $fascicolo->tipo_pratica_label }}
      @if($fascicolo->titolo) — {{ $fascicolo->titolo }} @endif
    </strong>.<br><br>
    Il nostro team li verificherà e ti contatteremo a breve.
  </div>
  <div style="background:var(--green-bg);border:1px solid rgba(34,197,94,.3);border-radius:var(--radius);padding:16px;font-size:13px;color:var(--green-text)">
    ✓ Ricevuto il {{ now()->format('d/m/Y') }} alle {{ now()->format('H:i') }}
  </div>
</div>

<div style="text-align:center;font-size:12px;color:var(--text3)">
  Puoi chiudere questa pagina in sicurezza.
</div>

@endsection