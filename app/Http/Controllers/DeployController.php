<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    public function run(Request $request)
    {
        $secret = config('app.deploy_secret', env('DEPLOY_SECRET', ''));
        if (!$secret || $request->input('token') !== $secret) {
            abort(403, 'Unauthorized');
        }

        $output = [];
        $cwd = base_path();

        // Rimuovi git lock se presente
        $lockFile = $cwd . '/.git/index.lock';
        if (file_exists($lockFile)) {
            @unlink($lockFile);
            $output[] = "Rimosso .git/index.lock";
        }

        exec("cd {$cwd} && git fetch origin 2>&1", $output);
        exec("cd {$cwd} && git reset --hard origin/main 2>&1", $output);
        exec("cd {$cwd} && php artisan migrate --force 2>&1", $output);
        exec("cd {$cwd} && php artisan route:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan config:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan view:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan cache:clear 2>&1", $output);

        // Ripristina .env se sovrascritto dal reset
        $envFile = $cwd . '/.env';
        if (file_exists($envFile)) {
            $env = file_get_contents($envFile);
            $output[] = "✓ .env presente (" . strlen($env) . " bytes)";
        } else {
            $output[] = "⚠ .env mancante!";
        }

        return response('<pre>' . implode("\n", $output) . '</pre>');
    }

    // Patch diretta file — usata solo in emergenza deploy bloccato
    public function patch(Request $request)
    {
        $secret = config('app.deploy_secret', env('DEPLOY_SECRET', ''));
        if (!$secret || $request->input('token') !== $secret) {
            abort(403, 'Unauthorized');
        }

        $file    = $request->input('file');  // path relativo da base_path()
        $content = $request->input('content');

        if (!$file || $content === null) {
            return response()->json(['error' => 'file e content richiesti'], 400);
        }

        // Sicurezza: solo file dentro il progetto, no path traversal
        $target = realpath(base_path()) . '/' . ltrim($file, '/');
        if (!str_starts_with($target, realpath(base_path()))) {
            abort(403, 'Path non consentito');
        }

        @mkdir(dirname($target), 0755, true);
        file_put_contents($target, $content);

        // Svuota cache view se è un blade
        if (str_ends_with($file, '.blade.php')) {
            exec(base_path() . '/artisan view:clear 2>&1');
        }

        return response()->json(['ok' => true, 'file' => $file, 'bytes' => strlen($content)]);
    }
}
