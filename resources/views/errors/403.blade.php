@extends('layouts.app')
@section('title', 'Accesso negato')
@section('content')
<div style="text-align:center;padding:80px 24px">
  <div style="font-size:80px;margin-bottom:16px">🔒</div>
  <div style="font-family:var(--font-display);font-size:32px;font-weight:700;color:var(--text);margin-bottom:12px">Accesso negato</div>
  <div style="font-size:15px;color:var(--text3);margin-bottom:32px">Non hai i permessi per accedere a questa sezione.<br>Contatta l'amministratore per richiedere l'accesso.</div>
  <a href="{{ route('dashboard') }}" class="btn btn-primary">← Torna alla dashboard</a>
</div>
@endsection