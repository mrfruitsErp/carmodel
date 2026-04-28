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

        exec("cd {$cwd} && git pull origin main 2>&1", $output);
        exec("cd {$cwd} && php artisan route:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan config:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan view:clear 2>&1", $output);
        exec("cd {$cwd} && php artisan cache:clear 2>&1", $output);

        return response('<pre>' . implode("\n", $output) . '</pre>');
    }
}
