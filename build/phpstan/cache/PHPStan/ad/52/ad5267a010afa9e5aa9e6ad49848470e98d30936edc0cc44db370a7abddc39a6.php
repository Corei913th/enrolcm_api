<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Http\Controllers\Auth\AuthController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Auth\AuthController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-29ca93036563a6be35dce11d608cdab38d58c4ac54eb15b09ca71168b4f39565',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Http/Controllers/Auth/AuthController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Auth',
    'name' => 'App\\Http\\Controllers\\Auth\\AuthController',
    'shortName' => 'AuthController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 260,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Controllers\\Controller',
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
      'authService' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'name' => 'authService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Auth\\AuthService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 9,
        'endColumn' => 49,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'notificationService' => 
      array (
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'name' => 'notificationService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Notification\\NotificationService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 9,
        'endColumn' => 65,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'authService' => 
          array (
            'name' => 'authService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Auth\\AuthService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 19,
            'endLine' => 19,
            'startColumn' => 9,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'notificationService' => 
          array (
            'name' => 'notificationService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Notification\\NotificationService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 20,
            'endLine' => 20,
            'startColumn' => 9,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 18,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'login' => 
      array (
        'name' => 'login',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Http\\Requests\\Auth\\LoginRequest',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 27,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Connexion d\'un utilisateur.
 *
 * Endpoint : POST /api/auth/login
 *
 * @param LoginRequest $request Requête validée contenant user_name et mot_de_passe
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON avec utilisateur et tokens
 *
 * @throws ValidationException Si la requête est invalide
 * @throws \\Exception Si l\'authentification échoue
 */',
        'startLine' => 35,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'loginCandidat' => 
      array (
        'name' => 'loginCandidat',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Http\\Requests\\Auth\\LoginRequest',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 35,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Connexion d\'un utilisateur.
 *
 * Endpoint : POST /api/auth/candidat/login
 *
 * @param LoginRequest $request Requête validée contenant email et mot_de_passe
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON avec utilisateur et tokens
 *
 * @throws ValidationException Si la requête est invalide
 * @throws \\Exception Si l\'authentification échoue
 */',
        'startLine' => 68,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'refresh' => 
      array (
        'name' => 'refresh',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 29,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Rafraîchir le token d\'accès.
 *
 * Endpoint : POST /api/auth/refresh
 *
 * @param Request $request Requête contenant le refresh_token
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON avec nouveaux tokens
 *
 * @throws \\Exception Si le refresh échoue
 */',
        'startLine' => 99,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'logout' => 
      array (
        'name' => 'logout',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 130,
            'endLine' => 130,
            'startColumn' => 28,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Déconnexion de l\'utilisateur courant.
 *
 * Endpoint : POST /api/auth/logout
 *
 * @param Request $request Requête contenant l\'utilisateur connecté
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON succès
 */',
        'startLine' => 130,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'logoutAll' => 
      array (
        'name' => 'logoutAll',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 31,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Déconnexion de tous les appareils.
 *
 * Endpoint : POST /api/auth/logout-all
 *
 * @param Request $request Requête contenant l\'utilisateur connecté
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON succès
 */',
        'startLine' => 146,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'me' => 
      array (
        'name' => 'me',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 162,
            'endLine' => 162,
            'startColumn' => 24,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir l\'utilisateur connecté.
 *
 * Endpoint : GET /api/auth/me
 *
 * @param Request $request Requête contenant l\'utilisateur connecté
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON avec utilisateur
 */',
        'startLine' => 162,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'changePassword' => 
      array (
        'name' => 'changePassword',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Http\\Requests\\Auth\\ChangePasswordRequest',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 180,
            'endLine' => 180,
            'startColumn' => 36,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Changer le mot de passe de l\'utilisateur connecté.
 *
 * Endpoint : POST /api/auth/change-password
 *
 * @param ChangePasswordRequest $request Requête validée contenant current_password et new_password
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON succès ou erreur
 *
 * @throws \\Exception Si le changement échoue
 */',
        'startLine' => 180,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'verifyEmail' => 
      array (
        'name' => 'verifyEmail',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'id' => 
          array (
            'name' => 'id',
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 51,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'hash' => 
          array (
            'name' => 'hash',
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 63,
            'endColumn' => 74,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifier l\'email d\'un utilisateur.
 *
 * Endpoint : GET /api/auth/email/verify/{id}/{hash}
 *
 * @param Request $request Requête contenant l\'utilisateur connecté
 * @param string $id ID de l\'utilisateur
 * @param string $hash Hash de vérification
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON succès ou erreur
 */',
        'startLine' => 203,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'aliasName' => NULL,
      ),
      'resendVerificationEmail' => 
      array (
        'name' => 'resendVerificationEmail',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 45,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Renvoyer l\'email de vérification.
 *
 * Endpoint : POST /api/auth/email/resend
 *
 * @param Request $request Requête contenant l\'utilisateur connecté
 *
 * @return \\Illuminate\\Http\\JsonResponse Réponse JSON succès ou erreur
 */',
        'startLine' => 244,
        'endLine' => 259,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Auth',
        'declaringClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'implementingClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
        'currentClassName' => 'App\\Http\\Controllers\\Auth\\AuthController',
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