<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCanDo
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();

        // Se non loggato → al login (non 403, ha senso solo per chi è dentro)
        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Unauthenticated'], 401)
                : redirect()->guest(route('login'));
        }

        if (!$user->canDo($permission)) {
            abort(403, "Permesso richiesto: {$permission}");
        }

        return $next($request);
    }
}