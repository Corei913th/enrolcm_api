<?php

use App\Helpers\ResponseHelper;

if (!function_exists('api_success')) {
    function api_success($data = null, string $message = null, int $code = 200)
    {
        return ResponseHelper::success($data, $message, $code);
    }
}

if (!function_exists('api_error')) {
    function api_error(string $message, $errors = null, int $code = 400)
    {
        return ResponseHelper::error($message, $errors, $code);
    }
}

if (!function_exists('api_created')) {
    function api_created($data = null, string $message = 'Ressource créée avec succès')
    {
        return ResponseHelper::created($data, $message);
    }
}

if (!function_exists('api_updated')) {
    function api_updated($data = null, string $message = 'Ressource mise à jour avec succès')
    {
        return ResponseHelper::updated($data, $message);
    }
}

if (!function_exists('api_deleted')) {
    function api_deleted(string $message = 'Ressource supprimée avec succès')
    {
        return ResponseHelper::deleted($message);
    }
}

if (!function_exists('api_not_found')) {
    function api_not_found(string $message = 'Ressource non trouvée')
    {
        return ResponseHelper::notFound($message);
    }
}

if (!function_exists('api_unauthorized')) {
    function api_unauthorized(string $message = 'Non autorisé')
    {
        return ResponseHelper::unauthorized($message);
    }
}

if (!function_exists('api_forbidden')) {
    function api_forbidden(string $message = 'Accès interdit')
    {
        return ResponseHelper::forbidden($message);
    }
}

if (!function_exists('api_validation_error')) {
    function api_validation_error($errors, string $message = 'Erreur de validation')
    {
        return ResponseHelper::validationError($errors, $message);
    }
}

if (!function_exists('api_paginated')) {
    function api_paginated($paginatedData, string $message = null)
    {
        return ResponseHelper::paginated($paginatedData, $message);
    }
}
