@extends('layouts.app')
@section('title', 'Lesione #'.($lesione->injury_number ?? $lesione->id))
@section('topbar-actions')
<a href="{{ route('lesioni.edit', $lesione) }}" class="btn btn-ghost btn-sm">✏️ Modifica</a>
@endsection
@section('content')
<div style="margin-bottom:16px"><a href="{{ route('lesioni.index') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Lesioni</a></div>
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">
  <div>
    <div class="card">
      <div class="card-title">🏥 Dati lesione</div>
      <div class="two-col">
        <div>
          <div class="info-row"><span class="info-label">Numero</span><span class="info-value" style="font-family:var(--mono)">{{ $lesione->injury_number ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">Tipo lesione</span><span class="info-value">{{ $lesione->injury_type }}</span></div>
          <div class="info-row">
            <span class="info-label">Stato</span>
            <span class="info-value">
              <span class="badge badge-{{ match($lesione->status) {
                'aperta'        => 'orange',
                'visita_medica' => 'blue',
                'perizia_medica'=> 'purple',
                'trattativa'    => 'amber',
                'accordo'       => 'teal',
                'liquidata'     => 'green',
                default         => 'gray'
              } }}">{{ ucfirst(str_replace('_',' ',$lesione->status)) }}</span>
            </span>
          </div>
          <div class="info-row"><span class="info-label">Importo stimato</span><span class="info-value">{{ $lesione->estimated_amount ? '€ '.number_format($lesione->estimated_amount,2,',','.') : '—' }}</span></div>
          <div class="info-row"><span class="info-label">Importo concordato</span><span class="info-value" style="color:var(--green-text);font-weight:600">{{ $lesione->agreed_amount ? '€ '.number_format($lesione->agreed_amount,2,',','.') : '—' }}</span></div>
        </div>
        <div>
          <div class="info-row"><span class="info-label">Data visita</span><span class="info-value">{{ $lesione->medical_visit_date?->format('d/m/Y') ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">Data perizia</span><span class="info-value">{{ $lesione->medical_report_date?->format('d/m/Y') ?? '—' }}</span></div>
          <div class="info-row"><span class="info-label">Importo pagato</span><span class="info-value">{{ $lesione->paid_amount ? '€ '.number_format($lesione->paid_amount,2,',','.') : '—' }}</span></div>
          <div class="info-row"><span class="info-label">Data pagamento</span><span class="info-value">{{ $lesione->paid_date?->format('d/m/Y') ?? '—' }}</span></div>
          @if($lesione->lawyer)
            <div class="info-row"><span class="info-label">Avvocato</span><span class="info-value">{{ $lesione->lawyer->name }}</span></div>
          @endif
        </div>
      </div>
      @if($lesione->injury_description)
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
          <div class="form-label" style="margin-bottom:6px">Descrizione</div>
          <div style="font-size:13px;color:var(--text2);line-height:1.6">{{ $lesione->injury_description }}</div>
        </div>
      @endif
      @if($lesione->notes)
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
          <div class="form-label" style="margin-bottom:6px">Note</div>
          <div style="font-size:13px;color:var(--text2);line-height:1.6">{{ $lesione->notes }}</div>
        </div>
      @endif
    </div>

    <div class="card">
      <div class="card-title">📄 Aggiorna stato</div>
      <form action="{{ route('lesioni.update', $lesione) }}" method="POST">
        @csrf @method('PUT')
        <div class="three-col">
          <div class="form-group">
            <label class="form-label">Stato</label>
            <select name="status" class="form-select">
              @foreach(['aperta','visita_medica','perizia_medica','trattativa','accordo','liquidata','contenzioso','chiusa'] as $s)
                <option value="{{ $s }}" {{ $lesione->status===$s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Importo concordato (€)</label>
            <input type="number" name="agreed_amount" value="{{ $lesione->agreed_amount }}" class="form-input" step="0.01">
          </div>
          <div class="form-group">
            <label class="form-label">Data pagamento</label>
            <input type="date" name="paid_date" value="{{ $lesione->paid_date?->format('Y-m-d') }}" class="form-input">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Note</label>
          <textarea name="notes" class="form-textarea" rows="3">{{ $lesione->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">💾 Salva</button>
      </form>
    </div>
  </div>

  {{-- COLONNA DESTRA --}}
  <div>
    @if($lesione->claim)
      <div class="card">
        <div class="card-title">⚡ Sinistro</div>
        <div class="info-row">
          <span class="info-label">Numero</span>
          <a href="{{ route('sinistri.show', $lesione->claim) }}" style="color:var(--blue-text);font-family:var(--mono);font-size:12px">{{ $lesione->claim->claim_number }}</a>
        </div>
        <div class="info-row">
          <span class="info-label">Tipo</span>
          <span class="info-value">{{ ucfirst($lesione->claim->claim_type ?? '—') }}</span>
        </div>
        @if($lesione->claim->counterpart_plate)
          <div class="info-row">
            <span class="info-label">Targa controparte</span>
            <span class="info-value" style="font-family:var(--mono)">{{ $lesione->claim->counterpart_plate }}</span>
          </div>
        @endif
      </div>
    @endif

    @if($lesione->customer)
      <div class="card">
        <div class="card-title">👤 Cliente</div>
        <div class="info-row">
          <span class="info-label">Nome</span>
          <a href="{{ route('clienti.show', $lesione->customer) }}" style="color:var(--blue-text)">{{ $lesione->customer->display_name }}</a>
        </div>
        @if($lesione->customer->telefono)
          <div class="info-row">
            <span class="info-label">Telefono</span>
            <a href="tel:{{ $lesione->customer->telefono }}" style="color:var(--text)">{{ $lesione->customer->telefono }}</a>
          </div>
        @endif
        @if($lesione->customer->email)
          <div class="info-row">
            <span class="info-label">Email</span>
            <a href="mailto:{{ $lesione->customer->email }}" style="color:var(--blue-text)">{{ $lesione->customer->email }}</a>
          </div>
        @endif
      </div>
    @endif

    @if($lesione->doctor)
      <div class="card">
        <div class="card-title">🩺 Medico</div>
        <div class="info-row"><span class="info-label">Nome</span><span class="info-value">{{ $lesione->doctor->name }}</span></div>
        @if($lesione->doctor->phone)
          <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{{ $lesione->doctor->phone }}</span></div>
        @endif
      </div>
    @endif

    @if($lesione->lawyer)
      <div class="card">
        <div class="card-title">⚖️ Avvocato</div>
        <div class="info-row"><span class="info-label">Nome</span><span class="info-value">{{ $lesione->lawyer->name }}</span></div>
        @if($lesione->lawyer->phone)
          <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{{ $lesione->lawyer->phone }}</span></div>
        @endif
      </div>
    @endif

    <div class="card" style="background:var(--bg3)">
      <div style="font-size:11px;color:var(--text3);line-height:1.8">
        <div>Creata: {{ $lesione->created_at->format('d/m/Y H:i') }}</div>
        <div>Aggiornata: {{ $lesione->updated_at->format('d/m/Y H:i') }}</div>
      </div>
      <form action="{{ route('lesioni.destroy', $lesione) }}" method="POST" style="margin-top:12px"
        onsubmit="return confirm('Eliminare questa lesione?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);border-color:var(--red);width:100%">
          🗑 Elimina lesione
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
