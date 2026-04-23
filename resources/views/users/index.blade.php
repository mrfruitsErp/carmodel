@extends('layouts.app')
@section('title', 'Gestione Utenti')
@section('topbar-actions')
<a href="{{ route('utenti.create') }}" class="btn btn-primary btn-sm">+ Nuovo utente</a>
@endsection
@section('content')

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Utente</th>
        <th>Ruolo</th>
        <th>Email</th>
        <th>Telefono</th>
        <th>Stato</th>
        <th>Ultimo accesso</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
      <tr>
        <td>
          <div style="font-weight:600">{{ $user->name }}</div>
          @if($user->notes)<div style="font-size:11px;color:var(--text3)">{{ Str::limit($user->notes,40) }}</div>@endif
        </td>
        <td>
          @php $roleColors = ['admin'=>'orange','manager'=>'blue','operatore'=>'green','vendite'=>'purple']; @endphp
          <span class="badge badge-{{ $roleColors[$user->role] ?? 'gray' }}">{{ ucfirst($user->role) }}</span>
        </td>
        <td style="font-size:13px">{{ $user->email }}</td>
        <td style="font-size:13px">{{ $user->phone ?? '-' }}</td>
        <td>
          <form action="{{ route('utenti.toggle', $user) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="badge {{ $user->active ? 'badge-green' : 'badge-red' }}" style="border:none;cursor:pointer">
              {{ $user->active ? 'Attivo' : 'Disattivo' }}
            </button>
          </form>
        </td>
        <td style="font-size:12px;color:var(--text3)">
          {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Mai' }}
        </td>
        <td>
          <div style="display:flex;gap:6px">
            <a href="{{ route('utenti.edit', $user) }}" class="btn btn-ghost btn-sm">Modifica</a>
            @if($user->id !== auth()->id())
            <form action="{{ route('utenti.destroy', $user) }}" method="POST"
              onsubmit="return confirm('Eliminare {{ $user->name }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;color:var(--text3);padding:40px">Nessun utente</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection