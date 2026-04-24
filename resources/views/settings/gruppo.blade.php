@extends('layouts.app')
@section('title', 'Impostazioni — '.($gruppiLabel[$gruppo] ?? ucfirst($gruppo)))
@section('content')
<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:start">
  <div class="card" style="padding:8px">
    <div style="font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppiLabel as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}" class="nav-item {{ $gruppo == $key ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0">
      @php $icons = ['generale'=>'🏢','mail'=>'✉️','sms'=>'📱','fascicoli'=>'📁','documenti'=>'📄','notifiche'=>'🔔','privacy'=>'🔒','veicoli'=>'🚗'] @endphp
      <span style="font-size:13px">{{ $icons[$key] ?? '⚙️' }}</span> {{ $label }}
    </a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <div style="border-top:1px solid var(--border2);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item {{ $gruppo == 'permessi' ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0">👥 Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0">📋 Catalogo documenti</a>
    @endif
  </div>

  <div>
    @if($gruppo === 'permessi')
    <div class="card">
      <div class="card-title">Permessi operatori</div>
      @php $tutti = \App\Models\User::where('tenant_id', auth()->user()->tenant_id)->where('role','!=','admin')->get(); @endphp
      @forelse($tutti as $op)
      <form method="POST" action="{{ route('settings.permessi.aggiorna') }}" style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border2)">
        @csrf
        <input type="hidden" name="user_id" value="{{ $op->id }}">
        <div style="font-weight:600;font-size:13px;margin-bottom:10px">{{ $op->name }} <span class="badge badge-gray">{{ ucfirst($op->role) }}</span></div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px">
          @foreach($gruppiLabel as $gKey => $gLabel)
          @php $hasPerm = \DB::table('setting_permissions')->where('tenant_id', auth()->user()->tenant_id)->where('user_id', $op->id)->where('gruppo', $gKey)->where('can_edit', true)->exists(); @endphp
          <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer">
            <input type="checkbox" name="permessi[{{ $gKey }}]" value="1" {{ $hasPerm ? 'checked' : '' }}> {{ $gLabel }}
          </label>
          @endforeach
        </div>
        <button type="submit" class="btn btn-ghost btn-sm">Salva permessi</button>
      </form>
      @empty
      <div style="text-align:center;color:var(--text3);padding:20px">Nessun operatore trovato</div>
      @endforelse
    </div>

    @else
    <form method="POST" action="{{ route('settings.salva', $gruppo) }}">
      @csrf
      <div class="card">
        <div class="card-title">{{ $gruppiLabel[$gruppo] ?? ucfirst($gruppo) }}</div>
        @php $defaults = \App\Models\Setting::defaultPerGruppo()[$gruppo] ?? []; @endphp

        @if($gruppo === 'mail')
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Driver</label>
            <select name="mail_driver" class="form-select">
              <option value="smtp" {{ ($settings['mail_driver']?->valore ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
              <option value="sendmail" {{ ($settings['mail_driver']?->valore ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Cifratura</label>
            <select name="mail_encryption" class="form-select">
              <option value="tls" {{ ($settings['mail_encryption']?->valore ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
              <option value="ssl" {{ ($settings['mail_encryption']?->valore ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
              <option value="" {{ ($settings['mail_encryption']?->valore ?? '') === '' ? 'selected' : '' }}>Nessuna</option>
            </select>
          </div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Host SMTP</label><input name="mail_host" class="form-input" value="{{ $settings['mail_host']?->valore ?? 'smtp.legalmail.it' }}" placeholder="smtp.legalmail.it"></div>
          <div class="form-group"><label class="form-label">Porta</label><input name="mail_port" class="form-input" value="{{ $settings['mail_port']?->valore ?? '587' }}" placeholder="587"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Username (PEC)</label><input name="mail_username" class="form-input" value="{{ $settings['mail_username']?->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
          <div class="form-group"><label class="form-label">Password</label><input type="password" name="mail_password" class="form-input" value="{{ $settings['mail_password']?->valore ?? '' }}" autocomplete="new-password"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Nome mittente</label><input name="mail_from_name" class="form-input" value="{{ $settings['mail_from_name']?->valore ?? '' }}" placeholder="AleCar S.r.l."></div>
          <div class="form-group"><label class="form-label">Email mittente</label><input name="mail_from_address" class="form-input" value="{{ $settings['mail_from_address']?->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
        </div>
        <div class="form-group" style="margin-top:8px">
          <a href="{{ route('settings.mail.test') }}" class="btn btn-ghost btn-sm">📨 Invia mail di test</a>
        </div>

        @else
        @foreach($defaults as $chiave => $default)
        @php $valore = $settings[$chiave]?->valore ?? $default; $isSecret = $settings[$chiave]?->is_secret ?? false; $label = ucwords(str_replace('_',' ',$chiave)); @endphp
        <div class="form-group">
          <label class="form-label">{{ $label }}</label>
          @if(str_contains($chiave,'testo') || str_contains($chiave,'template'))
            <textarea name="{{ $chiave }}" class="form-textarea" style="min-height:120px">{{ $valore }}</textarea>
          @elseif(in_array($chiave,['notifica_campanellina','notifica_email','notifica_email_admin','firma_cartacea_attiva']))
            <div style="display:flex;gap:12px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"><input type="radio" name="{{ $chiave }}" value="1" {{ $valore=='1'?'checked':'' }}> Abilitato</label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px"><input type="radio" name="{{ $chiave }}" value="0" {{ $valore!='1'?'checked':'' }}> Disabilitato</label>
            </div>
          @elseif($chiave==='sms_provider')
            <select name="{{ $chiave }}" class="form-select">
              @foreach(['self_hosted'=>'Self-hosted (OTP email)','twilio'=>'Twilio','esendex'=>'eSendex','smshosting'=>'SMSHOSTING','vonage'=>'Vonage'] as $v=>$l)
              <option value="{{ $v }}" {{ $valore==$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select>
          @elseif($chiave==='firma_modalita')
            <select name="{{ $chiave }}" class="form-select">
              <option value="self_hosted" {{ $valore=='self_hosted'?'selected':'' }}>Self-hosted</option>
              <option value="provider_esterno" {{ $valore=='provider_esterno'?'selected':'' }}>Provider esterno</option>
            </select>
          @elseif($chiave==='firma_provider')
            <select name="{{ $chiave }}" class="form-select">
              @foreach(['=>Nessuno','yousign'=>'Yousign','namirial'=>'Namirial','docusign'=>'DocuSign'] as $v=>$l)
              <option value="{{ $v }}" {{ $valore==$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select>
          @elseif($isSecret)
            <input type="password" name="{{ $chiave }}" class="form-input" value="{{ $valore }}" autocomplete="new-password">
          @elseif(in_array($chiave,['otp_timeout_minuti','otp_lunghezza','link_scadenza_giorni','upload_max_mb','km_alert_soglia','revisione_alert_giorni']))
            <input type="number" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">
          @else
            <input type="text" name="{{ $chiave }}" class="form-input" value="{{ $valore }}">
          @endif
        </div>
        @endforeach
        @endif

        <div style="margin-top:8px">
          <button type="submit" class="btn btn-primary">✓ Salva impostazioni</button>
        </div>
      </div>
    </form>
    @endif
  </div>
</div>
@endsection