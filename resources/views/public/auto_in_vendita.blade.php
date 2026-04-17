@extends('public.layout')
@section('title', 'Auto Usate in Vendita - AleCar S.r.l. Torino')
@section('description', 'AleCar S.r.l. - Auto usate selezionate a Torino. Qualità garantita, prezzi trasparenti, IVA esposta. Via Ignazio Collino 29, Torino.')

@section('content')

{{-- HERO --}}
<section style="background:linear-gradient(135deg,var(--bg) 0%,var(--bg2) 50%,#0a0a14 100%);padding:48px 0 40px;border-bottom:1px solid var(--border)">
  <div class="container">
    {{-- Stats --}}
    <div style="display:flex;justify-content:center;gap:48px;margin-bottom:32px;flex-wrap:wrap">
      <div style="text-align:center">
        <div style="font-size:36px;font-weight:800;color:var(--orange)">{{ $vehicles->total() }}</div>
        <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.1em">Disponibili</div>
      </div>
      <div style="text-align:center">
        <div style="font-size:36px;font-weight:800;color:var(--orange)">100%</div>
        <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.1em">Garantiti</div>
      </div>
      <div style="text-align:center">
        <div style="font-size:36px;font-weight:800;color:var(--orange)">24h</div>
        <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.1em">Risposta</div>
      </div>
    </div>

    {{-- Search --}}
    <div style="max-width:860px;margin:0 auto">
      <form method="GET" action="{{ route('public.vehicles.index') }}"
            style="background:var(--bg2);border:1px solid var(--border2);border-radius:14px;padding:16px 20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;box-shadow:0 8px 32px rgba(0,0,0,.3)">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cerca marca, modello..."
               style="flex:1;min-width:200px;background:var(--bg3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;transition:.2s"
               onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='var(--border2)'">
        <select name="fuel"
                style="min-width:150px;background:var(--bg3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:10px 14px;font-size:14px;outline:none">
          <option value="">Tutti i carburanti</option>
          @foreach(['benzina','diesel','ibrido','elettrico','gpl','metano'] as $fuel)
          <option value="{{ $fuel }}" {{ request('fuel')===$fuel?'selected':'' }}>{{ ucfirst($fuel) }}</option>
          @endforeach
        </select>
        <select name="price_max"
                style="min-width:160px;background:var(--bg3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:10px 14px;font-size:14px;outline:none">
          <option value="">Qualsiasi prezzo</option>
          @foreach([10000=>'Fino a 10.000 €',20000=>'Fino a 20.000 €',30000=>'Fino a 30.000 €',50000=>'Fino a 50.000 €'] as $val=>$label)
          <option value="{{ $val }}" {{ request('price_max')==$val?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="white-space:nowrap">Cerca</button>
        @if(request()->hasAny(['search','fuel','price_max']))
          <a href="{{ route('public.vehicles.index') }}" style="font-size:13px;color:var(--text3);white-space:nowrap;text-decoration:none">✕ Reset</a>
        @endif
      </form>
    </div>
  </div>
</section>

<div class="container" style="padding-top:32px;padding-bottom:48px">

  {{-- VANTAGGI --}}
  <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:20px 24px;margin-bottom:28px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">
    @foreach([
      ['M9 12l2 2 4-4|circle cx="12" cy="12" r="10"','Veicoli controllati e garantiti'],
      ['M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z','Prezzi trasparenti, IVA esposta'],
      ['M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 012 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z','Risposta garantita in 24 ore'],
      ['rect x="1" y="3" width="15" height="13" rx="2"|path d="M16 8h4l3 3v5h-7V8z"|circle cx="5.5" cy="18.5" r="2.5"|circle cx="18.5" cy="18.5" r="2.5"','Consegna a domicilio'],
    ] as [$icon,$text])
    <div style="display:flex;align-items:center;gap:12px">
      <div style="width:38px;height:38px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="18" height="18" fill="none" stroke="var(--orange)" stroke-width="2" viewBox="0 0 24 24">
          <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
      </div>
      <span style="font-size:13px;color:var(--text2);font-weight:500">{{ $text }}</span>
    </div>
    @endforeach
  </div>

  {{-- RISULTATI --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <span style="font-size:15px;color:var(--text2)"><strong style="color:var(--text)">{{ $vehicles->total() }}</strong> veicoli trovati</span>
  </div>

  {{-- GRIGLIA VEICOLI --}}
  @if($vehicles->isEmpty())
  <div style="text-align:center;padding:60px 20px;background:var(--bg2);border:1px solid var(--border);border-radius:14px">
    <svg width="64" height="64" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:16px"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
    <h2 style="font-size:20px;color:var(--text2);margin-bottom:8px">Nessun veicolo trovato</h2>
    <p style="color:var(--text3);font-size:14px;margin-bottom:20px">Prova a modificare i filtri di ricerca</p>
    <a href="{{ route('public.vehicles.index') }}" class="btn btn-ghost btn-sm">Reset filtri</a>
  </div>
  @else
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px">
    @foreach($vehicles as $vehicle)
    @php
      $photoUrl   = $vehicle->getFirstMediaUrl('sale_photos', 'thumb');
      $photoCount = $vehicle->getMedia('sale_photos')->count();
      $publicUrl  = route('public.vehicles.show', ['id'=>$vehicle->id, 'slug'=>Str::slug($vehicle->brand.'-'.$vehicle->model)]);
    @endphp
    <div class="card">
      <a href="{{ $publicUrl }}" style="text-decoration:none;color:inherit;display:block">
        {{-- FOTO --}}
        <div style="position:relative;height:200px;overflow:hidden;background:var(--bg3)">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" loading="lazy"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .3s">
          @else
            <div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--border2);gap:8px">
              <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>
              <span style="font-size:12px">Nessuna foto</span>
            </div>
          @endif

          {{-- BADGE SINISTRO --}}
          @if($vehicle->badge_label)
            <span style="position:absolute;top:12px;left:12px;background:rgba(255,107,0,.92);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px">{{ $vehicle->badge_label }}</span>
          @endif

          {{-- BADGE DESTRO --}}
          @if($vehicle->status === 'venduto')
            <span style="position:absolute;top:12px;right:12px;background:rgba(59,130,246,.92);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px">Venduto</span>
          @elseif($vehicle->price_negotiable)
            <span style="position:absolute;top:12px;right:12px;background:rgba(255,255,255,.12);color:var(--orange);font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;border:1px solid rgba(255,107,0,.3)">Trattabile</span>
          @endif

          @if($photoCount > 0)
            <div style="position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;font-size:11px;padding:3px 8px;border-radius:8px">{{ $photoCount }} foto</div>
          @endif
        </div>

        {{-- INFO --}}
        <div style="padding:16px">
          <div style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:2px">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
          <div style="font-size:12px;color:var(--text3);margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $vehicle->version ?? '' }}</div>
          <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
            <span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500">{{ number_format($vehicle->mileage,0,',','.') }} km</span>
            <span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500">{{ ucfirst(str_replace('_',' ',$vehicle->fuel_type)) }}</span>
            @if($vehicle->transmission)<span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500">{{ ucfirst($vehicle->transmission) }}</span>@endif
            @if($vehicle->power_hp)<span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500">{{ $vehicle->power_hp }} CV</span>@endif
            @if($vehicle->color)<span style="background:var(--bg3);color:var(--text2);font-size:12px;padding:3px 9px;border-radius:6px;font-weight:500">{{ $vehicle->color }}</span>@endif
          </div>

          {{-- PREZZO --}}
          <div style="display:flex;align-items:flex-end;justify-content:space-between">
            <div>
              @if($vehicle->display_price)
                @if($vehicle->price_visible && !$vehicle->price_label && $vehicle->asking_price)
                  <div style="font-size:22px;font-weight:800;color:var(--orange)">{{ $vehicle->display_price }} €</div>
                @else
                  <div style="font-size:14px;font-weight:600;color:var(--text2);font-style:italic">{{ $vehicle->display_price }}</div>
                @endif
                @if($vehicle->vat_deductible && $vehicle->price_visible && !$vehicle->price_label)
                  <div style="font-size:11px;color:#4ade80;font-weight:600">IVA detraibile</div>
                @endif
              @endif
            </div>
            <span style="background:var(--bg3);color:var(--orange);border:1px solid rgba(255,107,0,.2);border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600">Scopri →</span>
          </div>
        </div>
      </a>
    </div>
    @endforeach
  </div>
  <div style="margin-top:24px">{{ $vehicles->links() }}</div>
  @endif

</div>

{{-- WHATSAPP BUTTON FLOATING --}}

@push('styles')
<style>
@media(max-width:600px){
  div[style*="grid-template-columns:repeat(auto-fill"]{grid-template-columns:1fr!important}
  div[style*="grid-template-columns:repeat(auto-fit"]{grid-template-columns:1fr 1fr!important}
}
</style>
@endpush
@endsection