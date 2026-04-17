@extends('public.layout')
@section('title', $veicolo->brand.' '.$veicolo->model.' - Noleggio AleCar Torino')
@section('description', 'Noleggia '.$veicolo->brand.' '.$veicolo->model.' a Torino con AleCar S.r.l.')

@section('content')

<section class="section">
  <div class="container">

    <div style="margin-bottom:20px">
      <a href="{{ route('public.noleggio') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Torna alla flotta</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start">

      {{-- SINISTRA --}}
      <div>
        {{-- Header veicolo --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:28px;margin-bottom:20px">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px">
            <div>
              <h1 style="font-size:26px;font-weight:800;margin-bottom:4px">{{ $veicolo->brand }} {{ $veicolo->model }}</h1>
              <div style="color:var(--text3);font-size:14px">{{ $veicolo->year }} — Categoria {{ $veicolo->category }}</div>
            </div>
            @php $rate = $veicolo->daily_rate_public ?: $veicolo->daily_rate; @endphp
            @if($rate > 0)
              <div style="text-align:right">
                <div style="font-size:32px;font-weight:800;color:var(--orange)">€{{ number_format($rate,0,',','.') }}</div>
                <div style="font-size:12px;color:var(--text3)">al giorno</div>
              </div>
            @endif
          </div>

          {{-- Specifiche --}}
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
            @foreach([
              ['Posti', $veicolo->seats],
              ['Carburante', ucfirst($veicolo->fuel_type ?? '—')],
              ['Colore', $veicolo->color ?: '—'],
              ['Categoria', 'Cat. '.$veicolo->category],
            ] as [$label,$val])
            <div style="background:var(--bg3);border-radius:8px;padding:12px">
              <div style="font-size:10px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">{{ $label }}</div>
              <div style="font-size:14px;font-weight:600">{{ $val }}</div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Descrizione --}}
        @if($veicolo->web_description)
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:28px;margin-bottom:20px">
          <div class="orange-line"></div>
          <h3 style="font-size:16px;font-weight:700;margin-bottom:12px">Descrizione</h3>
          <p style="font-size:14px;color:var(--text2);line-height:1.8">{{ $veicolo->web_description }}</p>
        </div>
        @endif

        {{-- Info noleggio --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:28px">
          <div class="orange-line"></div>
          <h3 style="font-size:16px;font-weight:700;margin-bottom:16px">Informazioni noleggio</h3>
          <div style="display:flex;flex-direction:column;gap:10px">
            @foreach([
              ['✅','Veicolo controllato e revisionato'],
              ['✅','Assicurazione RC inclusa'],
              ['📍','Ritiro in sede: Via Ignazio Collino 29, Torino'],
              ['🚗','Consegna a domicilio disponibile (chiedi info)'],
              ['📞','Supporto telefonico durante il noleggio'],
            ] as [$icon,$text])
            <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text2)">
              <span>{{ $icon }}</span><span>{{ $text }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- DESTRA: FORM PRENOTAZIONE --}}
      <div style="position:sticky;top:80px">
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
          <div style="background:var(--orange);padding:16px 20px">
            <div style="font-size:14px;font-weight:700;color:#000">
              {{ $veicolo->booking_enabled ? '📅 Prenota questo veicolo' : '📩 Richiedi informazioni' }}
            </div>
            <div style="font-size:12px;color:rgba(0,0,0,.7)">Risposta garantita entro 24 ore</div>
          </div>
          <div style="padding:20px">

            @if(session('booking_success'))
              <div class="alert-success">{{ session('booking_success') }}</div>
            @endif

            @if($errors->any())
              <div class="alert-error">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
              </div>
            @endif

            <form method="POST" action="{{ route('public.noleggio.booking', $veicolo->id) }}">
              @csrf
              <div class="form-group">
                <label class="form-label">Nome e Cognome *</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name') }}" placeholder="Mario Rossi">
              </div>
              <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input" required value="{{ old('email') }}" placeholder="mario@email.it">
              </div>
              <div class="form-group">
                <label class="form-label">Telefono</label>
                <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+39 333 1234567">
              </div>

              @if($veicolo->booking_enabled)
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                  <label class="form-label">Data inizio *</label>
                  <input type="date" name="date_start" class="form-input" required value="{{ old('date_start') }}" min="{{ date('Y-m-d') }}" id="date_start">
                </div>
                <div class="form-group">
                  <label class="form-label">Data fine *</label>
                  <input type="date" name="date_end" class="form-input" required value="{{ old('date_end') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" id="date_end">
                </div>
              </div>

              {{-- Preventivo dinamico --}}
              <div id="price-preview" style="display:none;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px">
                <div style="display:flex;justify-content:space-between">
                  <span style="color:var(--text2)">Durata stimata:</span>
                  <span id="days-count" style="font-weight:600">—</span>
                </div>
                @if($rate > 0)
                <div style="display:flex;justify-content:space-between;margin-top:6px">
                  <span style="color:var(--text2)">Totale indicativo:</span>
                  <span id="total-price" style="font-weight:700;color:var(--orange)">—</span>
                </div>
                @endif
              </div>

              {{-- Calendario date occupate --}}
              @if($dateOccupate->count())
              <div style="background:var(--bg3);border-radius:8px;padding:12px;margin-bottom:16px;font-size:12px;color:var(--text3)">
                ⚠️ Alcune date potrebbero non essere disponibili. Ti confermeremo la disponibilità entro 24h.
              </div>
              @endif
              @endif

              <div class="form-group">
                <label class="form-label">Messaggio</label>
                <textarea name="message" class="form-textarea" rows="3" placeholder="Eventuali richieste particolari...">{{ old('message') }}</textarea>
              </div>

              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
                {{ $veicolo->booking_enabled ? '📅 Invia richiesta di prenotazione' : '📩 Invia richiesta info' }}
              </button>
              <p style="font-size:11px;color:var(--text3);margin-top:10px;text-align:center">Prenotazione soggetta a conferma. Ti contatteremo entro 24 ore.</p>
            </form>
          </div>
        </div>

        {{-- Contatto diretto --}}
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:20px;margin-top:16px;text-align:center">
          <div style="font-size:13px;color:var(--text3);margin-bottom:10px">Preferisci chiamare?</div>
          <a href="tel:+393278072650" class="btn btn-ghost" style="width:100%;justify-content:center">📞 +39 327 807 2650</a>
        </div>
      </div>

    </div>
  </div>
</section>

@push('scripts')
<script>
const dailyRate = {{ $rate ?? 0 }};
const dateStart = document.getElementById('date_start');
const dateEnd   = document.getElementById('date_end');

function updatePreview() {
  if (!dateStart || !dateEnd || !dateStart.value || !dateEnd.value) {
    document.getElementById('price-preview').style.display = 'none';
    return;
  }
  const s = new Date(dateStart.value);
  const e = new Date(dateEnd.value);
  if (e <= s) return;
  const days = Math.round((e - s) / (1000 * 60 * 60 * 24));
  document.getElementById('price-preview').style.display = 'block';
  document.getElementById('days-count').textContent = days + (days === 1 ? ' giorno' : ' giorni');
  if (dailyRate > 0) {
    const total = (days * dailyRate).toLocaleString('it-IT', {minimumFractionDigits:0});
    document.getElementById('total-price').textContent = '€ ' + total;
  }
  // Aggiorna min date_end
  const minEnd = new Date(s);
  minEnd.setDate(minEnd.getDate() + 1);
  dateEnd.min = minEnd.toISOString().split('T')[0];
}

dateStart?.addEventListener('change', updatePreview);
dateEnd?.addEventListener('change', updatePreview);
</script>
@endpush
@endsection