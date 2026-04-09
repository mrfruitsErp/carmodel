@extends('layouts.app')
@section('title', 'Marketplace - Dashboard')
@section('topbar-actions')
<a href="{{ route('marketplace.vehicles.create') }}" class="btn btn-primary btn-sm">+ Nuovo veicolo</a>
@endsection
@section('content')

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
  <div>
    <div class="card">
      <div class="card-title">Performance piattaforme</div>
      @if(empty($stats['listings_live']))
        <div style="text-align:center;padding:40px;color:var(--text3)">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:12px"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <div style="font-size:14px;font-weight:600;margin-bottom:6px">Nessun annuncio pubblicato</div>
          <a href="{{ route('marketplace.settings') }}" style="color:var(--orange);font-size:13px">Configura le piattaforme</a>
        </div>
      @else
        @foreach($platformStats ?? [] as $platform => $data)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)">
          <div style="font-weight:600;color:var(--text);width:100px">{{ ucfirst($platform) }}</div>
          <div style="flex:1"><div style="font-size:12px;color:var(--text3)">{{ $data['listings'] ?? 0 }} annunci</div></div>
          <div style="text-align:right"><div style="font-size:14px;font-weight:600">{{ number_format($data['views'] ?? 0) }}</div><div style="font-size:11px;color:var(--text3)">views</div></div>
          <div style="text-align:right"><div style="font-size:14px;font-weight:600;color:var(--green-text)">{{ $data['leads'] ?? 0 }}</div><div style="font-size:11px;color:var(--text3)">lead</div></div>
        </div>
        @endforeach
      @endif
    </div>

    <div class="card">
      <div class="card-title">Lead recenti</div>
      @forelse($recentLeads ?? [] as $lead)
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="flex:1">
          <div style="font-weight:500;font-size:13px">{{ $lead->name }}</div>
          <div style="font-size:11px;color:var(--text3)">{{ $lead->vehicle->brand ?? '-' }} {{ $lead->vehicle->model ?? '' }} - {{ $lead->created_at->diffForHumans() }}</div>
        </div>
        <div style="font-size:11px;color:var(--text3)">{{ $lead->phone ?? $lead->email }}</div>
      </div>
      @empty
      <div style="text-align:center;padding:30px;color:var(--text3);font-size:13px">Nessun lead ancora</div>
      @endforelse
    </div>
  </div>

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
      <a href="{{ route('marketplace.vehicles.index') }}" style="display:block;text-align:center;margin-top:14px;font-size:12px;color:var(--orange);text-decoration:none">Vedi tutti i veicoli</a>
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
@endsection