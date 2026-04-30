<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pratica Sinistro #{{ $claim->claim_number }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; background: #fff; }
  .page { max-width: 800px; margin: 0 auto; padding: 20px; }

  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #e65c00; padding-bottom: 12px; margin-bottom: 16px; }
  .header-left h1 { font-size: 18px; color: #e65c00; font-weight: 800; }
  .header-left h2 { font-size: 13px; color: #333; margin-top: 2px; }
  .header-right { text-align: right; font-size: 10px; color: #666; }
  .badge-stato { display: inline-block; background: #e65c00; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .05em; }

  .section { margin-bottom: 14px; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }
  .section-title { background: #f4f4f4; border-bottom: 1px solid #ddd; padding: 6px 12px; font-size: 11px; font-weight: 700; color: #333; text-transform: uppercase; letter-spacing: .05em; }
  .section-body { padding: 8px 12px; }

  .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; }
  .grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px 16px; }
  .row { display: flex; gap: 6px; padding: 3px 0; border-bottom: 1px solid #f0f0f0; }
  .row:last-child { border-bottom: none; }
  .lbl { min-width: 160px; color: #666; font-size: 10px; flex-shrink: 0; }
  .val { font-weight: 600; color: #111; }
  .val.green { color: #16a34a; }
  .val.orange { color: #e65c00; }
  .val.red { color: #dc2626; }
  .val.muted { color: #999; font-weight: 400; }

  .importi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 8px; }
  .imp-box { border: 1px solid #e5e7eb; border-radius: 5px; padding: 8px; text-align: center; }
  .imp-box .imp-label { font-size: 9px; color: #666; text-transform: uppercase; margin-bottom: 3px; }
  .imp-box .imp-val { font-size: 14px; font-weight: 800; color: #111; }
  .imp-box.highlight { border-color: #e65c00; background: #fff7f0; }
  .imp-box.highlight .imp-val { color: #e65c00; }
  .imp-box.green { border-color: #16a34a; background: #f0fdf4; }
  .imp-box.green .imp-val { color: #16a34a; }

  .scad-box { display: inline-flex; align-items: center; gap: 8px; border: 1px solid #ddd; border-radius: 5px; padding: 6px 10px; margin: 3px; }
  .scad-box.past { border-color: #dc2626; background: #fef2f2; }
  .scad-box.past .scad-date { color: #dc2626; }
  .scad-box .scad-days { font-size: 9px; color: #666; }
  .scad-box .scad-date { font-weight: 700; font-size: 12px; }
  .scad-box .scad-lbl { font-size: 10px; color: #555; }

  .diary-item { padding: 8px 0; border-bottom: 1px solid #f0f0f0; display: flex; gap: 10px; }
  .diary-item:last-child { border-bottom: none; }
  .diary-date { min-width: 50px; font-size: 10px; color: #666; font-weight: 700; }
  .diary-tipo { display: inline-block; font-size: 9px; padding: 2px 6px; border-radius: 10px; background: #e5e7eb; color: #333; margin-bottom: 3px; text-transform: uppercase; }
  .diary-testo { font-size: 11px; color: #222; white-space: pre-wrap; }
  .diary-importo { font-size: 11px; color: #16a34a; font-weight: 700; }

  .print-actions { text-align: center; margin-bottom: 16px; }
  .print-actions button { background: #e65c00; color: #fff; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-size: 12px; margin: 0 4px; }
  .print-actions a { color: #666; text-decoration: none; font-size: 12px; }

  .footer { margin-top: 16px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999; }

  @media print {
    .print-actions { display: none; }
    body { font-size: 10px; }
    .page { padding: 10px; }
  }
</style>
</head>
<body>
<div class="page">

  <div class="print-actions">
    <button onclick="window.print()">🖨️ Stampa / Salva PDF</button>
    <a href="{{ route('sinistri.show', $claim) }}">← Torna al sinistro</a>
  </div>

  {{-- INTESTAZIONE --}}
  <div class="header">
    <div class="header-left">
      <h1>PRATICA SINISTRO</h1>
      <h2>#{{ $claim->claim_number }}
        @if($claim->numero_sinistro_compagnia)
          &nbsp;·&nbsp; N° Sx compagnia: <strong>{{ $claim->numero_sinistro_compagnia }}</strong>
        @endif
      </h2>
      @if($claim->riferimento_gestore)
      <div style="font-size:11px;color:#555;margin-top:4px">Ref. gestore: {{ $claim->riferimento_gestore }}</div>
      @endif
    </div>
    <div class="header-right">
      <div class="badge-stato">{{ str_replace('_',' ', strtoupper($claim->status)) }}</div>
      <div style="margin-top:6px">Stampa: {{ now()->format('d/m/Y H:i') }}</div>
      <div>Sinistro del: {{ $claim->event_date?->format('d/m/Y') }}</div>
    </div>
  </div>

  {{-- DANNEGGIATO & VEICOLO --}}
  <div class="section">
    <div class="section-title">👤 Danneggiato & Veicolo</div>
    <div class="section-body grid2">
      <div>
        <div class="row"><span class="lbl">Danneggiato</span><span class="val">{{ $claim->customer?->display_name }}</span></div>
        @if($claim->customer?->codice_fiscale || $claim->danneggiato_cf)
        <div class="row"><span class="lbl">C.F. / P.IVA</span><span class="val">{{ $claim->danneggiato_cf ?: $claim->customer?->codice_fiscale }}</span></div>
        @endif
        @if($claim->customer?->phone)<div class="row"><span class="lbl">Tel. cliente</span><span class="val">{{ $claim->customer->phone }}</span></div>@endif
      </div>
      <div>
        @if($claim->vehicle)
        <div class="row"><span class="lbl">Targa</span><span class="val orange">{{ $claim->vehicle->plate }}</span></div>
        <div class="row"><span class="lbl">Veicolo</span><span class="val">{{ $claim->vehicle->brand }} {{ $claim->vehicle->model }}</span></div>
        @if($claim->valore_commerciale)<div class="row"><span class="lbl">Valore commerciale</span><span class="val">€ {{ number_format($claim->valore_commerciale,2,',','.') }}</span></div>@endif
        @endif
        <div class="row"><span class="lbl">Recupero IVA</span><span class="val">{{ $claim->recupera_iva ? '✅ SÌ' : 'NO' }}</span></div>
      </div>
    </div>
  </div>

  {{-- EVENTO --}}
  <div class="section">
    <div class="section-title">📍 Evento sinistro</div>
    <div class="section-body">
      <div class="grid2">
        <div class="row"><span class="lbl">Data sinistro</span><span class="val">{{ $claim->event_date?->format('d/m/Y') }}</span></div>
        <div class="row"><span class="lbl">Tipo</span><span class="val">{{ strtoupper($claim->claim_type) }}</span></div>
        <div class="row"><span class="lbl">Luogo</span><span class="val">{{ $claim->event_location ?? '—' }}</span></div>
      </div>
      @if($claim->event_description)
      <div style="margin-top:6px;background:#f9f9f9;padding:8px;border-radius:4px;font-size:10px;color:#444">{{ $claim->event_description }}</div>
      @endif
    </div>
  </div>

  {{-- COMPAGNIA & CONTROPARTE --}}
  <div class="section">
    <div class="section-title">🏢 Compagnia assicurativa & Controparte</div>
    <div class="section-body grid2">
      <div>
        @if($claim->insuranceCompany)
        <div class="row"><span class="lbl">Ass. gestione</span><span class="val">{{ $claim->insuranceCompany->name }}</span></div>
        @if($claim->insuranceCompany->phone)<div class="row"><span class="lbl">Tel. compagnia</span><span class="val">{{ $claim->insuranceCompany->phone }}</span></div>@endif
        @if($claim->insuranceCompany->pec)<div class="row"><span class="lbl">PEC compagnia</span><span class="val">{{ $claim->insuranceCompany->pec }}</span></div>@endif
        @if($claim->insuranceCompany->address)<div class="row"><span class="lbl">Sede legale</span><span class="val">{{ $claim->insuranceCompany->address }}</span></div>@endif
        @endif
        <div class="row"><span class="lbl">N° Polizza</span><span class="val">{{ $claim->policy_number ?? '—' }}</span></div>
      </div>
      <div>
        <div class="row"><span class="lbl">Targa controparte</span><span class="val orange">{{ $claim->counterpart_plate ?? '—' }}</span></div>
        <div class="row"><span class="lbl">Ass. controparte</span><span class="val">{{ $claim->counterpart_insurance ?? '—' }}</span></div>
        @if($claim->counterpart_policy)<div class="row"><span class="lbl">Polizza controparte</span><span class="val">{{ $claim->counterpart_policy }}</span></div>@endif
      </div>
    </div>
  </div>

  {{-- PERITO & LIQUIDATORE --}}
  <div class="section">
    <div class="section-title">🔍 Perito & Liquidatore</div>
    <div class="section-body grid2">
      <div>
        <div style="font-size:10px;font-weight:700;color:#666;margin-bottom:4px;text-transform:uppercase">Perito</div>
        @if($claim->expert)
        <div class="row"><span class="lbl">Nome</span><span class="val">{{ $claim->expert->name }}</span></div>
        @if($claim->expert->phone)<div class="row"><span class="lbl">Tel</span><span class="val">{{ $claim->expert->phone }}</span></div>@endif
        @if($claim->survey_date)<div class="row"><span class="lbl">Data perizia</span><span class="val">{{ $claim->survey_date->format('d/m/Y') }}</span></div>@endif
        @else
        <div style="color:#999;font-size:10px">Non assegnato</div>
        @endif
      </div>
      <div>
        <div style="font-size:10px;font-weight:700;color:#666;margin-bottom:4px;text-transform:uppercase">Liquidatore</div>
        @if($claim->liquidatore)
        <div class="row"><span class="lbl">Nome</span><span class="val">{{ $claim->liquidatore->name }}</span></div>
        @if($claim->liquidatore->phone)<div class="row"><span class="lbl">Tel</span><span class="val">{{ $claim->liquidatore->phone }}</span></div>@endif
        @if($claim->liquidatore->orario_disponibilita)<div class="row"><span class="lbl">Orario</span><span class="val">{{ $claim->liquidatore->orario_disponibilita }}</span></div>@endif
        @if($claim->liquidatore->email)<div class="row"><span class="lbl">Email</span><span class="val">{{ $claim->liquidatore->email }}</span></div>@endif
        @else
        <div style="color:#999;font-size:10px">Non assegnato</div>
        @endif
      </div>
    </div>
  </div>

  {{-- IMPORTI --}}
  <div class="section">
    <div class="section-title">💰 Importi</div>
    <div class="importi-grid">
      @if($claim->importo_richiesto)
      <div class="imp-box highlight"><div class="imp-label">Richiesto</div><div class="imp-val">€ {{ number_format($claim->importo_richiesto,2,',','.') }}</div></div>
      @endif
      @if($claim->importo_perizia)
      <div class="imp-box"><div class="imp-label">Perizia</div><div class="imp-val">€ {{ number_format($claim->importo_perizia,2,',','.') }}</div>
        @if($claim->costo_ora_mo || $claim->ore_lavoro)<div style="font-size:9px;color:#666;margin-top:3px">{{ $claim->ore_lavoro }}h × M.O. €{{ $claim->costo_ora_mo }} + Mat. €{{ $claim->costo_ora_materiali }}/h</div>@endif
      </div>
      @endif
      @if($claim->importo_concordato)
      <div class="imp-box green"><div class="imp-label">Concordato{{ $claim->concordato ? ' ✓' : '' }}</div><div class="imp-val">€ {{ number_format($claim->importo_concordato,2,',','.') }}</div></div>
      @endif
      @if($claim->noleggio_importo)
      <div class="imp-box"><div class="imp-label">Noleggio{{ $claim->noleggio_giorni ? ' ('.$claim->noleggio_giorni.'gg)' : '' }}</div><div class="imp-val">€ {{ number_format($claim->noleggio_importo,2,',','.') }}</div></div>
      @endif
      @if($claim->fermo_tecnico_importo)
      <div class="imp-box"><div class="imp-label">Fermo tecnico{{ $claim->fermo_tecnico_giorni ? ' ('.$claim->fermo_tecnico_giorni.'gg)' : '' }}</div><div class="imp-val">€ {{ number_format($claim->fermo_tecnico_importo,2,',','.') }}</div></div>
      @endif
      @if($claim->traino_importo)
      <div class="imp-box"><div class="imp-label">Traino</div><div class="imp-val">€ {{ number_format($claim->traino_importo,2,',','.') }}</div></div>
      @endif
      @if($claim->paid_amount)
      <div class="imp-box green"><div class="imp-label">LIQUIDATO</div><div class="imp-val">€ {{ number_format($claim->paid_amount,2,',','.') }}</div>
        @if($claim->paid_date)<div style="font-size:9px;color:#16a34a">{{ $claim->paid_date->format('d/m/Y') }}</div>@endif
      </div>
      @endif
    </div>
    @if($claim->iban_liquidazione || $claim->beneficiario_liquidazione)
    <div class="section-body" style="padding-top:0">
      <div class="row"><span class="lbl">IBAN</span><span class="val">{{ $claim->iban_liquidazione }}</span></div>
      <div class="row"><span class="lbl">Beneficiario</span><span class="val">{{ $claim->beneficiario_liquidazione }}</span></div>
      @if($claim->onorario_percentuale)<div class="row"><span class="lbl">Onorario</span><span class="val">{{ $claim->onorario_percentuale }}%</span></div>@endif
    </div>
    @endif
  </div>

  {{-- SCADENZE CID --}}
  @if($claim->cid_expiry || $claim->scadenza_nomina_perito || $claim->scadenza_chiusura_perito || $claim->scadenza_chiusura_totale)
  <div class="section">
    <div class="section-title">📅 Scadenze</div>
    <div class="section-body" style="display:flex;flex-wrap:wrap;gap:4px">
      @if($claim->cid_date)
      <div class="scad-box {{ $claim->cid_expiry?->isPast() ? 'past' : '' }}">
        <div>
          <div class="scad-lbl">CID firmato</div>
          <div class="scad-date">{{ $claim->cid_date->format('d/m/Y') }}</div>
          @if($claim->cid_expiry)<div class="scad-days">Scad. {{ $claim->cid_expiry->format('d/m/Y') }}</div>@endif
        </div>
      </div>
      @endif
      @if($claim->scadenza_nomina_perito)
      @php $past = $claim->scadenza_nomina_perito->isPast(); @endphp
      <div class="scad-box {{ $past ? 'past' : '' }}">
        <div>
          <div class="scad-lbl">10gg — Nomina perito</div>
          <div class="scad-date">{{ $claim->scadenza_nomina_perito->format('d/m/Y') }}</div>
        </div>
      </div>
      @endif
      @if($claim->scadenza_chiusura_perito)
      @php $past = $claim->scadenza_chiusura_perito->isPast(); @endphp
      <div class="scad-box {{ $past ? 'past' : '' }}">
        <div>
          <div class="scad-lbl">35gg — Chiusura perito</div>
          <div class="scad-date">{{ $claim->scadenza_chiusura_perito->format('d/m/Y') }}</div>
        </div>
      </div>
      @endif
      @if($claim->scadenza_chiusura_totale)
      @php $past = $claim->scadenza_chiusura_totale->isPast(); @endphp
      <div class="scad-box {{ $past ? 'past' : '' }}">
        <div>
          <div class="scad-lbl">60gg — Chiusura totale</div>
          <div class="scad-date">{{ $claim->scadenza_chiusura_totale->format('d/m/Y') }}</div>
        </div>
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- DIARIO COMUNICAZIONI --}}
  @if($claim->diary->count())
  <div class="section">
    <div class="section-title">📋 Diario comunicazioni ({{ $claim->diary->count() }} voci)</div>
    <div class="section-body">
      @foreach($claim->diary->sortBy('data_evento') as $d)
      <div class="diary-item">
        <div class="diary-date">{{ $d->data_evento->format('d/m/Y') }}</div>
        <div style="flex:1">
          <span class="diary-tipo">{{ $d->tipo }}</span>
          @if($d->oggetto)<div style="font-weight:700;font-size:11px">{{ $d->oggetto }}</div>@endif
          <div class="diary-testo">{{ $d->testo }}</div>
          @if($d->importo)<div class="diary-importo">€ {{ number_format($d->importo,2,',','.') }}</div>@endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- NOTE --}}
  @if($claim->notes)
  <div class="section">
    <div class="section-title">📝 Note</div>
    <div class="section-body" style="white-space:pre-wrap;font-size:10px;color:#333">{{ $claim->notes }}</div>
  </div>
  @endif

  <div class="footer">
    Documento generato il {{ now()->format('d/m/Y H:i') }} — {{ config('app.name') }}
  </div>

</div>
<script>window.onload = function() { /* auto-print se ?print=1 */ if (location.search.includes('print=1')) window.print(); }</script>
</body>
</html>
