<?php

namespace App\Http\Middleware;

use App\Enums\TypeUtilisateur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return api_error('Non authentifié', 'UNAUTHENTICATED', 401);
        }

        $user = $request->user();

        // SUPER_ADMIN a accès à tout
        if ($user->type_utilisateur === TypeUtilisateur::SUPER_ADMIN) {
            return $next($request);
        }

        // Vérifier d'abord le type_utilisateur (pour CANDIDAT notamment)
        $userTypeValue = $user->type_utilisateur instanceof TypeUtilisateur
            ? $user->type_utilisateur->value
            : $user->type_utilisateur;

        if (in_array($userTypeValue, $roles)) {
            return $next($request);
        }

        // Vérifier ensuite les rôles de la table roles (pour ADMIN, RESPONSABLE, etc.)
        $userRoles = $user->roles->pluck('libelle_role')->toArray();

        if (in_array(TypeUtilisateur::SUPER_ADMIN->value, $userRoles)) {
            return $next($request);
        }

        $hasRole = ! empty(array_intersect($roles, $userRoles));

        if (! $hasRole) {
            return api_error(
                'Accès refusé. Rôle requis: ' . implode(' ou ', $roles),
                'FORBIDDEN',
                403
            );
        }

        return $next($request);
    }
}
