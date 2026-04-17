@extends('public.layout')
@section('title', 'Noleggio Veicoli - AleCar Torino')
@section('description', 'Noleggio auto breve e lungo termine a Torino. Flotta veicoli sempre controllata. AleCar S.r.l.')

@section('content')

{{-- HERO --}}
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:60px 0 48px">
  <div class="container">
    <div class="section-label">Flotta disponibile</div>
    <h1 class="section-title">Noleggio veicoli</h1>
    <p class="section-sub">Breve e lungo termine. Veicoli controllati, sempre pronti. Consegna a domicilio disponibile.</p>
  </div>
</section>

{{-- VEICOLI --}}
<section class="section">
  <div class="container">
    @if($veicoli->isEmpty())
      <div style="text-align:center;padding:80px 20px;background:var(--bg2);border:1px solid var(--border);border-radius:16px">
        <div style="font-size:48px;margin-bottom:16px">🚗</div>
        <h2 style="font-size:20px;color:var(--text2);margin-bottom:8px">Flotta in aggiornamento</h2>
        <p style="color:var(--text3);margin-bottom:24px">Contattaci per conoscere la disponibilità</p>
        <a href="{{ route('public.contatti') }}" class="btn btn-primary">Contattaci</a>
      </div>
    @else
      {{-- Categorie --}}
      @php $categorie = $veicoli->groupBy('category'); @endphp
      @foreach($categorie as $cat => $vlist)
      <div style="margin-bottom:48px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
          <div style="width:36px;height:36px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--orange)">{{ $cat }}</div>
          <div>
            <div style="font-size:16px;font-weight:700">Categoria {{ $cat }}</div>
            <div style="font-size:12px;color:var(--text3)">{{ $vlist->count() }} {{ $vlist->count() === 1 ? 'veicolo' : 'veicoli' }} disponibili</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px">
          @foreach($vlist as $v)
          @php $rate = $v->daily_rate_public ?: $v->daily_rate; @endphp
          <div class="card">
            <div style="padding:24px">
              <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px">
                <div>
                  <div style="font-size:16px;font-weight:700;margin-bottom:2px">{{ $v->brand }} {{ $v->model }}</div>
                  <div style="font-size:12px;color:var(--text3)">{{ $v->year }} — {{ ucfirst($v->fuel_type ?? '') }}</div>
                </div>
                <span class="badge badge-orange">Cat. {{ $v->category }}</span>
              </div>

              {{-- Spec --}}
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px">
                <div style="background:var(--bg3);border-radius:6px;padding:8px 10px">
                  <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em">Posti</div>
                  <div style="font-size:14px;font-weight:600">{{ $v->seats }}</div>
                </div>
                <div style="background:var(--bg3);border-radius:6px;padding:8px 10px">
                  <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em">Cambio</div>
                  <div style="font-size:14px;font-weight:600">—</div>
                </div>
              </div>

              @if($v->web_description)
                <p style="font-size:13px;color:var(--text2);margin-bottom:16px;line-height:1.6">{{ $v->web_description }}</p>
              @endif

              <div style="display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--border)">
                @if($rate > 0)
                  <div><span style="font-size:22px;font-weight:800;color:var(--orange)">€{{ number_format($rate,0,',','.') }}</span><span style="font-size:12px;color:var(--text3)">/giorno</span></div>
                @else
                  <div style="font-size:13px;color:var(--text3)">Prezzo su richiesta</div>
                @endif
                <a href="{{ route('public.noleggio.show', $v->id) }}" class="btn btn-primary btn-sm">
                  {{ $v->booking_enabled ? 'Prenota' : 'Richiedi info' }}
                </a>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endforeach
    @endif

    {{-- Info noleggio --}}
    <div style="margin-top:48px;background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:40px">
      <div class="orange-line"></div>
      <h3 style="font-size:20px;font-weight:700;margin-bottom:24px">Come funziona il noleggio</h3>
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
        @foreach([
          ['1','Scegli il veicolo','Sfoglia la nostra flotta e scegli il veicolo più adatto alle tue esigenze'],
          ['2','Richiedi le date','Seleziona le date di ritiro e riconsegna tramite il calendario'],
          ['3','Conferma','Ti contatteremo entro 24h per confermare la disponibilità e i dettagli'],
          ['4','Ritira o ricevi','Ritira il veicolo in sede o richiedi la consegna a domicilio'],
        ] as [$num,$title,$desc])
        <div>
          <div style="width:36px;height:36px;background:var(--orange);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#000;margin-bottom:12px">{{ $num }}</div>
          <div style="font-size:14px;font-weight:600;margin-bottom:6px">{{ $title }}</div>
          <div style="font-size:12px;color:var(--text3);line-height:1.6">{{ $desc }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
@media(max-width:600px){
  div[style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:repeat(2,1fr)!important}
}
</style>
@endpush
@endsection