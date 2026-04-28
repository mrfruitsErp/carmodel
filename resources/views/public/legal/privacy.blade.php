@extends('public.layout')
@php
  use App\Models\Setting;
  $nome      = Setting::get('azienda_nome', 'AleCar S.r.l.');
  $indirizzo = Setting::get('azienda_indirizzo', 'Via Ignazio Collino 29, 10100 Torino (TO)');
  $piva      = Setting::get('azienda_piva', '11352180019');
  $email     = Setting::get('azienda_email', 'alecarto7@gmail.com');
  $pec       = Setting::get('azienda_pec', 'alecar@legalmail.it');
  $sito      = Setting::get('seo_site_title', 'alecar.it');
  $privacy_testo = Setting::get('legal_privacy_testo', '');
@endphp
@section('title', Setting::get('legal_privacy_title', 'Privacy Policy - '.$nome))
@section('description', Setting::get('legal_privacy_desc', 'Informativa sul trattamento dei dati personali ai sensi del GDPR - '.$nome))

@section('content')
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:48px 0 32px">
  <div class="container">
    <div class="section-label">Documento legale</div>
    <h1 class="section-title" style="font-size:28px">Privacy Policy</h1>
    <p style="color:var(--text3);font-size:13px">Ultimo aggiornamento: {{ date('d/m/Y') }} — Ai sensi del Regolamento (UE) 2016/679 (GDPR)</p>
  </div>
</section>

<section class="section-sm">
  <div class="container">
    <div class="legal-content" style="max-width:800px;font-size:14px;color:var(--text2);line-height:1.9">

      <div style="background:var(--bg2);border:1px solid rgba(255,107,0,.2);border-radius:10px;padding:20px;margin-bottom:32px">
        <strong style="color:var(--orange)">Titolare del trattamento:</strong><br>
        {{ $nome }} — {{ $indirizzo }}<br>
        P.IVA: {{ $piva }}
        @if($email) — Email: <a href="mailto:{{ $email }}" style="color:var(--orange)">{{ $email }}</a>@endif
        @if($pec) — PEC: <a href="mailto:{{ $pec }}" style="color:var(--orange)">{{ $pec }}</a>@endif
      </div>

      @if($privacy_testo)
        {!! nl2br(e($privacy_testo)) !!}
      @else
      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">1. Finalità e base giuridica del trattamento</h4>
      <p>{{ $nome }} tratta i dati personali degli utenti che visitano il sito <strong>alecar.it</strong> esclusivamente per le seguenti finalità:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Risposta a richieste di informazioni</strong> (base giuridica: esecuzione di misure precontrattuali — art. 6.1.b GDPR)</li>
        <li><strong>Gestione di richieste di prenotazione noleggio</strong> (base giuridica: esecuzione contrattuale — art. 6.1.b GDPR)</li>
        <li><strong>Adempimento di obblighi legali</strong> (base giuridica: art. 6.1.c GDPR)</li>
        <li><strong>Cookie analitici</strong> solo previo consenso esplicito (base giuridica: consenso — art. 6.1.a GDPR)</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">2. Dati raccolti</h4>
      <p>Il sito raccoglie i seguenti dati personali:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Dati di navigazione:</strong> indirizzo IP, tipo di browser, pagine visitate</li>
        <li><strong>Dati forniti volontariamente:</strong> nome, cognome, email, numero di telefono, messaggi inseriti nei form</li>
        <li><strong>Dati di prenotazione:</strong> date di noleggio richieste, veicolo selezionato</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">3. Conservazione</h4>
      <p>I dati sono conservati su server sicuri nell'Unione Europea per un massimo di <strong>24 mesi</strong> dalla ricezione, salvo obblighi di legge.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">4. Diritti dell'interessato</h4>
      <p>Ai sensi degli artt. 15-22 GDPR, l'utente ha diritto di accesso, rettifica, cancellazione, limitazione, portabilità e opposizione al trattamento. Per esercitarli: <a href="mailto:{{ $email }}" style="color:var(--orange)">{{ $email }}</a></p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">5. Cookie</h4>
      <p>Per informazioni sull'utilizzo dei cookie, si rinvia alla <a href="{{ route('public.cookie_policy') }}" style="color:var(--orange)">Cookie Policy</a>.</p>

      <div style="margin-top:32px;padding:16px;background:var(--bg3);border-radius:8px;font-size:12px;color:var(--text3)">
        Il presente documento può essere aggiornato. La data di ultimo aggiornamento è indicata in cima alla pagina.
      </div>
      @endif
    </div>
  </div>
</section>
@endsection
