<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Gestion des exceptions de validation
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation.',
                    'error_code' => 'VALIDATION_ERROR',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Gestion des exceptions d'authentification
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié.',
                    'error_code' => 'UNAUTHENTICATED',
                ], 401);
            }
        });

        // Gestion des exceptions d'autorisation
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Action non autorisée.',
                    'error_code' => 'FORBIDDEN',
                ], 403);
            }
        });

        // Gestion des erreurs 404
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ressource introuvable.',
                    'error_code' => 'NOT_FOUND',
                ], 404);
            }
        });

        // Gestion des erreurs 405 (Method Not Allowed)
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Méthode HTTP non autorisée.',
                    'error_code' => 'METHOD_NOT_ALLOWED',
                ], 405);
            }
        });

        // Gestion des erreurs de throttle (trop de requêtes)
        $exceptions->renderable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trop de tentatives. Veuillez réessayer plus tard.',
                    'error_code' => 'TOO_MANY_REQUESTS',
                ], 429);
            }
        });

        // Gestion des erreurs de base de données
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->expectsJson()) {
                $message = config('app.debug') 
                    ? $e->getMessage() 
                    : 'Erreur de base de données.';
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'DATABASE_ERROR',
                ], 500);
            }
        });

        // Gestion des erreurs génériques
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson() && !($e instanceof \Illuminate\Http\Exceptions\HttpResponseException)) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                
                $message = config('app.debug') 
                    ? $e->getMessage() 
                    : 'Une erreur est survenue.';
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'INTERNAL_SERVER_ERROR',
                    'trace' => config('app.debug') ? $e->getTrace() : null,
                ], $statusCode);
            }
        });
    })->create();
