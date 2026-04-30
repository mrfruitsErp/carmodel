<?php
if (($_GET['k'] ?? '') !== 'alecar2026patch') { http_response_code(403); die('403'); }

$target = dirname(__DIR__) . '/resources/views/esperti/show.blade.php';

// Prova a cambiare permessi prima
@chmod(dirname($target), 0775);
@chmod($target, 0664);

$content = '@extends(\'layouts.app\')
@section(\'title\', $esperto->name)
@section(\'topbar-actions\')
<a href="{{ route(\'periti.edit\', [\'periti\' => $esperto->id]) }}" class="btn btn-ghost btn-sm">✎ Modifica</a>
@endsection
@section(\'content\')
<div style="margin-bottom:16px"><a href="{{ route(\'periti.index\') }}" style="color:var(--text3);text-decoration:none;font-size:13px">← Esperti & Contatti</a></div>
<div class="two-col">
  <div>
    <div class="card">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div class="avatar" style="width:48px;height:48px;font-size:17px;background:var(--purple-bg);border-color:var(--purple);color:var(--purple)">{{ strtoupper(substr($esperto->name,0,2)) }}</div>
        <div>
          <div style="font-size:17px;font-weight:700">{{ $esperto->title ? $esperto->title.\' \' : \'\' }}{{ $esperto->name }}</div>
          @if($esperto->company_name)<div style="font-size:12px;color:var(--text3)">{{ $esperto->company_name }}</div>@endif
          <span class="badge badge-purple" style="margin-top:4px">{{ ucfirst(str_replace(\'_\',\' \',$esperto->type)) }}</span>
        </div>
      </div>
      <div class="info-row"><span class="info-label">Telefono</span><span class="info-value">{!! $esperto->phone ? \'<a href="tel:\'.$esperto->phone.\'" style="color:var(--green)">\'.$esperto->phone.\'</a>\' : \'—\' !!}</span></div>
      @if($esperto->phone2)<div class="info-row"><span class="info-label">Tel. 2</span><span class="info-value"><a href="tel:{{ $esperto->phone2 }}" style="color:var(--green)">{{ $esperto->phone2 }}</a></span></div>@endif
      @if(isset($esperto->orario_disponibilita) && $esperto->orario_disponibilita)<div class="info-row"><span class="info-label">Orario disp.</span><span class="info-value">{{ $esperto->orario_disponibilita }}</span></div>@endif
      @if($esperto->email)<div class="info-row"><span class="info-label">Email</span><span class="info-value"><a href="mailto:{{ $esperto->email }}" style="color:var(--green)">{{ $esperto->email }}</a></span></div>@endif
      @if(isset($esperto->pec) && $esperto->pec)<div class="info-row"><span class="info-label">PEC</span><span class="info-value">{{ $esperto->pec }}</span></div>@endif
      @if($esperto->address)<div class="info-row"><span class="info-label">Indirizzo</span><span class="info-value">{{ $esperto->address }}</span></div>@endif
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-title">Dati fiscali</div>
      @if($esperto->fiscal_code)<div class="info-row"><span class="info-label">Codice Fiscale</span><span class="info-value" style="font-family:var(--mono)">{{ $esperto->fiscal_code }}</span></div>@endif
      @if($esperto->vat_number)<div class="info-row"><span class="info-label">Partita IVA</span><span class="info-value" style="font-family:var(--mono)">{{ $esperto->vat_number }}</span></div>@endif
      @if(isset($esperto->insuranceCompany) && $esperto->insuranceCompany)<div class="info-row"><span class="info-label">Compagnia</span><span class="info-value">{{ $esperto->insuranceCompany->name }}</span></div>@endif
      <div class="info-row"><span class="info-label">Valutazione</span><span class="info-value">{{ str_repeat(\'★\', $esperto->rating ?? 3) }}{{ str_repeat(\'☆\', 5 - ($esperto->rating ?? 3)) }}</span></div>
    </div>
    @if($esperto->notes)
    <div class="card">
      <div class="card-title">Note</div>
      <div style="font-size:13px;color:var(--text2);white-space:pre-wrap">{{ $esperto->notes }}</div>
    </div>
    @endif
    <div style="display:flex;gap:8px;margin-top:8px">
      <a href="{{ route(\'periti.edit\', [\'periti\' => $esperto->id]) }}" class="btn btn-ghost" style="flex:1;justify-content:center">✎ Modifica</a>
      <form method="POST" action="{{ route(\'periti.destroy\', [\'periti\' => $esperto->id]) }}" onsubmit="return confirm(\'Eliminare questo contatto?\')">
        @csrf @method(\'DELETE\')
        <button type="submit" class="btn btn-ghost" style="color:var(--red)">Elimina</button>
      </form>
    </div>
  </div>
</div>
@endsection
';

$written = file_put_contents($target, $content);

if ($written !== false) {
    echo "✅ Scritto show.blade.php ($written bytes)<br>";
} else {
    echo "✗ Scrittura fallita — provo con copy()<br>";
    // Scrivo in tmp e copio
    $tmp = sys_get_temp_dir() . '/show_tmp.blade.php';
    file_put_contents($tmp, $content);
    if (copy($tmp, $target)) {
        echo "✅ Copiato da tmp ($written bytes)<br>";
    } else {
        echo "✗ Anche copy() fallita<br>";
        echo "Owner file: " . posix_getpwuid(fileowner($target))['name'] . "<br>";
        echo "Owner processo: " . get_current_user() . "<br>";
        echo "Perms: " . substr(sprintf('%o', fileperms($target)), -4) . "<br>";
    }
}

// Clear view cache
exec("cd " . dirname(__DIR__) . " && php artisan view:clear 2>&1", $out);
echo "View clear: " . implode(' ', $out) . "<br>";
echo '<br><a href="/esperti/2">Testa /esperti/2</a>';
