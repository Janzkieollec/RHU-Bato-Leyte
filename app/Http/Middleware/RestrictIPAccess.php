<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIPAccess
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
        // Replace with your own IP address
        $allowedIp = '192.168.254.110'; // e.g. 192.168.1.100

        // Check if the user's IP matches the allowed IP
        if ($request->ip() !== $allowedIp) {
            // Redirect to maintenance page
            return redirect()->route('maintenance');
        }

        return $next($request);
    }
}