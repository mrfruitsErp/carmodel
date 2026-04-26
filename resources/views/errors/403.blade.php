@php
  // Estrai il messaggio dell'eccezione (es. "Permesso richiesto: clienti.edit")
  $msg = isset($exception) ? $exception->getMessage() : '';
  $permission = null;
  if (preg_match('/Permesso richiesto:\s*([a-zA-Z0-9_.-]+)/', $msg, $m)) {
      $permission = $m[1];
  }
  $userLoggedIn = auth()->check();
@endphp

@if($userLoggedIn)
  @extends('layouts.app')
  @section('title', 'Accesso negato')
  @section('content')
    <div style="text-align:center;padding:60px 24px;max-width:560px;margin:0 auto">
      <div style="font-size:80px;margin-bottom:16px">🔒</div>
      <div style="font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--text);margin-bottom:12px">Accesso negato</div>
      <div style="font-size:15px;color:var(--text2);margin-bottom:8px;line-height:1.6">
        Non hai i permessi necessari per accedere a questa sezione.
      </div>
      @if($permission)
      <div style="display:inline-block;background:rgba(255,107,0,.08);border:1px solid rgba(255,107,0,.25);color:#ff9d4d;font-size:12px;font-family:var(--mono);padding:6px 14px;border-radius:6px;margin:14px 0;letter-spacing:.04em">
        Permesso richiesto: <strong>{{ $permission }}</strong>
      </div>
      @endif
      <div style="font-size:13px;color:var(--text3);margin-bottom:28px;line-height:1.7">
        Se ritieni che dovresti avere accesso, contatta l'amministratore<br>
        chiedendo l'attivazione del permesso indicato.
      </div>
      <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
        <a href="javascript:history.back()" class="btn btn-ghost">← Torna indietro</a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Vai alla dashboard</a>
      </div>
    </div>
  @endsection
@else
  @extends('errors.layout')
  @section('content')
    <div class="error-icon">🔒</div>
    <div class="error-code">403</div>
    <div class="error-title">Accesso negato</div>
    <div class="error-msg">Non hai i permessi per accedere a questa pagina.</div>
    @if($permission)
    <div class="error-detail">Permesso richiesto: <strong>{{ $permission }}</strong></div>
    @endif
    <div class="error-hint">Effettua il login con un account autorizzato.</div>
    <div class="actions">
      <a href="{{ url('/') }}" class="btn btn-ghost">Home</a>
      <a href="{{ route('login') }}" class="btn btn-primary">Accedi</a>
    </div>
  @endsection
@endif
