@php $userLoggedIn = auth()->check(); @endphp

@if($userLoggedIn)
  @extends('layouts.app')
  @section('title', 'Errore del server')
  @section('content')
    <div style="text-align:center;padding:60px 24px;max-width:560px;margin:0 auto">
      <div style="font-size:80px;margin-bottom:8px">⚠️</div>
      <div style="font-family:var(--font-display);font-size:48px;font-weight:800;background:linear-gradient(135deg,#ff6b00 0%,#ffa64d 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:8px">500</div>
      <div style="font-family:var(--font-display);font-size:24px;font-weight:700;color:var(--text);margin-bottom:14px">Errore del server</div>
      <div style="font-size:14px;color:var(--text2);margin-bottom:8px;line-height:1.7">
        Si è verificato un errore inatteso durante l'elaborazione della richiesta.
      </div>
      <div style="font-size:13px;color:var(--text3);margin-bottom:28px;line-height:1.7">
        Il problema è stato registrato. Riprova fra qualche istante o contatta l'assistenza se persiste.
      </div>
      <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
        <a href="javascript:history.back()" class="btn btn-ghost">← Torna indietro</a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
      </div>
    </div>
  @endsection
@else
  @extends('errors.layout')
  @section('content')
    <div class="error-icon">⚠️</div>
    <div class="error-code">500</div>
    <div class="error-title">Errore del server</div>
    <div class="error-msg">Si è verificato un errore inatteso. Il problema è stato registrato.</div>
    <div class="error-hint">Riprova fra qualche istante.</div>
    <div class="actions">
      <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
      <a href="javascript:location.reload()" class="btn btn-ghost">Ricarica</a>
    </div>
  @endsection
@endif
