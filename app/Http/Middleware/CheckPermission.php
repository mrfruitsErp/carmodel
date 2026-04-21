<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        Log::info('CheckPermission', [
            'user_id' => $user->id,
            'role' => $user->role,
            'permission' => $permission,
            'canDo' => $user->hasAccess($permission),
        ]);
        if (!$user->hasAccess($permission)) {
            abort(403, 'Non hai i permessi per accedere a questa sezione.');
        }
        return $next($request);
    }
}
