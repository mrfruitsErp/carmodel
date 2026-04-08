@extends('layouts.app')
@section('title', 'Impostazioni Marketplace')

@section('content')
<div style="max-width:800px">
  <p style="font-size:13px;color:var(--text3);margin-bottom:20px">Configura le credenziali API per ogni piattaforma di vendita.</p>

  @php
  $platformDefs = [
    'autoscout24' => [
      'name'   => 'AutoScout24',
      'icon'   => '🔵',
      'note'   => 'OAuth2 — Richiede contratto dealer. Contatta AutoScout24 per client_id e client_secret.',
      'fields' => [
        ['key'=>'client_id',     'label'=>'Client ID',     'type'=>'text',     'required'=>true],
        ['key'=>'client_secret', 'label'=>'Client Secret', 'type'=>'password', 'required'=>true],
      ],
    ],
    'automobile_it' => [
      'name'   => 'Automobile.it',
      'icon'   => '🔴',
      'note'   => 'API Key — Generala nel pannello dealer di Automobile.it → Impostazioni → API.',
      'fields' => [
        ['key'=>'api_key', 'label'=>'API Key', 'type'=>'password', 'required'=>true],
      ],
    ],
    'ebay_motors' => [
      'name'   => 'eBay Motors',
      'icon'   => '🟡',
      'note'   => 'OAuth2 user token — Registra l\'app su developer.ebay.com.',
      'fields' => [
        ['key'=>'app_id',               'label'=>'App ID',               'type'=>'text',     'required'=>true],
        ['key'=>'cert_id',              'label'=>'Cert ID',              'type'=>'password', 'required'=>true],
        ['key'=>'refresh_token',        'label'=>'Refresh Token',        'type'=>'password', 'required'=>true],
        ['key'=>'fulfillment_policy_id','label'=>'Fulfillment Policy ID','type'=>'text',     'required'=>false],
        ['key'=>'payment_policy_id',    'label'=>'Payment Policy ID',    'type'=>'text',     'required'=>false],
        ['key'=>'return_policy_id',     'label'=>'Return Policy ID',     'type'=>'text',     'required'=>false],
      ],
    ],
    'subito_it' => [
      'name'   => 'Subito.it',
      'icon'   => '🟠',
      'note'   => 'Automazione browser — Inserisci le credenziali del tuo account Subito.it.',
      'fields' => [
        ['key'=>'email',    'label'=>'Email account',    'type'=>'email',    'required'=>true],
        ['key'=>'password', 'label'=>'Password account', 'type'=>'password', 'required'=>true],
      ],
    ],
    'facebook_marketplace' => [
      'name'   => 'Facebook Marketplace',
      'icon'   => '🔷',
      'note'   => 'Catalog API — Crea un Automotive Catalog su Meta Business Manager.',
      'fields' => [
        ['key'=>'page_access_token','label'=>'Page Access Token','type'=>'password','required'=>true],
        ['key'=>'catalog_id',       'label'=>'Catalog ID',       'type'=>'text',   'required'=>true],
        ['key'=>'page_id',          'label'=>'Page ID (opz.)',   'type'=>'text',   'required'=>false],
      ],
    ],
  ];
  @endphp

  @foreach($platformDefs as $platformKey => $def)
  @php
    $cred       = $credentials->get($platformKey);
    $isEnabled  = $cred?->enabled ?? false;
    $credValues = $cred?->credentials ?? [];
  @endphp

  <div class="card" style="margin-bottom:12px;padding:0;overflow:hidden">
    {{-- Header accordion --}}
    <div onclick="togglePlatform('{{ $platformKey }}')" style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;cursor:pointer;user-select:none">
      <div style="display:flex;align-items:center;gap:10px">
        <span style="font-size:18px">{{ $def['icon'] }}</span>
        <div>
          <span style="font-size:14px;font-weight:700;color:var(--text)">{{ $def['name'] }}</span>
        </div>
        @if($isEnabled)
          <span style="background:var(--green-bg);color:var(--green-text);font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;border:1px solid rgba(34,197,94,.2)">Attiva</span>
        @else
          <span style="background:var(--bg3);color:var(--text3);font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px">Non attiva</span>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <button onclick="event.stopPropagation();testConnection('{{ $platformKey }}',this)" style="background:var(--bg3);border:1px solid var(--border2);border-radius:6px;padding:4px 10px;font-size:11px;color:var(--text2);cursor:pointer">Test</button>
        <span id="arrow_{{ $platformKey }}" style="color:var(--text3);transition:transform .2s;display:inline-block;{{ $isEnabled?'transform:rotate(180deg)':'' }}">▼</span>
      </div>
    </div>

    {{-- Body accordion --}}
    <div id="platform_{{ $platformKey }}" style="{{ $isEnabled?'':'display:none' }};border-top:1px solid var(--border);padding:20px">
      <div style="background:var(--blue-bg);border:1px solid rgba(59,130,246,.2);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--blue-text);margin-bottom:16px">
        ℹ️ {{ $def['note'] }}
      </div>
      <form action="{{ route('marketplace.settings.credentials', $platformKey) }}" method="POST">
        @csrf
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
          <input type="checkbox" name="enabled" value="1" id="en_{{ $platformKey }}" {{ $isEnabled?'checked':'' }} style="width:16px;height:16px;accent-color:var(--orange)">
          <label for="en_{{ $platformKey }}" style="font-size:13px;color:var(--text);cursor:pointer">Abilita questa piattaforma</label>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
          @foreach($def['fields'] as $field)
          <div>
            <div class="form-label">{{ $field['label'] }} @if($field['required'])<span style="color:var(--red)">*</span>@endif</div>
            <input type="{{ $field['type'] }}" name="credentials[{{ $field['key'] }}]"
              value="{{ $field['type']!=='password' ? ($credValues[$field['key']] ?? '') : '' }}"
              placeholder="{{ $field['type']==='password' ? '••••••••••' : '' }}"
              class="form-input" style="font-family:var(--mono);font-size:12px" autocomplete="off">
          </div>
          @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <button type="submit" class="btn btn-primary btn-sm">Salva credenziali</button>
          <span id="test_{{ $platformKey }}" style="font-size:12px;font-weight:600"></span>
        </div>
      </form>
    </div>
  </div>
  @endforeach
</div>

@push('scripts')
<script>
function togglePlatform(key) {
  const body  = document.getElementById('platform_' + key);
  const arrow = document.getElementById('arrow_' + key);
  const hidden = body.style.display === 'none';
  body.style.display  = hidden ? 'block' : 'none';
  arrow.style.transform = hidden ? 'rotate(180deg)' : '';
}
function testConnection(platform, btn) {
  const el = document.getElementById('test_' + platform);
  btn.textContent = '...';
  el.textContent  = '';
  fetch(`/marketplace/settings/${platform}/test`, {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  })
  .then(r => r.json())
  .then(d => {
    el.textContent = d.ok ? '✓ ' + d.message : '✗ ' + d.message;
    el.style.color = d.ok ? 'var(--green-text)' : 'var(--red-text)';
  })
  .catch(() => { el.textContent = '✗ Errore'; el.style.color = 'var(--red-text)'; })
  .finally(() => btn.textContent = 'Test');
}
</script>
@endpush
@endsection