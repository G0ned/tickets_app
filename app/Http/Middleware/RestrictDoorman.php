<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictDoorman
{
    private array $allowedRoutes = ['checkin', 'checkin-store', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isDoorman() && !Auth::user()->is_admin) {
            if (!in_array($request->route()?->getName(), $this->allowedRoutes)) {
                return redirect()->route('checkin');
            }
        }

        return $next($request);
    }
}
