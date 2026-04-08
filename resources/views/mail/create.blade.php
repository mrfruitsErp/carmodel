@extends('layouts.app')
@section('title', 'Nuova Mail')

@section('content')
<div style="max-width:600px">
  <div style="margin-bottom:16px">
    <a href="{{ route('mail.index') }}" style="font-size:13px;color:var(--text3);text-decoration:none">← Torna a Mail</a>
  </div>
  <div class="card">
    <div class="card-title">✉️ Invia nuova mail</div>
    <form action="{{ route('mail.template.store') }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Destinatario <span style="color:var(--red)">*</span></label>
        <input type="email" name="to_email" class="form-input" required placeholder="cliente@email.it">
      </div>
      <div class="form-group">
        <label class="form-label">Oggetto <span style="color:var(--red)">*</span></label>
        <input type="text" name="subject" class="form-input" required placeholder="Oggetto della mail">
      </div>
      <div class="form-group">
        <label class="form-label">Messaggio</label>
        <textarea name="body" class="form-textarea" rows="8" placeholder="Testo della mail..."></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('mail.index') }}" class="btn btn-ghost">Annulla</a>
        <button type="submit" class="btn btn-primary">📤 Invia mail</button>
      </div>
    </form>
  </div>
</div>
@endsection