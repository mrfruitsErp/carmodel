@extends('layouts.app')
@section('title', 'Marketplace â€” Dashboard')

@section('topbar-actions')
<form action="{{ route('marketplace.sync.leads') }}" method="POST" style="display:inline">
  @csrf
  <button type="submit" class="btn btn-ghost btn-sm">â†» Sync lead</button>
</form>
<a href="{{ route('marketplace.vehicles.create') }}" class="btn btn-primary btn-sm">+ Nuovo veicolo</a>
@endsection

@section('content')

{{-- KPI --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  @foreach([
    ['Annunci live',        $stats['listings_published'], 'su '.$stats['vehicles_active'].' veicoli attivi', 'var(--green)'],
    ['Visualizzazioni',     number_format($stats['total_views'],0,',','.'), 'su tutte le piattaforme', 'var(--blue)'],
    ['Lead nuovi',          $stats['leads_new'], $stats['leads_total'].' totali', 'var(--orange)'],
    ['Venduti',             $stats['vehicles_sold'], 'questo periodo', 'var(--purple)'],
  ] as [$label, $val, $sub, $color])
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:16px;position:relative;overflow:hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $color }}"></div>
    <div style="font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">{{ $label }}</div>
    <div style="font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--text);line-height:1">{{ $val }}</div>
    <div style="font-size:11px;color:var(--text3);margin-top:4px">{{ $sub }}</div>
  </div>
  @endforeach
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px">

  {{-- Performance piattaforme --}}
  <div class="card">
    <div class="card-title">ðŸ“Š Performance piattaforme</div>
    @forelse($stats['by_platform'] as $platform => $data)
    @php $maxViews = max(1, collect($stats['by_platform'])->max('views')); $pct = min(100, round(($data['views']/$maxViews)*100)); @endphp
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
      <div style="width:120px;flex-shrink:0">@include('marketplace.partials._platform_badge', ['platform' => $platform])</div>
      <div style="flex:1;height:6px;background:var(--bg3);border-radius:3px;overflow:hidden">
        <div style="height:6px;background:var(--orange);border-radius:3px;width:{{ $pct }}%"></div>
      </div>
      <div style="display:flex;gap:12px;font-size:11px;color:var(--text3);white-space:nowrap">
        <span>{{ number_format($data['views'],0,',','.') }} views</span>
        <span style="color:var(--green-text)">{{ $data['contacts'] }} contatti</span>
        <span style="font-weight:700;color:var(--text)">{{ $data['cnt'] }} ann.</span>
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:30px;color:var(--text3)">
      <div style="font-size:32px;margin-bottom:8px">ðŸš€</div>
      <div style="font-size:13px;margin-bottom:12px">Nessun annuncio pubblicato</div>
      <a href="{{ route('marketplace.settings') }}" style="color:var(--orange);font-size:13px">Configura le piattaforme â†’</a>
    </div>
    @endforelse
  </div>

  {{-- Stato stock --}}
  <div class="card">
    <div class="card-title">ðŸš— Stato stock</div>
    <div style="space-y:8px">
      @foreach([
        ['Attivi',        $stats['vehicles_active'],  'var(--green)',  'var(--green-bg)'],
        ['Bozze',         $stats['vehicles_draft'],   'var(--text3)',  'var(--bg3)'],
        ['Venduti',       $stats['vehicles_sold'],    'var(--blue)',   'var(--blue-bg)'],
        ['Errori annunci',$stats['listings_error'],   'var(--red)',    'var(--red-bg)'],
      ] as [$label, $count, $color, $bg])
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:{{ $bg }};border-radius:8px;margin-bottom:6px">
        <div style="display:flex;align-items:center;gap:8px">
          <span style="width:8px;height:8px;border-radius:50%;background:{{ $color }};display:inline-block"></span>
          <span style="font-size:13px;color:var(--text)">{{ $label }}</span>
        </div>
        <span style="font-family:var(--font-display);font-size:20px;font-weight:700;color:{{ $color }}">{{ $count }}</span>
      </div>
      @endforeach
    </div>
    <div style="padding-top:10px;border-top:1px solid var(--border);margin-top:4px">
      <a href="{{ route('marketplace.vehicles.index') }}" style="font-size:12px;color:var(--text3);text-decoration:none">Vedi tutti i veicoli â†’</a>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

  {{-- Lead recenti --}}
  <div class="card" style="padding:0;overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)">
      <div class="card-title" style="margin-bottom:0">ðŸ’¬ Lead recenti</div>
      @if($stats['leads_new'] > 0)
        <span style="background:var(--green-bg);color:var(--green-text);font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">{{ $stats['leads_new'] }} nuovi</span>
      @endif
    </div>
    @forelse($leads as $lead)
    <div style="display:flex;align-items:start;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border)">
      <div style="width:30px;height:30px;border-radius:50%;background:var(--orange-bg);border:1px solid var(--orange-border);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--orange);flex-shrink:0">
        {{ strtoupper(substr($lead->lead_name ?? 'U',0,1)) }}
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
          <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $lead->lead_name ?? 'Anonimo' }}</span>
          @include('marketplace.partials._platform_badge', ['platform' => $lead->platform])
        </div>
        @if($lead->saleVehicle)
          <div style="font-size:11px;color:var(--blue-text);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $lead->saleVehicle->display_name }}</div>
        @endif
        @if($lead->lead_message)
          <div style="font-size:11px;color:var(--text3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $lead->lead_message }}</div>
        @endif
      </div>
      <span style="font-size:10px;color:var(--text3);white-space:nowrap">{{ $lead->created_at->diffForHumans() }}</span>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--text3)">
      <div style="font-size:32px;margin-bottom:8px">ðŸ“­</div>
      <div style="font-size:13px">Nessun lead ancora</div>
    </div>
    @endforelse
    @if($leads->count() > 0)
    <div style="padding:10px 20px;border-top:1px solid var(--border)">
      <a href="{{ route('marketplace.leads.index') }}" style="font-size:12px;color:var(--text3);text-decoration:none">Tutti i lead â†’</a>
    </div>
    @endif
  </div>

  {{-- Annunci con errori --}}
  <div class="card" style="padding:0;overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)">
      <div class="card-title" style="margin-bottom:0">âš ï¸ Annunci con problemi</div>
    </div>
    @forelse($errors as $errListing)
    <div style="display:flex;align-items:start;gap:10px;padding:12px 20px;border-bottom:1px solid var(--border)">
      <div style="width:8px;height:8px;border-radius:50%;background:var(--red);margin-top:5px;flex-shrink:0"></div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
          <span style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $errListing->saleVehicle?->display_name ?? 'Veicolo #'.$errListing->sale_vehicle_id }}</span>
          @include('marketplace.partials._platform_badge', ['platform' => $errListing->platform])
        </div>
        <div style="font-size:11px;color:var(--red-text);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $errListing->last_error_message }}</div>
        <div style="font-size:10px;color:var(--text3);margin-top:2px">{{ $errListing->last_error_at?->diffForHumans() }}</div>
      </div>
      <a href="{{ route('marketplace.vehicles.show', $errListing->sale_vehicle_id) }}" style="font-size:11px;color:var(--blue-text);text-decoration:none;white-space:nowrap">Risolvi â†’</a>
    </div>
    @empty
    <div style="text-align:center;padding:40px">
      <div style="width:40px;height:40px;border-radius:50%;background:var(--green-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green-text)" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
      </div>
      <div style="font-size:13px;color:var(--text3)">Nessun errore!</div>
    </div>
    @endforelse
    @if($errors->count() > 0)
    <div style="padding:10px 20px;border-top:1px solid var(--border)">
      <form action="{{ route('marketplace.sync.stats') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" style="background:none;border:none;font-size:12px;color:var(--text3);cursor:pointer">Forza ri-sincronizzazione â†’</button>
      </form>
    </div>
    @endif
  </div>

</div>
@endsection