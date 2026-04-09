@extends('layouts.app')
@section('title', 'Veicoli in vendita')

@section('topbar-actions')
<a href="{{ route('marketplace.vehicles.create') }}" class="btn btn-primary btn-sm">+ Aggiungi veicolo</a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  @foreach([
    ['Totale stock', $vehicles->total(), 'var(--blue)'],
    ['Attivi', $stats['vehicles_active'], 'var(--green)'],
    ['Bozze', $stats['vehicles_draft'], 'var(--amber)'],
    ['Venduti', $stats['vehicles_sold'], 'var(--orange)'],
  ] as [$label, $val, $color])
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:10px;padding:14px 16px;position:relative;overflow:hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $color }}"></div>
    <div style="font-size:10px;color:var(--text3);font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">{{ $label }}</div>
    <div style="font-family:var(--font-display);font-size:26px;font-weight:700;color:var(--text);line-height:1">{{ $val }}</div>
  </div>
  @endforeach
</div>

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
  <div class="search-bar" style="max-width:280px">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Marca, modello, targa...">
  </div>
  <select name="status" onchange="this.form.submit()" style="background:var(--bg2);border:1px solid var(--border2);border-radius:6px;padding:7px 12px;font-size:13px;color:var(--text);outline:none;cursor:pointer">
    <option value="">Tutti gli stati</option>
    @foreach(['bozza'=>'Bozze','attivo'=>'Attivi','venduto'=>'Venduti','sospeso'=>'Sospesi'] as $v=>$l)
      <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
    @endforeach
  </select>
  @if(request()->hasAny(['search','status']))
    <a href="{{ route('marketplace.vehicles.index') }}" class="btn btn-ghost btn-sm">Reset</a>
  @endif
  <div style="margin-left:auto;font-size:12px;color:var(--text3)">{{ $vehicles->total() }} veicoli</div>
</form>

@if($vehicles->isEmpty())
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:12px;padding:60px 24px;text-align:center">
    <div style="margin-bottom:16px">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
    </div>
    <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px">Nessun veicolo in stock</div>
    <div style="font-size:13px;color:var(--text3);margin-bottom:20px">Aggiungi il primo veicolo per iniziare a vendere</div>
    <a href="{{ route('marketplace.vehicles.create') }}" class="btn btn-primary">+ Aggiungi veicolo</a>
  </div>
@else

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
  @foreach($vehicles as $vehicle)
  @php
    $statusCfg = match($vehicle->status) {
      'attivo'  => ['bg'=>'var(--green-bg)', 'color'=>'var(--green-text)', 'label'=>'Attivo',  'dot'=>'var(--green)'],
      'venduto' => ['bg'=>'var(--blue-bg)',  'color'=>'var(--blue-text)',  'label'=>'Venduto', 'dot'=>'var(--blue)'],
      'sospeso' => ['bg'=>'var(--amber-bg)', 'color'=>'var(--amber-text)', 'label'=>'Sospeso', 'dot'=>'var(--amber)'],
      default   => ['bg'=>'var(--bg4)',       'color'=>'var(--text3)',      'label'=>'Bozza',   'dot'=>'var(--border3)'],
    };
    $publishedCount = $vehicle->listings->where('status','published')->count();
    $totalViews = $vehicle->listings->sum('views');
    $leadsCount = $vehicle->leads()->count();
    $photoUrl = $vehicle->getFirstMediaUrl('sale_photos', 'thumb');
    $photoCount = $vehicle->getMedia('sale_photos')->count();
  @endphp
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:12px;overflow:hidden;transition:all .2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">

    <a href="{{ route('marketplace.vehicles.show', $vehicle) }}" style="display:block;position:relative;height:180px;background:#f0f0f0;overflow:hidden">
      @if($photoUrl)
        <img src="{{ $photoUrl }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" style="width:100%;height:100%;object-fit:cover">
      @else
        <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:linear-gradient(135deg,#f5f7fa,#e8ecf0)">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
          <span style="font-size:11px;color:#bbb;margin-top:8px">Nessuna foto</span>
        </div>
      @endif
      <div style="position:absolute;top:10px;left:10px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;display:flex;align-items:center;gap:4px">
        <span style="width:5px;height:5px;border-radius:50%;background:{{ $statusCfg['dot'] }}"></span>
        {{ $statusCfg['label'] }}
      </div>
      @if($photoCount > 0)
        <div style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;padding:3px 8px;border-radius:10px">
          {{ $photoCount }} foto
        </div>
      @endif
      @if($publishedCount > 0)
        <div style="position:absolute;top:10px;right:10px;background:rgba(255,107,0,.9);color:#000;font-size:10px;font-weight:700;padding:3px 8px;border-radius:10px">
          {{ $publishedCount }} live
        </div>
      @endif
    </a>

    <div style="padding:14px">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
        <div style="min-width:0">
          <div style="font-size:14px;font-weight:700;color:var(--text)">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
          @if($vehicle->version)
            <div style="font-size:11px;color:var(--text3);margin-top:1px">{{ Str::limit($vehicle->version, 35) }}</div>
          @endif
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:8px">
          <div style="font-size:16px;font-weight:800;color:var(--orange)">{{ number_format($vehicle->asking_price,0,',','.') }} &euro;</div>
          @if($vehicle->margin_percent)
            <div style="font-size:10px;color:var(--green-text)">+{{ $vehicle->margin_percent }}%</div>
          @endif
        </div>
      </div>

      <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px">
        <span style="font-size:11px;color:var(--text3);background:var(--bg3);padding:2px 7px;border-radius:4px">{{ $vehicle->year }}</span>
        <span style="font-size:11px;color:var(--text3);background:var(--bg3);padding:2px 7px;border-radius:4px">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
        <span style="font-size:11px;color:var(--text3);background:var(--bg3);padding:2px 7px;border-radius:4px">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
        @if($vehicle->plate)
          <span style="font-family:var(--mono);font-size:11px;color:var(--text);background:var(--bg4);padding:2px 7px;border-radius:4px;border:1px solid var(--border2)">{{ $vehicle->plate }}</span>
        @endif
        @if($vehicle->power_hp)
          <span style="font-size:11px;color:var(--text3);background:var(--bg3);padding:2px 7px;border-radius:4px">{{ $vehicle->power_hp }} cv</span>
        @endif
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid var(--border)">
        <div style="display:flex;gap:12px">
          @if($totalViews > 0)
            <span style="font-size:11px;color:var(--text3)">{{ number_format($totalViews,0,',','.') }} views</span>
          @endif
          @if($leadsCount > 0)
            <span style="font-size:11px;color:var(--green-text);font-weight:600">{{ $leadsCount }} lead</span>
          @endif
        </div>
        <a href="{{ route('marketplace.vehicles.show', $vehicle) }}" style="font-size:12px;font-weight:600;color:var(--orange);text-decoration:none">
          Gestisci &rsaquo;
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div style="margin-top:20px">{{ $vehicles->links() }}</div>
@endif
@endsection