<?php

use App\Helpers\ResponseHelper;



/**
 * Create a standardized API success response
 *
 * @param mixed $data The data to include in the response (optional) - often an API Resource
 * @param string|null $message Success message (optional)
 * @param int $code HTTP status code (default: 200)
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_success(new EcoleResource($ecole), 'Ecole retrieved successfully');
 *
 * @see ResponseHelper::success()
 */
if (!function_exists('api_success')) {
    function api_success($data = null, string $message = null, int $code = 200)
    {
        return ResponseHelper::success($data, $message, $code);
    }
}

/**
 * Create a standardized API error response
 *
 * @param string $message Error message
 * @param mixed $errors Additional error details (optional)
 * @param int $code HTTP status code (default: 400)
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_error('Invalid input', $validationErrors, 422);
 *
 * @see ResponseHelper::error()
 */
if (!function_exists('api_error')) {
    function api_error(string $message, $errors = null, int $code = 400)
    {
        return ResponseHelper::error($message, $errors, $code);
    }
}

/**
 * Create a standardized API resource creation response
 *
 * @param mixed $data The created resource data (optional) - typically an API Resource
 * @param string $message Success message (default: 'Ressource créée avec succès')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_created(new FiliereResource($filiere));
 *
 * @see ResponseHelper::created()
 */
if (!function_exists('api_created')) {
    function api_created($data = null, string $message = 'Ressource créée avec succès')
    {
        return ResponseHelper::created($data, $message);
    }
}

/**
 * Create a standardized API resource update response
 *
 * @param mixed $data The updated resource data (optional) - typically an API Resource
 * @param string $message Success message (default: 'Ressource mise à jour avec succès')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_updated(new CandidatResource($candidat));
 *
 * @see ResponseHelper::updated()
 */
if (!function_exists('api_updated')) {
    function api_updated($data = null, string $message = 'Ressource mise à jour avec succès')
    {
        return ResponseHelper::updated($data, $message);
    }
}

/**
 * Create a standardized API resource deletion response
 *
 * @param string $message Success message (default: 'Ressource supprimée avec succès')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_deleted();
 *
 * @see ResponseHelper::deleted()
 */
if (!function_exists('api_deleted')) {
    function api_deleted(string $message = 'Ressource supprimée avec succès')
    {
        return ResponseHelper::deleted($message);
    }
}

/**
 * Create a standardized API 404 Not Found response
 *
 * @param string $message Error message (default: 'Ressource non trouvée')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_not_found('User not found');
 *
 * @see ResponseHelper::notFound()
 */
if (!function_exists('api_not_found')) {
    function api_not_found(string $message = 'Ressource non trouvée')
    {
        return ResponseHelper::notFound($message);
    }
}

/**
 * Create a standardized API 401 Unauthorized response
 *
 * @param string $message Error message (default: 'Non autorisé')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_unauthorized('Authentication required');
 *
 * @see ResponseHelper::unauthorized()
 */
if (!function_exists('api_unauthorized')) {
    function api_unauthorized(string $message = 'Non autorisé')
    {
        return ResponseHelper::unauthorized($message);
    }
}

/**
 * Create a standardized API 403 Forbidden response
 *
 * @param string $message Error message (default: 'Accès interdit')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_forbidden('Insufficient permissions');
 *
 * @see ResponseHelper::forbidden()
 */
if (!function_exists('api_forbidden')) {
    function api_forbidden(string $message = 'Accès interdit')
    {
        return ResponseHelper::forbidden($message);
    }
}

/**
 * Create a standardized API validation error response
 *
 * @param mixed $errors Validation errors
 * @param string $message Error message (default: 'Erreur de validation')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * return api_validation_error($validator->errors());
 *
 * @see ResponseHelper::validationError()
 */
if (!function_exists('api_validation_error')) {
    function api_validation_error($errors, string $message = 'Erreur de validation')
    {
        return ResponseHelper::validationError($errors, $message);
    }
}

/**
 * Create a standardized API paginated response
 *
 * Automatically transforms paginated data using the specified resource class.
 * Essential for controller methods that return lists of resources.
 *
 * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginatedData Laravel paginated data
 * @param string|null $message Optional success message
 * @param string|null $resourceClass Optional API resource class for data transformation (e.g., 'App\Http\Resources\EcoleResource')
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @example
 * $ecoles = Ecole::paginate(10);
 * return api_paginated($ecoles, 'Ecoles retrieved', EcoleResource::class);
 *
 * @example
 * $concours = Concours::with('filieres')->paginate(15);
 * return api_paginated($concours, null, ConcoursResource::class);
 *
 * @see ResponseHelper::paginated()
 */
if (!function_exists('api_paginated')) {
    function api_paginated($paginatedData, string $message = null, ?string $resourceClass = null)
    {
        return ResponseHelper::paginated($paginatedData, $message, $resourceClass);
    }
}
