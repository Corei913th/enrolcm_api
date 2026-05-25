<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Application\Dashboard\DashboardService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Application\Dashboard\DashboardService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-faff75877ec99887a08d3623500910ff2588e088b9cb0406ad53ab6dd975567f',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Application/Dashboard/DashboardService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Application\\Dashboard',
    'name' => 'App\\Services\\Application\\Dashboard\\DashboardService',
    'shortName' => 'DashboardService',
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
    'endLine' => 446,
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
      'getGlobalStats' => 
      array (
        'name' => 'getGlobalStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir les statistiques globales (Super Admin)
 * 
 * @return array Statistiques globales du système
 */',
        'startLine' => 23,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getEcoleStats' => 
      array (
        'name' => 'getEcoleStats',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 46,
            'endLine' => 46,
            'startColumn' => 35,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir les statistiques d\'une école spécifique (Admin École)
 * 
 * @param string $ecoleId UUID de l\'école
 * @return array Statistiques de l\'école
 */',
        'startLine' => 46,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getEcolesStats' => 
      array (
        'name' => 'getEcolesStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques des écoles
 */',
        'startLine' => 72,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getConcoursStats' => 
      array (
        'name' => 'getConcoursStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques des concours (global)
 */',
        'startLine' => 84,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getCandidatsStats' => 
      array (
        'name' => 'getCandidatsStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques des candidats (global)
 */',
        'startLine' => 103,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getFinancialStats' => 
      array (
        'name' => 'getFinancialStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques financières (global)
 */',
        'startLine' => 117,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getCompletionStats' => 
      array (
        'name' => 'getCompletionStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Taux de complétion des dossiers (global)
 */',
        'startLine' => 132,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getEvolutionStats' => 
      array (
        'name' => 'getEvolutionStats',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Évolution des inscriptions sur 30 jours
 */',
        'startLine' => 157,
        'endLine' => 178,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getTopEcoles' => 
      array (
        'name' => 'getTopEcoles',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Top 5 écoles avec le plus d\'inscriptions
 */',
        'startLine' => 186,
        'endLine' => 216,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getProchainesEcheances' => 
      array (
        'name' => 'getProchainesEcheances',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prochaines échéances (concours fermant dans 48h)
 */',
        'startLine' => 221,
        'endLine' => 242,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getAlertes' => 
      array (
        'name' => 'getAlertes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Alertes urgentes (global)
 */',
        'startLine' => 247,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getConcoursStatsForEcole' => 
      array (
        'name' => 'getConcoursStatsForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 262,
            'endLine' => 262,
            'startColumn' => 47,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques des concours pour une école
 */',
        'startLine' => 262,
        'endLine' => 279,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getCandidatsStatsForEcole' => 
      array (
        'name' => 'getCandidatsStatsForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 284,
            'endLine' => 284,
            'startColumn' => 48,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques des candidats pour une école
 */',
        'startLine' => 284,
        'endLine' => 294,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getFinancialStatsForEcole' => 
      array (
        'name' => 'getFinancialStatsForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 48,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Statistiques financières pour une école
 */',
        'startLine' => 299,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getCompletionStatsForEcole' => 
      array (
        'name' => 'getCompletionStatsForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 315,
            'endLine' => 315,
            'startColumn' => 49,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Taux de complétion pour une école
 */',
        'startLine' => 315,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getAlertesForEcole' => 
      array (
        'name' => 'getAlertesForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 344,
            'endLine' => 344,
            'startColumn' => 41,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Alertes pour une école
 */',
        'startLine' => 344,
        'endLine' => 368,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getProchainesEcheancesForEcole' => 
      array (
        'name' => 'getProchainesEcheancesForEcole',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 373,
            'endLine' => 373,
            'startColumn' => 53,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prochaines échéances pour une école
 */',
        'startLine' => 373,
        'endLine' => 393,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'aliasName' => NULL,
      ),
      'getActiviteRecente' => 
      array (
        'name' => 'getActiviteRecente',
        'parameters' => 
        array (
          'ecoleId' => 
          array (
            'name' => 'ecoleId',
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
            'startLine' => 398,
            'endLine' => 398,
            'startColumn' => 41,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Activité récente pour une école
 */',
        'startLine' => 398,
        'endLine' => 445,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Application\\Dashboard',
        'declaringClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'implementingClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
        'currentClassName' => 'App\\Services\\Application\\Dashboard\\DashboardService',
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