<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param $perm
     * @return mixed
     */
    public function handle($request, Closure $next, $perm) {
        if (!Auth::user()->hasPermAnyWay($perm)) {
            abort(403);
        }
        return $next($request);
    }
}
