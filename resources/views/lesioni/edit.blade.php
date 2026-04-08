@extends('layouts.app')
@section('title', isset($lesione) ? 'Modifica Lesione' : 'Nuova Lesione')
@section('content')
<div style="max-width:800px">
  <div style="margin-bottom:16px"><a href="{{ route('lesioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">&larr; Lesioni</a></div>
  <div class="card">
    <div class="card-title">{{ isset($lesione) ? 'Modifica lesione' : 'Nuova lesione personale' }}</div>
    <form action="{{ isset($lesione) ? route('lesioni.update',$lesione) : route('lesioni.store') }}" method="POST">
      @csrf
      @if(isset($lesione)) @method('PUT') @endif
      @if($errors->any())<div class="alert alert-red">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
      <div class="two-col">
        <div class="form-group"><label class="form-label">Sinistro collegato</label><select name="claim_id" class="form-select"><option value="">-- Nessuno --</option>@foreach($sinistri as $s)<option value="{{ $s->id }}" {{ old('claim_id',($lesione->claim_id??''))==$s->id?'selected':'' }}>#{{ $s->claim_number }} -- {{ $s->customer?->display_name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Cliente</label><select name="customer_id" class="form-select"><option value="">-- Seleziona --</option>@foreach($clienti as $c)<option value="{{ $c->id }}" {{ old('customer_id',($lesione->customer_id??''))==$c->id?'selected':'' }}>{{ $c->display_name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Tipo lesione *</label><input type="text" name="injury_type" value="{{ old('injury_type',($lesione->injury_type??'')) }}" class="form-input" required placeholder="es. Colpo di frusta cervicale"></div>
        <div class="form-group"><label class="form-label">Stato</label><select name="status" class="form-select">@foreach(['aperta','visita_medica','perizia_medica','trattativa','accordo','liquidata','contenzioso','chiusa'] as $s)<option value="{{ $s }}" {{ old('status',($lesione->status??'aperta'))===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Avvocato</label><select name="lawyer_id" class="form-select"><option value="">-- Nessuno --</option>@foreach($avvocati as $a)<option value="{{ $a->id }}" {{ old('lawyer_id',($lesione->lawyer_id??''))==$a->id?'selected':'' }}>{{ $a->name }}</option>@endforeach</select></div>
        <div class="form-group"><label class="form-label">Codice ICD</label><input type="text" name="icd_code" value="{{ old('icd_code',($lesione->icd_code??'')) }}" class="form-input" placeholder="es. S13.4"></div>
        <div class="form-group"><label class="form-label">Data visita medica</label><input type="date" name="medical_visit_date" value="{{ old('medical_visit_date',($lesione->medical_visit_date??null)?->format('Y-m-d')) }}" class="form-input"></div>
        <div class="form-group"><label class="form-label">Data perizia medica</label><input type="date" name="medical_report_date" value="{{ old('medical_report_date',($lesione->medical_report_date??null)?->format('Y-m-d')) }}" class="form-input"></div>
        <div class="form-group"><label class="form-label">Importo stimato (euro)</label><input type="number" name="estimated_amount" value="{{ old('estimated_amount',($lesione->estimated_amount??'')) }}" class="form-input" step="0.01" min="0"></div>
        <div class="form-group"><label class="form-label">Importo concordato (euro)</label><input type="number" name="agreed_amount" value="{{ old('agreed_amount',($lesione->agreed_amount??'')) }}" class="form-input" step="0.01" min="0"></div>
        <div class="form-group"><label class="form-label">Importo pagato (euro)</label><input type="number" name="paid_amount" value="{{ old('paid_amount',($lesione->paid_amount??'')) }}" class="form-input" step="0.01" min="0"></div>
        <div class="form-group"><label class="form-label">Data pagamento</label><input type="date" name="paid_date" value="{{ old('paid_date',($lesione->paid_date??null)?->format('Y-m-d')) }}" class="form-input"></div>
      </div>
      <div class="form-group"><label class="form-label">Descrizione lesione</label><textarea name="injury_description" class="form-textarea" rows="3">{{ old('injury_description',($lesione->injury_description??'')) }}</textarea></div>
      <div class="form-group"><label class="form-label">Note</label><textarea name="notes" class="form-textarea" rows="3">{{ old('notes',($lesione->notes??'')) }}</textarea></div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('lesioni.index') }}" class="btn btn-ghost">Annulla</a>
        <button type="submit" class="btn btn-primary">{{ isset($lesione) ? 'Salva modifiche' : 'Crea lesione' }}</button>
      </div>
    </form>
  </div>
</div>
@endsection