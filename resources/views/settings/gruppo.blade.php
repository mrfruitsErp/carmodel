@extends('layouts.app')
@section('title', 'Impostazioni — ' . ($gruppiLabel[$gruppo] ?? ucfirst($gruppo)))

@section('content')

<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">

  {{-- MENU GRUPPI (identico a index) --}}
  <div class="card" style="padding:8px">
    <div style="font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppiLabel as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}"
       class="nav-item {{ $gruppo == $key ? 'active' : '' }}"
       style="border-radius:var(--radius);margin:2px 0">
      {{ $label }}
    </a>
    @endforeach
    @if(auth()->user()->hasRole('admin'))
    <div style="border-top:1px solid var(--border2);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item {{ $gruppo == 'permessi' ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0">Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0">Catalogo documenti</a>
    @endif
  </div>

  {{-- FORM GRUPPO --}}
  <div>

  @if($gruppo === 'permessi')
    {{-- SEZIONE PERMESSI --}}
    <div class="card">
      <div class="card-title">Permessi operatori</div>
      <p style="font-size:13px;color:var(--text2);margin-bottom:16px">
        Seleziona un operatore e scegli quali sezioni delle impostazioni può modificare.
      </p>

      @php $tutti = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)->whereDoesntHave('roles', fn($q)=>$q->where('name','admin'))->get(); @endphp

      @foreach($tutti as $op)
      <form method="POST" action="{{ route('settings.permessi.aggiorna') }}" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border2)">
        @csrf
        <input type="hidden" name="user_id" value="{{ $op->id }}">
        <div style="font-weight:600;font-size:13px;margin-bottom:10px">{{ $op->name }}</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px">
          @foreach($gruppiLabel as $gKey => $gLabel)
          @php
            $hasPerm = \DB::table('setting_permissions')
              ->where('tenant_id', auth()->user()->tenant_id)
              ->where('user_id', $op->id)
              ->where('gruppo', $gKey)
              ->where('can_edit', true)
              ->exists();
          @endphp
          <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer">
            <input type="checkbox" name="permessi[{{ $gKey }}]" value="1" {{ $hasPerm ? 'checked' : '' }}>
            {{ $gLabel }}
          </label>
          @endforeach
        </div>
        <button type="submit" class="btn btn-ghost btn-sm">Salva permessi {{ $op->name }}</button>
      </form>
      @endforeach

      @if($tutti->isEmpty())
        <div style="text-align:center;color:var(--text3);padding:20px">Nessun operatore trovato</div>
      @endif
    </div>

  @else
    {{-- FORM IMPOSTAZIONI --}}
    <form method="POST" action="{{ route('settings.salva', $gruppo) }}">
      @csrf

      <div class="card">
        <div class="card-title">{{ $gruppiLabel[$gruppo] ?? ucfirst($gruppo) }}</div>

        @php $defaults = \App\Models\Setting::defaultPerGruppo()[$gruppo] ?? []; @endphp

        @foreach($defaults as $chiave => $default)
          @php
            $record = $settings[$chiave] ?? null;
            $valore = $record?->valore ?? $default;
            $isSecret = $record?->is_secret ?? false;
            $label = ucwords(str_replace('_', ' ', $chiave));
          @endphp

          <div class="form-group">
            <label class="form-label">{{ $label }}</label>

            {{-- Textarea per testi lunghi --}}
            @if(str_contains($chiave, 'testo') || str_contains($chiave, 'template'))
              <textarea name="{{ $chiave }}" class="form-textarea" style="min-height:120px">{{ $valore }}</textarea>

            {{-- Toggle per booleani --}}
            @elseif(in_array($chiave, ['notifica_campanellina','notifica_email','notifica_email_admin','firma_cartacea_attiva']))
              <div style="display:flex;gap:12px">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                  <input type="radio" name="{{ $chiave }}" value="1" {{ $valore == '1' ? 'checked' : '' }}> Abilitato
                </label>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                  <input type="radio" name="{{ $chiave }}" value="0" {{ $valore != '1' ? 'checked' : '' }}> Disabilitato
                </label>
              </div>

            {{-- Select per provider SMS --}}
            @elseif($chiave === 'sms_provider')
              <select name="{{ $chiave }}" class="form-select">
                <option value="self_hosted" {{ $valore == 'self_hosted' ? 'selected' : '' }}>Self-hosted (OTP email)</option>
                <option value="twilio" {{ $valore == 'twilio' ? 'selected' : '' }}>Twilio</option>
                <option value="esendex" {{ $valore == 'esendex' ? 'selected' : '' }}>eSendex</option>
                <option value="smshosting" {{ $valore == 'smshosting' ? 'selected' : '' }}>SMSHOSTING</option>
                <option value="vonage" {{ $valore == 'vonage' ? 'selected' : '' }}>Vonage</option>
              </select>

            {{-- Select per modalità firma --}}
            @elseif($chiave === 'firma_modalita')
              <select name="{{ $chiave }}" class="form-select">
                <option value="self_hosted" {{ $valore == 'self_hosted' ? 'selected' : '' }}>Self-hosted (OTP email)</option>
                <option value="provider_esterno" {{ $valore == 'provider_esterno' ? 'selected' : '' }}>Provider esterno</option>
              </select>

            {{-- Select per provider firma --}}
            @elseif($chiave === 'firma_provider')
              <select name="{{ $chiave }}" class="form-select">
                <option value="" {{ !$valore ? 'selected' : '' }}>Nessuno</option>
                <option value="yousign" {{ $valore == 'yousign' ? 'selected' : '' }}>Yousign</option>
                <option value="namirial" {{ $valore == 'namirial' ? 'selected' : '' }}>Namirial</option>
                <option value="docusign" {{ $valore == 'docusign' ? 'selected' : '' }}>DocuSign</option>
              </select>

            {{-- Campi secret (API key) --}}
            @elseif($isSecret)
              <input type="password" name="{{ $chiave }}" class="form-input"
                value="{{ $valore }}" autocomplete="new-password"
                placeholder="●●●●●●●●●●●●">
              <div style="font-size:11px;color:var(--text3);margin-top:3px">⚠ Valore cifrato — verrà aggiornato solo se modificato</div>

            {{-- Input numerico --}}
            @elseif(in_array($chiave, ['otp_timeout_minuti','otp_lunghezza','link_scadenza_giorni','upload_max_mb','km_alert_soglia','revisione_alert_giorni']))
              <input type="number" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">

            {{-- Input testo standard --}}
            @else
              <input type="text" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">
            @endif

          </div>
        @endforeach

        <div style="margin-top:8px">
          <button type="submit" class="btn btn-primary">✓ Salva impostazioni</button>
        </div>
      </div>
    </form>
  @endif

  </div>
</div>

@endsection