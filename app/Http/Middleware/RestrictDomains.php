<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Limita una rotta a essere accessibile solo da una lista di hostname.
 *
 * Uso nelle route:
 *   Route::middleware('restrict:erp.alecar.it,142.93.99.245')->group(...)
 *
 * Se la richiesta arriva da un host non in lista, ritorna 404 (la route
 * non è "esposta" su quel dominio — più sicuro di una 403 perché non
 * rivela l'esistenza della rotta).
 */
class RestrictDomains
{
    public function handle(Request $request, Closure $next, ...$domains)
    {
        $host = strtolower($request->getHost());
        $allowed = array_map('strtolower', $domains);

        if (!in_array($host, $allowed, true)) {
            abort(404);
        }

        return $next($request);
    }
}
