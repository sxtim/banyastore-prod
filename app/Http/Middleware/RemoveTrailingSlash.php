<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Если URL заканчивается на слэш, редиректим на URL без слэша
        if (rtrim($request->getRequestUri(), '/') !== $request->getRequestUri()) {
            return redirect(rtrim($request->url(), '/'), 301);
        }

        return $next($request);
    }
}
