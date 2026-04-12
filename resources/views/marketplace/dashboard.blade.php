@extends('layouts.app')
@section('title', 'Marketplace - Dashboard')
@section('topbar-actions')
<a href="{{ route('marketplace.vehicles.create') }}" class="btn btn-primary btn-sm">+ Nuovo veicolo</a>
@endsection
@section('content')

{{-- STATS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  @foreach([
    ['Annunci live', $stats['listings_live'] ?? 0, 'var(--green)', 'su '.($stats['vehicles_active']??0).' veicoli attivi'],
    ['Visualizzazioni', $stats['total_views'] ?? 0, 'var(--blue)', 'su tutte le piattaforme'],
    ['Lead nuovi', $stats['leads_new'] ?? 0, 'var(--orange)', ($stats['leads_total']??0).' totali'],
    ['Venduti', $stats['vehicles_sold'] ?? 0, 'var(--purple)', 'questo periodo'],
  ] as [$label, $val, $color, $sub])
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:16px;position:relative;overflow:hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $color }}"></div>
    <div style="font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">{{ $label }}</div>
    <div style="font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--text);line-height:1">{{ number_format($val) }}</div>
    <div style="font-size:11px;color:var(--text3);margin-top:6px">{{ $sub }}</div>
  </div>
  @endforeach
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

  {{-- COLONNA SINISTRA --}}
  <div>

    {{-- LISTA VEICOLI RAPIDA --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="card-title" style="margin:0">Veicoli in stock</div>
        <div style="display:flex;gap:6px">
          @foreach(['tutti'=>'Tutti','attivo'=>'Attivi','bozza'=>'Bozze','venduto'=>'Venduti'] as $k=>$v)
          <button onclick="filterVeicoli('{{ $k }}')" id="flt-{{ $k }}"
            style="padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;border:1px solid var(--border2);background:{{ $k==='tutti'?'var(--orange)':'transparent' }};color:{{ $k==='tutti'?'#000':'var(--text2)' }}">
            {{ $v }}
          </button>
          @endforeach
        </div>
      </div>

      <div id="veicoli-list">
        @forelse($allVehicles ?? [] as $v)
        <div class="veicolo-row" data-status="{{ $v->status }}"
          style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);transition:.15s"
          onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">

          {{-- FOTO --}}
          <div style="width:64px;height:48px;border-radius:6px;overflow:hidden;flex-shrink:0;background:var(--bg3)">
            @if($v->primary_photo_url)
              <img src="{{ $v->primary_photo_url }}" style="width:100%;height:100%;object-fit:cover">
            @else
              <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                <svg width="20" height="20" fill="none" stroke="var(--text3)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
              </div>
            @endif
          </div>

          {{-- INFO --}}
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $v->brand }} {{ $v->model }} {{ $v->version }}
            </div>
            <div style="font-size:11px;color:var(--text3)">
              {{ $v->year }} · {{ number_format($v->mileage,0,',','.') }} km · {{ ucfirst($v->fuel_type) }}
              @if($v->plate) · <span style="font-family:var(--mono)">{{ $v->plate }}</span>@endif
            </div>
          </div>

          {{-- PREZZO --}}
          <div style="text-align:right;flex-shrink:0">
            <div style="font-size:14px;font-weight:700;color:var(--orange)">{{ number_format($v->asking_price,0,',','.') }} €</div>
            @if($v->margin_percent)<div style="font-size:10px;color:var(--green-text)">+{{ $v->margin_percent }}%</div>@endif
          </div>

          {{-- STATO --}}
          <div style="flex-shrink:0">
            <span class="badge badge-{{ $v->status==='attivo'?'green':($v->status==='venduto'?'blue':($v->status==='sospeso'?'amber':'gray')) }}" style="font-size:10px">
              {{ ucfirst($v->status) }}
            </span>
          </div>

          {{-- AZIONI RAPIDE --}}
          <div style="display:flex;gap:4px;flex-shrink:0">
            <a href="{{ route('marketplace.vehicles.show', $v) }}" title="Visualizza"
              style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--text2);text-decoration:none;transition:.15s"
              onmouseover="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'"
              onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="{{ route('marketplace.vehicles.edit', $v) }}" title="Modifica"
              style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--text2);text-decoration:none;transition:.15s"
              onmouseover="this.style.borderColor='var(--orange)';this.style.color='var(--orange)'"
              onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            @if($v->status !== 'venduto')
            <form action="{{ route('marketplace.vehicles.status', $v) }}" method="POST" style="display:inline">
              @csrf
              <input type="hidden" name="status" value="{{ $v->status === 'attivo' ? 'sospeso' : 'attivo' }}">
              <button type="submit" title="{{ $v->status === 'attivo' ? 'Sospendi' : 'Attiva' }}"
                style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border2);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text2);transition:.15s"
                onmouseover="this.style.borderColor='var(--amber)';this.style.color='var(--amber)'"
                onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
              </button>
            </form>
            <button type="button" title="Segna venduto" onclick="apriVenduto({{ $v->id }}, {{ $v->asking_price }})"
              style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border2);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text2);transition:.15s"
              onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
              onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </button>
            @endif
            <form action="{{ route('marketplace.vehicles.destroy', $v) }}" method="POST" style="display:inline"
              onsubmit="return confirm('Eliminare {{ $v->brand }} {{ $v->model }}?')">
              @csrf @method('DELETE')
              <button type="submit" title="Elimina"
                style="width:28px;height:28px;border-radius:6px;border:1px solid var(--border2);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text2);transition:.15s"
                onmouseover="this.style.borderColor='var(--red)';this.style.color='var(--red)'"
                onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--text2)'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
              </button>
            </form>
          </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--text3);font-size:13px">Nessun veicolo ancora</div>
        @endforelse
      </div>
      <div style="margin-top:12px;text-align:right">
        <a href="{{ route('marketplace.vehicles.index') }}" style="font-size:12px;color:var(--orange);text-decoration:none">Vedi tutti →</a>
      </div>
    </div>

    {{-- LEAD RECENTI --}}
    <div class="card">
      <div class="card-title">Lead recenti</div>
      @forelse($recentLeads ?? [] as $lead)
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $lead->name }}</div>
          <div style="font-size:11px;color:var(--text3)">{{ $lead->vehicle->brand ?? '-' }} {{ $lead->vehicle->model ?? '' }} · {{ $lead->created_at->diffForHumans() }}</div>
        </div>
        <div style="font-size:11px;color:var(--text3)">{{ $lead->phone ?? $lead->email }}</div>
      </div>
      @empty
      <div style="text-align:center;padding:30px;color:var(--text3);font-size:13px">Nessun lead ancora</div>
      @endforelse
    </div>

  </div>

  {{-- COLONNA DESTRA --}}
  <div>
    <div class="card">
      <div class="card-title">Stato stock</div>
      @foreach([
        ['Attivi', $stats['vehicles_active']??0, 'var(--green)'],
        ['Bozze', $stats['vehicles_draft']??0, 'var(--text3)'],
        ['Venduti', $stats['vehicles_sold']??0, 'var(--blue)'],
        ['Errori annunci', $stats['listings_error']??0, 'var(--red)'],
      ] as [$label, $val, $color])
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px"><span style="width:8px;height:8px;border-radius:50%;background:{{ $color }}"></span><span style="font-size:13px">{{ $label }}</span></div>
        <span style="font-size:16px;font-weight:700;color:{{ $color }}">{{ $val }}</span>
      </div>
      @endforeach
    </div>

    <div class="card">
      <div class="card-title">Performance piattaforme</div>
      @if(empty($stats['listings_live']))
        <div style="text-align:center;padding:30px;color:var(--text3);font-size:13px">
          <div style="margin-bottom:8px">Nessun annuncio pubblicato</div>
          <a href="{{ route('marketplace.settings') }}" style="color:var(--orange);font-size:12px">Configura piattaforme</a>
        </div>
      @else
        @foreach($platformStats ?? [] as $platform => $data)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
          <div style="font-weight:600;font-size:13px;width:80px">{{ ucfirst($platform) }}</div>
          <div style="flex:1;font-size:12px;color:var(--text3)">{{ $data['listings'] ?? 0 }} annunci</div>
          <div style="font-size:13px;font-weight:600">{{ number_format($data['views'] ?? 0) }} <span style="font-size:10px;color:var(--text3)">views</span></div>
          <div style="font-size:13px;font-weight:600;color:var(--green-text)">{{ $data['leads'] ?? 0 }} <span style="font-size:10px;color:var(--text3)">lead</span></div>
        </div>
        @endforeach
      @endif
    </div>

    <div class="card">
      <div class="card-title">Annunci con problemi</div>
      @forelse($errorListings ?? [] as $listing)
      <div style="padding:8px 0;border-bottom:1px solid var(--border);font-size:12px">
        <div style="color:var(--red-text)">{{ $listing->vehicle->brand ?? '-' }} {{ $listing->vehicle->model ?? '' }}</div>
        <div style="color:var(--text3)">{{ Str::limit($listing->last_error_message, 50) }}</div>
      </div>
      @empty
      <div style="text-align:center;padding:20px;color:var(--green-text);font-size:13px">Nessun errore</div>
      @endforelse
    </div>
  </div>
</div>

{{-- MODAL VENDUTO --}}
<div id="modal-venduto-dash" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border-radius:12px;padding:24px;width:340px">
    <div style="font-size:16px;font-weight:700;margin-bottom:16px">Segna come venduto</div>
    <form id="form-venduto-dash" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Prezzo di vendita (euro) *</label>
        <input type="number" name="sold_price" id="dash-sold-price" class="form-input" step="100" min="0" required>
      </div>
      <div class="form-group">
        <label class="form-label">Cliente (opzionale)</label>
        <select name="customer_id" class="form-select">
          <option value="">-- Nessuno --</option>
          @foreach(\App\Models\Customer::where('tenant_id', auth()->user()->tenant_id)->orderBy('last_name')->get() as $c)
<option value="{{ $c->id }}">{{ trim($c->first_name.' '.$c->last_name) }} {{ $c->company_name ? '('.$c->company_name.')' : '' }}</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px">
        <button type="button" onclick="document.getElementById('modal-venduto-dash').style.display='none'" class="btn btn-ghost btn-sm">Annulla</button>
        <button type="submit" class="btn btn-primary btn-sm">Conferma vendita</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function filterVeicoli(status) {
  document.querySelectorAll('[id^="flt-"]').forEach(function(b) {
    b.style.background = 'transparent';
    b.style.color = 'var(--text2)';
  });
  document.getElementById('flt-' + status).style.background = 'var(--orange)';
  document.getElementById('flt-' + status).style.color = '#000';
  document.querySelectorAll('.veicolo-row').forEach(function(row) {
    if (status === 'tutti' || row.dataset.status === status) {
      row.style.display = 'flex';
    } else {
      row.style.display = 'none';
    }
  });
}

function apriVenduto(id, prezzo) {
  var base = '{{ url("marketplace/vehicles") }}';
  document.getElementById('form-venduto-dash').action = base + '/' + id + '/sold';
  document.getElementById('dash-sold-price').value = prezzo;
  document.getElementById('modal-venduto-dash').style.display = 'flex';
}
</script>
@endpush
@endsection