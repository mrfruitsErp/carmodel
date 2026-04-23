<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCanDo
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user || !$user->canDo($permission)) {
            abort(403, 'Accesso negato');
        }

        return $next($request);
    }
}