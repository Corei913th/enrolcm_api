<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Helpers\ResponseHelper.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Helpers\ResponseHelper
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-c2b70b0653361f763311f461f2a8ac566f3059e0abd832492b357a5ff554b0b1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Helpers\\ResponseHelper',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/ResponseHelper.php',
      ),
    ),
    'namespace' => 'App\\Helpers',
    'name' => 'App\\Helpers\\ResponseHelper',
    'shortName' => 'ResponseHelper',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 252,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'success' => 
      array (
        'name' => 'success',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 32,
                'startFilePos' => 1126,
                'endTokenPos' => 32,
                'endFilePos' => 1129,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 41,
                'startFilePos' => 1150,
                'endTokenPos' => 41,
                'endFilePos' => 1153,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 50,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => '200',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 50,
                'startFilePos' => 1168,
                'endTokenPos' => 50,
                'endFilePos' => 1170,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 74,
            'endColumn' => 88,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized success response
 *
 * @param mixed $data The data to include in the response (optional) - often an API Resource or array
 * @param string|null $message Success message (optional)
 * @param int $code HTTP status code (default: 200)
 *
 * @return JsonResponse JSON response with success structure
 *
 * @example
 * // Controller usage with API Resources
 * $user = User::find(1);
 * return ResponseHelper::success(new \\App\\Http\\Resources\\UserResource($user), \'User retrieved successfully\');
 * // Returns: {"success": true, "message": "User retrieved successfully", "data": {...}}
 *
 * @example
 * // Controller usage with collections
 * $concours = Concours::all();
 * return ResponseHelper::success(\\App\\Http\\Resources\\ConcoursResource::collection($concours), \'Concours list retrieved\');
 * // Returns: {"success": true, "message": "Concours list retrieved", "data": [...]}
 */',
        'startLine' => 31,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'error' => 
      array (
        'name' => 'error',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 34,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'errors' => 
          array (
            'name' => 'errors',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 61,
                'endLine' => 61,
                'startTokenPos' => 154,
                'startFilePos' => 2075,
                'endTokenPos' => 154,
                'endFilePos' => 2078,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 51,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => '400',
              'attributes' => 
              array (
                'startLine' => 61,
                'endLine' => 61,
                'startTokenPos' => 163,
                'startFilePos' => 2093,
                'endTokenPos' => 163,
                'endFilePos' => 2095,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 67,
            'endColumn' => 81,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized error response
 *
 * @param string $message Error message
 * @param mixed $errors Additional error details or validation errors (optional)
 * @param int $code HTTP status code (default: 400)
 *
 * @return JsonResponse JSON response with error structure
 *
 * @example
 * ResponseHelper::error(\'Invalid input\', [\'email\' => \'Email is required\'], 422);
 * // Returns: {"success": false, "message": "Invalid input", "errors": {"email": "Email is required"}}
 */',
        'startLine' => 61,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'created' => 
      array (
        'name' => 'created',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 89,
                'endLine' => 89,
                'startTokenPos' => 249,
                'startFilePos' => 3100,
                'endTokenPos' => 249,
                'endFilePos' => 3103,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Ressource créée avec succès\'',
              'attributes' => 
              array (
                'startLine' => 89,
                'endLine' => 89,
                'startTokenPos' => 258,
                'startFilePos' => 3124,
                'endTokenPos' => 258,
                'endFilePos' => 3155,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 50,
            'endColumn' => 99,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized resource creation response
 *
 * @param mixed $data The created resource data (optional) - typically an API Resource
 * @param string $message Success message (default: \'Ressource créée avec succès\')
 *
 * @return JsonResponse JSON response with 201 status code
 *
 * @example
 * // Controller usage after creating a resource
 * $concours = Concours::create($validatedData);
 * return ResponseHelper::created(new \\App\\Http\\Resources\\ConcoursResource($concours), \'Concours created successfully\');
 * // Returns: {"success": true, "message": "Concours created successfully", "data": {...}} with HTTP 201
 */',
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'updated' => 
      array (
        'name' => 'updated',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 108,
                'endLine' => 108,
                'startTokenPos' => 298,
                'startFilePos' => 3924,
                'endTokenPos' => 298,
                'endFilePos' => 3927,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Ressource mise à jour avec succès\'',
              'attributes' => 
              array (
                'startLine' => 108,
                'endLine' => 108,
                'startTokenPos' => 307,
                'startFilePos' => 3948,
                'endTokenPos' => 307,
                'endFilePos' => 3984,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 50,
            'endColumn' => 104,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized resource update response
 *
 * @param mixed $data The updated resource data (optional) - typically an API Resource
 * @param string $message Success message (default: \'Ressource mise à jour avec succès\')
 *
 * @return JsonResponse JSON response with success structure
 *
 * @example
 * // Controller usage after updating a resource
 * $departement->update($validatedData);
 * return ResponseHelper::updated($departement, \'Departement updated successfully\');
 * // Returns: {"success": true, "message": "Departement updated successfully", "data": {...}}
 */',
        'startLine' => 108,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'deleted' => 
      array (
        'name' => 'deleted',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Ressource supprimée avec succès\'',
              'attributes' => 
              array (
                'startLine' => 124,
                'endLine' => 124,
                'startTokenPos' => 349,
                'startFilePos' => 4534,
                'endTokenPos' => 349,
                'endFilePos' => 4568,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 124,
            'endLine' => 124,
            'startColumn' => 36,
            'endColumn' => 88,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized resource deletion response
 *
 * @param string $message Success message (default: \'Ressource supprimée avec succès\')
 *
 * @return JsonResponse JSON response with success structure (no data)
 *
 * @example
 * ResponseHelper::deleted(\'User deleted successfully\');
 * // Returns: {"success": true, "message": "User deleted successfully"}
 */',
        'startLine' => 124,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'notFound' => 
      array (
        'name' => 'notFound',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Ressource non trouvée\'',
              'attributes' => 
              array (
                'startLine' => 140,
                'endLine' => 140,
                'startTokenPos' => 391,
                'startFilePos' => 5083,
                'endTokenPos' => 391,
                'endFilePos' => 5106,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 140,
            'endLine' => 140,
            'startColumn' => 37,
            'endColumn' => 78,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized 404 Not Found response
 *
 * @param string $message Error message (default: \'Ressource non trouvée\')
 *
 * @return JsonResponse JSON response with 404 status code
 *
 * @example
 * ResponseHelper::notFound(\'User not found\');
 * // Returns: {"success": false, "message": "User not found"} with HTTP 404
 */',
        'startLine' => 140,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'unauthorized' => 
      array (
        'name' => 'unauthorized',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Non autorisé\'',
              'attributes' => 
              array (
                'startLine' => 156,
                'endLine' => 156,
                'startTokenPos' => 433,
                'startFilePos' => 5639,
                'endTokenPos' => 433,
                'endFilePos' => 5653,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 156,
            'endLine' => 156,
            'startColumn' => 41,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized 401 Unauthorized response
 *
 * @param string $message Error message (default: \'Non autorisé\')
 *
 * @return JsonResponse JSON response with 401 status code
 *
 * @example
 * ResponseHelper::unauthorized(\'Authentication required\');
 * // Returns: {"success": false, "message": "Authentication required"} with HTTP 401
 */',
        'startLine' => 156,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'forbidden' => 
      array (
        'name' => 'forbidden',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Accès interdit\'',
              'attributes' => 
              array (
                'startLine' => 172,
                'endLine' => 172,
                'startTokenPos' => 475,
                'startFilePos' => 6181,
                'endTokenPos' => 475,
                'endFilePos' => 6197,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 38,
            'endColumn' => 72,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized 403 Forbidden response
 *
 * @param string $message Error message (default: \'Accès interdit\')
 *
 * @return JsonResponse JSON response with 403 status code
 *
 * @example
 * ResponseHelper::forbidden(\'Insufficient permissions\');
 * // Returns: {"success": false, "message": "Insufficient permissions"} with HTTP 403
 */',
        'startLine' => 172,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'validationError' => 
      array (
        'name' => 'validationError',
        'parameters' => 
        array (
          'errors' => 
          array (
            'name' => 'errors',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 44,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Erreur de validation\'',
              'attributes' => 
              array (
                'startLine' => 189,
                'endLine' => 189,
                'startTokenPos' => 520,
                'startFilePos' => 6900,
                'endTokenPos' => 520,
                'endFilePos' => 6921,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 53,
            'endColumn' => 92,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized validation error response
 *
 * @param mixed $errors Validation errors array or object
 * @param string $message Error message (default: \'Erreur de validation\')
 *
 * @return JsonResponse JSON response with 422 status code and validation errors
 *
 * @example
 * ResponseHelper::validationError([\'email\' => \'Email is required\'], \'Validation failed\');
 * // Returns: {"success": false, "message": "Validation failed", "errors": {"email": "Email is required"}} with HTTP 422
 */',
        'startLine' => 189,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
      'paginated' => 
      array (
        'name' => 'paginated',
        'parameters' => 
        array (
          'paginatedData' => 
          array (
            'name' => 'paginatedData',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 223,
            'endLine' => 223,
            'startColumn' => 38,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 223,
                'endLine' => 223,
                'startTokenPos' => 565,
                'startFilePos' => 8116,
                'endTokenPos' => 565,
                'endFilePos' => 8119,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 223,
            'endLine' => 223,
            'startColumn' => 54,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'resourceClass' => 
          array (
            'name' => 'resourceClass',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 223,
                'endLine' => 223,
                'startTokenPos' => 575,
                'startFilePos' => 8147,
                'endTokenPos' => 575,
                'endFilePos' => 8150,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 223,
            'endLine' => 223,
            'startColumn' => 78,
            'endColumn' => 106,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a standardized paginated response
 *
 * Formats Laravel paginated data with optional resource transformation and includes
 * comprehensive pagination metadata in the response.
 *
 * @param \\Illuminate\\Contracts\\Pagination\\LengthAwarePaginator $paginatedData Laravel paginated data
 * @param string|null $message Optional success message
 * @param string|null $resourceClass Optional API resource class for data transformation
 *
 * @return JsonResponse JSON response with paginated data and metadata
 *
 * @example
 * $users = User::paginate(10);
 * ResponseHelper::paginated($users, \'Users retrieved\', UserResource::class);
 * // Returns: {
 * //   "success": true,
 * //   "message": "Users retrieved",
 * //   "data": [...],
 * //   "meta": {
 * //     "current_page": 1,
 * //     "last_page": 5,
 * //     "per_page": 10,
 * //     "total": 50,
 * //     "from": 1,
 * //     "to": 10
 * //   }
 * // }
 */',
        'startLine' => 223,
        'endLine' => 251,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Helpers',
        'declaringClassName' => 'App\\Helpers\\ResponseHelper',
        'implementingClassName' => 'App\\Helpers\\ResponseHelper',
        'currentClassName' => 'App\\Helpers\\ResponseHelper',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));