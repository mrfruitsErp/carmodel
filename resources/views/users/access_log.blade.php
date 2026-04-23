@extends('layouts.app')
@section('title', 'Registro Accessi')
@section('topbar-actions')
<a href="{{ route('utenti.index') }}" class="btn btn-ghost btn-sm">&larr; Utenti</a>
@endsection
@section('content')

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Utente</th>
        <th>Azione</th>
        <th>IP</th>
        <th>Browser</th>
        <th>Data/Ora</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
      <tr>
        <td style="font-weight:500">{{ $log->user->name ?? '-' }}</td>
        <td>
          @php $actionCfg = match($log->action) {
            'login'        => ['badge-green','Login'],
            'logout'       => ['badge-gray','Logout'],
            'failed_login' => ['badge-red','Tentativo fallito'],
            default        => ['badge-gray',$log->action],
          }; @endphp
          <span class="badge {{ $actionCfg[0] }}">{{ $actionCfg[1] }}</span>
        </td>
        <td style="font-family:var(--mono);font-size:12px">{{ $log->ip_address ?? '-' }}</td>
        <td style="font-size:11px;color:var(--text3);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
          {{ $log->user_agent ? Str::limit($log->user_agent, 50) : '-' }}
        </td>
        <td style="font-size:12px;color:var(--text3)">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;color:var(--text3);padding:40px">Nessun accesso registrato</td></tr>
      @endforelse
    </tbody>
  </table>
  <div style="margin-top:16px">{{ $logs->links() }}</div>
</div>
@endsection