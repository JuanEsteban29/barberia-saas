<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Str::lower(Auth::user()->role);

        foreach ($roles as $role) {
            if ($userRole === Str::lower($role)) {
                return $next($request);
            }
        }

        // Si es barbero intentando entrar a ruta admin, lo mandamos a su panel
        if ($userRole === 'barbero') {
            return redirect()->route('barbero.dashboard')
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        // Si es cualquier otro caso, abortamos con 403
        abort(403, 'Acceso no autorizado.');
    }
}
