@extends('layouts.app')
@section('title', 'Calendario Movimenti')

@section('topbar-actions')
<a href="{{ route('movimenti.index') }}" class="btn btn-ghost btn-sm">☰ Lista</a>
<a href="{{ route('movimenti.create') }}" class="btn btn-primary btn-sm">+ Nuovo Movimento</a>
@endsection

@section('content')

{{-- Legenda tipi --}}
<div class="card" style="padding:12px 16px;margin-bottom:16px">
  <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">
    <span style="font-size:12px;color:var(--text3);font-weight:600">LEGENDA:</span>
    @foreach(\App\Models\VehicleMovement::tipi() as $k => $t)
      <span style="font-size:11px;display:flex;align-items:center;gap:4px">
        <span style="width:10px;height:10px;border-radius:50%;background:{{ match($t['color']) {
          'success'=>'#22c55e','warning'=>'#f59e0b','danger'=>'#ef4444',
          'info'=>'#3b82f6','primary'=>'#8b5cf6','dark'=>'#374151',default=>'#6b7280'
        } }};display:inline-block"></span>
        {{ $t['icon'] }} {{ $t['label'] }}
      </span>
    @endforeach
  </div>
</div>

{{-- Calendario --}}
<div class="card" style="padding:16px">
  <div id="calendar"></div>
</div>

{{-- Modal dettaglio evento --}}
<div id="modal-evento" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center">
  <div style="background:var(--bg1);border-radius:var(--radius-lg);padding:24px;min-width:320px;max-width:480px;box-shadow:var(--shadow-lg)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <div id="modal-title" style="font-weight:700;font-size:15px"></div>
      <button onclick="chiudiModal()" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--text3)">✕</button>
    </div>
    <div id="modal-body" style="font-size:13px;color:var(--text2);display:flex;flex-direction:column;gap:8px"></div>
    <div style="margin-top:16px;display:flex;gap:8px">
      <a id="modal-link" href="#" class="btn btn-primary btn-sm">Dettaglio →</a>
      <button onclick="chiudiModal()" class="btn btn-ghost btn-sm">Chiudi</button>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/it.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'it',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        height: 'auto',
        events: '{{ route('movimenti.api-eventi') }}',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const p = info.event.extendedProps;
            document.getElementById('modal-title').textContent = info.event.title;
            document.getElementById('modal-body').innerHTML =
                '<div>📅 ' + info.event.startStr.replace('T',' ').substring(0,16) + '</div>' +
                (p.stato ? '<div>Stato: <strong>' + p.stato + '</strong></div>' : '') +
                (p.luogo_partenza ? '<div>📍 Partenza: ' + p.luogo_partenza + '</div>' : '') +
                (p.luogo_arrivo   ? '<div>🏁 Arrivo: '  + p.luogo_arrivo   + '</div>' : '') +
                (p.cliente !== '—' ? '<div>👤 ' + p.cliente + '</div>' : '');
            document.getElementById('modal-link').href = p.url;
            document.getElementById('modal-evento').style.display = 'flex';
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        slotMinTime: '07:00:00',
        slotMaxTime: '21:00:00',
        nowIndicator: true,
        businessHours: { daysOfWeek:[1,2,3,4,5,6], startTime:'08:00', endTime:'19:00' },
    });
    cal.render();
});

function chiudiModal() {
    document.getElementById('modal-evento').style.display = 'none';
}
document.getElementById('modal-evento').addEventListener('click', function(e) {
    if (e.target === this) chiudiModal();
});
</script>
@endsection
