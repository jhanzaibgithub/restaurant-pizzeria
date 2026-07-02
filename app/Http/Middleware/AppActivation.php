<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppActivation
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $app_id
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $app_id)
    {
        return $next($request);
    }
}
