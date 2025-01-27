<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
      if (auth()->check() && in_array(auth()->user()->role, $roles)) {
          return $next($request);
      }

        return redirect('/pages/misc-error');
    }
}