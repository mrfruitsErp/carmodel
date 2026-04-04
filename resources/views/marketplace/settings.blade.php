@extends('layouts.app')
@section('title', 'Impostazioni Marketplace')

@section('content')
<div class="min-h-screen bg-gray-50/50 px-4 py-6 md:px-8">
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Impostazioni Marketplace</h1>
        <p class="text-sm text-gray-500 mt-1">Configura le credenziali API per ogni piattaforma.</p>
    </div>

    @php
    $platformDefs = [
        'autoscout24' => [
            'name'   => 'AutoScout24',
            'note'   => 'OAuth2 — Richiede contratto dealer. Contatta AutoScout24 per ottenere client_id e client_secret.',
            'fields' => [
                ['key'=>'client_id',     'label'=>'Client ID',     'type'=>'text',     'required'=>true],
                ['key'=>'client_secret', 'label'=>'Client Secret', 'type'=>'password', 'required'=>true],
            ],
        ],
        'automobile_it' => [
            'name'   => 'Automobile.it',
            'note'   => 'API Key — Generala nel pannello dealer di Automobile.it → Impostazioni → API.',
            'fields' => [
                ['key'=>'api_key', 'label'=>'API Key', 'type'=>'password', 'required'=>true],
            ],
        ],
        'ebay_motors' => [
            'name'   => 'eBay Motors',
            'note'   => 'OAuth2 user token — Registra l\'app su developer.ebay.com e ottieni il refresh_token.',
            'fields' => [
                ['key'=>'app_id',               'label'=>'App ID',              'type'=>'text',     'required'=>true],
                ['key'=>'cert_id',              'label'=>'Cert ID',             'type'=>'password', 'required'=>true],
                ['key'=>'refresh_token',        'label'=>'Refresh Token',       'type'=>'password', 'required'=>true],
                ['key'=>'fulfillment_policy_id','label'=>'Fulfillment Policy ID','type'=>'text',    'required'=>false],
                ['key'=>'payment_policy_id',   'label'=>'Payment Policy ID',   'type'=>'text',     'required'=>false],
                ['key'=>'return_policy_id',    'label'=>'Return Policy ID',    'type'=>'text',     'required'=>false],
            ],
        ],
        'subito_it' => [
            'name'   => 'Subito.it',
            'note'   => 'Automazione browser — Nessuna API ufficiale. Inserisci le credenziali del tuo account Subito.it.',
            'fields' => [
                ['key'=>'email',    'label'=>'Email account',    'type'=>'email',    'required'=>true],
                ['key'=>'password', 'label'=>'Password account', 'type'=>'password', 'required'=>true],
            ],
        ],
        'facebook_marketplace' => [
            'name'   => 'Facebook Marketplace',
            'note'   => 'Catalog API — Crea un Automotive Catalog su Meta Business Manager e genera un Page Access Token.',
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
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
        <div class="flex items-center justify-between px-6 py-4 cursor-pointer" onclick="toggleSection('{{ $platformKey }}')">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm text-gray-800">{{ $def['name'] }}</span>
                        @if($isEnabled)
                            <span class="text-xs bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Attiva</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Non attiva</span>
                        @endif
                    </div>
                </div>
            </div>
            <svg id="arrow_{{ $platformKey }}" class="w-4 h-4 text-gray-400 transition-transform {{ $isEnabled ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div id="section_{{ $platformKey }}" class="{{ $isEnabled ? '' : 'hidden' }} px-6 pb-6">
            <p class="text-xs text-blue-600 bg-blue-50 p-3 rounded-xl mb-4">ℹ {{ $def['note'] }}</p>
            <form action="{{ route('marketplace.settings.credentials', $platformKey) }}" method="POST">
                @csrf
                <div class="flex items-center gap-2 mb-4">
                    <input type="checkbox" name="enabled" value="1" id="enabled_{{ $platformKey }}" {{ $isEnabled ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-900">
                    <label for="enabled_{{ $platformKey }}" class="text-sm text-gray-700">Abilita questa piattaforma</label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    @foreach($def['fields'] as $field)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $field['label'] }} @if($field['required'])<span class="text-red-400">*</span>@endif</label>
                        <input type="{{ $field['type'] }}" name="credentials[{{ $field['key'] }}]"
                               value="{{ $field['type'] !== 'password' ? ($credValues[$field['key']] ?? '') : '' }}"
                               placeholder="{{ $field['type'] === 'password' ? '••••••••••••' : '' }}"
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 outline-none font-mono" autocomplete="off">
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2 text-sm bg-gray-900 text-white rounded-xl hover:bg-gray-700">Salva credenziali</button>
                    <button type="button" onclick="testConnection('{{ $platformKey }}', this)" class="px-4 py-2 text-sm border border-gray-200 rounded-xl bg-white hover:bg-gray-50 text-gray-600">Test connessione</button>
                    <span id="test_result_{{ $platformKey }}" class="text-xs font-medium"></span>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>
</div>

@push('scripts')
<script>
function toggleSection(key) {
    const section = document.getElementById('section_' + key);
    const arrow   = document.getElementById('arrow_' + key);
    section.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}
function testConnection(platform, btn) {
    const resultEl = document.getElementById('test_result_' + platform);
    btn.disabled   = true;
    btn.textContent = 'Testing...';
    resultEl.textContent = '';
    fetch(`/marketplace/settings/${platform}/test`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        resultEl.textContent = data.ok ? '✓ ' + data.message : '✗ ' + data.message;
        resultEl.className = 'text-xs font-medium ' + (data.ok ? 'text-emerald-600' : 'text-red-500');
    })
    .catch(() => { resultEl.textContent = '✗ Errore di connessione'; resultEl.className = 'text-xs font-medium text-red-500'; })
    .finally(() => { btn.disabled = false; btn.textContent = 'Test connessione'; });
}
</script>
@endpush
@endsection