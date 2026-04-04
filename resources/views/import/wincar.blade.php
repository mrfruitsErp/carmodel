@extends('layouts.app')
@section('title', 'Import da Wincar')
@section('content')
<div class="two-col">
  <div>
    <div class="card">
      <div class="card-title">Importa file CSV da Wincar</div>
      <div class="alert alert-blue" style="border-color:var(--blue);background:var(--blue-bg);color:var(--blue-text)"><span>ℹ</span><span>Esporta da Wincar in formato CSV, poi carica il file qui. Il sistema importerà clienti, veicoli o lavorazioni.</span></div>
      <form method="POST" action="{{ route('import.wincar.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group"><label class="form-label">Tipo di import</label>
          <select name="type" class="form-select">
            <option value="customers">Clienti (anagrafiche)</option>
            <option value="vehicles">Veicoli</option>
            <option value="jobs">Lavorazioni</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">File CSV</label><input type="file" name="file" class="form-input" accept=".csv,.txt"></div>
        <button type="submit" class="btn btn-primary" style="width:100%">Avvia import</button>
      </form>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Log import recenti</div>
      @forelse($log as $l)
      <div class="info-row">
        <span class="info-label" style="font-size:12px">{{ $l->file_name }} · {{ $l->import_type }}</span>
        <span class="info-value"><span class="badge {{ $l->status === 'completed' ? 'badge-green' : ($l->status === 'failed' ? 'badge-red' : 'badge-amber') }}">{{ ucfirst($l->status) }}</span></span>
      </div>
      @if($l->status === 'completed')
      <div style="font-size:11px;color:var(--text3);padding:0 0 8px">{{ $l->rows_imported }} importati · {{ $l->rows_skipped }} saltati · {{ $l->rows_error }} errori</div>
      @endif
      @empty
      <div style="color:var(--text3);font-size:13px">Nessun import effettuato</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
