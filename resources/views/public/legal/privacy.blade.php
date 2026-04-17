@extends('public.layout')
@section('title', 'Privacy Policy - AleCar S.r.l.')
@section('description', 'Informativa sul trattamento dei dati personali ai sensi del GDPR - AleCar S.r.l.')

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
        AleCar S.r.l. — Via Ignazio Collino 29, 10100 Torino (TO)<br>
        P.IVA: 11352180019 — Email: <a href="mailto:alecarto7@gmail.com" style="color:var(--orange)">alecarto7@gmail.com</a> — PEC: <a href="mailto:alecar@legalmail.it" style="color:var(--orange)">alecar@legalmail.it</a>
      </div>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">1. Finalità e base giuridica del trattamento</h4>
      <p>AleCar S.r.l. tratta i dati personali degli utenti che visitano il sito <strong>alecar.it</strong> esclusivamente per le seguenti finalità:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Risposta a richieste di informazioni</strong> (base giuridica: esecuzione di misure precontrattuali — art. 6.1.b GDPR)</li>
        <li><strong>Gestione di richieste di prenotazione noleggio</strong> (base giuridica: esecuzione contrattuale — art. 6.1.b GDPR)</li>
        <li><strong>Adempimento di obblighi legali</strong> (base giuridica: art. 6.1.c GDPR)</li>
        <li><strong>Cookie analitici</strong> solo previo consenso esplicito (base giuridica: consenso — art. 6.1.a GDPR)</li>
      </ul>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">2. Dati raccolti</h4>
      <p>Il sito raccoglie i seguenti dati personali:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Dati di navigazione:</strong> indirizzo IP, tipo di browser, pagine visitate (automaticamente dal server)</li>
        <li><strong>Dati forniti volontariamente:</strong> nome, cognome, email, numero di telefono, messaggi inseriti nei form di contatto e prenotazione</li>
        <li><strong>Dati di prenotazione:</strong> date di noleggio richieste, veicolo selezionato</li>
      </ul>
      <p>Non vengono raccolti dati sensibili (categorie particolari ai sensi dell'art. 9 GDPR).</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">3. Modalità e periodo di conservazione</h4>
      <p>I dati sono trattati con strumenti informatici e conservati su server sicuri ubicati nell'Unione Europea. I dati delle richieste di contatto e prenotazione sono conservati per un massimo di <strong>24 mesi</strong> dalla ricezione, salvo diversi obblighi di legge (es. obblighi fiscali: 10 anni).</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">4. Comunicazione e trasferimento dei dati</h4>
      <p>I dati non vengono ceduti a terzi per finalità commerciali. Possono essere comunicati a:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li>Fornitori di servizi IT che operano come responsabili del trattamento (hosting, email)</li>
        <li>Autorità competenti per adempimenti di legge</li>
      </ul>
      <p>Non viene effettuato alcun trasferimento di dati verso Paesi extra-UE.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">5. Diritti dell'interessato</h4>
      <p>Ai sensi degli artt. 15-22 GDPR, l'utente ha diritto di:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><strong>Accesso</strong> ai propri dati personali</li>
        <li><strong>Rettifica</strong> dei dati inesatti</li>
        <li><strong>Cancellazione</strong> (diritto all'oblio)</li>
        <li><strong>Limitazione</strong> del trattamento</li>
        <li><strong>Portabilità</strong> dei dati</li>
        <li><strong>Opposizione</strong> al trattamento</li>
        <li><strong>Revoca del consenso</strong> in qualsiasi momento, senza pregiudizio della liceità del trattamento precedente</li>
      </ul>
      <p>Per esercitare i propri diritti o per qualsiasi informazione, l'utente può scrivere a: <a href="mailto:alecarto7@gmail.com" style="color:var(--orange)">alecarto7@gmail.com</a></p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">6. Reclamo all'Autorità di controllo</h4>
      <p>L'utente ha il diritto di proporre reclamo al <strong>Garante per la protezione dei dati personali</strong> (www.garanteprivacy.it) se ritiene che il trattamento dei propri dati personali violi il GDPR.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">7. Cookie</h4>
      <p>Per informazioni dettagliate sull'utilizzo dei cookie, si rinvia alla <a href="{{ route('public.cookie_policy') }}" style="color:var(--orange)">Cookie Policy</a>.</p>

      <div style="margin-top:32px;padding:16px;background:var(--bg3);border-radius:8px;font-size:12px;color:var(--text3)">
        Il presente documento può essere aggiornato. La data di ultimo aggiornamento è indicata in cima alla pagina.
      </div>
    </div>
  </div>
</section>
@endsection