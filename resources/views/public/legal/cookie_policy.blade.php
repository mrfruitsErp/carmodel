@extends('public.layout')
@php
  use App\Models\Setting;
  $nome  = Setting::get('azienda_nome', 'AleCar S.r.l.');
  $email = Setting::get('azienda_email', 'alecarto7@gmail.com');
@endphp
@section('title', Setting::get('legal_cookie_title', 'Cookie Policy - '.$nome))
@section('description', Setting::get('legal_cookie_desc', 'Informativa sull\'utilizzo dei cookie su alecar.it - '.$nome))

@section('content')
<section style="background:var(--bg2);border-bottom:1px solid var(--border);padding:48px 0 32px">
  <div class="container">
    <div class="section-label">Documento legale</div>
    <h1 class="section-title" style="font-size:28px">Cookie Policy</h1>
    <p style="color:var(--text3);font-size:13px">Ultimo aggiornamento: {{ date('d/m/Y') }} — Ai sensi del D.Lgs. 196/2003 e del Provvedimento Garante Privacy 8 maggio 2014</p>
  </div>
</section>
<section class="section-sm">
  <div class="container">
    <div class="legal-content" style="max-width:800px;font-size:14px;color:var(--text2);line-height:1.9">

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">Cosa sono i cookie</h4>
      <p>I cookie sono piccoli file di testo che i siti web visitati dall'utente inviano al suo terminale (computer, tablet, smartphone), dove vengono memorizzati per essere poi ritrasmessi agli stessi siti alla visita successiva.</p>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">Cookie utilizzati da questo sito</h4>
      <div style="overflow-x:auto;margin:12px 0">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead>
            <tr style="background:var(--bg3)">
              <th style="padding:10px 14px;text-align:left;color:var(--text3);font-weight:600;border-bottom:1px solid var(--border)">Nome</th>
              <th style="padding:10px 14px;text-align:left;color:var(--text3);font-weight:600;border-bottom:1px solid var(--border)">Tipo</th>
              <th style="padding:10px 14px;text-align:left;color:var(--text3);font-weight:600;border-bottom:1px solid var(--border)">Finalità</th>
              <th style="padding:10px 14px;text-align:left;color:var(--text3);font-weight:600;border-bottom:1px solid var(--border)">Durata</th>
            </tr>
          </thead>
          <tbody>
            @foreach([
              ['alecar-erp-session','Tecnico (sessione)','Mantiene la sessione di navigazione e i dati del form','Fine sessione browser'],
              ['XSRF-TOKEN','Tecnico (sicurezza)','Protezione da attacchi CSRF sui form','Fine sessione browser'],
              ['alecar_cookie_prefs','Tecnico (preferenze)','Memorizza le scelte sui cookie dell\'utente','12 mesi'],
              ['_ga, _gid','Analitico (opzionale)','Statistiche di navigazione anonime tramite Google Analytics — solo se accettato','2 anni / 24h'],
            ] as [$nome,$tipo,$finalita,$durata])
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:10px 14px;font-family:monospace;color:var(--orange);font-size:12px">{{ $nome }}</td>
              <td style="padding:10px 14px;color:var(--text2)">{{ $tipo }}</td>
              <td style="padding:10px 14px;color:var(--text2)">{{ $finalita }}</td>
              <td style="padding:10px 14px;color:var(--text3)">{{ $durata }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <h4 style="font-size:16px;font-weight:700;color:var(--text);margin:28px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border)">Come gestire i cookie</h4>
      <p>L'utente può gestire le proprie preferenze sui cookie in qualsiasi momento cliccando su <strong>"Gestione cookie"</strong> nel footer del sito. Inoltre, è possibile disabilitare i cookie dal browser:</p>
      <ul style="margin:12px 0 12px 20px;display:flex;flex-direction:column;gap:6px">
        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener" style="color:var(--orange)">Google Chrome</a></li>
        <li><a href="https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie" target="_blank" rel="noopener" style="color:var(--orange)">Mozilla Firefox</a></li>
        <li><a href="https://support.apple.com/it-it/guide/safari/sfri11471/mac" target="_blank" rel="noopener" style="color:var(--orange)">Apple Safari</a></li>
        <li><a href="https://support.microsoft.com/it-it/windows/eliminare-e-gestire-i-cookie-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener" style="color:var(--orange)">Microsoft Edge</a></li>
      </ul>
      <p>La disabilitazione dei cookie tecnici potrebbe compromettere il corretto funzionamento del sito.</p>

      <div style="margin-top:28px">
        <button onclick="openCookieSettings()" class="btn btn-primary btn-sm">⚙️ Gestisci le tue preferenze cookie</button>
      </div>

      <p style="margin-top:24px">Per ulteriori informazioni: <a href="mailto:{{ $email }}" style="color:var(--orange)">{{ $email }}</a> — <a href="{{ route('public.privacy') }}" style="color:var(--orange)">Privacy Policy</a></p>
    </div>
  </div>
</section>
@endsection