<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return api_error('Non authentifié', 'UNAUTHENTICATED', 401);
        }

        $user = $request->user();
        $userRoles = $user->roles->pluck('libelle_role')->toArray();

        // Vérifier si l'utilisateur a au moins un des rôles requis
        $hasRole = !empty(array_intersect($roles, $userRoles));

        if (!$hasRole) {
            return api_error(
                'Accès refusé. Rôle requis: ' . implode(' ou ', $roles),
                'FORBIDDEN',
                403
            );
        }

        return $next($request);
    }
}
