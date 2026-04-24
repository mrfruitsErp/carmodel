@extends('layouts.app')
@section('title', 'Impostazioni')
@section('content')
<div style="display:grid;grid-template-columns:180px 1fr;gap:16px;align-items:start">
  <div style="background:#111827;border-radius:var(--radius-lg);padding:6px;border:1px solid rgba(255,255,255,.06)">
    <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.25);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppi as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0;font-size:12px">{{ $label }}</a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <div style="border-top:1px solid rgba(255,255,255,.06);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Catalogo documenti</a>
    @endif
  </div>
  <div class="card">
    <div style="text-align:center;padding:32px;color:var(--text3)">
      <div style="font-size:40px;opacity:.3;margin-bottom:12px">⚙️</div>
      <div>Seleziona una sezione dal menu</div>
    </div>
  </div>
</div>
@endsection