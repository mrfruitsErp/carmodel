@extends('public.layout')
@section('title', 'Contatti - AleCar S.r.l. Torino')
@section('description', 'Contatta AleCar S.r.l. a Torino. Telefono, email e form di contatto.')

@section('content')

<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:60px 0 48px">
  <div class="container">
    <div class="section-label">Siamo qui per te</div>
    <h1 class="section-title">Contattaci</h1>
    <p class="section-sub">Hai domande su un veicolo, vuoi un preventivo noleggio o hai bisogno di assistenza? Scrivici o chiamaci.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:40px;align-items:start">

      {{-- Info contatto --}}
      <div>
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:28px;margin-bottom:20px">
          <div class="orange-line"></div>
          <h3 style="font-size:18px;font-weight:700;margin-bottom:20px">Informazioni</h3>
          <div style="display:flex;flex-direction:column;gap:16px">
            @foreach([
              ['📞','Telefono','+39 327 807 2650','tel:+393278072650'],
              ['✉️','Email','alecarto7@gmail.com','mailto:alecarto7@gmail.com'],
              ['🏛️','PEC','alecar@legalmail.it','mailto:alecar@legalmail.it'],
              ['📍','Indirizzo','Via Ignazio Collino 29, 10100 Torino (TO)',null],
            ] as [$icon,$label,$val,$href])
            <div style="display:flex;gap:14px;align-items:flex-start">
              <div style="width:40px;height:40px;background:var(--orange-bg);border:1px solid rgba(255,107,0,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $icon }}</div>
              <div>
                <div style="font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px">{{ $label }}</div>
                @if($href)
                  <a href="{{ $href }}" style="font-size:14px;color:var(--text);text-decoration:none;font-weight:500">{{ $val }}</a>
                @else
                  <div style="font-size:14px;color:var(--text);font-weight:500">{{ $val }}</div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:28px">
          <div class="orange-line"></div>
          <h3 style="font-size:18px;font-weight:700;margin-bottom:16px">Orari</h3>
          @foreach([
            ['Lunedì – Venerdì', '09:00 – 18:00', true],
            ['Sabato', '09:00 – 13:00', true],
            ['Domenica', 'Chiuso', false],
          ] as [$day,$time,$open])
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px">
            <span style="color:var(--text2)">{{ $day }}</span>
            <span style="font-weight:600;color:{{ $open ? 'var(--orange)' : 'var(--text3)' }}">{{ $time }}</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Form --}}
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:32px">
        <div class="orange-line"></div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:24px">Invia un messaggio</h3>

        @if(session('contact_success'))
          <div class="alert-success">{{ session('contact_success') }}</div>
        @endif

        @if($errors->any())
          <div class="alert-error">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('public.contatti.send') }}" autocomplete="off">
          @csrf
          {{-- Anti-spam: honeypot (nascosto agli umani, visibile ai bot) --}}
          <div style="position:absolute;left:-9999px;top:-9999px;visibility:hidden" aria-hidden="true">
            <label>Sito web (lascia vuoto)</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
            <input type="text" name="url" tabindex="-1" autocomplete="off" value="">
          </div>
          {{-- Anti-spam: timestamp form load (i bot inviano in <2s) --}}
          <input type="hidden" name="_form_ts" value="{{ time() }}">

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
              <label class="form-label">Nome e Cognome *</label>
              <input type="text" name="name" class="form-input" required value="{{ old('name') }}" placeholder="Mario Rossi">
            </div>
            <div class="form-group">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-input" required value="{{ old('email') }}" placeholder="mario@email.it">
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="form-group">
              <label class="form-label">Telefono</label>
              <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+39 333 1234567">
            </div>
            <div class="form-group">
              <label class="form-label">Oggetto</label>
              <select name="subject" class="form-select">
                <option value="">Seleziona...</option>
                <option value="Informazioni auto in vendita" {{ old('subject') == 'Informazioni auto in vendita' ? 'selected' : '' }}>Informazioni auto in vendita</option>
                <option value="Preventivo noleggio" {{ old('subject') == 'Preventivo noleggio' ? 'selected' : '' }}>Preventivo noleggio</option>
                <option value="Perizia / valutazione" {{ old('subject') == 'Perizia / valutazione' ? 'selected' : '' }}>Perizia / valutazione</option>
                <option value="Altro" {{ old('subject') == 'Altro' ? 'selected' : '' }}>Altro</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Messaggio *</label>
            <textarea name="message" class="form-textarea" required rows="5" placeholder="Scrivi il tuo messaggio...">{{ old('message') }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-check" style="display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--text2);cursor:pointer;line-height:1.5">
              <input type="checkbox" name="gdpr_consent" value="1" {{ old('gdpr_consent') ? 'checked' : '' }} required style="margin-top:3px;flex-shrink:0;width:16px;height:16px;accent-color:var(--orange)">
              <span>Ho letto e accetto il trattamento dei dati ai sensi della <a href="{{ route('public.privacy') }}" target="_blank" style="color:var(--orange)">Privacy Policy</a> *</span>
            </label>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:15px;padding:13px">
            ✉️ Invia messaggio
          </button>
          <p style="font-size:11px;color:var(--text3);margin-top:10px;text-align:center">
            Risposta garantita entro 24 ore lavorative. I tuoi dati sono trattati nel rispetto del GDPR.
          </p>
        </form>
      </div>

    </div>
  </div>
</section>

@push('styles')
<style>
@media(max-width:768px){
  div[style*="grid-template-columns:1fr 1.5fr"]{grid-template-columns:1fr!important}
  div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>
@endpush
@endsection