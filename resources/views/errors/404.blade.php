@php $userLoggedIn = auth()->check(); @endphp

@if($userLoggedIn)
  @extends('layouts.app')
  @section('title', 'Pagina non trovata')
  @section('content')
    <div style="text-align:center;padding:60px 24px;max-width:560px;margin:0 auto">
      <div style="font-size:80px;margin-bottom:8px">🧭</div>
      <div style="font-family:var(--font-display);font-size:48px;font-weight:800;background:linear-gradient(135deg,#ff6b00 0%,#ffa64d 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:8px">404</div>
      <div style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--text);margin-bottom:14px">Pagina non trovata</div>
      <div style="font-size:14px;color:var(--text2);margin-bottom:8px;line-height:1.7">
        L'URL richiesto non esiste o è stato spostato.
      </div>
      <div style="font-size:13px;color:var(--text3);margin-bottom:28px;line-height:1.7">
        Verifica il link, oppure torna alla dashboard.
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
    <div class="error-icon">🧭</div>
    <div class="error-code">404</div>
    <div class="error-title">Pagina non trovata</div>
    <div class="error-msg">L'URL richiesto non esiste o è stato spostato.</div>
    <div class="error-hint">Controlla il link, oppure torna al sito.</div>
    <div class="actions">
      <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
      @if(\Route::has('login'))
      <a href="{{ route('login') }}" class="btn btn-ghost">Accedi</a>
      @endif
    </div>
  @endsection
@endif
