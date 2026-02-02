<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return api_error('Non authentifié', 'UNAUTHENTICATED', 401);
        }

        $user = $request->user();
        
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a au moins une des permissions requises
        $hasPermission = $user->roles()
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('libelle_permission', $permissions);
            })
            ->exists();

        if (!$hasPermission) {
            return api_error(
                'Accès refusé. Permission requise: ' . implode(' ou ', $permissions),
                'FORBIDDEN',
                403
            );
        }

        return $next($request);
    }
}
