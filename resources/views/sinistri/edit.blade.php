@extends('layouts.app')
@section('title', 'Modifica Sinistro #'.$claim->claim_number)
@section('topbar-actions')
<a href="{{ route('sinistri.stampa', $claim) }}" class="btn btn-ghost btn-sm" target="_blank">🖨️ Stampa pratica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('sinistri.show', $claim) }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Sinistro #{{ $claim->claim_number }}</a></div>
<form method="POST" action="{{ route('sinistri.update', $claim) }}">
@csrf @method('PUT')

{{-- ═══ SEZIONE 1 — IDENTIFICAZIONE ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">📋 Identificazione pratica</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">N° Sinistro interno</label>
      <input class="form-input" value="{{ $claim->claim_number }}" disabled style="opacity:.5">
    </div>
    <div class="form-group"><label class="form-label">N° Sinistro compagnia</label>
      <input name="numero_sinistro_compagnia" class="form-input" value="{{ old('numero_sinistro_compagnia', $claim->numero_sinistro_compagnia) }}" placeholder="es. 2025/0005/0000038484">
    </div>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Riferimento gestore / intermediario</label>
      <input name="riferimento_gestore" class="form-input" value="{{ old('riferimento_gestore', $claim->riferimento_gestore) }}" placeholder="es. OCEANO - ELLEZETA">
    </div>
    <div class="form-group"><label class="form-label">Tipo sinistro</label>
      <select name="claim_type" class="form-select">
        @foreach(['rca'=>'RCA','kasko'=>'Kasko','grandine'=>'Grandine','furto'=>'Furto','incendio'=>'Incendio','altro'=>'Altro'] as $v => $l)
        <option value="{{ $v }}" {{ old('claim_type',$claim->claim_type) === $v ? 'selected' : '' }}>{{ $l }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>

{{-- ═══ SEZIONE 2 — DANNEGGIATO & VEICOLO ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">👤 Danneggiato & Veicolo</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Cliente *</label>
      <select name="customer_id" class="form-select" required>
        <option value="">— Seleziona cliente —</option>
        @foreach($clienti as $c)<option value="{{ $c->id }}" {{ old('customer_id',$claim->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->display_name }}</option>@endforeach
      </select>
    </div>
    <div class="form-group"><label class="form-label">Codice Fiscale / P.IVA danneggiato</label>
      <input name="danneggiato_cf" class="form-input" style="text-transform:uppercase" value="{{ old('danneggiato_cf', $claim->danneggiato_cf) }}" placeholder="CF o P.IVA">
    </div>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Veicolo</label>
      <select name="vehicle_id" class="form-select">
        <option value="">— Nessuno —</option>
        @foreach($veicoli as $v)<option value="{{ $v->id }}" {{ old('vehicle_id',$claim->vehicle_id) == $v->id ? 'selected' : '' }}>{{ $v->plate }} — {{ $v->brand }} {{ $v->model }}</option>@endforeach
      </select>
    </div>
    <div class="form-group"><label class="form-label">Valore commerciale veicolo €</label>
      <input type="number" name="valore_commerciale" class="form-input" value="{{ old('valore_commerciale', $claim->valore_commerciale) }}" step="0.01">
    </div>
  </div>
</div>

{{-- ═══ SEZIONE 3 — EVENTO ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">📍 Evento sinistro</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Data sinistro *</label><input type="date" name="event_date" class="form-input" value="{{ old('event_date', $claim->event_date?->format('Y-m-d')) }}" required></div>
    <div class="form-group"><label class="form-label">Luogo sinistro</label><input name="event_location" class="form-input" value="{{ old('event_location', $claim->event_location) }}" placeholder="es. Torino - Corso Marconi ang. Via Saluzzo"></div>
  </div>
  <div class="form-group"><label class="form-label">Dinamica sinistro</label><textarea name="event_description" class="form-textarea">{{ old('event_description', $claim->event_description) }}</textarea></div>
</div>

{{-- ═══ SEZIONE 4 — CONTROPARTE ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">🚗 Controparte</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Targa controparte</label><input name="counterpart_plate" class="form-input" style="text-transform:uppercase" value="{{ old('counterpart_plate', $claim->counterpart_plate) }}"></div>
    <div class="form-group"><label class="form-label">Compagnia controparte</label><input name="counterpart_insurance" class="form-input" value="{{ old('counterpart_insurance', $claim->counterpart_insurance) }}" placeholder="es. ALLIANZ SPA"></div>
  </div>
  <div class="form-group"><label class="form-label">N° Polizza controparte</label><input name="counterpart_policy" class="form-input" value="{{ old('counterpart_policy', $claim->counterpart_policy) }}"></div>
</div>

{{-- ═══ SEZIONE 5 — COMPAGNIA GESTORE ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">🏢 Compagnia assicurativa & Gestione</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Compagnia assicurativa</label>
      <select name="insurance_company_id" class="form-select">
        <option value="">— Seleziona —</option>
        @foreach($compagnie as $comp)<option value="{{ $comp->id }}" {{ old('insurance_company_id',$claim->insurance_company_id) == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>@endforeach
      </select>
    </div>
    <div class="form-group"><label class="form-label">N° Polizza cliente</label><input name="policy_number" class="form-input" value="{{ old('policy_number', $claim->policy_number) }}"></div>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Perito assegnato</label>
      <select name="expert_id" class="form-select">
        <option value="">— Nessuno —</option>
        @foreach($periti as $p)<option value="{{ $p->id }}" {{ old('expert_id',$claim->expert_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->phone ? ' — '.$p->phone : '' }}</option>@endforeach
      </select>
    </div>
    <div class="form-group"><label class="form-label">Data perizia</label><input type="date" name="survey_date" class="form-input" value="{{ old('survey_date', $claim->survey_date?->format('Y-m-d')) }}"></div>
  </div>
  <div class="form-group"><label class="form-label">Liquidatore</label>
    <select name="liquidatore_id" class="form-select">
      <option value="">— Nessuno —</option>
      @foreach($liquidatori as $l)<option value="{{ $l->id }}" {{ old('liquidatore_id', $claim->liquidatore_id) == $l->id ? 'selected' : '' }}>{{ $l->name }}{{ $l->phone ? ' — '.$l->phone : '' }}{{ $l->orario_disponibilita ? ' ('.$l->orario_disponibilita.')' : '' }}</option>@endforeach
    </select>
  </div>
</div>

{{-- ═══ SEZIONE 6 — CID & SCADENZE ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">📅 CID & Scadenze</div>
  <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;cursor:pointer">
    <input type="checkbox" name="cid_signed" value="1" {{ old('cid_signed', $claim->cid_signed) ? 'checked' : '' }} id="cid_signed">
    <label for="cid_signed" class="form-label" style="margin:0;cursor:pointer">CID firmato da entrambe le parti</label>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Data CID</label><input type="date" name="cid_date" class="form-input" value="{{ old('cid_date', $claim->cid_date?->format('Y-m-d')) }}"></div>
    <div class="form-group"><label class="form-label">Scadenza CID</label><input type="date" name="cid_expiry" class="form-input" value="{{ old('cid_expiry', $claim->cid_expiry?->format('Y-m-d')) }}"></div>
  </div>
  <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:10px">
    <div style="font-size:11px;color:var(--text3);margin-bottom:8px;font-weight:600">📌 SCADENZE LEGALI SINISTRO</div>
    <div class="two-col" style="gap:10px">
      <div class="form-group" style="margin:0">
        <label class="form-label">10gg — Nomina perito + N° SX</label>
        <input type="date" name="scadenza_nomina_perito" class="form-input" value="{{ old('scadenza_nomina_perito', $claim->scadenza_nomina_perito?->format('Y-m-d')) }}">
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">35gg — Chiusura con perito</label>
        <input type="date" name="scadenza_chiusura_perito" class="form-input" value="{{ old('scadenza_chiusura_perito', $claim->scadenza_chiusura_perito?->format('Y-m-d')) }}">
      </div>
    </div>
    <div class="form-group" style="margin-top:10px;margin-bottom:0">
      <label class="form-label">60gg — Chiusura totale + Legale</label>
      <input type="date" name="scadenza_chiusura_totale" class="form-input" value="{{ old('scadenza_chiusura_totale', $claim->scadenza_chiusura_totale?->format('Y-m-d')) }}">
    </div>
  </div>
</div>

{{-- ═══ SEZIONE 7 — IMPORTI ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">💰 Importi & Perizia</div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Importo richiesto €</label><input type="number" name="importo_richiesto" class="form-input" value="{{ old('importo_richiesto', $claim->importo_richiesto) }}" step="0.01" placeholder="9700.00"></div>
    <div class="form-group"><label class="form-label">Importo stimato €</label><input type="number" name="estimated_amount" class="form-input" value="{{ old('estimated_amount', $claim->estimated_amount) }}" step="0.01"></div>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Importo perizia €</label><input type="number" name="importo_perizia" class="form-input" value="{{ old('importo_perizia', $claim->importo_perizia) }}" step="0.01" placeholder="8890.00"></div>
    <div class="form-group" style="display:flex;flex-direction:column;justify-content:center">
      <label class="form-label">Concordato</label>
      <div style="display:flex;gap:16px;margin-top:6px">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="concordato" value="1" {{ old('concordato', $claim->concordato) == '1' ? 'checked' : '' }}> Sì</label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="concordato" value="0" {{ old('concordato', $claim->concordato) === '0' ? 'checked' : '' }}> No</label>
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="concordato" value="" {{ old('concordato', $claim->concordato) === null ? 'checked' : '' }}> —</label>
      </div>
    </div>
  </div>
  <div class="two-col" style="gap:10px">
    <div class="form-group"><label class="form-label">Importo concordato €</label><input type="number" name="importo_concordato" class="form-input" value="{{ old('importo_concordato', $claim->importo_concordato) }}" step="0.01" placeholder="9300.00"></div>
    <div class="form-group"><label class="form-label">Importo approvato €</label><input type="number" name="approved_amount" class="form-input" value="{{ old('approved_amount', $claim->approved_amount) }}" step="0.01"></div>
  </div>

  <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:10px">
    <div style="font-size:11px;color:var(--text3);margin-bottom:8px;font-weight:600">🔧 DETTAGLIO PERIZIA</div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
      <div class="form-group" style="margin:0"><label class="form-label">Costo M.O. €/ora</label><input type="number" name="costo_ora_mo" class="form-input" value="{{ old('costo_ora_mo', $claim->costo_ora_mo) }}" step="0.01" placeholder="45.00"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Costo materiali €/ora</label><input type="number" name="costo_ora_materiali" class="form-input" value="{{ old('costo_ora_materiali', $claim->costo_ora_materiali) }}" step="0.01" placeholder="25.00"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Ore lavoro</label><input type="number" name="ore_lavoro" class="form-input" value="{{ old('ore_lavoro', $claim->ore_lavoro) }}" step="0.5" placeholder="35"></div>
    </div>
  </div>

  <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px;margin-bottom:10px">
    <div style="font-size:11px;color:var(--text3);margin-bottom:8px;font-weight:600">🚗 NOLEGGIO / TRAINO / FERMO</div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
      <div class="form-group" style="margin:0"><label class="form-label">Noleggio € tot.</label><input type="number" name="noleggio_importo" class="form-input" value="{{ old('noleggio_importo', $claim->noleggio_importo) }}" step="0.01" placeholder="427.00"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Noleggio giorni</label><input type="number" name="noleggio_giorni" class="form-input" value="{{ old('noleggio_giorni', $claim->noleggio_giorni) }}"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Traino €</label><input type="number" name="traino_importo" class="form-input" value="{{ old('traino_importo', $claim->traino_importo) }}" step="0.01"></div>
    </div>
    <div class="two-col" style="gap:10px;margin-top:10px">
      <div class="form-group" style="margin:0"><label class="form-label">Fermo tecnico giorni</label><input type="number" name="fermo_tecnico_giorni" class="form-input" value="{{ old('fermo_tecnico_giorni', $claim->fermo_tecnico_giorni) }}" placeholder="5"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Fermo tecnico €</label><input type="number" name="fermo_tecnico_importo" class="form-input" value="{{ old('fermo_tecnico_importo', $claim->fermo_tecnico_importo) }}" step="0.01" placeholder="560.00"></div>
    </div>
  </div>

  <div style="background:var(--bg3);border:1px solid var(--border2);border-radius:var(--radius);padding:12px">
    <div style="font-size:11px;color:var(--text3);margin-bottom:8px;font-weight:600">🏦 PAGAMENTO & ONORARI</div>
    <div class="two-col" style="gap:10px">
      <div class="form-group" style="margin:0"><label class="form-label">IBAN beneficiario</label><input name="iban_liquidazione" class="form-input" value="{{ old('iban_liquidazione', $claim->iban_liquidazione) }}" placeholder="IT74G..."></div>
      <div class="form-group" style="margin:0"><label class="form-label">Beneficiario liquidazione</label><input name="beneficiario_liquidazione" class="form-input" value="{{ old('beneficiario_liquidazione', $claim->beneficiario_liquidazione) }}" placeholder="es. Ellezeta SNC"></div>
    </div>
    <div class="two-col" style="gap:10px;margin-top:10px">
      <div class="form-group" style="margin:0"><label class="form-label">Importo liquidato €</label><input type="number" name="paid_amount" class="form-input" value="{{ old('paid_amount', $claim->paid_amount) }}" step="0.01"></div>
      <div class="form-group" style="margin:0"><label class="form-label">Data pagamento</label><input type="date" name="paid_date" class="form-input" value="{{ old('paid_date', $claim->paid_date?->format('Y-m-d')) }}"></div>
    </div>
    <div class="two-col" style="gap:10px;margin-top:10px">
      <div class="form-group" style="margin:0"><label class="form-label">Onorario %</label>
        <select name="onorario_percentuale" class="form-select">
          <option value="">— Seleziona —</option>
          @foreach([20, 15, 12, 10, 8, 5] as $pct)
          <option value="{{ $pct }}" {{ old('onorario_percentuale', $claim->onorario_percentuale) == $pct ? 'selected' : '' }}>{{ $pct }}%</option>
          @endforeach
        </select>
      </div>
      <div class="form-group" style="margin:0;display:flex;flex-direction:column;justify-content:center">
        <label class="form-label">Recupero IVA</label>
        <div style="display:flex;gap:16px;margin-top:6px">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="recupera_iva" value="1" {{ old('recupera_iva', $claim->recupera_iva) ? 'checked' : '' }}> Sì</label>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer"><input type="radio" name="recupera_iva" value="0" {{ !old('recupera_iva', $claim->recupera_iva) ? 'checked' : '' }}> No</label>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══ SEZIONE 8 — NOTE ═══ --}}
<div class="card" style="margin-bottom:14px">
  <div class="card-title">📝 Note</div>
  <div class="form-group"><label class="form-label">Note</label><textarea name="notes" class="form-textarea" style="min-height:80px">{{ old('notes', $claim->notes) }}</textarea></div>
  <div class="form-group"><label class="form-label">Note interne</label><textarea name="internal_notes" class="form-textarea" style="min-height:60px">{{ old('internal_notes', $claim->internal_notes) }}</textarea></div>
</div>

<div style="display:flex;gap:8px;margin-bottom:24px">
  <a href="{{ route('sinistri.show', $claim) }}" class="btn btn-ghost" style="flex:1;justify-content:center">Annulla</a>
  <a href="{{ route('sinistri.stampa', $claim) }}" class="btn btn-ghost" target="_blank">🖨️ Stampa</a>
  <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center">✓ Salva modifiche</button>
</div>
</form>
@endsection
