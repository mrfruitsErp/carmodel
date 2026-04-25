@extends('layouts.app')
@section('title', isset($user->id) ? 'Modifica Utente' : 'Nuovo Utente')
@section('content')
<div style="max-width:800px">
  <div style="margin-bottom:16px"><a href="{{ route('utenti.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&larr; Utenti</a></div>

  <form action="{{ isset($user->id) ? route('utenti.update',$user) : route('utenti.store') }}" method="POST">
    @csrf @if(isset($user->id)) @method('PUT') @endif
    @if($errors->any())<div class="alert alert-red">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    {{-- DATI BASE --}}
    <div class="card">
      <div class="card-title">Dati utente</div>
      <div class="two-col">
        <div class="form-group">
          <label class="form-label">Nome *</label>
          <input type="text" name="name" value="{{ old('name',$user->name??'') }}" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input type="email" name="email" value="{{ old('email',$user->email??'') }}" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">Password {{ isset($user->id) ? '(lascia vuoto per non cambiare)' : '*' }}</label>
          <input type="password" name="password" class="form-input" {{ isset($user->id) ? '' : 'required' }}>
        </div>
        <div class="form-group">
          <label class="form-label">Conferma password</label>
          <input type="password" name="password_confirmation" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Telefono</label>
          <input type="text" name="phone" value="{{ old('phone',$user->phone??'') }}" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Ruolo base *</label>
          <select name="role" class="form-select">
            @foreach(['admin'=>'Admin','manager'=>'Manager','operatore'=>'Operatore','vendite'=>'Vendite'] as $val=>$label)
            <option value="{{ $val }}" {{ old('role',$user->role??'operatore')===$val?'selected':'' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Note interne</label>
        <textarea name="notes" class="form-textarea" rows="2">{{ old('notes',$user->notes??'') }}</textarea>
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
          <input type="checkbox" name="active" value="1" {{ old('active',$user->active??true)?'checked':'' }} style="accent-color:var(--orange)">
          Utente attivo
        </label>
      </div>
    </div>

    {{-- PERMESSI CUSTOM --}}
    <div class="card">
      <div class="card-title">Permessi personalizzati</div>
      <div style="font-size:12px;color:var(--text3);margin-bottom:8px;line-height:1.7">
        <strong style="color:var(--orange)">Regola di funzionamento:</strong><br>
        • <strong>Nessuna checkbox spuntata</strong> → l'utente usa i permessi predefiniti del ruolo base (Admin, Manager, Operatore, Vendite).<br>
        • <strong>Almeno una checkbox spuntata</strong> → vengono usati ESATTAMENTE i permessi spuntati (le altre voci sono negate).<br>
        • <strong>"Vedere"</strong> = leggere/aprire la sezione · <strong>"Modificare"</strong> = creare/aggiornare/eliminare.<br>
        • Gli utenti con ruolo <strong>Admin</strong> hanno sempre tutti i permessi (le checkbox vengono ignorate).
      </div>
      <div style="background:var(--orange-bg);border:1px solid rgba(255,107,0,.3);border-radius:6px;padding:8px 12px;margin-bottom:16px;font-size:11px;color:var(--text2)">
        💡 Esempio: per dare a un utente accesso solo a Clienti e Veicoli, spunta solo le 4 caselle in quei due gruppi.
      </div>

      @php $customPerms = $user->custom_permissions ?? []; @endphp

      @foreach(\App\Models\User::ALL_PERMISSIONS as $section => $actions)
      <div style="margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border)">
        <div style="font-size:11px;font-weight:700;color:var(--text2);letter-spacing:.1em;text-transform:uppercase;margin-bottom:10px">
          {{ ucfirst($section) }}
        </div>
        <div style="display:flex;gap:20px;flex-wrap:wrap">
          @foreach($actions as $action => $label)
          @php
            $key = "{$section}.{$action}";
            $fieldName = "perm_{$section}_{$action}";
            $checked = old($fieldName) !== null
              ? old($fieldName)
              : (isset($customPerms[$key]) ? $customPerms[$key] : null);
          @endphp
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;background:var(--bg3);padding:8px 12px;border-radius:6px;border:1px solid var(--border2)">
            <input type="checkbox" name="{{ $fieldName }}" value="1"
              {{ $checked ? 'checked' : '' }}
              style="accent-color:var(--orange);width:15px;height:15px">
            {{ $label }}
          </label>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end">
      <a href="{{ route('utenti.index') }}" class="btn btn-ghost">Annulla</a>
      <button type="submit" class="btn btn-primary">{{ isset($user->id) ? 'Salva modifiche' : 'Crea utente' }}</button>
    </div>
  </form>
</div>
@endsection