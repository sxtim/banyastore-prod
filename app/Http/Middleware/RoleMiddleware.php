<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->hasRole($role)) {
            return abort(403);
        }

        return $next($request);
    }
}
