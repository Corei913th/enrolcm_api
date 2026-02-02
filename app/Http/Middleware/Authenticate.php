<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   */
  protected function redirectTo(Request $request): ?string
  {
    // Pour les requêtes API, ne pas rediriger, retourner null
    // Cela fera que Laravel retournera une erreur 401 au lieu de rediriger
    return null;
  }
}
