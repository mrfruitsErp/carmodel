@extends('public.layout')
@section('title', 'Termini e Condizioni di Vendita - AleCar S.r.l.')
@section('description', 'Condizioni generali di vendita veicoli usati AleCar S.r.l. Torino.')

@section('content')
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:48px 0 32px">
  <div class="container">
    <div class="section-label">Documento legale</div>
    <h1 class="section-title" style="font-size:28px">Termini e Condizioni di Vendita</h1>
    <p style="color:var(--text3);font-size:13px">Ultimo aggiornamento: {{ date('d/m/Y') }}</p>
  </div>
</section>
<section class="section-sm">
  <div class="container">
    <div class="legal-content" style="max-width:800px;font-size:14px;color:var(--text2);line-height:1.9">

      <div style="background:var(--bg2);border:1px solid rgba(255,107,0,.2);border-radius:10px;padding:20px;margin-bottom:32px">
        <strong style="color:var(--orange)">AleCar S.r.l.</strong> — Via Ignazio Collino 29, 10100 Torino (TO) — P.IVA: 11352180019
      </div>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">1. Ambito di applicazione</h4>
      <p>Le presenti condizioni generali di vendita si applicano a tutte le vendite di veicoli usati effettuate da AleCar S.r.l. nei confronti di consumatori (art. 3 D.Lgs. 206/2005) e operatori professionali.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">2. Prezzi e IVA</h4>
      <p>Tutti i prezzi indicati sul sito e negli annunci sono comprensivi di IVA, salvo diversa indicazione esplicita. AleCar S.r.l. si riserva il diritto di modificare i prezzi in qualsiasi momento e senza preavviso. Il prezzo vincolante è quello concordato al momento della sottoscrizione del contratto di vendita.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">3. Disponibilità dei veicoli</h4>
      <p>I veicoli presenti sul sito sono soggetti a disponibilità. AleCar S.r.l. non è responsabile per vendite avvenute nel periodo intercorrente tra la visualizzazione online e la conferma definitiva dell'acquisto. La vendita si intende perfezionata solo con la firma del contratto e il versamento della caparra.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">4. Stato e condizioni del veicolo</h4>
      <p>I veicoli usati vengono venduti nello stato in cui si trovano al momento della consegna, come descritto nell'annuncio e verificato in sede. Il compratore ha il diritto di ispezionare il veicolo prima dell'acquisto. La firma del contratto di vendita implica l'accettazione delle condizioni del veicolo.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">5. Garanzia</h4>
      <p>I veicoli usati venduti a consumatori sono soggetti alla garanzia legale di conformità prevista dal D.Lgs. 206/2005, come modificato dal D.Lgs. 170/2021, con durata minima di <strong>12 mesi</strong> dalla consegna, salvo accordo scritto per riduzione a 6 mesi. La garanzia copre i vizi di conformità esistenti al momento della consegna. Sono esclusi dalla garanzia i danni derivanti da uso improprio, incidenti, mancata manutenzione ordinaria o modifiche non autorizzate.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">6. Caparra e pagamento</h4>
      <p>Al momento della stipula del contratto preliminare è richiesto il versamento di una caparra confirmatoria. Le modalità di pagamento accettate (bonifico bancario, assegno circolare, contanti nei limiti di legge) sono concordate al momento della compravendita. In caso di recesso del compratore dopo la stipula, la caparra sarà trattenuta da AleCar S.r.l. a titolo di risarcimento.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">7. Consegna e passaggio di proprietà</h4>
      <p>Il passaggio di proprietà avviene al momento della consegna del veicolo e del saldo integrale del prezzo. AleCar S.r.l. provvede, a titolo di servizio, alla gestione delle pratiche di trasferimento di proprietà presso il PRA, salvo diverso accordo.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">8. Diritto di recesso</h4>
      <p>Per i contratti stipulati in sede (non a distanza né fuori dai locali commerciali), il diritto di recesso previsto dal Codice del Consumo non si applica. Per eventuali contratti conclusi a distanza, si applicano gli artt. 52 e ss. del D.Lgs. 206/2005.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">9. Foro competente</h4>
      <p>Per qualsiasi controversia relativa all'interpretazione, esecuzione o risoluzione del contratto di vendita, è competente il <strong>Foro di Torino</strong>. Per i consumatori si applica l'art. 66-bis del D.Lgs. 206/2005.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">10. Risoluzione alternativa delle controversie</h4>
      <p>Per controversie con consumatori, è possibile ricorrere alla piattaforma europea ODR: <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener" style="color:var(--orange)">https://ec.europa.eu/consumers/odr</a></p>

    </div>
  </div>
</section>
@endsection