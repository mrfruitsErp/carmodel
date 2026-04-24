@extends('layouts.app')
@section('title', 'Impostazioni')
@section('content')
<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">
  <div class="card" style="padding:8px">
    <div style="font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppi as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}" class="nav-item {{ request()->route('gruppo') == $key ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0">
      @php $icons = ['generale'=>'🏢','mail'=>'✉️','sms'=>'📱','fascicoli'=>'📁','documenti'=>'📄','notifiche'=>'🔔','privacy'=>'🔒','veicoli'=>'🚗'] @endphp
      <span style="font-size:13px">{{ $icons[$key] ?? '⚙️' }}</span> {{ $label }}
    </a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <div style="border-top:1px solid var(--border2);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0">👥 Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0">📋 Catalogo documenti</a>
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