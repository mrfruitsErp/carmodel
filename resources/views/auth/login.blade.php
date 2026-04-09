<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CarModel Software - Accesso</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0f0f1a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;background-image:radial-gradient(ellipse at 20% 50%,rgba(255,107,0,.08) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(59,130,246,.06) 0%,transparent 60%)}
.login-box{width:100%;max-width:400px}
.logo{text-align:center;margin-bottom:32px}
.logo-icon{width:52px;height:52px;background:#ff6b00;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:#000;box-shadow:0 0 30px rgba(255,107,0,.4);margin-bottom:12px}
.logo-name{font-family:'Rajdhani',sans-serif;font-size:26px;font-weight:700;color:#fff;letter-spacing:.08em;display:block}
.logo-sub{font-size:12px;color:rgba(255,255,255,.3);letter-spacing:.15em;text-transform:uppercase;display:block;margin-top:3px}
.card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;backdrop-filter:blur(10px)}
.card h2{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:#fff;letter-spacing:.06em;text-transform:uppercase;margin-bottom:24px;text-align:center}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:11px;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px}
.form-input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:11px 14px;color:#fff;font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:all .15s}
.form-input:focus{border-color:#ff6b00;background:rgba(255,107,0,.05);box-shadow:0 0 0 3px rgba(255,107,0,.1)}
.form-input::placeholder{color:rgba(255,255,255,.2)}
.checkbox-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.checkbox-label{display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.5);cursor:pointer}
.checkbox-label input{width:15px;height:15px;accent-color:#ff6b00}
.forgot{font-size:12px;color:rgba(255,107,0,.7);text-decoration:none}
.forgot:hover{color:#ff6b00}
.btn-login{width:100%;background:#ff6b00;color:#000;border:none;border-radius:8px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;letter-spacing:.03em;transition:all .2s;box-shadow:0 0 20px rgba(255,107,0,.3)}
.btn-login:hover{background:#e55f00;box-shadow:0 0 30px rgba(255,107,0,.5);transform:translateY(-1px)}
.alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:10px 14px;color:#fca5a5;font-size:13px;margin-bottom:16px}
.alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:10px 14px;color:#86efac;font-size:13px;margin-bottom:16px}
.footer-text{text-align:center;font-size:11px;color:rgba(255,255,255,.2);margin-top:20px}
</style>
</head>
<body>
<div class="login-box">
  <div class="logo">
    <div class="logo-icon">CM</div>
    <span class="logo-name">CARMODEL</span>
    <span class="logo-sub">Software &middot; Automotive</span>
  </div>
  <div class="card">
    <h2>Accesso</h2>
    @if(session('status'))
      <div class="alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" required autofocus autocomplete="username" placeholder="admin@carmodel.it">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
      </div>
      <div class="checkbox-row">
        <label class="checkbox-label">
          <input type="checkbox" name="remember"> Ricordami
        </label>
        @if(Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="forgot">Password dimenticata?</a>
        @endif
      </div>
      <button type="submit" class="btn-login">Accedi &rarr;</button>
    </form>
  </div>
  <div class="footer-text">&copy; {{ date('Y') }} CarModel Software &mdash; Area riservata</div>
</div>
</body>
</html>