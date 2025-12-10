<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production') && !$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        // Force asset URLs to use HTTPS only in production
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }

        return $next($request);
    }
}
