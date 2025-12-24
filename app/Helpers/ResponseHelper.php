<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Réponse de succès
     */
    public static function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Réponse d'erreur
     */
    public static function error(string $message, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Réponse de création
     */
    public static function created($data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Réponse de mise à jour
     */
    public static function updated($data = null, string $message = 'Ressource mise à jour avec succès'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    /**
     * Réponse de suppression
     */
    public static function deleted(string $message = 'Ressource supprimée avec succès'): JsonResponse
    {
        return self::success(null, $message, 200);
    }

    /**
     * Réponse non trouvée
     */
    public static function notFound(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return self::error($message, null, 404);
    }

    /**
     * Réponse non autorisée
     */
    public static function unauthorized(string $message = 'Non autorisé'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Réponse interdite
     */
    public static function forbidden(string $message = 'Accès interdit'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Réponse de validation échouée
     */
    public static function validationError($errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    /**
     * Réponse avec pagination
     */
    public static function paginated($paginatedData, string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        $response['data'] = $paginatedData->items();
        $response['meta'] = [
            'current_page' => $paginatedData->currentPage(),
            'last_page' => $paginatedData->lastPage(),
            'per_page' => $paginatedData->perPage(),
            'total' => $paginatedData->total(),
            'from' => $paginatedData->firstItem(),
            'to' => $paginatedData->lastItem(),
        ];

        return response()->json($response, 200);
    }
}
