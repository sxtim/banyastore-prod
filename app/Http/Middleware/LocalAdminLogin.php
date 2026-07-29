<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LocalAdminLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! app()->environment('local')
            || ! config('app.local_admin.enabled')
            || ! $request->is('backend', 'backend/*')
            || Auth::check()
        ) {
            return $next($request);
        }

        $userId = (int) config('app.local_admin.user_id');
        if ($userId < 1 || ! Auth::loginUsingId($userId)) {
            abort(500, "Не найден локальный администратор ID {$userId}.");
        }

        return $next($request);
    }
}
