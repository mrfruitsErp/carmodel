<?php
// PATCH DEPLOY - eliminare dopo l'uso
// Accesso: https://app.alecar.it/deploy_patch.php?secret=alecar2026patch

if (($_GET['secret'] ?? '') !== 'alecar2026patch') {
    http_response_code(403);
    die('Forbidden');
}

$base = dirname(__DIR__);
$results = [];

$files = [
    'app/Models/ClaimDiary.php',
    'app/Models/Claim.php',
    'app/Models/Expert.php',
    'app/Http/Controllers/ClaimController.php',
    'app/Http/Controllers/ExpertController.php',
    'database/migrations/2026_04_30_100001_extend_claims_and_experts.php',
    'routes/web.php',
    'resources/views/esperti/create.blade.php',
    'resources/views/esperti/index.blade.php',
    'resources/views/esperti/show.blade.php',
    'resources/views/sinistri/edit.blade.php',
    'resources/views/sinistri/show.blade.php',
    'resources/views/sinistri/stampa.blade.php',
    'resources/views/settings/gruppo.blade.php',
];

echo "<pre>\n";
echo "=== DEPLOY PATCH ===\n\n";

foreach ($files as $f) {
    $exists = file_exists("$base/$f");
    $mtime  = $exists ? date('Y-m-d H:i:s', filemtime("$base/$f")) : 'NOT FOUND';
    echo ($exists ? '✓' : '✗') . " $f  [$mtime]\n";
    $results[] = $exists;
}

echo "\n=== GIT STATUS ===\n";
$out = shell_exec("cd $base && git log --oneline -5 2>&1");
echo $out . "\n";

echo "\n=== GIT PULL ===\n";
// Rimuovi lock se esiste
@unlink("$base/.git/index.lock");
$pull = shell_exec("cd $base && git fetch origin && git reset --hard origin/main 2>&1");
echo $pull . "\n";

echo "\n=== MIGRATE ===\n";
$migrate = shell_exec("cd $base && php artisan migrate --force 2>&1");
echo $migrate . "\n";

echo "\n=== CACHE CLEAR ===\n";
$cache = shell_exec("cd $base && php artisan view:clear && php artisan config:cache && php artisan route:clear 2>&1");
echo $cache . "\n";

echo "\n=== DONE ===\n";
echo "</pre>";
