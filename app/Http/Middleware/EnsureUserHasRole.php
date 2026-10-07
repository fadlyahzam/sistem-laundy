<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        if (!in_array($userRole, $roles)) {
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
            } elseif ($userRole === 'driver') {
                return redirect()->route('driver.dashboard')->with('error', 'Akses ditolak.');
            } else {
                return redirect()->route('pelanggan.dashboard')->with('error', 'Akses ditolak.');
            }
        }

        return $next($request);
    }
}
