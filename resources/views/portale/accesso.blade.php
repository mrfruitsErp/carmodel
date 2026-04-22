{{-- portale/accesso.blade.php --}}
@extends('portale.layout')
@section('title', 'Accesso')

@section('content')

<div class="card" style="text-align:center;padding:32px 24px">
  <div style="font-size:36px;margin-bottom:12px">🔐</div>
  <div class="card-title" style="margin-bottom:8px">Accesso al portale documenti</div>
  <div style="font-size:13px;color:var(--text2);margin-bottom:24px">
    Hai ricevuto questo link da <strong>CarModel</strong>.<br>
    Per accedere ai tuoi documenti, verifica la tua identità.
  </div>

  <div style="background:var(--bg3);border-radius:var(--radius);padding:14px;margin-bottom:24px;font-size:13px;color:var(--text2)">
    Pratica: <strong>{{ $fascicoloToken->fascicolo->tipo_pratica_label }}</strong>
    @if($fascicoloToken->fascicolo->titolo)
      &nbsp;·&nbsp; {{ $fascicoloToken->fascicolo->titolo }}
    @endif
  </div>

  <form method="POST" action="{{ route('portale.verifica', $token) }}">
    @csrf
    <div class="form-group" style="text-align:left">
      <label class="form-label">
        @if($tipo === 'privato')
          Codice Fiscale
        @else
          Partita IVA
        @endif
      </label>
      <input type="text" name="codice" class="form-input"
        placeholder="{{ $tipo === 'privato' ? 'Es. RSSMRA80A01H501U' : 'Es. 01234567890' }}"
        style="text-transform:uppercase;letter-spacing:.1em;font-size:16px;text-align:center"
        autocomplete="off" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary" style="margin-top:8px">
      → Accedi
    </button>
  </form>
</div>

<div style="text-align:center;font-size:12px;color:var(--text3)">
  🔒 Connessione sicura · I tuoi dati sono protetti
</div>

@endsection