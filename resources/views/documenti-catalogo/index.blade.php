@extends('layouts.app')
@section('title', 'Catalogo Documenti')

@section('topbar-actions')
<a href="{{ route('documenti-catalogo.create') }}" class="btn btn-primary btn-sm">+ Nuovo documento</a>
<a href="{{ route('settings.index') }}" class="btn btn-ghost btn-sm">← Settings</a>
@endsection

@section('content')

{{-- FILTRO SEZIONE --}}
<div class="card" style="margin-bottom:16px">
  <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
    <div class="form-group" style="flex:1;min-width:160px;margin-bottom:0">
      <label class="form-label">Filtra per sezione</label>
      <select name="sezione" class="form-select" onchange="this.form.submit()">
        <option value="">Tutte le sezioni</option>
        @foreach(\App\Models\DocumentoCatalogo::sezioniDisponibili() as $key => $label)
          <option value="{{ $key }}" {{ request('sezione') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group" style="flex:1;min-width:140px;margin-bottom:0">
      <label class="form-label">Tipo soggetto</label>
      <select name="tipo_soggetto" class="form-select" onchange="this.form.submit()">
        <option value="">Tutti</option>
        <option value="privato" {{ request('tipo_soggetto') == 'privato' ? 'selected' : '' }}>Privato</option>
        <option value="azienda" {{ request('tipo_soggetto') == 'azienda' ? 'selected' : '' }}>Azienda</option>
        <option value="entrambi" {{ request('tipo_soggetto') == 'entrambi' ? 'selected' : '' }}>Entrambi</option>
      </select>
    </div>
    @if(request()->hasAny(['sezione','tipo_soggetto']))
      <a href="{{ route('documenti-catalogo.index') }}" class="btn btn-ghost btn-sm">✕ Reset</a>
    @endif
  </form>
</div>

<div class="card" style="padding:0">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border2)">
    <span class="card-title" style="margin-bottom:0">Catalogo documenti ({{ $documenti->total() }})</span>
  </div>
  <table>
    <thead>
      <tr>
        <th style="width:40px">Ord.</th>
        <th>Nome documento</th>
        <th>Sezioni collegate</th>
        <th>Soggetto</th>
        <th>Tipo</th>
        <th>Obbligatorio</th>
        <th>Stato</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($documenti as $doc)
      <tr>
        <td style="color:var(--text3);font-size:11px">{{ $doc->ordine }}</td>
        <td>
          <div style="font-weight:500">{{ $doc->nome }}</div>
          @if($doc->descrizione)
            <div style="font-size:11px;color:var(--text3)">{{ Str::limit($doc->descrizione, 60) }}</div>
          @endif
        </td>
        <td>
          <div style="display:flex;flex-wrap:wrap;gap:3px">
            @foreach($doc->sezioni_collegate as $s)
              <span class="badge badge-blue" style="font-size:9px">{{ $s }}</span>
            @endforeach
          </div>
        </td>
        <td>
          @if($doc->tipo_soggetto === 'entrambi')
            <span class="badge badge-gray" style="font-size:10px">Tutti</span>
          @elseif($doc->tipo_soggetto === 'azienda')
            <span class="badge badge-purple" style="font-size:10px">Azienda</span>
          @else
            <span class="badge badge-teal" style="font-size:10px">Privato</span>
          @endif
        </td>
        <td>
          <div style="display:flex;flex-direction:column;gap:3px">
            @if($doc->richiede_upload)
              <span class="badge badge-blue" style="font-size:9px">📎 Upload</span>
            @endif
            @if($doc->richiede_firma)
              <span class="badge badge-amber" style="font-size:9px">✍ Firma</span>
            @endif
          </div>
        </td>
        <td>
          @if($doc->obbligatorio_default)
            <span class="badge badge-red" style="font-size:10px">Sì</span>
          @else
            <span class="badge badge-gray" style="font-size:10px">No</span>
          @endif
        </td>
        <td>
          @if($doc->attivo)
            <span class="badge badge-green" style="font-size:10px">Attivo</span>
          @else
            <span class="badge badge-gray" style="font-size:10px">Inattivo</span>
          @endif
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <a href="{{ route('documenti-catalogo.edit', $doc) }}" class="btn btn-ghost btn-sm">✏</a>
            <form method="POST" action="{{ route('documenti-catalogo.destroy', $doc) }}"
              onsubmit="return confirm('Eliminare questo documento dal catalogo?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">✕</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:var(--text3);padding:32px">Nessun documento nel catalogo</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($documenti->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border2)">
    {{ $documenti->withQueryString()->links() }}
  </div>
  @endif
</div>

@endsection