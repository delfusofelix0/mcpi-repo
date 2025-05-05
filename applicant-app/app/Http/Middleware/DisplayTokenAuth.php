<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisplayTokenAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validToken = config('app.display_token', 'UWUXD');

        if ($request->query('token') !== $validToken) {
            abort(403, 'Invalid display token');
        }

        return $next($request);
    }
}
