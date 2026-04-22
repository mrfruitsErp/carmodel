@extends('portale.layout')
@section('title', 'Verifica codice')
@section('content')
<div class="card" style="text-align:center;padding:32px 24px">
  <div style="font-size:36px;margin-bottom:12px">📧</div>
  <div class="card-title">Inserisci il codice</div>
  <div style="font-size:13px;color:var(--text2);margin-bottom:24px">
    Abbiamo inviato un codice di verifica alla tua email.<br>
    Inseriscilo qui sotto (valido 10 minuti).
  </div>
  <form method="POST" action="{{ route('portale.otp.verifica', $token) }}">
    @csrf
    <div class="form-group">
      <input type="text" name="otp" class="form-input" maxlength="6" placeholder="_ _ _ _ _ _"
        style="font-size:28px;text-align:center;letter-spacing:.3em;font-weight:700" autofocus required>
    </div>
    <button type="submit" class="btn btn-primary">✓ Verifica codice</button>
  </form>
  <form method="POST" action="{{ route('portale.otp.reinvia', $token) }}" style="margin-top:12px">
    @csrf
    <button type="submit" class="btn btn-ghost" style="font-size:13px">↺ Invia nuovo codice</button>
  </form>
</div>
@endsection