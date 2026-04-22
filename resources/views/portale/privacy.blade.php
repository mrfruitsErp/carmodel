@extends('portale.layout')
@section('title', 'Informativa Privacy')
@section('content')

{{-- Step bar --}}
<div class="step-bar">
  <div class="step">
    <div class="step-circle done">✓</div>
    <div class="step-label">Identità</div>
  </div>
  <div class="step-line done"></div>
  <div class="step">
    <div class="step-circle active">2</div>
    <div class="step-label">Privacy</div>
  </div>
  <div class="step-line"></div>
  <div class="step">
    <div class="step-circle pending">3</div>
    <div class="step-label">Documenti</div>
  </div>
</div>

<div class="card">
  <div class="card-title">Informativa Privacy</div>
  <div style="font-size:13px;color:var(--text2);line-height:1.8;max-height:280px;overflow-y:auto;background:var(--bg3);padding:14px;border-radius:var(--radius);margin-bottom:20px;border:1px solid var(--border)">
    {!! nl2br(e($testoGdpr)) !!}
  </div>
  <div style="font-size:11px;color:var(--text3);margin-bottom:16px">
    Versione {{ $versioneGdpr }} · Data accettazione: {{ now()->format('d/m/Y H:i') }}
  </div>
  <form method="POST" action="{{ route('portale.privacy.accetta', $token) }}">
    @csrf
    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;margin-bottom:20px;font-size:13px">
      <input type="checkbox" name="accetto" value="1" required style="margin-top:3px;flex-shrink:0">
      <span>Ho letto e accetto l'informativa sul trattamento dei dati personali ai sensi del GDPR 2016/679</span>
    </label>
    <button type="submit" class="btn btn-primary">✓ Accetto e continua</button>
  </form>
</div>
@endsection