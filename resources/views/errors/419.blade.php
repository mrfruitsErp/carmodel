@extends('errors.layout')
@section('content')
  <div class="error-icon">⏱️</div>
  <div class="error-code">419</div>
  <div class="error-title">Sessione scaduta</div>
  <div class="error-msg">La pagina è stata aperta troppo tempo fa. Per sicurezza la sessione è scaduta.</div>
  <div class="error-hint">Ricarica la pagina e riprova.</div>
  <div class="actions">
    <a href="javascript:location.reload()" class="btn btn-primary">Ricarica</a>
    <a href="{{ url('/') }}" class="btn btn-ghost">Home</a>
  </div>
@endsection
