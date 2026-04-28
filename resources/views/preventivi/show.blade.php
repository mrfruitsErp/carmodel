@extends('layouts.app')
@section('title', 'Preventivo #'.$preventivo->quote_number)

@section('topbar-actions')
<a href="{{ route('preventivi.edit', $preventivo) }}" class="btn btn-ghost btn-sm">✏️ Modifica</a>
@endsection

@section('content')
<div style="margin-bottom:16px">
  <a href="{{ route('preventivi.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Preventivi</a>
</div>

{{-- HEADER STATO --}}
<div class="card" style="margin-bottom:16px">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div style="font-family:var(--mono);font-size:20px;font-weight:700;color:var(--orange)">{{ $preventivo->quote_number }}</div>
      <div style="font-size:12px;color:var(--text3);margin-top:2px">
        {{ ucfirst($preventivo->job_type) }} —
        Creato il {{ $preventivo->created_at->format('d/m/Y') }}
        @if($preventivo->valid_until)
          — Valido fino al
          <span style="color:{{ $preventivo->valid_until->isPast() ? 'var(--red)' : 'var(--text2)' }}">
            {{ $preventivo->valid_until->format('d/m/Y') }}
          </span>
        @endif
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
      @php
        $sc = ['bozza'=>'badge-gray','inviato'=>'badge-blue','accettato'=>'badge-green','rifiutato'=>'badge-red','scaduto'=>'badge-gray'];
      @endphp
      <span class="badge {{ $sc[$preventivo->status] ?? 'badge-gray' }}" style="font-size:13px;padding:5px 14px">
        {{ ucfirst($preventivo->status) }}
      </span>

      {{-- Azioni stato --}}
      @if($preventivo->status === 'bozza')
        <form method="POST" action="{{ route('preventivi.stato', $preventivo) }}">
          @csrf <input type="hidden" name="status" value="inviato">
          <button type="submit" class="btn btn-primary btn-sm">📤 Segna inviato</button>
        </form>
      @endif
      @if(in_array($preventivo->status, ['bozza','inviato']))
        <form method="POST" action="{{ route('preventivi.stato', $preventivo) }}" style="display:inline">
          @csrf <input type="hidden" name="status" value="accettato">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--green-text);border-color:var(--green)">✓ Accettato</button>
        </form>
        <form method="POST" action="{{ route('preventivi.stato', $preventivo) }}" style="display:inline">
          @csrf <input type="hidden" name="status" value="rifiutato">
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);border-color:var(--red)">✗ Rifiutato</button>
        </form>
      @endif
      @if($preventivo->status === 'accettato' && !$preventivo->converted_to_job_id)
        <form method="POST" action="{{ route('preventivi.converti', $preventivo) }}">
          @csrf
          <button type="submit" class="btn btn-primary btn-sm">🔧 Converti in commessa</button>
        </form>
      @endif
      @if($preventivo->converted_to_job_id)
        <a href="{{ route('lavorazioni.show', $preventivo->convertedJob) }}" class="btn btn-ghost btn-sm" style="color:var(--teal-text);border-color:var(--teal)">
          → Commessa {{ $preventivo->convertedJob->job_number }}
        </a>
      @endif
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start">
  <div>

    {{-- VOCI --}}
    <div class="card" style="padding:0;overflow:hidden">
      <div style="padding:16px 20px;border-bottom:1px solid var(--border2)">
        <div class="card-title" style="margin-bottom:0">📦 Voci preventivo</div>
      </div>
      @if($preventivo->items->count())
      <table>
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Descrizione</th>
            <th style="text-align:right">Qtà</th>
            <th style="text-align:right">Prezzo</th>
            <th style="text-align:right">Sc.%</th>
            <th style="text-align:right">Totale</th>
          </tr>
        </thead>
        <tbody>
          @foreach($preventivo->items->sortBy('sort_order') as $item)
          <tr>
            <td>
              @php $tipoLabel = ['manodopera'=>'🔧 Manodopera','ricambio'=>'🔩 Ricambio','materiale'=>'🪣 Materiale','servizio'=>'⚙️ Servizio','altro'=>'📁 Altro']; @endphp
              <span class="badge badge-teal" style="font-size:11px">{{ $tipoLabel[$item->item_type] ?? ucfirst($item->item_type) }}</span>
            </td>
            <td>{{ $item->description }}</td>
            <td style="text-align:right;font-family:var(--mono)">{{ number_format($item->quantity, 2, ',', '.') }}</td>
            <td style="text-align:right;font-family:var(--mono)">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td style="text-align:right;color:var(--text3)">
              {{ $item->discount_percent > 0 ? number_format($item->discount_percent, 1, ',', '.').'%' : '—' }}
            </td>
            <td style="text-align:right;font-family:var(--mono);font-weight:600">
              € {{ number_format($item->total_price, 2, ',', '.') }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <div style="text-align:center;padding:30px;color:var(--text3)">Nessuna voce nel preventivo</div>
      @endif

      {{-- TOTALI --}}
      <div style="padding:16px 20px;border-top:1px solid var(--border2);background:var(--bg3)">
        <div style="display:flex;flex-direction:column;gap:6px;max-width:260px;margin-left:auto">
          <div style="display:flex;justify-content:space-between;font-size:13px">
            <span style="color:var(--text3)">Subtotale</span>
            <span style="font-family:var(--mono)">€ {{ number_format($preventivo->subtotal, 2, ',', '.') }}</span>
          </div>
          @if($preventivo->discount_percent > 0)
          <div style="display:flex;justify-content:space-between;font-size:13px">
            <span style="color:var(--text3)">Sconto ({{ number_format($preventivo->discount_percent,1,',','.') }}%)</span>
            <span style="font-family:var(--mono);color:var(--red-text)">- € {{ number_format($preventivo->discount_amount, 2, ',', '.') }}</span>
          </div>
          @endif
          <div style="display:flex;justify-content:space-between;font-size:13px">
            <span style="color:var(--text3)">Imponibile</span>
            <span style="font-family:var(--mono)">€ {{ number_format($preventivo->subtotal - $preventivo->discount_amount, 2, ',', '.') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:13px">
            <span style="color:var(--text3)">IVA ({{ number_format($preventivo->vat_percent,0,',','.') }}%)</span>
            <span style="font-family:var(--mono)">€ {{ number_format($preventivo->vat_amount, 2, ',', '.') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:700;border-top:1px solid var(--border);padding-top:8px;margin-top:4px">
            <span>TOTALE</span>
            <span style="font-family:var(--mono);color:var(--orange)">€ {{ number_format($preventivo->total, 2, ',', '.') }}</span>
          </div>
        </div>
      </div>
    </div>

    @if($preventivo->description)
    <div class="card">
      <div class="card-title">📋 Descrizione lavori</div>
      <div style="font-size:13px;color:var(--text2);line-height:1.7">{{ $preventivo->description }}</div>
    </div>
    @endif

    @if($preventivo->notes)
    <div class="card">
      <div class="card-title">📝 Note</div>
      <div style="font-size:13px;color:var(--text2);line-height:1.7">{{ $preventivo->notes }}</div>
    </div>
    @endif

  </div>

  {{-- COLONNA DESTRA --}}
  <div>
    <div class="card">
      <div class="card-title">👤 Cliente</div>
      <div class="info-row">
        <span class="info-label">Nome</span>
        <a href="{{ route('clienti.show', $preventivo->customer) }}" style="color:var(--blue-text)">
          {{ $preventivo->customer->display_name }}
        </a>
      </div>
      @if($preventivo->customer->phone)
      <div class="info-row">
        <span class="info-label">Telefono</span>
        <a href="tel:{{ $preventivo->customer->phone }}" style="color:var(--text)">{{ $preventivo->customer->phone }}</a>
      </div>
      @endif
      @if($preventivo->customer->email)
      <div class="info-row">
        <span class="info-label">Email</span>
        <a href="mailto:{{ $preventivo->customer->email }}" style="color:var(--blue-text);font-size:12px">{{ $preventivo->customer->email }}</a>
      </div>
      @endif
    </div>

    <div class="card">
      <div class="card-title">🚗 Veicolo</div>
      <div class="info-row">
        <span class="info-label">Targa</span>
        <span class="targa">{{ $preventivo->vehicle->plate }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Modello</span>
        <span class="info-value">{{ $preventivo->vehicle->brand }} {{ $preventivo->vehicle->model }}</span>
      </div>
      @if($preventivo->vehicle->year)
      <div class="info-row">
        <span class="info-label">Anno</span>
        <span class="info-value">{{ $preventivo->vehicle->year }}</span>
      </div>
      @endif
      <div style="margin-top:10px">
        <a href="{{ route('veicoli.show', $preventivo->vehicle) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">
          Scheda veicolo
        </a>
      </div>
    </div>

    @if($preventivo->claim)
    <div class="card">
      <div class="card-title">⚡ Sinistro collegato</div>
      <div class="info-row">
        <span class="info-label">Numero</span>
        <a href="{{ route('sinistri.show', $preventivo->claim) }}" style="color:var(--blue-text);font-family:var(--mono);font-size:12px">
          #{{ $preventivo->claim->claim_number }}
        </a>
      </div>
    </div>
    @endif

    <div class="card" style="background:var(--bg3)">
      <div style="font-size:11px;color:var(--text3);line-height:1.8">
        <div>Creato: {{ $preventivo->created_at->format('d/m/Y H:i') }}</div>
        <div>Aggiornato: {{ $preventivo->updated_at->format('d/m/Y H:i') }}</div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px">
        <a href="{{ route('preventivi.edit', $preventivo) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">
          ✏️ Modifica preventivo
        </a>
        <form action="{{ route('preventivi.destroy', $preventivo) }}" method="POST"
          onsubmit="return confirm('Eliminare questo preventivo?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);border-color:var(--red);width:100%">
            🗑 Elimina
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
