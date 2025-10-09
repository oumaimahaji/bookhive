<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirige si non connecté
        }

        $user = Auth::user();

        // Vérifier le rôle
        if ($user->role !== $role) {
            abort(403, 'Accès interdit !'); // Accès interdit si rôle non correspondant
        }

        return $next($request);
    }
}
