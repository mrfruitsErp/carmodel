@extends('public.layout')
@php
  use App\Models\Setting;
  $nome      = Setting::get('azienda_nome', 'AleCar S.r.l.');
  $indirizzo = Setting::get('azienda_indirizzo', 'Via Ignazio Collino 29, 10100 Torino (TO)');
  $piva      = Setting::get('azienda_piva', '11352180019');
  $email     = Setting::get('azienda_email', 'alecarto7@gmail.com');
  $tn_testo  = Setting::get('legal_termini_noleggio_testo', '');
@endphp
@section('title', Setting::get('legal_termini_noleggio_title', 'Termini e Condizioni di Noleggio - '.$nome))
@section('description', Setting::get('legal_termini_noleggio_desc', 'Condizioni generali di noleggio veicoli '.$nome))

@section('content')
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:48px 0 32px">
  <div class="container">
    <div class="section-label">Documento legale</div>
    <h1 class="section-title" style="font-size:28px">Termini e Condizioni di Noleggio</h1>
    <p style="color:var(--text3);font-size:13px">Ultimo aggiornamento: {{ date('d/m/Y') }}</p>
  </div>
</section>
<section class="section-sm">
  <div class="container">
    <div class="legal-content" style="max-width:800px;font-size:14px;color:var(--text2);line-height:1.9">

      <div style="background:var(--bg2);border:1px solid rgba(255,107,0,.2);border-radius:10px;padding:20px;margin-bottom:32px">
        <strong style="color:var(--orange)">AleCar S.r.l.</strong> — Via Ignazio Collino 29, 10100 Torino (TO) — P.IVA: 11352180019
      </div>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">1. Definizioni</h4>
      <p><strong>Locatore:</strong> AleCar S.r.l. — <strong>Locatario:</strong> il soggetto che noleggia il veicolo — <strong>Veicolo:</strong> il mezzo concesso in noleggio come specificato nel contratto.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">2. Prenotazione e conferma</h4>
      <p>La prenotazione effettuata tramite il sito è una <strong>richiesta di disponibilità</strong> e non costituisce contratto definitivo. Il contratto di noleggio si perfeziona solo con la <strong>conferma scritta</strong> da parte di AleCar S.r.l. e la firma del contratto in sede al momento del ritiro del veicolo.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">3. Requisiti del locatario</h4>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li>Età minima: <strong>21 anni</strong> (25 anni per veicoli di categoria superiore a C)</li>
        <li>Patente di guida valida, conseguita da almeno <strong>2 anni</strong></li>
        <li>Documento di identità in corso di validità</li>
        <li>Carta di credito o debito intestata al locatario per il deposito cauzionale</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">4. Tariffe e pagamento</h4>
      <p>Le tariffe indicate sono per giorno solare (0:00–24:00). Il pagamento avviene anticipatamente al momento del ritiro. In caso di ritardo nella restituzione superiore a 2 ore, verrà addebitata una giornata aggiuntiva.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">5. Deposito cauzionale</h4>
      <p>Al momento del ritiro verrà richiesto un deposito cauzionale proporzionale alla categoria del veicolo (da €200 a €1.000). Il deposito viene restituito entro 3 giorni lavorativi dalla restituzione del veicolo, previo accertamento dell'assenza di danni.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">6. Coperture assicurative e franchigia</h4>
      <p>Il veicolo è coperto da assicurazione RC Auto obbligatoria. Il locatario è responsabile per i danni al veicolo e a terzi non coperti dall'assicurazione, nei limiti del deposito cauzionale. Polizze aggiuntive (collisione, furto) possono essere sottoscritte al momento del ritiro.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">7. Utilizzo del veicolo</h4>
      <p>Il veicolo deve essere utilizzato esclusivamente su strade pubbliche regolarmente asfaltate, nel rispetto del Codice della Strada. Sono vietati:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li>Uso del veicolo per gare o competizioni</li>
        <li>Subnoleggio a terzi</li>
        <li>Trasporto di merci pericolose</li>
        <li>Guida sotto l'effetto di alcol o sostanze stupefacenti</li>
        <li>Utilizzo fuori dal territorio italiano (salvo autorizzazione scritta)</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">8. Restituzione del veicolo</h4>
      <p>Il veicolo deve essere restituito nelle stesse condizioni di ritiro, con lo stesso livello di carburante, nella sede di AleCar S.r.l. nella data e orario concordati. Eventuali danni verranno documentati e addebitati al locatario.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">9. Cancellazione e rimborso</h4>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Cancellazione oltre 48h prima del ritiro:</strong> rimborso integrale</li>
        <li><strong>Cancellazione tra 24h e 48h prima:</strong> rimborso del 50%</li>
        <li><strong>Cancellazione entro 24h o no-show:</strong> nessun rimborso</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">10. Foro competente</h4>
      <p>Per qualsiasi controversia è competente il <strong>Foro di Torino</strong>, salvo diversa disposizione inderogabile per i consumatori (art. 66-bis D.Lgs. 206/2005).</p>

    </div>
  </div>
</section>
@endsection