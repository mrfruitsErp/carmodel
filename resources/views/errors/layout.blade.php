{{--
  Layout standalone per pagine errore.
  Funziona sia per utenti loggati che non — non richiede sessione né middleware.
--}}
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Errore' }} — AleCar</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0f0f1a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 20% 50%,rgba(255,107,0,.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(59,130,246,.06) 0%,transparent 60%);color:#fff}
.error-box{width:100%;max-width:520px;text-align:center}
.logo{margin-bottom:32px}
.logo img{max-width:160px;width:auto;height:auto}
.error-code{font-family:'Rajdhani',sans-serif;font-size:96px;font-weight:700;line-height:1;background:linear-gradient(135deg,#ff6b00 0%,#ffa64d 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:8px}
.error-icon{font-size:64px;margin-bottom:16px}
.error-title{font-family:'Rajdhani',sans-serif;font-size:24px;font-weight:700;color:#fff;letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px}
.error-msg{font-size:14px;color:rgba(255,255,255,.6);line-height:1.7;margin-bottom:8px;padding:0 16px}
.error-hint{font-size:12px;color:rgba(255,255,255,.35);line-height:1.7;margin-bottom:32px;padding:0 16px}
.error-detail{display:inline-block;background:rgba(255,107,0,.08);border:1px solid rgba(255,107,0,.25);color:#ff9d4d;font-size:11px;font-family:'Courier New',monospace;padding:6px 14px;border-radius:6px;margin:14px 0 28px;letter-spacing:.04em}
.actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;border:1px solid transparent;cursor:pointer;font-family:'DM Sans',sans-serif}
.btn-primary{background:#ff6b00;color:#000;box-shadow:0 0 20px rgba(255,107,0,.3)}
.btn-primary:hover{background:#e55f00;box-shadow:0 0 30px rgba(255,107,0,.5);transform:translateY(-1px)}
.btn-ghost{background:rgba(255,255,255,.04);color:rgba(255,255,255,.7);border-color:rgba(255,255,255,.1)}
.btn-ghost:hover{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,107,0,.4)}
.footer-text{margin-top:48px;font-size:11px;color:rgba(255,255,255,.2)}
</style>
</head>
<body>
<div class="error-box">
  <div class="logo">
    <img src="{{ asset('images/logo-alecar-compact.png') }}" alt="AleCar">
  </div>
  @yield('content')
  <div class="footer-text">&copy; {{ date('Y') }} AleCar S.r.l.</div>
</div>
</body>
</html>
