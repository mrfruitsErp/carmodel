@extends('layouts.app')
@section('title', 'Lead — Marketplace')

@section('topbar-actions')
<form action="{{ route('marketplace.sync.leads') }}" method="POST">
  @csrf
  <button class="btn btn-ghost btn-sm">↻ Aggiorna lead</button>
</form>
@endsection

@section('content')

{{-- Filtri --}}
<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
  <select name="status" onchange="this.form.submit()" style="background:var(--bg2);border:1px solid var(--border2);border-radius:6px;padding:7px 12px;font-size:13px;color:var(--text);outline:none;cursor:pointer">
    <option value="">Tutti gli stati</option>
    @foreach(['nuovo','contattato','appuntamento','trattativa','vinto','perso'] as $s)
      <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <select name="platform" onchange="this.form.submit()" style="background:var(--bg2);border:1px solid var(--border2);border-radius:6px;padding:7px 12px;font-size:13px;color:var(--text);outline:none;cursor:pointer">
    <option value="">Tutte le piattaforme</option>
    @foreach(['autoscout24','automobile_it','ebay_motors','subito_it','facebook_marketplace','manual'] as $p)
      <option value="{{ $p }}" {{ request('platform')===$p?'selected':'' }}>{{ ucwords(str_replace('_',' ',$p)) }}</option>
    @endforeach
  </select>
  @if(request()->hasAny(['status','platform']))
    <a href="{{ route('marketplace.leads.index') }}" class="btn btn-ghost btn-sm">✕ Reset</a>
  @endif
  <div style="margin-left:auto;font-size:12px;color:var(--text3)">{{ $leads->total() }} lead</div>
</form>

@if($leads->isEmpty())
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:12px;padding:60px;text-align:center">
    <div style="font-size:48px;margin-bottom:16px">📭</div>
    <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px">Nessun lead trovato</div>
    <div style="font-size:13px;color:var(--text3)">I lead arriveranno quando pubblicherai annunci sulle piattaforme</div>
  </div>
@else
<div class="card" style="padding:0;overflow:hidden">
  <table>
    <thead>
      <tr>
        <th>Contatto</th>
        <th>Veicolo</th>
        <th>Piattaforma</th>
        <th>Stato</th>
        <th>Ricevuto</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($leads as $lead)
      @php
        $statusColor = match($lead->status) {
          'nuovo'        => ['bg'=>'var(--green-bg)',  'text'=>'var(--green-text)'],
          'contattato'   => ['bg'=>'var(--blue-bg)',   'text'=>'var(--blue-text)'],
          'appuntamento' => ['bg'=>'var(--purple-bg)', 'text'=>'var(--purple-text)'],
          'trattativa'   => ['bg'=>'var(--amber-bg)',  'text'=>'var(--amber-text)'],
          'vinto'        => ['bg'=>'var(--green-bg)',  'text'=>'var(--green-text)'],
          default        => ['bg'=>'var(--bg4)',       'text'=>'var(--text3)'],
        };
      @endphp
      <tr>
        <td>
          <div style="font-weight:600;color:var(--text)">{{ $lead->lead_name ?? 'Anonimo' }}</div>
          <div style="font-size:11px;color:var(--text3);margin-top:2px;display:flex;gap:8px;flex-wrap:wrap">
            @if($lead->lead_email)<a href="mailto:{{ $lead->lead_email }}" style="color:var(--blue-text)">{{ $lead->lead_email }}</a>@endif
            @if($lead->lead_phone)<a href="tel:{{ $lead->lead_phone }}" style="color:var(--blue-text)">{{ $lead->lead_phone }}</a>@endif
          </div>
          @if($lead->lead_message)
            <div style="font-size:11px;color:var(--text3);margin-top:3px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $lead->lead_message }}</div>
          @endif
        </td>
        <td>
          @if($lead->saleVehicle)
            <a href="{{ route('marketplace.vehicles.show', $lead->sale_vehicle_id) }}" style="font-size:13px;color:var(--text);text-decoration:none;font-weight:500">{{ $lead->saleVehicle->full_name }}</a>
          @else
            <span style="color:var(--text3)">—</span>
          @endif
        </td>
        <td>@include('marketplace.partials._platform_badge', ['platform' => $lead->platform])</td>
        <td>
          <form action="{{ route('marketplace.leads.update', $lead) }}" method="POST">
            @csrf @method('PATCH')
            <select name="status" onchange="this.form.submit()" style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};border:none;border-radius:6px;padding:4px 8px;font-size:11px;font-weight:600;cursor:pointer;outline:none">
              @foreach(['nuovo','contattato','appuntamento','trattativa','vinto','perso'] as $s)
                <option value="{{ $s }}" {{ $lead->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
          </form>
        </td>
        <td style="white-space:nowrap">
          <div style="font-size:12px;color:var(--text)">{{ $lead->created_at->format('d/m/Y') }}</div>
          <div style="font-size:10px;color:var(--text3)">{{ $lead->created_at->format('H:i') }}</div>
        </td>
        <td style="text-align:right">
          <div style="display:flex;gap:8px;justify-content:flex-end">
            @if($lead->lead_email)
              <a href="mailto:{{ $lead->lead_email }}" class="btn btn-ghost btn-sm">✉ Scrivi</a>
            @endif
            @if($lead->lead_phone)
              <a href="tel:{{ $lead->lead_phone }}" class="btn btn-primary btn-sm">📞 Chiama</a>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div style="margin-top:16px">{{ $leads->links() }}</div>
@endif

@endsection