@extends('layouts.app')
@section('title', 'Impostazioni — '.($gruppiLabel[$gruppo] ?? ucfirst($gruppo)))
@section('content')
<div style="display:grid;grid-template-columns:180px 1fr;gap:16px;align-items:start">
  <div style="background:#111827;border-radius:var(--radius-lg);padding:6px;border:1px solid rgba(255,255,255,.06)">
    <div style="font-size:10px;font-weight:600;color:rgba(255,255,255,.25);letter-spacing:.1em;text-transform:uppercase;padding:8px 10px 4px">Sezioni</div>
    @foreach($gruppiLabel as $key => $label)
    <a href="{{ route('settings.gruppo', $key) }}" class="nav-item {{ $gruppo == $key ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0;font-size:12px">{{ $label }}</a>
    @endforeach
    @if(auth()->user()->isAdmin())
    <div style="border-top:1px solid rgba(255,255,255,.06);margin:8px 0"></div>
    <a href="{{ route('settings.gruppo', 'permessi') }}" class="nav-item {{ $gruppo == 'permessi' ? 'active' : '' }}" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Permessi operatori</a>
    <a href="{{ route('documenti-catalogo.index') }}" class="nav-item" style="border-radius:var(--radius);margin:2px 0;font-size:12px">Catalogo documenti</a>
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
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px">
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
              <option value="smtp" {{ ($settings['mail_driver']->valore ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
              <option value="sendmail" {{ ($settings['mail_driver']->valore ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
            </select>
          </div>
          <div class="form-group"><label class="form-label">Cifratura</label>
            <select name="mail_encryption" class="form-select">
              <option value="tls" {{ ($settings['mail_encryption']->valore ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
              <option value="ssl" {{ ($settings['mail_encryption']->valore ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
              <option value="" {{ ($settings['mail_encryption']->valore ?? '') === '' ? 'selected' : '' }}>Nessuna</option>
            </select>
          </div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Host SMTP</label><input name="mail_host" class="form-input" value="{{ $settings['mail_host']->valore ?? 'smtp.legalmail.it' }}" placeholder="smtp.legalmail.it"></div>
          <div class="form-group"><label class="form-label">Porta</label><input name="mail_port" class="form-input" value="{{ $settings['mail_port']->valore ?? '587' }}" placeholder="587"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Username (PEC)</label><input name="mail_username" class="form-input" value="{{ $settings['mail_username']->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
          <div class="form-group"><label class="form-label">Password</label><input type="password" name="mail_password" class="form-input" value="{{ $settings['mail_password']->valore ?? '' }}" autocomplete="new-password"></div>
        </div>
        <div class="two-col" style="gap:10px">
          <div class="form-group"><label class="form-label">Nome mittente</label><input name="mail_from_name" class="form-input" value="{{ $settings['mail_from_name']->valore ?? '' }}" placeholder="AleCar S.r.l."></div>
          <div class="form-group"><label class="form-label">Email mittente</label><input name="mail_from_address" class="form-input" value="{{ $settings['mail_from_address']->valore ?? '' }}" placeholder="tuamail@legalmail.it"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button type="submit" class="btn btn-primary">✓ Salva</button>
          <a href="{{ route('settings.mail.test') }}" class="btn btn-ghost">📨 Invia mail di test</a>
        </div>

        @elseif($gruppo === 'ai')
        @php
          $savedKey   = $settings['ai_api_key']->valore ?? '';
          $savedModel = $settings['ai_model']->valore   ?? '';
          // Rileva provider dalla chiave salvata
          $detected = 'non rilevato';
          $detectedSlug = '';
          if (str_starts_with($savedKey, 'sk-ant-')) { $detected = 'Anthropic (Claude)'; $detectedSlug = 'anthropic'; }
          elseif (str_starts_with($savedKey, 'AIza')) { $detected = 'Google (Gemini)';    $detectedSlug = 'google'; }
          $defaultModel = $detectedSlug === 'google' ? 'gemini-2.0-flash' : 'claude-3-5-sonnet-20240620';
        @endphp
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:16px">
          <div style="font-size:12px;font-weight:600;margin-bottom:4px">🤖 Intelligenza Artificiale</div>
          <div style="font-size:11px;color:var(--text3)">Incolla la chiave API: il provider viene rilevato automaticamente. Supportati: <strong>Anthropic Claude</strong> (chiavi <code>sk-ant-...</code>) e <strong>Google Gemini</strong> (chiavi <code>AIza...</code>, free tier su <a href="https://aistudio.google.com/apikey" target="_blank" style="color:var(--green)">aistudio.google.com</a>).</div>
        </div>
        <div class="form-group">
          <label class="form-label">Chiave API</label>
          <input type="password" name="ai_api_key" id="ai_api_key_input" class="form-input"
            value="{{ $savedKey }}"
            autocomplete="new-password"
            placeholder="sk-ant-... oppure AIza...">
          <div style="font-size:11px;margin-top:6px;display:flex;align-items:center;gap:6px" id="provider_badge">
            <span style="color:var(--text3)">Provider rilevato:</span>
            <span id="provider_label" style="font-weight:600;color:{{ $detectedSlug ? 'var(--green)' : 'var(--amber)' }}">{{ $detected }}</span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Modello AI <span style="color:var(--text3);font-weight:400">(opzionale)</span></label>
          <input type="text" name="ai_model" id="ai_model_input" class="form-input"
            value="{{ $savedModel }}"
            placeholder="Lascia vuoto per usare il default: {{ $defaultModel }}">
          <div style="font-size:11px;color:var(--text3);margin-top:3px">
            Lascia vuoto per il default automatico. Esempi: <code>claude-3-5-sonnet-20240620</code>, <code>claude-haiku-4-5-20251001</code>, <code>gemini-2.0-flash</code>, <code>gemini-1.5-flash</code>
          </div>
        </div>
        {{-- Manteniamo ai_provider sincronizzato lato server in fase di salvataggio --}}
        <input type="hidden" name="ai_provider" id="ai_provider_hidden" value="{{ $detectedSlug ?: 'anthropic' }}">
        <script>
        (function(){
          const inp   = document.getElementById('ai_api_key_input');
          const lbl   = document.getElementById('provider_label');
          const hid   = document.getElementById('ai_provider_hidden');
          if (!inp || !lbl || !hid) return;

          function detect() {
            const v = (inp.value || '').trim();
            let provider = '', label = 'non rilevato', color = 'var(--amber)';
            if (v.startsWith('sk-ant-')) { provider = 'anthropic'; label = 'Anthropic (Claude)'; color = 'var(--green)'; }
            else if (v.startsWith('AIza')) { provider = 'google';  label = 'Google (Gemini)';    color = 'var(--green)'; }
            lbl.textContent = label;
            lbl.style.color = color;
            hid.value = provider || 'anthropic';
          }
          inp.addEventListener('input', detect);
        })();
        </script>
        @if(auth()->user()->isAdmin())
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:10px;margin-top:4px">
          <div style="font-size:10px;color:var(--text3);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Info tecniche</div>
          <div style="font-size:11px;color:var(--text3)">
            Provider attivo: <strong style="color:var(--text2)">{{ $settings['ai_provider']->valore ?? 'anthropic' }}</strong><br>
            Stato: <strong style="color:{{ isset($settings['ai_api_key']) && $settings['ai_api_key']->valore ? 'var(--green)' : 'var(--red)' }}">{{ (isset($settings['ai_api_key']) && $settings['ai_api_key']->valore) ? '✓ Configurato' : '✗ Non configurato' }}</strong>
          </div>
        </div>
        @endif
        <div style="margin-top:16px">
          <button type="submit" class="btn btn-primary">✓ Salva configurazione AI</button>
        </div>

        @else
        @foreach($defaults as $chiave => $default)
        @php 
            $settingObj = $settings[$chiave] ?? null;
            $valore = $settingObj ? $settingObj->valore : $default; 
            $isSecret = $settingObj ? $settingObj->is_secret : false; 
            $label = ucwords(str_replace('_',' ',$chiave)); 
        @endphp
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
              @foreach(['self_hosted'=>'Self-hosted','twilio'=>'Twilio','esendex'=>'eSendex','smshosting'=>'SMSHOSTING','vonage'=>'Vonage'] as $v=>$l)
              <option value="{{ $v }}" {{ $valore==$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          @elseif($chiave==='firma_modalita')
            <select name="{{ $chiave }}" class="form-select">
              <option value="self_hosted" {{ $valore=='self_hosted'?'selected':'' }}>Self-hosted</option>
              <option value="provider_esterno" {{ $valore=='provider_esterno'?'selected':'' }}>Provider esterno</option>
            </select>
          @elseif($chiave==='firma_provider')
            <select name="{{ $chiave }}" class="form-select">
              <option value="" {{ !$valore?'selected':'' }}>Nessuno</option>
              <option value="yousign" {{ $valore=='yousign'?'selected':'' }}>Yousign</option>
              <option value="namirial" {{ $valore=='namirial'?'selected':'' }}>Namirial</option>
              <option value="docusign" {{ $valore=='docusign'?'selected':'' }}>DocuSign</option>
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
        <div style="margin-top:8px"><button type="submit" class="btn btn-primary">✓ Salva impostazioni</button></div>
        @endif
      </div>
    </form>
    @endif
  </div>
</div>
@endsection