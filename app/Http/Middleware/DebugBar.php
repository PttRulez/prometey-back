<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugBar
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
        if (auth()->user() && in_array(auth()->id(), [1])) {
            \Debugbar::enable();
        }
        else {
            \Debugbar::disable();
        }

        return $next($request);
    }
}
