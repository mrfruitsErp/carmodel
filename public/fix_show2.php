<?php
if (($_GET['k'] ?? '') !== 'alecar2026patch') { http_response_code(403); die('403'); }

$base = dirname(__DIR__);
$target = $base . '/resources/views/esperti/show.blade.php';

echo "<pre>";
echo "PHP user: " . exec('whoami') . "\n";
echo "File owner: " . exec("ls -la $target") . "\n";
echo "Writable: " . (is_writable($target) ? 'YES' : 'NO') . "\n";
echo "Writable dir: " . (is_writable(dirname($target)) ? 'YES' : 'NO') . "\n";

// Prova con chmod via shell
exec("chmod 666 $target 2>&1", $o1); echo "chmod file: " . implode('', $o1) . "\n";
exec("chmod 777 " . dirname($target) . " 2>&1", $o2); echo "chmod dir: " . implode('', $o2) . "\n";

// Prova git direttamente
echo "\n--- GIT PULL ---\n";
exec("cd $base && git fetch origin 2>&1", $o3); echo implode("\n", $o3) . "\n";
exec("cd $base && git reset --hard origin/main 2>&1", $o4); echo implode("\n", $o4) . "\n";

// Migrate
echo "\n--- MIGRATE ---\n";
exec("cd $base && php artisan migrate --force 2>&1", $o5); echo implode("\n", $o5) . "\n";

// Cache
echo "\n--- CACHE ---\n";
exec("cd $base && php artisan view:clear && php artisan route:clear && php artisan config:cache 2>&1", $o6);
echo implode("\n", $o6) . "\n";

echo "\n--- FILE DOPO RESET ---\n";
echo exec("ls -la $target") . "\n";
echo "Contenuto riga 4: " . exec("sed -n '4p' $target") . "\n";

echo "\nDONE";
echo "</pre>";
echo '<a href="/esperti/2">Testa /esperti/2</a>';
