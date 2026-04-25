<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recupera password — AleCar</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0f0f1a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 20% 50%,rgba(255,107,0,.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(59,130,246,.06) 0%,transparent 60%)}
.login-box{width:100%;max-width:420px}
.logo{text-align:center;margin-bottom:32px}
.logo img{max-width:180px;width:auto;height:auto}
.logo-sub{font-size:12px;color:rgba(255,255,255,.3);letter-spacing:.15em;text-transform:uppercase;display:block;margin-top:6px}
.card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;backdrop-filter:blur(10px)}
.card h2{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px;text-align:center}
.intro{font-size:13px;color:rgba(255,255,255,.5);text-align:center;line-height:1.6;margin-bottom:24px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:11px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px}
.form-input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:11px 14px;color:#fff;font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:all .15s}
.form-input:focus{border-color:#ff6b00;background:rgba(255,107,0,.05);box-shadow:0 0 0 3px rgba(255,107,0,.1)}
.form-input::placeholder{color:rgba(255,255,255,.2)}
.btn-primary{width:100%;background:#ff6b00;color:#000;border:none;border-radius:8px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.03em;transition:all .2s;box-shadow:0 0 20px rgba(255,107,0,.3)}
.btn-primary:hover{background:#e55f00;box-shadow:0 0 30px rgba(255,107,0,.5);transform:translateY(-1px)}
.alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:10px 14px;color:#fca5a5;font-size:13px;margin-bottom:16px}
.alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:10px 14px;color:#86efac;font-size:13px;margin-bottom:16px}
.bottom-link{text-align:center;margin-top:18px;font-size:13px}
.bottom-link a{color:rgba(255,107,0,.7);text-decoration:none;transition:color .15s}
.bottom-link a:hover{color:#ff6b00}
.footer-text{text-align:center;font-size:11px;color:rgba(255,255,255,.2);margin-top:20px}
</style>
</head>
<body>
<div class="login-box">
  <div class="logo">
    <img src="{{ asset('images/logo-alecar-compact.png') }}" alt="AleCar">
    <span class="logo-sub">Gestionale Automotive</span>
  </div>

  <div class="card">
    <h2>Recupera password</h2>
    <p class="intro">Inserisci la tua email e ti invieremo il link per impostare una nuova password.</p>

    @if (session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert-error">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" required autofocus autocomplete="username" placeholder="nome@alecar.it">
      </div>

      <button type="submit" class="btn-primary">Invia link di recupero &rarr;</button>
    </form>

    <div class="bottom-link">
      <a href="{{ route('login') }}">&larr; Torna al login</a>
    </div>
  </div>

  <div class="footer-text">&copy; {{ date('Y') }} AleCar S.r.l. &mdash; Area riservata</div>
</div>
</body>
</html>
