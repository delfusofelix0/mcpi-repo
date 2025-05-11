<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DisplayTokenAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validToken = config('display.token');

        if (empty($validToken)) {
            abort(403, 'Display token not configured');
        }

        try {
            $requestToken = $request->query('token');

            // Decrypt the token from the request
            $decryptedToken = Crypt::decryptString($requestToken);

            if ($decryptedToken !== $validToken) {
                throw new \Exception('Invalid token');
            }
        } catch (\Exception $e) {
            abort(403, 'Invalid display token');
        }

        return $next($request);
    }
}
