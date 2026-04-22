<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>CarModel — @yield('title', 'Portale Documenti')</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --orange:#ff6b00;--orange2:#e55f00;--orange-bg:rgba(255,107,0,.08);--orange-border:rgba(255,107,0,.25);
  --green:#22c55e;--green-bg:rgba(34,197,94,.08);--green-text:#16a34a;
  --red:#ef4444;--red-bg:rgba(239,68,68,.08);--red-text:#dc2626;
  --blue:#3b82f6;--blue-bg:rgba(59,130,246,.08);--blue-text:#2563eb;
  --amber:#f59e0b;--amber-bg:rgba(245,158,11,.08);
  --bg:#f6f7f9;--bg2:#ffffff;--bg3:#f1f3f6;
  --border:#e5e7eb;--border2:#dfe3e8;
  --text:#1f2937;--text2:#6b7280;--text3:#9ca3af;
  --font-display:'Rajdhani',sans-serif;--font-body:'DM Sans',sans-serif;
  --radius:8px;--radius-lg:14px;
}
body{font-family:var(--font-body);background:var(--bg);color:var(--text);font-size:14px;line-height:1.5;min-height:100vh}

/* HEADER */
.portale-header{background:#111827;padding:14px 20px;display:flex;align-items:center;justify-content:center;border-bottom:2px solid var(--orange)}
.portale-logo{font-family:var(--font-display);font-size:22px;font-weight:700;color:#fff;letter-spacing:.08em;text-transform:uppercase}
.portale-logo span{color:var(--orange)}

/* WRAPPER */
.portale-wrap{max-width:600px;margin:0 auto;padding:24px 16px}

/* CARD */
.card{background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);padding:24px;margin-bottom:16px}
.card-title{font-family:var(--font-display);font-size:16px;font-weight:700;color:var(--text);margin-bottom:16px;letter-spacing:.06em;text-transform:uppercase}

/* FORM */
.form-group{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
.form-label{font-size:11px;font-weight:600;color:var(--text3);letter-spacing:.08em;text-transform:uppercase}
.form-input{background:var(--bg3);border:1.5px solid var(--border2);border-radius:var(--radius);padding:10px 13px;color:var(--text);font-size:14px;font-family:var(--font-body);outline:none;width:100%;transition:border-color .15s}
.form-input:focus{border-color:var(--orange)}
.form-input::placeholder{color:var(--text3)}

/* BTN */
.btn{padding:11px 20px;font-size:14px;border-radius:var(--radius);cursor:pointer;font-family:var(--font-body);font-weight:600;transition:all .15s;border:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;width:100%}
.btn-primary{background:var(--orange);color:#000;box-shadow:0 0 16px rgba(255,107,0,.3)}.btn-primary:hover{background:var(--orange2)}
.btn-ghost{background:transparent;border:1.5px solid var(--border2);color:var(--text2)}.btn-ghost:hover{background:var(--bg3)}

/* ALERT */
.alert{padding:12px 16px;border-radius:var(--radius);margin-bottom:16px;font-size:13px;border-left:3px solid}
.alert-red{background:var(--red-bg);border-color:var(--red);color:var(--red-text)}
.alert-green{background:var(--green-bg);border-color:var(--green);color:var(--green-text)}
.alert-amber{background:var(--amber-bg);border-color:var(--amber);color:#92400e}

/* BADGE */
.badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:4px;font-size:12px;font-weight:600}
.badge-green{background:var(--green-bg);color:var(--green-text)}
.badge-red{background:var(--red-bg);color:var(--red-text)}
.badge-gray{background:var(--bg3);color:var(--text2);border:1px solid var(--border)}

/* STEP PROGRESS */
.step-bar{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:24px}
.step{display:flex;flex-direction:column;align-items:center;gap:4px;flex:1}
.step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;border:2px solid var(--border)}
.step-circle.done{background:var(--green);border-color:var(--green);color:#fff}
.step-circle.active{background:var(--orange);border-color:var(--orange);color:#000}
.step-circle.pending{background:var(--bg3);color:var(--text3)}
.step-label{font-size:10px;color:var(--text3);text-align:center;display:none}
@media(min-width:480px){.step-label{display:block}}
.step-line{flex:1;height:2px;background:var(--border);max-width:40px}
.step-line.done{background:var(--green)}

/* PROGRESS BAR */
.progress{background:var(--bg3);border-radius:4px;height:8px;overflow:hidden}
.progress-fill{height:100%;background:var(--orange);border-radius:4px;transition:width .4s}

/* DOC ITEM */
.doc-item{border:1.5px solid var(--border);border-radius:var(--radius);padding:14px;margin-bottom:10px;transition:border-color .15s}
.doc-item.completato{border-color:rgba(34,197,94,.4);background:rgba(34,197,94,.03)}
.doc-item.obbligatorio{border-left:3px solid var(--red)}

/* FOOTER */
.portale-footer{text-align:center;padding:20px;font-size:11px;color:var(--text3)}
</style>
</head>
<body>
<div class="portale-header">
  <div class="portale-logo">Car<span>Model</span></div>
</div>

<div class="portale-wrap">
  @if(session('success'))
    <div class="alert alert-green">✓ {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-red">✕ {{ session('error') }}</div>
  @endif
  @if($errors->any())
    @foreach($errors->all() as $e)
      <div class="alert alert-red">{{ $e }}</div>
    @endforeach
  @endif

  @yield('content')
</div>

<div class="portale-footer">
  CarModel Software · Portale documenti sicuro · {{ date('Y') }}
</div>

@stack('scripts')
</body>
</html>