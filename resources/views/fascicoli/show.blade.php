@extends('layouts.app')
@section('title', 'Fascicolo #' . $fascicolo->id)

@section('topbar-actions')
<a href="{{ route('fascicoli.index') }}" class="btn btn-ghost btn-sm">← Fascicoli</a>
<a href="{{ route('fascicoli.edit', $fascicolo) }}" class="btn btn-ghost btn-sm">✏ Modifica</a>
@if(!$fascicolo->isCompletato())
  <button onclick="document.getElementById('modal-link').style.display='flex'" class="btn btn-primary btn-sm">🔗 Genera link cliente</button>
@endif
@endsection

@section('content')

{{-- HEADER FASCICOLO --}}
<div class="card" style="margin-bottom:16px">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
    <div>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
        <span style="font-family:var(--font-display);font-size:22px;font-weight:700">
          {{ $fascicolo->titolo ?? 'Fascicolo #' . $fascicolo->id }}
        </span>
        <span class="badge badge-{{ $fascicolo->stato_color }}">{{ $fascicolo->stato_label }}</span>
        <span class="badge badge-blue">{{ $fascicolo->tipo_pratica_label }}</span>
      </div>
      <div style="font-size:13px;color:var(--text2)">
        Cliente: <strong>{{ $fascicolo->cliente->display_name ?? ($fascicolo->cliente->nome . ' ' . $fascicolo->cliente->cognome) }}</strong>
        &nbsp;·&nbsp;
        {{ $fascicolo->cliente->tipo_soggetto === 'azienda' ? '🏢 Azienda' : '👤 Privato' }}
        @if($fascicolo->operatore)
          &nbsp;·&nbsp; Operatore: <strong>{{ $fascicolo->operatore->name }}</strong>
        @endif
        &nbsp;·&nbsp; Creato il {{ $fascicolo->created_at->format('d/m/Y') }}
      </div>
    </div>
    {{-- PROGRESSO --}}
    <div style="text-align:right;min-width:120px">
      <div style="font-size:11px;color:var(--text3);margin-bottom:4px">COMPLETAMENTO</div>
      <div style="font-family:var(--font-display);font-size:28px;font-weight:700;color:{{ $fascicolo->progresso == 100 ? 'var(--green)' : 'var(--orange)' }}">
        {{ $fascicolo->progresso }}%
      </div>
      <div class="progress" style="width:120px;margin-top:4px">
        <div class="progress-fill" style="width:{{ $fascicolo->progresso }}%;background:{{ $fascicolo->progresso == 100 ? 'var(--green)' : 'var(--orange)' }}"></div>
      </div>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

{{-- COLONNA SINISTRA — DOCUMENTI --}}
<div>
  <div class="card" style="padding:0">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between">
      <span class="card-title" style="margin-bottom:0">
        Documenti richiesti ({{ $fascicolo->documenti->count() }})
      </span>
      <div style="display:flex;gap:8px">
        <form method="POST" action="{{ route('fascicoli.popola-documenti', $fascicolo) }}" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-ghost btn-sm" title="Ricarica documenti dal catalogo">↺ Aggiorna da catalogo</button>
        </form>
        <button onclick="document.getElementById('modal-doc').style.display='flex'" class="btn btn-ghost btn-sm">+ Aggiungi</button>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th style="width:30px">#</th>
          <th>Documento</th>
          <th>Tipo</th>
          <th>Stato</th>
          <th>File</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($fascicolo->documenti as $doc)
        <tr>
          <td style="color:var(--text3);font-size:11px">{{ $doc->ordine }}</td>
          <td>
            <div style="font-weight:500;font-size:13px">
              {{ $doc->nome }}
              @if($doc->obbligatorio)
                <span style="color:var(--red);font-size:10px;margin-left:4px">*</span>
              @endif
            </div>
            <div style="font-size:10px;color:var(--text3);margin-top:2px">
              @if($doc->richiede_upload) 📎 Upload &nbsp; @endif
              @if($doc->richiede_firma) ✍ Firma ({{ $doc->modalita_firma }}) @endif
            </div>
          </td>
          <td>
            @if($doc->obbligatorio)
              <span class="badge badge-red" style="font-size:10px">Obbligatorio</span>
            @else
              <span class="badge badge-gray" style="font-size:10px">Facoltativo</span>
            @endif
          </td>
          <td>
            @php
              $statoColor = [
                'richiesto' => 'gray',
                'caricato'  => 'blue',
                'firmato'   => 'teal',
                'verificato'=> 'green',
                'rifiutato' => 'red',
              ][$doc->stato] ?? 'gray';
            @endphp
            <span class="badge badge-{{ $statoColor }}">{{ ucfirst($doc->stato) }}</span>
            @if($doc->firmato_il)
              <div style="font-size:10px;color:var(--text3);margin-top:2px">
                {{ $doc->firmato_da_nome }} · {{ $doc->firmato_il->format('d/m H:i') }}
              </div>
            @endif
          </td>
          <td>
            @if($doc->getFirstMedia('file_documento'))
              @php $media = $doc->getFirstMedia('file_documento'); @endphp
              <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-ghost btn-sm">
                📄 {{ Str::limit($media->file_name, 20) }}
              </a>
            @else
              <span style="color:var(--text3);font-size:11px">—</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px">
              {{-- Aggiorna stato --}}
              <form method="POST" action="{{ route('fascicoli.documenti.update', [$fascicolo, $doc]) }}">
                @csrf @method('PATCH')
                <select name="stato" class="form-select" style="padding:4px 8px;font-size:11px;width:auto"
                  onchange="this.form.submit()">
                  @foreach(['richiesto','caricato','firmato','verificato','rifiutato'] as $s)
                    <option value="{{ $s }}" {{ $doc->stato == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                  @endforeach
                </select>
              </form>
              {{-- Elimina --}}
              <form method="POST" action="{{ route('fascicoli.documenti.destroy', [$fascicolo, $doc]) }}"
                onsubmit="return confirm('Rimuovere questo documento?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">✕</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;color:var(--text3);padding:24px">
            Nessun documento — <a href="#" onclick="document.getElementById('modal-doc').style.display='flex'" style="color:var(--orange)">aggiungi</a> o
            <form method="POST" action="{{ route('fascicoli.popola-documenti', $fascicolo) }}" style="display:inline">
              @csrf
              <button type="submit" style="background:none;border:none;color:var(--orange);cursor:pointer;font-size:13px;padding:0">carica dal catalogo</button>
            </form>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- NOTE --}}
  @if($fascicolo->note)
  <div class="card">
    <div class="card-title">Note operative</div>
    <p style="font-size:13px;color:var(--text2);line-height:1.7">{{ $fascicolo->note }}</p>
  </div>
  @endif
</div>

{{-- COLONNA DESTRA --}}
<div>

  {{-- LINK PORTALE --}}
  <div class="card">
    <div class="card-title">Portale cliente</div>
    @if($tokenAttivo)
      <div style="margin-bottom:12px">
        <div style="font-size:11px;color:var(--text3);margin-bottom:6px">LINK ATTIVO</div>
        <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:8px 10px;font-family:var(--mono);font-size:10px;word-break:break-all;color:var(--text2)">
          {{ $linkPortale }}
        </div>
        <div style="margin-top:8px;display:flex;gap:6px">
          <button onclick="navigator.clipboard.writeText('{{ $linkPortale }}').then(()=>alert('Link copiato!'))" class="btn btn-ghost btn-sm" style="flex:1">📋 Copia</button>
          <form method="POST" action="{{ route('fascicoli.disattiva-link', $fascicolo) }}" onsubmit="return confirm('Disattivare il link?')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">✕ Disattiva</button>
          </form>
        </div>
      </div>
      @if($tokenAttivo->scadenza)
        <div style="font-size:11px;color:var(--text3)">
          Scade: {{ $tokenAttivo->scadenza->format('d/m/Y H:i') }}
          @if($tokenAttivo->scadenza->isPast())
            <span class="badge badge-red" style="font-size:9px">Scaduto</span>
          @endif
        </div>
      @endif
      @if($tokenAttivo->gdpr_accettato_il)
        <div style="font-size:11px;color:var(--green-text);margin-top:6px">✓ GDPR accettato il {{ $tokenAttivo->gdpr_accettato_il->format('d/m/Y H:i') }}</div>
      @endif
    @else
      <div style="text-align:center;padding:16px 0;color:var(--text3);font-size:13px">
        Nessun link attivo.<br>
        <button onclick="document.getElementById('modal-link').style.display='flex'" class="btn btn-primary btn-sm" style="margin-top:10px">🔗 Genera link</button>
      </div>
    @endif
  </div>

  {{-- INFO CLIENTE --}}
  <div class="card">
    <div class="card-title">Cliente</div>
    <div style="font-size:13px;line-height:2">
      <div><strong>{{ $fascicolo->cliente->display_name ?? ($fascicolo->cliente->nome . ' ' . $fascicolo->cliente->cognome) }}</strong></div>
      @if($fascicolo->cliente->email)
        <div style="color:var(--text2)">✉ {{ $fascicolo->cliente->email }}</div>
      @endif
      @if($fascicolo->cliente->phone ?? $fascicolo->cliente->telefono)
        <div style="color:var(--text2)">📞 {{ $fascicolo->cliente->phone ?? $fascicolo->cliente->telefono }}</div>
      @endif
      @if($fascicolo->cliente->codice_fiscale)
        <div style="color:var(--text3);font-size:11px;font-family:var(--mono)">CF: {{ $fascicolo->cliente->codice_fiscale }}</div>
      @endif
      @if($fascicolo->cliente->partita_iva)
        <div style="color:var(--text3);font-size:11px;font-family:var(--mono)">P.IVA: {{ $fascicolo->cliente->partita_iva }}</div>
      @endif
    </div>
  </div>

  {{-- DATI PRATICA --}}
  @if($fascicolo->data_inizio || $fascicolo->riferimento_veicolo)
  <div class="card">
    <div class="card-title">Dati pratica</div>
    <div style="font-size:13px;line-height:2;color:var(--text2)">
      @if($fascicolo->riferimento_veicolo)
        <div>🚗 {{ $fascicolo->riferimento_veicolo }}</div>
      @endif
      @if($fascicolo->data_inizio)
        <div>📅 Dal {{ $fascicolo->data_inizio->format('d/m/Y') }}
          @if($fascicolo->data_fine) al {{ $fascicolo->data_fine->format('d/m/Y') }} @endif
        </div>
      @endif
    </div>
  </div>
  @endif

  {{-- AZIONI --}}
  @if(!$fascicolo->isCompletato())
  <div class="card">
    <div class="card-title">Azioni</div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <form method="POST" action="{{ route('fascicoli.completa', $fascicolo) }}"
        onsubmit="return confirm('Segnare questo fascicolo come verificato?')">
        @csrf
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">✓ Segna verificato</button>
      </form>
    </div>
  </div>
  @endif

</div>
</div>

{{-- MODAL GENERA LINK --}}
<div id="modal-link" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg2);border-radius:var(--radius-lg);padding:24px;width:400px;max-width:90vw">
    <div class="card-title" style="margin-bottom:16px">🔗 Genera link portale cliente</div>
    <form method="POST" action="{{ route('fascicoli.genera-link', $fascicolo) }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Scadenza link (giorni)</label>
        <input type="number" name="giorni_scadenza" class="form-input" value="7" min="0" max="365">
        <div style="font-size:11px;color:var(--text3);margin-top:3px">0 = nessuna scadenza</div>
      </div>
      <div style="display:flex;gap:8px;margin-top:16px">
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Genera</button>
        <button type="button" onclick="document.getElementById('modal-link').style.display='none'" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL AGGIUNGI DOCUMENTO --}}
<div id="modal-doc" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:var(--bg2);border-radius:var(--radius-lg);padding:24px;width:420px;max-width:90vw">
    <div class="card-title" style="margin-bottom:16px">+ Aggiungi documento</div>
    <form method="POST" action="{{ route('fascicoli.documenti.store', $fascicolo) }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Nome documento *</label>
        <input type="text" name="nome" class="form-input" placeholder="Es. Autorizzazione speciale..." required>
      </div>
      <div class="two-col">
        <div class="form-group">
          <label class="form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer">
            <input type="checkbox" name="obbligatorio" value="1"> Obbligatorio
          </label>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer">
            <input type="checkbox" name="richiede_firma" value="1"> Richiede firma
          </label>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" style="display:flex;align-items:center;gap:6px;cursor:pointer">
          <input type="checkbox" name="richiede_upload" value="1" checked> Richiede upload file
        </label>
      </div>
      <div style="display:flex;gap:8px;margin-top:16px">
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">Aggiungi</button>
        <button type="button" onclick="document.getElementById('modal-doc').style.display='none'" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</button>
      </div>
    </form>
  </div>
</div>

@endsection